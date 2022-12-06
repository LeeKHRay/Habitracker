<?php
$title = "My Progress Today";
$js = "user_goal.js";
$css = ["user_table.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';
?>

<h5 id="msg" class='text-success m-2'></h5>

<h1 class="text-center mt-4"><?php echo $title ?></h1>

<?php
$username = $_SESSION['username'];
$today = date("Y-m-d", time());

//retrieve the goals that are still in progress
$sql = "SELECT * FROM goal WHERE username = ? AND goal_end_date >= ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $username, $today);
$stmt->execute();
$goals = $stmt->get_result();

if ($goals->num_rows > 0) {
    echo '<table class="content-table">
            <thead>
                <tr>
                    <th>Goal name</th>
                    <th>Description</th>
                    <th>Subtask</th>
                    <th>End date</th>
                    <th>Start time</th>
                    <th>End time</th>
                    <th>Streaks*</th>
                    <th>Completed today</th>
                </tr>
            </thead>
            <tbody>';
            
    while($goal = $goals->fetch_assoc()) {
        $goal_start_time = isset($goal['goal_start_time']) ? substr($goal['goal_start_time'], 0, 5) : "-";
        $goal_end_time = isset($goal['goal_end_time']) ? substr($goal['goal_end_time'], 0, 5) : "-";
        $checked = $goal['goal_completed'] ? "checked" : "";

        echo "<tr>";
        echo "<td>{$goal['goal_name']}</td>";
        echo "<td>{$goal['goal_description']}</td>";
        echo "<td>{$goal['goal_subtask']}</td>";
        echo "<td>{$goal['goal_end_date']}</td>";
        echo "<td>{$goal_start_time}</td>";
        echo "<td>{$goal_end_time}</td>";        
        echo "<td>{$goal['streak']}</td>";        
        echo "<td><input type='checkbox' data-goal-id='{$goal['goal_id']}' {$checked}></td>";
        echo "</tr>";
    }
    echo '</tbody></table>';

    echo '<p class="text-center"><strong>* Remark: The streaks are updated at midnight every day.</strong></p>';
    echo '<button type="button" id="update-goal-progress-btn">Save Progress</button>';
}
else {
    echo '<h3 class="text-center">You don\'t have any goals</h3>';
}

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php';
?>
