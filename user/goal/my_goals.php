<?php
$title = "My Goals";
$js = "user_goal.js";
$css = ["user_table.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';

//echo feedback messages
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<h5 id="msg" class='text-success m-2'><?php echo $msg ?></h5>

<h1 class="text-center mt-4"><?php echo $title ?></h1>

<?php
$username = $_SESSION['username'];

// retrieve all goals created by this user
$sql = "SELECT * FROM goal WHERE username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $username);
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
                    <th>Visible by other users</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>';
    
    while ($goal = $goals->fetch_assoc()) {
        $goal_description = empty($goal['goal_description']) ? "-" : $goal['goal_description'];
        $goal_subtask = empty($goal['goal_subtask']) ? "-" : $goal['goal_subtask'];
        $goal_start_time = isset($goal['goal_start_time']) ? substr($goal['goal_start_time'], 0, 5) : "-";
        $goal_end_time = isset($goal['goal_end_time']) ? substr($goal['goal_end_time'], 0, 5) : "-" ;
        $goal_public = $goal['goal_public'] ? "Yes" : "No";
        
        echo "<tr>";
        echo "<td>{$goal['goal_name']}</td>";
        echo "<td>{$goal_description}</td>";        
        echo "<td>{$goal_subtask}</td>";
        echo "<td>{$goal['goal_end_date']}</td>";
        echo "<td>{$goal_start_time}</td>";
        echo "<td>{$goal_end_time}</td>";
        echo "<td>{$goal_public}</td>";
        echo "<td><a href='/user/goal/edit_goal.php?goal_id={$goal['goal_id']}'>Edit</a></td>";
        echo "<td><a href='' class='delete-goal' data-goal-id='{$goal['goal_id']}'>Delete</a></td>";
        echo "</tr>";
    }

    echo '</tbody></table>';
}
else {
    echo '<h3 class="text-center">You don\'t have any goals</h3>';
}

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php';
?>
