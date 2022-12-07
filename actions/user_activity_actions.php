<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/utils.php';

function createOneOff() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;
        $activity_name = $_POST['activityName'];
        $date = $_POST['date'];
        $time = $_POST['time'];

        if (empty($activity_name) || empty($date) || empty($time)) {
            echo response(false, "Please fill in all the required fields!");
            exit();
        }

        $activity_repetition = 0;
        $activity_one_off_datetime=$date." ".$time.":00";
        $activity_location = $_POST['location'];
        $activity_time_remark = $_POST['timeRemark'];
        $activity_remark = $_POST['remark'];
        $activity_status_open = 1;
        
        $sql = "INSERT INTO activity (activity_name, activity_repetition, activity_one_off_datetime, activity_location, 
                    activity_time_remark, activity_remark, activity_status_open, host)
                VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        
        $stmt->bind_param("ssssssss", $activity_name, $activity_repetition, $activity_one_off_datetime, $activity_location,
            $activity_time_remark, $activity_remark, $activity_status_open, $_SESSION["username"]);
        $stmt->execute();

        //insert username into the users list table
        $activityID = $mysqli->insert_id;
        $sql = "INSERT INTO user_activity (user_id, activity_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);    
        $stmt->bind_param("ss", $_SESSION["user_id"], $activityID);
        $stmt->execute();

        $_SESSION['msg'] = "One-off activity created";
        echo response(true, "/user/activity/my_activities.php");
    }
}

function createRecurring() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;
        $activity_name = $_POST['activityName'];

        if (empty($activity_name)) {
            echo response(false, "Please fill in all the required fields!");
            exit();
        }

        $activity_repetition = 0;
        $activity_recurring_date = [NULL, NULL, NULL];
        $activity_recurring_time = [NULL, NULL, NULL];

        for ($i = 0; $i < 7; $i++){
            if(!empty($_POST['time'.$i])) {
                $activity_recurring_date[$activity_repetition] = $i;
                $activity_recurring_time[$activity_repetition] = $_POST['time'.$i];   
                $activity_repetition++;
            }
        }

        if($activity_repetition > 3){
            echo response(false, "Please choose at most 3 days!");
            exit();
        }

        if($activity_repetition == 0){
            echo response(false, "Please choose at least 1 day!");
            exit();
        }

        $activity_location = $_POST['location'];
        $activity_time_remark = $_POST['timeRemark'];
        $activity_remark = $_POST['remark'];
        $activity_status_open = 1;

        $username = $_SESSION["username"];
        $user_id = $_SESSION["user_id"];

        $sql = "INSERT INTO activity (activity_name, activity_repetition,
                    activity_recurring_date_0, activity_recurring_time_0,
                    activity_recurring_date_1, activity_recurring_time_1,
                    activity_recurring_date_2, activity_recurring_time_2,
                    activity_location, activity_time_remark, activity_remark, activity_status_open, host)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssssssssssss", $activity_name,  $activity_repetition,
            $activity_recurring_date[0],$activity_recurring_time[0],
            $activity_recurring_date[1],$activity_recurring_time[1],
            $activity_recurring_date[2],$activity_recurring_time[2],
            $activity_location, $activity_time_remark, $activity_remark, $activity_status_open, $username);
        $stmt->execute();

        //insert username into the users list table
        $activityID = $mysqli->insert_id;
        $sql = "INSERT INTO user_activity (user_id, activity_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);    
        $stmt->bind_param("ss", $_SESSION["user_id"], $activityID);
        $stmt->execute();

        $_SESSION['msg'] = "Recurring activity created";
        echo response(true, "/user/activity/my_activities.php");
    }
}

function editActivity() {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);
        
        $activity_name = $params['activityName'];
        if (empty($activity_name)) {
            echo response(false, "Please provide a name for the activity");
            exit();
        }

        $activity_id = $params['activityID'];
        $activity_time_remark = $params['timeRemark'];
        $activity_remark = $params['remark'];
        $activity_status_open = isset($params['activityClose']) ? 0 : 1;
        
        $sql = "UPDATE activity SET activity_name = ?, activity_time_remark = ?, activity_remark = ?, activity_status_open = ?
                WHERE activity_id = ?";
        $stmt = $mysqli->prepare($sql);    
        $stmt->bind_param("ssssi", $activity_name, $activity_time_remark, $activity_remark, $activity_status_open, $activity_id);
        $stmt->execute();

        // update activity name in report if any
        $sql = "UPDATE report SET activity_name = ? WHERE activity_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $activity_name, $activity_id);
        $stmt->execute();
    
        $_SESSION['msg'] = "Activity updated";
        echo response(true, "/user/activity/my_activities.php");
    }
}

function deleteActivity() {
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        $activity_id = $params['activityID'];

        // remove activity
        $sql = "DELETE FROM activity WHERE activity_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $activity_id);
        $stmt->execute();

        // remove activity group
        $sql = "DELETE FROM user_activity WHERE activity_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $activity_id);
        $stmt->execute();
            
        // remove group chat data
        $sql = "DELETE FROM activity_chat_message WHERE activity_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $activity_id);
        $stmt->execute();

        $_SESSION['msg'] = "Activity deleted";
        echo response(true, "/user/activity/my_activities.php");
    }
}

function joinActivity() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        $activity_id = $params['activityID'];

        $sql = "INSERT INTO user_activity (user_id, activity_id) VALUES (?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $_SESSION["user_id"],  $activity_id);
        $stmt->execute();

        $_SESSION['msg'] = "Activity joined";
        echo response(true, "/user/activity/my_activities.php");
    }
}

