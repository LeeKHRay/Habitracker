<?php 
session_start(); 

if (!isset($_SESSION['admin_username'])){
    header("Location: /admin_login.php");
    exit();
}
else if (isset($_SESSION['user_id'])){
    header("Location: /user/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title ?></title>
        
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="/public/css/header.css">

        <?php
        if (isset($css)) {
            echo '<link rel="stylesheet" href="/public/css/'.$css.'">';
        }
        ?>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js" defer integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" defer integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" defer integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="/public/js/admin.js" defer></script>        
    </head>
    <body>
        <div class="nav">
            <img id="logo" src="/public/img/logo.png" alt="Habitracker Logo">
            <a href="/admin/index.php">Home</a>

            <div class="dropdownmenu">
                <button class="dropdownbtn">Reports
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdownmenu-content">
                    <a href="/admin/goal_reports.php">View Goal Reports</a>
                    <a href="/admin/activity_reports.php">View Activity Reports</a>
                </div>
            </div>
            
            <a href="/admin/remove_user.php">Remove User</a>
            <a href="/admin/remove_goal.php">Remove Goal</a>
            <a href="/admin/remove_activity.php">Remove Activity</a>
            <a href="" id="logout">Logout</a>
            </form>
        </div>
