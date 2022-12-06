<?php
// backend code for handling admin actions

session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/utils.php';

function login() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['pwd'];

        if (empty($username) || empty($password)){ // check if all fields are filled
            echo response(false, "Please fill in all the fields!");
            exit();
        }
        if ($username != "Admin" || $password != "csci3100") { // check if username and password are correct
            echo response(false, "Wrong username or password!");
            exit();
        }
        
        $_SESSION['admin_username'] = $username;
        echo response(true, "/admin/index.php");
    }
}

function logout() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        session_unset(); // unset variables from $_SESSION
        
        // clear cookie
        if (ini_get("session.use_cookies")) { // check if it is using cookies to store the session id on the client side
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy(); // delete session file
        echo response(true, "/admin_login.php");
    }
}

function deleteGoal($username = "") {
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        global $mysqli;

        $deleteGoalHelper = function($goalID, $goalNum = 1) use ($mysqli) {
            $placeholder = is_array($goalID) ? "IN (".implode(",", array_fill(0, $goalNum, '?')).")" : "= ?";
            $type = str_repeat('i', $goalNum);

            $sql = "DELETE FROM goal WHERE goal_id {$placeholder}"; // remove goal(s)
            $stmt = $mysqli->prepare($sql);

            if (is_array($goalID)) {
                $stmt->bind_param($type, ...$goalID);
            }
            else {
                $stmt->bind_param($type, $goalID);
            }

            $stmt->execute();
        };
    
        if (empty($username)) { // only delete goal
            parse_str(file_get_contents("php://input"), $params);   

            // find goal name
            $sql = "SELECT goal_name FROM goal WHERE goal_id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $params['goalID']);
            $stmt->execute();
            $result = $stmt->get_result();
            $goalName = $result->fetch_assoc()['goal_name'];

            $deleteGoalHelper($params['goalID']);

            $_SESSION["msg"] = "Goal deleted";
            echo response(true, $goalName);
        }
        else { // delete goal after delete user
            $sql = "SELECT goal_id FROM goal WHERE username = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $goals = $stmt->get_result();
            $goalNum = $goals->num_rows;

            if ($goalNum > 0) {
                $goalIDs = [];

                while ($goal = $goals->fetch_assoc()) {
                    $goalIDs[] = $goal["goal_id"];
                }

                $deleteGoalHelper($goalIDs, $goalNum);
            }
        }
    }
}

function deleteActivity($username = "") {
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        global $mysqli;

        $deleteActivityHelper = function($activityID, $activityNum = 1) use ($mysqli) {
            $placeholder = is_array($activityID) ? "IN (".implode(",", array_fill(0, $activityNum, '?')).")" : "= ?";
            $type = str_repeat('i', $activityNum);

            $sql = "DELETE FROM activity WHERE activity_id {$placeholder}";  // remove activity(ies)
            $stmt = $mysqli->prepare($sql);

            $sql = "DELETE FROM user_activity WHERE activity_id {$placeholder}"; // remove activity group
            $stmt2 = $mysqli->prepare($sql);
            
            $sql = "DELETE FROM activity_chat_message WHERE activity_id {$placeholder}"; // remove group chat data
            $stmt3 = $mysqli->prepare($sql);

            if (is_array($activityID)) {
                $stmt->bind_param($type, ...$activityID);
                $stmt2->bind_param($type, ...$activityID);
                $stmt3->bind_param($type, ...$activityID);
            }
            else {
                $stmt->bind_param($type, $activityID);
                $stmt2->bind_param($type, $activityID);
                $stmt3->bind_param($type, $activityID);
            }

            $stmt->execute();
            $stmt2->execute();
            $stmt3->execute();
        };    
        
        if (empty($username)) { // only delete activity
            parse_str(file_get_contents("php://input"), $params);     
    
            // find activity name
            $sql = "SELECT activity_name FROM activity WHERE activity_id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $params['activityID']);
            $stmt->execute();
            $result = $stmt->get_result();
            $activityName = $result->fetch_assoc()['activity_name'];
            
            $deleteActivityHelper($params['activityID']);

            $_SESSION["msg"] = "Activity deleted";
            echo response(true, $activityName);
        }
        else {
            // delete activity after delete user
            $sql = "SELECT activity_id FROM activity WHERE host = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $activities = $stmt->get_result();
            $activityNum = $activities->num_rows;

            if ($activityNum > 0) {
                $activityIDs = [];

                while ($activity = $activities->fetch_assoc()) {
                    $activityIDs[] = $activity["activity_id"];
                }

                $deleteActivityHelper($activityIDs, $activityNum);
            }
        }
    }
}

// delete a user and his/her goals and activities
function deleteUser() {
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);
        
        $userID = $params['userID'];
        $avatar = $params['avatar'];

        // delete private chat data
        $sql = "DELETE FROM chat_message WHERE from_user_id = ? OR to_user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $userID, $userID);
        $stmt->execute(); 

        // delete group chat message sent by the user
        $sql = "DELETE FROM activity_chat_message WHERE from_user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();

        // delete user uploaded avatar
        if ($avatar != "avatar_default.jpg") {
            unlink($_SERVER['DOCUMENT_ROOT'].'/public/avatar/'.$avatar);
        }

        // delete user
        $sql = "DELETE FROM user WHERE user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
    
        deleteGoal($params['username']);
        deleteActivity($params['username']);

        $_SESSION["msg"] = "User deleted";
        echo response(true, "");
    }
}

// send email notification to the user
function notifyUser() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        session_write_close(); // avoid session lock to allow page reload after deletion
        global $mysqli;

        if ($_POST['type'] == "user") {
            $message = "<p>Dear {$_POST['username']},</p>";
            $message .= "<p>We have deleted your account because you keep creating goals/activities that contain inappropriate content.</p>";
            $message .= "<p>Please send an email to habitracker.noreply@gmail.com if you have any queries.</p>";
            sendEmail('[Habitracker] Your account has been deleted', $message, $_POST['email']);
        }
        else {
            // find user's email address
            $sql = "SELECT email FROM user WHERE username = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $_POST['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            $email = $result->fetch_assoc()["email"];

            $message = "<p>Dear {$_POST['username']},</p>";
            $message .= "<p>We have deleted your {$_POST['type']} \"{$_POST['itemName']}\" because it contains inappropriate content.</p>";
            $message .= "<p>Please send an email to habitracker.noreply@gmail.com if you have any queries.</p>";              
            sendEmail("[Habitracker] Your {$_POST['type']} has been deleted", $message, $email);
        }
    }
}

function dismissReport() {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        $sql = "UPDATE report SET dismissed = TRUE WHERE report_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $params['reportID']);
        $stmt->execute();

        $_SESSION["msg"] = "Report dismissed";
        echo response(true, "");
    }
}

function resolveReport() {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        $sql = "UPDATE report SET resolved = TRUE WHERE report_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $params['reportID']);
        $stmt->execute();

        $_SESSION["msg"] = "Report resolved";
        echo response(true, "");
    }
}

if (isset($_GET['action'])) {
    if (function_exists($_GET['action'])) {
        $_GET['action']();
    }
}
?>
