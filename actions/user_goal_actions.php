<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/utils.php';

function createGoal() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);
        $res = [];

        $goal_name = $params['goalName'];
        $duration = $params['duration'];
        if (empty($goal_name) || empty($duration)) {
            echo response(false, "Please fill in all the required fields!");
            exit();
        }
        
        if (intval($duration) <= 0) {
            echo response(false, "Please select a valid duration!");
            exit();
        }
        $duration = intval($duration) - 1;

        $username = $_SESSION['username'];
        $goal_description = $params['goalDescription'];
        $goal_subtask = $params['goalSubtask'];
        $goal_end_date = date("Y-m-d", strtotime("+{$duration} days"));    
        $goal_start_time = empty($params['goalStartTime']) ? NULL : $params['goalStartTime'];
        $goal_end_time = empty($params['goalEndTime']) ? NULL : $params['goalEndTime'];
        $goal_public = isset($params['goalPublic']) ? 1 : 0;

        $sql = "INSERT INTO goal (username, goal_name, goal_description, goal_subtask, goal_end_date, 
                    goal_start_time, goal_end_time, goal_public) 
                VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);    
        $stmt->bind_param("sssssssi", $username, $goal_name, $goal_description, $goal_subtask, $goal_end_date, 
            $goal_start_time, $goal_end_time, $goal_public);
        $stmt->execute();

        $_SESSION["msg"] = "Goal created";
        echo response(true, "/user/goal/my_goals.php");
    }
}

function editGoal() {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        $goal_name = $params['goalName'];
        $duration = $params['duration'];
        if (empty($goal_name) || empty($duration)) {
            echo response(false, "Please fill in all the required fields!");
            exit();
        }
        
        if (intval($duration) <= 0) {
            echo response(false, "Please select a valid duration!");
            exit();
        }
        $duration = intval($duration) - 1;

        $goal_description = $params['goalDescription'];
        $goal_subtask = $params['goalSubtask'];
        $goal_end_date = date("Y-m-d", strtotime("+{$duration} days"));
        $goal_start_time = empty($params['goalStartTime']) ? NULL : $params['goalStartTime'];
        $goal_end_time = empty($params['goalEndTime']) ? NULL : $params['goalEndTime'];
        $goal_public = isset($params['goalPublic']) ? 1 : 0;
        $goal_id = $params['goalID'];
        
        $sql = "UPDATE goal 
                SET goal_name = ?, goal_description = ?, goal_subtask = ?,
                    goal_end_date = ?, goal_start_time = ?, goal_end_time = ?,
                    goal_public = ? 
                WHERE goal_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssssssi", $goal_name, $goal_description, $goal_subtask, $goal_end_date, 
            $goal_start_time, $goal_end_time, $goal_public, $goal_id);
        $stmt->execute();
        
        // update goal name in report if any
        $sql = "UPDATE report SET goal_name = ? WHERE goal_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $goal_name, $goal_id);
        $stmt->execute();

        $_SESSION["msg"] = "Goal updated";
        echo response(true, "/user/goal/my_goals.php");
    }
}

function deleteGoal() {
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        $goal_id = $params['goalID'];
        
        //to delete the entry from mySQL
        $sql = "DELETE FROM goal WHERE goal_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $goal_id);
        $stmt->execute();

        $_SESSION["msg"] = "Goal deleted";
        echo response(true, "/user/goal/my_goals.php");
    }
}

