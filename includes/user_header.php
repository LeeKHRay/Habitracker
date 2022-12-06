<?php 
session_start(); 

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}
if (isset($_SESSION['admin_username'])) {
    header("Location: /login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo isset($title) ? $title : "Habitracker" ?></title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="/public/css/header.css">

        <script src="https://code.jquery.com/jquery-3.4.1.min.js" defer integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="/public/js/user.js" defer></script>  

        <?php
        if (isset($jQueryUI)) {
            echo '<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>';
        }

        if (isset($css)) {
            foreach ($css as $cssFile) {
                echo '<link rel="stylesheet" href="/public/css/'.$cssFile.'">';
            }
        }

        if (isset($js)) {
            echo '<script src="/public/js/'.$js.'" defer></script>';
        } 
        ?>        
    </head>

    <body>
        <div id="navbar" class="nav">
            <img id="logo" src="/public/img/logo.png" alt="Habitracker Logo">

            <a href="/user/index.php">Home</a>
            
            <!--to display the dropdown list navigated to different pages-->
            <div class="dropdownmenu">
                <button class="dropdownbtn">Goals
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdownmenu-content">
                    <a href="/user/goal/my_goals.php">My goals</a>
                    <a href="/user/goal/my_progress_today.php">My progress today</a>
                    <a href="/user/goal/create_goal.php">Create goal</a>
                    <a href="/user/goal/search_goals.php">Search goals</a>
                    <a href="/user/goal/leaderboard.php">Leaderboard</a>
                </div>
            </div>

            <div class="dropdownmenu">
                <button class="dropdownbtn">Activities
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdownmenu-content">
                    <a href="/user/activity/my_activities.php">My activities</a>
                    <a href="/user/activity/create_one_off.php">Create one-off activity</a>
                    <a href="/user/activity/create_recurring.php">Create recurring activity</a>
                    <a href="/user/activity/search_activities.php">Search activities</a>
                </div>
            </div>

            <a href="/user/chat.php">Chat</a>
            <a href="/user/group_chat.php">Group chat</a>

            <div class="dropdownmenu">
                <button class="dropdownbtn">
                    <strong class="text-success"><?php echo $_SESSION['username'] ?></strong>
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdownmenu-content">
                    <a href="/user/profile.php">My profile</a>
                    <a href="/user/settings.php">Settings</a>
                    <a href="/user/change_password.php">Change password</a>
                    <a href="" id="logout">Logout</a>
                </div>
            </div>

            <!-- <div id="nav-username" class="text-success"><strong></strong></div> -->
        </div>