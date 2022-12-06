<?php
$title = "Report Inappropriate Activity";
$js = "user_activity.js";
$css = ["user_form.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';

if (!isset($_GET['activity_id'])) {
    header("Location: /user/index.php");
    exit();   
}    
$activity_id = $_GET['activity_id'];

$sql = "SELECT * FROM activity WHERE activity_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $activity_id);
$stmt->execute();
$activities = $stmt->get_result();

if ($activities->num_rows == 0) {
    header("Location: /user/index.php");
    exit();
}

$activity = $activities->fetch_assoc();
$host = $activity['host'];
if($host == $_SESSION['username']) {
    header("Location: /user/index.php");
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

$activity_name = $activity['activity_name'];
$activity_repetition = $activity['activity_repetition'];
$activity_location = $activity['activity_location'];
$activity_time_remark = empty($activity['activity_time_remark']) ? "-" : $activity['activity_time_remark'];
$activity_remark = empty($activity['activity_remark']) ? "-" : $activity['activity_remark'];
$activity_start_time = isset($activity['activity_start_time']) ? substr($activity['activity_start_time'], 0, 5) : "-";;
$activity_end_time = isset($activity['activity_end_time']) ? substr($activity['activity_end_time'], 0, 5) : "-";;
?>

<div id="form" class="mt-100">
    <h1><?php echo $title ?></h1>

    <form>
        <!-- hidden fields store data that need to send to backend -->
        <input type="hidden" name="activityID" value="<?php echo $activity_id ?>" readonly>
        <input type="hidden" name="activityName" value="<?php echo $activity_name ?>" readonly>
        <input type="hidden" name="activityHost" value="<?php echo $host ?>" readonly>            

        <p>Host: <?php echo $host ?></p>         
        <p>Activity name: <?php echo $activity_name ?></p>

        <?php
        if ($activity_repetition == 0) {
            echo "<p>Date and time: ".substr($activity['activity_one_off_datetime'], 0, 16)."</p>";
        }
        else {
            $times = [1 => "once", 2 => "twice", 3 => "three times"];

            echo "<p>The activity will be held {$times[$activity_repetition]} a week on: ";
            echo "<ul>";
            for ($i = 0; $i < $activity_repetition; $i++) {
                echo "<li>{$dayOfWeek[$activity['activity_recurring_date_'.$i]]} at "
                    .substr($activity['activity_recurring_time_'.$i], 0, 5)."</li>";
            }
            echo "</ul>";
        }
        ?>
        <p>Location: <?php echo $activity['activity_location'] ?></p>
        <p>Remark on the date and time: <?php echo $activity_time_remark ?></p>
        <p>General remark: <?php echo $activity_remark ?></p>

        <label for="reason">Reason:</label><br>
        <textarea id="reason" name="reason" rows="4" required></textarea><br>
        
        <button type="button" id="report-activity-btn" class="submit-btn">Submit</button>
    </form>

    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
