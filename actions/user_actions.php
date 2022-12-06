<?php
// backend code for handling user actions

session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/utils.php';

function updateLastActivity() {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        global $mysqli;
        $sql = "UPDATE user SET last_activity = NOW() WHERE user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
    }
}

function login() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;
        ['usernameEmail' => $usernameEmail, 'pwd' => $password] = $_POST;

        // validate user input
        if (empty($usernameEmail) || empty($password)) {
            echo response(false, "Please fill in all the fields!");
            exit();
        }        

        if (filter_var($usernameEmail, FILTER_VALIDATE_EMAIL)) { // $usernameEmail is an email address
            $sql = "SELECT * FROM user WHERE email = ?";
        }
        else {
            $sql = "SELECT * FROM user WHERE username = ?";
        }
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $usernameEmail);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            echo response(false, "Wrong username or password!");
            exit();
        }

        //verify user's password with the password stored in database 
        $pwdcheck = password_verify($password, $user['password']);
        if (!$pwdcheck){
            echo response(false, "Wrong username or password!");
            exit();
        }

        //update the database after one has logged in
        $sql = "UPDATE user SET last_activity = NOW() WHERE user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $user['user_id']);
        $stmt->execute();

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        echo response(true, "/user/index.php");
    }
}

function signup() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;

        ["username" => $username, "email" => $email, "pwd" => $password, "confirmPwd" => $confirmPassword] = $_POST;

        // check the validity of user input and show the error message if invalid
        if(empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            echo response(false, "Please fill in all the fields!");
            exit();
        }

        if (!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
            echo response(false, "The username can only contain alphabets and numbers!");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo response(false, "Invalid email!");
            exit();
        }

        if ($password !== $confirmPassword) {
            echo response(false, "The two passwords do not match");
            exit();
        }
        
        // check if username already exists
        $sql = "SELECT COUNT(*) AS count FROM user WHERE username = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'];
        if ($count > 0) {
            echo response(false, "This username already exists!");
            exit();
        }
        
        $sql = "SELECT COUNT(*) AS count FROM user WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'];
        if ($count > 0) {
            echo response(false, "This email has been registered!");
            exit();
        }
        
        // add new user to database
        $sql = "INSERT INTO user (username, email, password) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $username, $email, $hashedPwd);
        $stmt->execute();
        
        $_SESSION['msg'] = "Register account successfully";
        echo response(true, "/login.php"); //redirect to login page with success message
    }
}

function logout() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;

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
        echo response(true, "/login.php"); // send redirect link for user to go back to the login page
    }
}

// send email including a link for resetting password
function resetPasswordRequest() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;
        $email = $_POST["email"]; 

        if (empty($email)) {
            echo response(false, "Please input your email!");
            exit();
        }

        // make sure no existing token of the same user in database
        $sql = "DELETE FROM pwd_reset WHERE email = ?";  
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $email); 
        $stmt->execute();
    
        // create a unique url for each password reset request 
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32); // authenticates user to make sure this is the correct user
        $url = "http://localhost/create_new_password.php?selector={$selector}&validator=".bin2hex($token);
        $expires = date("U") + 30 * 60; // 30 mins from now
    
        // update the database about the reset request 
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $sql = "INSERT INTO pwd_reset (email, selector, token, expires) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $email, $selector, $hashedToken, $expires); 
        $stmt->execute();
    
        //send email to users for resetting their password automatically after they request 
        $message = '<p>We received a password reset request. The link to reset your password is below. If you did not make this request, you can ignore this email or let us know.</p>';
        $message .= '<p>The password reset is only valid for 30 minutes.</p>';
        $message .= '<p>Here is your password reset link: <br>';
        $message .= "<a href='{$url}'>{$url}</a></p>";
        $message .= '<p>Please send an email to habitracker.noreply@gmail.com if you have any queries.</p>';    
        sendEmail("[Habitracker] Reset Your Password", $message, $email);

        echo response(true, "The email has been sent");    
    }
}

