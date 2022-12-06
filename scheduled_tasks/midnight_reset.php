<?php
// script for updating streaks and scores
// automated using Windows Task Scheduler

include_once __DIR__.'/../includes/db_connect.php';

//perform the checking goal by goal
$sql = "SELECT username, goal_id, goal_completed, streak FROM goal";
$goals = $mysqli->query($sql);

while($goal = $goals->fetch_assoc()) {
    //retrieve the past information
    $streak = $goal['streak'];
    $goal_id = $goal['goal_id'];
    $username = $goal['username'];
    
    if ($goal['goal_completed']) { // increment streak if the goal is completed
        $streak++;
    }
    else { // otherwise, reset streaks to 0
        $streak = 0;
    }

    if (date('D') == 'Sun') {
        // store streaks as of Saturday
        $sql = "UPDATE goal SET streak_last_week = streak WHERE goal_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $goal_id);
        $stmt->execute();
    }

    // update streaks and reset completion status
    $sql = "UPDATE goal SET streak = ?, goal_completed = FALSE WHERE goal_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("is", $streak, $goal_id);
    $stmt->execute();
    
    // score increased by (streak * 100) if 1 <= streak < 5
    //                    500            if streaks >= 5
    $scoreInc = min($streak, 5) * 100;
    $sql = "UPDATE user SET score = score + ? WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("is", $scoreInc, $username);
    $stmt->execute();
}
?>
