<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/utils.php';

date_default_timezone_set('Asia/Hong_Kong');

// fetch chat messages in activity group in chronological order
function fetchChatHistory() {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        session_write_close();
        global $mysqli;
        $userID = $_SESSION["user_id"];
        $last_time = $_GET["last_time"];
        $activityID = $_GET["activity_id"];
        $host = $_GET["host"];

        $sql = "SELECT c.chat_message, c.from_user_id, c.timestamp, u.username AS from_username
                FROM activity_chat_message c
                INNER JOIN user u
                ON c.from_user_id = u.user_id
                WHERE UNIX_TIMESTAMP(c.timestamp) > ? AND c.activity_id = ?
                ORDER BY c.timestamp DESC";

        set_time_limit(30); // set an appropriate time limit
        ignore_user_abort(false); // stop when long polling breaks
        
        // wait until there are new messages or user abort the request or timeout
        while (!connection_aborted()) {
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ii", $last_time, $activityID);
            $stmt->execute();
            $chatHistory = $stmt->get_result();
            $recordNum = $chatHistory->num_rows;

            if ($recordNum > 0) {
                $chatRecord = $chatHistory->fetch_assoc();
                $lastTime = strtotime($chatRecord['timestamp']);
                $msg = "";

                do {
                    $userName = null;
                    if($chatRecord["from_user_id"] == $userID) { //success means particular user send a message
                        $userName .= '<b class="text-success">You</b>';
                    }
                    else {
                        $host = $_GET["host"] == $chatRecord['from_username'] ? "(Host)" : "";
                        $userName = "<b class='text-danger'>{$chatRecord['from_username']} {$host}</b>";
                    }

                    //display chat message text and time of chat (below)
                    $msg .= "<li style='border-bottom:1px dotted #ccc'>
                                {$userName}
                                <p>{$chatRecord['chat_message']}
                                    <div align='right'>
                                        <small class='d-block text-right'> - <em>{$chatRecord['timestamp']}</em></small>
                                    </div>
                                </p>
                            </li>";
                } while ($chatRecord = $chatHistory->fetch_assoc());

                $sql = "UPDATE chat_message
                        SET status = TRUE
                        WHERE from_user_id = ? AND to_user_id = ?"; //update message from unseen to seen by changing from 0 to 1
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ii", $anotherUserID, $userID);
                $stmt->execute();

                $res = ["msg" => $msg, "lastTime" => $lastTime];
                echo json_encode($res);
                break;
            }

            sleep(1); // short pause to not break server
        }
    }
}

function fetchActivities() {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        global $mysqli;

        // select the activities that the user have joined
        $sql = "SELECT a.activity_id, a.activity_name, a.host
                FROM user_activity ua
                INNER JOIN activity a
                ON ua.activity_id = a.activity_id
                WHERE ua.user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $activities = $stmt->get_result();

        $table = '<table class="table table-sm table-bordered table-striped table-light">
                    <tr>
                        <th width="80%">Activity</th>
                        <th class="text-center">Chat</th>
                    </tr>';
        
        if ($activities->num_rows > 0) {
            while ($activity = $activities->fetch_assoc()) {
                //display number of unseen chat message and typing status on the frontend page of users in the system
                $table .= "<tr>";
                $table .= "<td><a href='/user/activity/activity_details.php?activity_id={$activity['activity_id']}'>{$activity['activity_name']}</a></td>";
                $table .= "<td class='text-center'><button type='button' class='btn btn-info btn-sm start-group-chat' 
                            data-activity-id='{$activity['activity_id']}' data-activity-name='{$activity['activity_name']}' data-host='{$activity['host']}'>Start Group Chat</button></td>";
                $table .= "</tr>";
            }
            
            $table .= '</table>';
            echo $table;
        }
        else {
            echo "<h3 class='text-center'>You have not joined any activities</h3>";
        }
    }
}

function sendMessage() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;

        $msg = $_POST['msg'];
        $userID = $_SESSION["user_id"];
        $activityID = $_POST['activityID'];
        $now = date("Y-m-d H:i:s"); //display the timestamp of a chat message when users view and send text messages

        //query for inserting chat message and its related information such as sender, receiver, 
        // and status (whether a message is read) into the database 
        $sql = "INSERT INTO activity_chat_message (activity_id, from_user_id, chat_message, timestamp) 
                VALUES (?, ?, ?, ?)";

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiss", $activityID, $userID, $msg, $now);
        $stmt->execute();
    }
}

//fetch chat history for groupchat system, similar to private chat system

if (isset($_GET['action'])) {
    if (function_exists($_GET['action'])) {
        $_GET['action']();
    }
}
?>