// reset password
function resetPassword() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $mysqli;
        $selector = $_POST["selector"];
        $validator = $_POST["validator"];
        $password = $_POST["pwd"];
        $confirmPassword = $_POST["confirmPwd"]; //get the variable name in create new password.php
    
        if (empty($password) || empty($confirmPassword)) {
            echo response(false, "Please fill in all the fields!");
            exit();
        }
        
        if ($password != $confirmPassword) {
            echo response(false, "The two passwords do not match");
            exit();
        }
        
        $currentDate = date("U");

        $sql = "SELECT * FROM pwd_reset WHERE selector = ? AND expires >= {$currentDate}"; // don't need placeholder for $currentDate since it is not input by user
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $selector); 
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {    
            echo response(false, "You need to re-submit your reset request");
            exit();
        }

        $row = $result->fetch_assoc();
        $tokenBin = hex2bin($validator);
        $tokenCheck = password_verify($tokenBin, $row["token"]);

        if (!$tokenCheck) {
            echo response(false, "You need to re-submit your reset request");
            exit();
        }
        
        $email = $row['email'];

        // check if the email really exists 
        $sql = "SELECT COUNT(*) as count FROM user WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'];
        if ($count == 0) {
            echo response(false, "The email does not exist");
            exit();
        }

        //update password after validation
        $newPwdHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET password = ? WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $newPwdHash, $email); 
        $stmt->execute();

        //delete request for resetting password to prevent clashing 
        $sql = "DELETE FROM pwd_reset WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $_SESSION['msg'] = "Your password has been reset";
        echo response(true, "/login.php"); //redirect to login page with success message
    }
}

function editProfile() {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        //allocate variables to store the input by the users
        $first_name = $params['firstName'];
        $last_name = $params['lastName'];
        $welcome_message = $params['welcomeMsg'];

        //update the database 
        $sql = "UPDATE user SET first_name = ?, last_name = ?, welcome_message = ? WHERE username = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $first_name, $last_name, $welcome_message, $_SESSION['username']);
        $stmt->execute();
        
        //redirect user to the profile display page 
        $_SESSION["msg"] = "Your profile is updated";
        echo response(true, "/user/profile.php");
    }
}

function uploadAvatar() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($_FILES['avatar'])) {
            echo response(true, "");
            exit();
        }

        global $mysqli;
        $file = $_FILES['avatar'];
        
        //check the validity of one's uploaded image such as the file type and the file size
        $fileError = $file['error'];        
        if($fileError > 0) {
            echo response(false, "Some errors occur, please try again");
            exit();
        }

        $fileName = basename($file['name']);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];
        if (!in_array($fileType, $allowedTypes)) {
            echo response(false, "Please choose a jpg/jpeg/png image for avatar");
            exit();
        }

        $fileSize = $file['size'];
        if ($fileSize >= 500000) {
            echo response(false, "The filesize of the image is too large");
            exit();
        }

        $fileTmpName = $file['tmp_name'];
        $avatar = "avatar{$_SESSION['user_id']}.{$fileType}";
        $fileDestination = $_SERVER['DOCUMENT_ROOT']."/public/avatar/{$avatar}";
        move_uploaded_file($fileTmpName, $fileDestination);

        // user's avatar status
        $sql = "UPDATE user SET avatar = {$avatar} WHERE username = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();

        echo response(true, "");
    }
}

function changeSettings() {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);

        //get the results input by the user and change them to proper SQL
        $user_id = $_SESSION['user_id'];
        $receive_daily_reminder = isset($params['receiveDailyReminder']) ? 1 : 0;
        $receive_weekly_report = isset($params['receiveWeeklyReport']) ? 1 : 0;

        //update the SQL entry
        $sql = "UPDATE user SET receive_daily_reminder = ?, receive_weekly_report = ? WHERE user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $receive_daily_reminder, $receive_weekly_report, $user_id); 
        $stmt->execute();

        echo response(true, "Settings saved");
    }
}

function changePassword() {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        global $mysqli;
        parse_str(file_get_contents("php://input"), $params);
        
        $username = $_SESSION['username'];
        $curPassword = $params["curPwd"];
        $newPassword = $params["newPwd"];
        $confirmPassword = $params["confirmPwd"];
        
        //show error message if input is invalid
        if (empty($curPassword) || empty($newPassword) || empty($confirmPassword) ) {
            echo response(false, "Please fill in all fields!");
            exit();
        }
        if ($newPassword != $confirmPassword) {
            echo response(false, "The confirm password does not match");
            exit();
        }

        // check if the entered password is correct for security reason
        $sql = "SELECT password FROM user WHERE username = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $username); 
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if (!password_verify($curPassword, $user["password"])){
            echo response(false, "The current password is wrong");
            exit();
        } 

        //update the new password
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET password = ? WHERE username = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $newHashedPassword, $username); 
        $stmt->execute();

        echo response(true, "Your password is changed");
    }
}

if (isset($_GET['action'])) {
    if (function_exists($_GET['action'])) {
        $_GET['action']();
    }
}
?>