function quitActivity() {
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        $activity_id = $params['activityID'];
        $user_id = $_SESSION["user_id"];

        $sql = "DELETE FROM user_activity WHERE user_id = ? AND activity_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $user_id,  $activity_id);
        $stmt->execute();

        $_SESSION['msg'] = "Activity quited";
        echo response(true, "/user/activity/my_activities.php");
    }
}

function searchActivities() {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        global $mysqli;

        if (empty($_GET['keyword'])) {
            echo response(false, "Please input the keyword!");
            exit();
        }

        $keywordMatch = "%{$_GET['keyword']}%";
        $sortby = $_GET['sortby'];
        $order = $_GET['order'];

        $sql = "SELECT a.activity_id, a.activity_name, a.activity_repetition, a.activity_one_off_datetime, 
                    a.activity_recurring_date_0, a.activity_recurring_time_0, 
                    a.activity_recurring_date_1, a.activity_recurring_time_1, 
                    a.activity_recurring_date_2, a.activity_recurring_time_2, 
                    a.activity_time_remark, a.activity_location, 
                    a.host, u.user_id AS host_id
                FROM activity a 
                INNER JOIN user u
                ON a.host = u.username
                WHERE a.activity_name LIKE ? AND a.activity_status_open = TRUE
                ORDER BY {$sortby} {$order}";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $keywordMatch);
        $stmt->execute();
        $activites = $stmt->get_result();

        if ($activites->num_rows > 0) {
            // find joined activities' id
            $sql = "SELECT activity_id FROM user_activity WHERE user_id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $joinedActivites = $stmt->get_result();
    
            // store ids into a "set"
            $joinedActivityIds = [];
            while ($activity = $joinedActivites->fetch_assoc()) {
                $joinedActivityIds[$activity["activity_id"]] = true;
            }

            $table = '<table class="content-table">
                        <thead>
                            <tr>
                                <th>Host</th>
                                <th>Activity Name</th>
                                <th>Type</th>
                                <th>Date and time</th>
                                <th>Day1</th>
                                <th>Time1</th>
                                <th>Day2</th>
                                <th>Time2</th>
                                <th>Day3</th>
                                <th>Time3</th>
                                <th>Join Activity</th>
                                <th>Report inappropriate</th>
                            </tr>
                        </thead>
                        <tbody>';

            while($activity = $activites->fetch_assoc()) {
                $activityID = $activity['activity_id'];
                $host = $activity['host'] == $_SESSION['username'] ? $activity['host'] : "<a href='/user/profile.php?user_id={$activity['host_id']}'>{$activity['host']}</a>";
                $recurrence = $activity['activity_repetition'] == 0 ? "One-off" : "Recurring";
                $joinActivity = isset($joinedActivityIds[$activityID]) ? "Joined" : "<a href='' class='join-activity' data-activity-id='{$activityID}'>Join</a>";
                
                // show the link to report goal if the user is not the creator of the goal
                $reportActivity = $activity['host'] == $_SESSION['username'] ? '-' : "<a href='/user/activity/report_activity.php?activity_id={$activityID}'>Click Here</a>";

                $table .= "<tr>";
                $table .= "<td>{$host}</td>";
                $table .= "<td><a href='/user/activity/activity_details.php?activity_id={$activity['activity_id']}'>{$activity['activity_name']}</a></td>";

                if ($activity['activity_repetition'] == 0) {
                    $datetime = substr($activity['activity_one_off_datetime'], 0, 16);

                    $table .= "<td>One-off</td>";
                    $table .= "<td>{$datetime}</td>";
                }
                else {
                    $table .= "<td>Recurring</td>";
                    $table .= "<td>-</td>";
                }
        
                for ($i = 0; $i < 3; $i++) {
                    if ($activity['activity_repetition'] >= $i + 1) {
                        $time = substr($activity['activity_recurring_time_'.$i], 0, 5);

                        $table .= "<td>{$activity['activity_recurring_date_'.$i]}</td>";
                        $table .= "<td>{$time}</td>";
                    }
                    else {
                        $table .= "<td>-</td>";
                        $table .= "<td>-</td>";
                    }
                }

                $table .= "<td>{$joinActivity}</td>";
                $table .= "<td>{$reportActivity}</td>";
                $table .= "</tr>";
            }
            $table .= "</tbody></table>";
            
            echo response(true, $table);
        }
        else {
            $res = '<h3 class="text-center mt-3">No activities are found</h3>';
            echo response(true, $res);
        }
    }
}

function reportActivity() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        if (empty($params['reason'])) {
            echo response(false, "Please specify the reason!");
            exit();
        }

        $report_type = "activity";
        $activity_id = $params['activityID'];
        $reporter = $_SESSION['username'];
        $owner = $params['activityHost'];
        $reason = $params['reason'];
        $activity_name = $params['activityName'];
        
        $sql = "INSERT INTO report (report_type, activity_id, reporter, owner, reason, activity_name) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sissss", $report_type, $activity_id, $reporter, $owner, $reason, $activity_name);
        $stmt->execute();
        
        $_SESSION["msg"] = "Activity reported";
        echo response(true, "/user/activity/search_activities.php");
    }
}

if (isset($_GET['action'])) {
    if (function_exists($_GET['action'])) {
        $_GET['action']();
    }
}
?>
