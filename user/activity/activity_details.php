<?php
$title = "Activity Details";
$js = "user_activity.js";
$css = ["user_form.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';

if (!isset($_GET['activity_id'])) {
    header("Location: /user/activity/my_activities.php");
    exit();
}
$activity_id = $_GET['activity_id'];

//retrieve information of the corresponding activity
$sql = "SELECT *
        FROM activity a
        LEFT JOIN user_activity ua
        ON a.activity_id = ua.activity_id AND ua.user_id = ?
        WHERE a.activity_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $_SESSION['user_id'], $activity_id);
$stmt->execute();
$activities = $stmt->get_result();
if ($activities->num_rows == 0) {
    header("Location: /user/activity/my_activities.php");
    exit();
}

$activity = $activities->fetch_assoc();
if (!$activity['activity_status_open'] && !isset($activity['user_id'])) {
    header("Location: /user/activity/my_activities.php");
    exit();
}

$dayOfWeek = [
    "SUN" => "Sunday",
    "MON" => "Monday",
    "TUE" => "Tuesday",
    "WED" => "Wednesday",
    "THU" => "Thursday",
    "FRI" => "Friday",
    "SAT" => "Saturday"
];

$host = $activity['host'];
$activity_name = $activity['activity_name'];
$activity_location = $activity['activity_location'];
$activity_repetition = $activity['activity_repetition'];
$activity_time_remark = empty($activity['activity_time_remark']) ? "-" : $activity['activity_time_remark'];
$activity_remark = empty($activity['activity_remark']) ? "-" : $activity['activity_remark'];

// find user ID of the activity host
$sql = "SELECT user_id FROM user WHERE username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $host);
$stmt->execute();
$host_id = $stmt->get_result()->fetch_assoc()['user_id'];
?>

<div id="form" class="mt-100">
    <h1><?php echo $title ?></h1>
        <?php
        if ($host == $_SESSION['username']) {
            echo "<p>Host: {$host}</p>";
        }
        else {
            echo "<p>Host: <a href='/user/profile.php?user_id={$host_id}'>{$host}</a></p>";
        }
        ?>

        <p>Activity name: <?php echo $activity_name ?></p>

        <?php
        if ($activity_repetition == 0) {
            echo "<p>Date and time: ".substr($activity['activity_one_off_datetime'], 0, 16)."</p>";
        }
        else {
            $times = [1 => "once", 2 => "twice", 3 => "three times"];

            echo "<p>The activity will be held {$times[$activity_repetition]} a week on: </p>";
            echo "<ul>";
            for ($i = 0; $i < $activity_repetition; $i++) {
                echo "<li>{$dayOfWeek[$activity['activity_recurring_date_'.$i]]} at "
                    .substr($activity['activity_recurring_time_'.$i], 0, 5)."</li>";
            }
            echo "</ul>";
        }
        ?>

        <p>Location: <?php echo $activity_location ?></p>
        <p>Remark on the date and time: <?php echo $activity_time_remark ?></p>
        <p>General remark: <?php echo $activity_remark ?></p>

        <?php
        // find and show the username of this activity
        $sql = "SELECT u.username, u.user_id
                FROM user_activity ua
                INNER JOIN user u
                ON ua.user_id = u.user_id
                WHERE ua.activity_id = ? AND u.username != ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("is", $activity_id, $host);
        $stmt->execute();
        $users = $stmt->get_result();

        $joined = false;

        if ($users->num_rows > 0) {
            echo "<p>Members: </p>";
            echo "<ul>";
            while ($user = $users->fetch_assoc()) {
                if ($user['username'] == $_SESSION["username"]) {
                    $joined = true;
                    echo "<li>{$_SESSION['username']}</li>";
                }
                else {
                    echo "<li><a href='/user/profile.php?user_id={$user['user_id']}'>{$user['username']}</a></li>";
                }
            }
            echo "</ul>";
        }
        else {
            echo "<p>No users joined this activity</p>";
        }

        if ($_SESSION["username"] != $host) {
            if ($joined) {
                echo "<button type='button' class='submit-btn quit-activity' data-activity-id='{$activity_id}'>Quit this activity</button>";
            }
            else {
                echo "<button type='button' class='submit-btn join-activity' data-activity-id='{$activity_id}'>Join this activity</button>";
            }
        }
        ?>
</div>



<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
