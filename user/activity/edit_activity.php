<?php 
$title = "Edit Activity";
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
$sql = "SELECT * FROM activity WHERE activity_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $activity_id);
$stmt->execute();
$activities = $stmt->get_result();

if ($activities->num_rows == 0) {
    header("Location: /activity/my_activities.php");
    exit();
}

$activity = $activities->fetch_assoc();
if ($activity['host'] != $_SESSION['username']) {
    header("Location: /activity/my_activities.php");
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

$activity_repetition = $activity['activity_repetition'];
$activity_location = $activity['activity_location'];
$activity_time_remark = $activity['activity_time_remark'];
$activity_remark = $activity['activity_remark'];
?>

<div id="form" class="mt-100">
    <h1><?php echo $title ?></h1>

    <form>
        <input type="hidden" name="activityID" value="<?php echo $activity_id ?>" readonly>

        <label for="activity-name">* Activity name:</label>
        <input type="text" id="activity-name" name="activityName" value="<?php echo $activity['activity_name'] ?>" required>

        <?php
        if ($activity_repetition == 0) {
            echo "<p>Date and time: ".substr($activity['activity_one_off_datetime'], 0, 16)."</p>";
        }
        else {
            $times = [1 => "once", 2 => "twice", 3 => "three times"];

            echo "<p>The activity will be held {$times[$activity_repetition]} a week on: <p>";
            echo "<ul>";
            for ($i = 0; $i < $activity_repetition; $i++) {
                echo "<li>{$dayOfWeek[$activity['activity_recurring_date_'.$i]]} at "
                    .substr($activity['activity_recurring_time_'.$i], 0, 5)."</li>";
            }
            echo "</ul>";
        }
        ?>
        <p>Location: <?php echo $activity_location ?></p>
        
        <label for="time-remark">Remark on the date and time (Optional): </label>
        <input type="text" id="time-remark" name="timeRemark" placeholder="Time remark of the activity" value="<?php echo $activity_time_remark ?>">

        <label for="remark">General Remark (Optional): </label>
        <input type="text" id="remark" name="remark" placeholder="General remark of the activity" value="<?php echo $activity_remark ?>">

        <input type="checkbox" id="activity-close" name="activityClose" <?php echo $activity['activity_status_open'] ? "" : "checked" ?>>
        <label for="activity-close">Close this activity</label><br>
        
        <p class="font-weight-normal">
            * Note: You can close the activity when you think the participant recruitment is already satisfactory.
            If you plan to delete this activity, you have to close it first.
        </p>

        <button type='button' id="edit-activity-btn" class="submit-btn">Update</button>
        <button type="button" id="delete-activity-btn" class="submit-btn <?php echo $activity['activity_status_open'] ? "d-none" : "" ?>">Delete this activity</button>
    </form>

    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
