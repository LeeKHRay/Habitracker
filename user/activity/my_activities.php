<?php
$title = "My Activities";
$js = "user_activity.js";
$css = ["user_table.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<h5 id="msg" class='text-success m-2'><?php echo $msg ?></h5>

<h1 class="text-center mt-4"><?php echo $title ?></h1>

<?php
$user_id = $_SESSION['user_id'];

$sql = "SELECT a.activity_id, a.activity_name, a.activity_repetition, a.activity_one_off_datetime, 
            a.activity_recurring_date_0, a.activity_recurring_time_0, 
            a.activity_recurring_date_1, a.activity_recurring_time_1, 
            a.activity_recurring_date_2, a.activity_recurring_time_2, 
            a.activity_time_remark, a.activity_location, 
            a.host, u.user_id AS host_id
        FROM user_activity ua
        INNER JOIN activity a 
            ON ua.activity_id = a.activity_id
        INNER JOIN user u
            ON a.host = u.username
        WHERE ua.user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$activities = $stmt->get_result();

if ($activities->num_rows > 0) {
    echo '<table class="content-table">
            <thead>
                <tr>
                    <th>Host</th>
                    <th>Activity Name</th>
                    <th>Type</th>
                    <th>Date and time</th>
                    <th>Day1</th>
                    <th>Time1</th>
                    <th>Day2</th>
                    <th>Time2</th>
                    <th>Day3</th>
                    <th>Time3</th>
                    <th>Edit</th>
                    <th>Quit</th>
                </tr>
            </thead>
            <tbody>';

    while ($activity = $activities->fetch_assoc()) {
        $host = $activity['host'] == $_SESSION['username'] ? $activity['host'] : "<a href='/user/profile.php?user_id={$activity['host_id']}'>{$activity['host']}</a>";
        $activityID = $activity['activity_id'];
        $editActivity = $activity['host'] == $_SESSION['username'] ? "<a href='/user/activity/edit_activity.php?activity_id={$activityID}'>Edit</a>" : "-";
        $quitActivity = $activity['host'] == $_SESSION['username'] ? "-" : "<a href='' class='quit-activity' data-activity-id='{$activityID}'>Quit</a>";

        echo "<tr>";
        echo "<td>{$host}</td>";
        echo "<td><a href='/user/activity/activity_details.php?activity_id={$activityID}'>{$activity['activity_name']}</a></td>";

        if ($activity['activity_repetition'] == 0) {
            $datetime = substr($activity['activity_one_off_datetime'], 0, 16);

            echo "<td>One-off</td>";
            echo "<td>{$datetime}</td>";
        }
        else {
            echo "<td>Recurring</td>";
            echo "<td>-</td>";
        }

        for ($i = 0; $i < 3; $i++) {
            if ($activity['activity_repetition'] >= $i + 1) {
                $time = substr($activity['activity_recurring_time_'.$i], 0, 5);

                echo "<td>{$activity['activity_recurring_date_'.$i]}</td>";
                echo "<td>{$time}</td>";
            }
            else {
                echo "<td>-</td>";
                echo "<td>-</td>";
            }
        }
        
        echo "<td>{$editActivity}</td>";
        echo "<td>{$quitActivity}</td>";
        echo "</tr>";
    }
    
    echo '</tbody></table>';
}
else {
    echo '<h3 class="text-center">You don\'t have any activities</h3>';
}

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php';
?>