function searchGoals() {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        global $mysqli;

        if (empty($_GET['keyword'])) {
            echo response(false, "Please input the keyword!");
            exit();
        }

        $keywordMatch = "%{$_GET['keyword']}%";
        $sortby = $_GET['sortby'];
        $order = $_GET['order'];
        
        //get all the entries that are set as public and match the keyword
        $sql = "SELECT u.user_id, g.goal_id, g.username, g.goal_name, g.goal_description, g.goal_subtask, g.goal_end_date, g.goal_start_time, g.goal_end_time
                FROM goal g 
                JOIN user u
                ON g.username = u.username
                WHERE g.goal_name LIKE ? AND goal_public = TRUE
                ORDER BY {$sortby} {$order}";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $keywordMatch);
        $stmt->execute();
        $goals = $stmt->get_result();

        if ($goals->num_rows > 0) { // show matched goal if any
            $table = '<h3 class="text-center mt-50">Search results</h3>
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Goal name</th>
                            <th>Description</th>
                            <th>Subtask</th>
                            <th>End date</th>
                            <th>Start time</th>
                            <th>End time</th>
                            <th>Report this goal</th>
                        </tr>
                    </thead>
                    <tbody>';

            while($goal = $goals->fetch_assoc()) { 
                $username = $goal['username'] == $_SESSION['username'] ? $goal['username'] : "<a href='/user/profile.php?user_id={$goal['user_id']}'>{$goal['username']}</a>";
                $goal_description = empty($goal['goal_description']) ? "-" : $goal['goal_description'];
                $goal_subtask = empty($goal['goal_subtask']) ? "-" : $goal['goal_subtask'];
                $goal_start_time = isset($goal['goal_start_time']) ? substr($goal['goal_start_time'], 0, 5) : "-";
                $goal_end_time = isset($goal['goal_end_time']) ? substr($goal['goal_end_time'], 0, 5) : "-";

                // display the link to report goal if the user is not the creator of the goal
                $reportGoal = $goal['username'] == $_SESSION['username'] ? '-' : "<a href='/user/goal/report_goal.php?goal_id={$goal['goal_id']}'>Click Here</a>";

                $table .= "<tr>";
                $table .= "<td>{$username}</td>";
                $table .= "<td>{$goal['goal_name']}</td>";
                $table .= "<td>{$goal_description}</td>";            
                $table .= "<td>{$goal_subtask}</td>";
                $table .= "<td>{$goal['goal_end_date']}</td>";
                $table .= "<td>{$goal_start_time}</td>";
                $table .= "<td>{$goal_end_time}</td>";            
                $table .= "<td>{$reportGoal}</td>";
                $table .= "</tr>";
            }

            $table .= "</tbody></table>";
            
            echo response(true, $table);
        }
        else {
            $res = '<h3 class="text-center mt-3">No goals are found</h3>';
            echo response(true, $res);
        }
    }
}

function updateGoalCompletion() {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);
        
        $username = $_SESSION['username'];
        $today = date("Y-m-d", time());

        if (isset($params["goalIDs"])) {
            $goalIDs = $params["goalIDs"]; // goal id of completed goals
            $idNum = count($goalIDs);

            if ($idNum > 0) {
                $placeholders = implode(",", array_fill(0, $idNum, '?'));
                $goal_completed = 1;
                $sql = "UPDATE goal SET goal_completed = ? WHERE username = ? AND goal_end_date >= ? AND goal_id IN ({$placeholders})";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("iss".str_repeat('i', $idNum), $goal_completed, $username, $today, ...$goalIDs);
                $stmt->execute();
                
                $goal_completed = 0;
                $sql = "UPDATE goal SET goal_completed = ? WHERE username = ? AND goal_end_date >= ? AND goal_id NOT IN ({$placeholders})";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("iss".str_repeat('i', $idNum), $goal_completed, $username, $today, ...$goalIDs);
                $stmt->execute();
            }
        }
        else {
            $goal_completed = 0;
            $sql = "UPDATE goal SET goal_completed = ? WHERE username = ? AND goal_end_date >= ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iss", $goal_completed, $username, $today);
            $stmt->execute();
        }

        echo response(true, "Progress saved");
    }
}

function reportGoal() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        if (empty($params['reason'])) {
            echo response(false, "Please specify the reason!");
            exit();
        }

        $report_type = "goal";
        $goal_id = $params['goalID'];
        $reporter = $_SESSION['username'];
        $owner = $params['owner'];
        $reason = $params['reason'];
        $goal_name = $params['goalName'];
        
        $sql = "INSERT INTO report (report_type, goal_id, reporter, owner, reason, goal_name) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sissss", $report_type, $goal_id, $reporter, $owner, $reason, $goal_name);
        $stmt->execute();

        $_SESSION["msg"] = "Goal reported";
        echo response(true, "/user/goal/search_goals.php");
    }
}

if (isset($_GET['action'])) {
    if (function_exists($_GET['action'])) {
        $_GET['action']();
    }
}
?>