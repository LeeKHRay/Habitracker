<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/utils.php';

date_default_timezone_set('Asia/Hong_Kong');

// fetch chat messages between 2 users in chronological order
function fetchChatHistory() {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        session_write_close(); // avoid session lock
        global $mysqli;
        $last_time = $_GET["last_time"];
        $userID = $_SESSION["user_id"];
        $anotherUserID = $_GET["user_id"];

        $sql = "SELECT c.chat_message, c.from_user_id, c.timestamp, u.username AS from_username
                FROM chat_message c
                INNER JOIN user u
                ON c.from_user_id = u.user_id
                WHERE UNIX_TIMESTAMP(c.timestamp) > ? 
                    AND ((c.from_user_id = ? AND c.to_user_id = ?) 
                    OR (c.from_user_id = ? AND c.to_user_id = ?))
                ORDER BY `timestamp` DESC";

        set_time_limit(30); // set an appropriate time limit
        ignore_user_abort(false); // stop when long polling breaks
        
        // wait until there are new messages or user abort the request or timeout
        while (!connection_aborted()) {
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iiiii", $last_time, $anotherUserID, $userID, $userID, $anotherUserID);
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
                        $userName = "<b class='text-danger'>{$chatRecord['from_username']}</b>";
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

function fetchUsers() {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        global $mysqli;
        $sql = "SELECT u.user_id, u.username, u.last_activity, u.is_typing, SUM(NOT c.status) AS unseen_message_num
                FROM user u
                LEFT JOIN chat_message c
                ON u.user_id = c.from_user_id AND c.to_user_id = ?
                WHERE u.user_id != ?
                GROUP BY u.user_id";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
        $stmt->execute();
        $users = $stmt->get_result();

        $table = '<table class="table table-sm table-bordered table-striped table-light">
                    <tr>
                        <th width="80%">User</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Chat</th>
                    </tr>';
        
        if ($users->num_rows > 0) {
            while ($user = $users->fetch_assoc()) {
                //var_dump($user);
                $now = strtotime(date("Y-m-d H:i:s").'- 10 second');
                $userLastActivity = strtotime($user['last_activity']);

                $status = $userLastActivity > $now ? "Online" : "Offline"; // user's online/offline status
                $statusColor = $userLastActivity > $now ? "success" : "danger"; // color of online/offline status show in table
                $unseenMsgNum = $user['unseen_message_num'] > 0 ? "<span class='badge badge-pill badge-success'>{$user['unseen_message_num']}</span>" : "";
                $isTyping = $user['is_typing'] ? ' - <small><em><span class="text-muted">Typing...</span></em></small>' : "";

                //display number of unseen chat message and typing status on the frontend page of users in the system
                $table .= "<tr>";
                $table .= "<td><a href='/user/profile.php?user_id={$user['user_id']}'>{$user['username']}</a> {$unseenMsgNum} {$isTyping}</td>";
                $table .= "<td class='text-center'><span class='badge badge-{$statusColor}'>{$status}</span></td>";
                $table .= "<td class='text-center'><button type='button' class='btn btn-info btn-sm start-chat' data-user-id='{$user['user_id']}' data-username='{$user['username']}'>Start Chat</button></td>";
                $table .= "</tr>";
            }
            
            $table .= '</table>';
            echo $table;
        }
        else {
            echo "<h3 class='text-center'>No other users</h3>";
        }
    }
}

function sendMessage() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;

        $msg = $_POST['msg'];
        $userID = $_SESSION["user_id"];
        $anotherUserID = $_POST['toUserID'];
        $now = date("Y-m-d H:i:s"); //display the timestamp of a chat message when users view and send text messages

        //query for inserting chat message and its related information such as sender, receiver, 
        // and status (whether a message is read) into the database 
        $sql = "INSERT INTO chat_message (to_user_id, from_user_id, chat_message, timestamp, status) 
                VALUES (?, ?, ?, ?, 0)";

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiss", $anotherUserID, $userID, $msg, $now);
        $stmt->execute();
    }
}

function updateIsTyping() {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);
        
        // update the typing status of a user when one is typing in the chat dialog box
        $sql = "UPDATE user
                SET is_typing = ?
                WHERE user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $params["isTyping"], $_SESSION["user_id"]);
        $stmt->execute();
    }
}

if (isset($_GET['action'])) {
    if (function_exists($_GET['action'])) {
        $_GET['action']();
    }
}
?>