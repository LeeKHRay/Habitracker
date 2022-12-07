<?php
// script for sending daily notification to users
// automated using Windows Task Scheduler

session_start();
include_once __DIR__.'/../includes/db_connect.php';
include_once __DIR__.'/../includes/utils.php';

$sql = "SELECT username, email 
        FROM user
        WHERE receive_daily_reminder = TRUE";
$users = $mysqli->query($sql);

while ($user = $users->fetch_assoc()) {
    $username = $user['username'];
    $today = date("Y-m-d", time());

    //select goals of the user that are not ended
    $sql = "SELECT goal_name FROM goal WHERE username = ? AND goal_end_date >= ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $username, $today);
    $stmt->execute();
    $goals = $stmt->get_result();
    
    // reminder user who has created any goals
    if ($goals->num_rows > 0) {         
        $message = '<img src="cid:logo" width="200">'; // $mail->AddEmbeddedImage($embeddedImage, 'logo');
        $message .= "<p>Hello {$username},</p>";
        $message .= '<p>Keep up with your good work with the help of Habitracker!';
        $message .= '<p>Habit is a cable; we weave a thread each day, and at last we cannot break it. -Horace Mann';
        $message .= '<p>Keep up with your goals and track your habits today!</br>';
        $message .= '<p>Your goal(s) today:</br>';

        $message .= '<ul>';
        while ($goal = $goals->fetch_assoc()) {
            $message .= "<li>{$goal['goal_name']}</li>";
        }
        $message .= '</ul>';

        $message .= '<p>It is never too late to start a habit! Want to equip yourself with a new skill or pick up a new interest?';
        $message .= '<p>Do not hesitate and create your new habit in ';
        $message .= '<a href="http://localhost/user/goal/create_goal.php">Habitracker</a>!</p>';
        $message .= '<p>Please send an email to habitracker.noreply@gmail.com if you have any queries.</p>';
        $message .= '<p>Cheers,</p>';
        $message .= '<p>Team Habitracker</p>';

        sendEmail("[Habitracker] Daily reminder", $message, $user['email'], __DIR__.'/../public/img/logo.png');
    }
}
