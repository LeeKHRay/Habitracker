<?php
$title = "Report Inappropriate Goal";
$js = "user_goal.js";
$css = ["user_form.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';

if (!isset($_GET['goal_id'])) {
    header("Location: /user/index.php");
    exit();   
}    
$goal_id = $_GET['goal_id'];

$sql = "select goal_id, goal_name, username, goal_description, goal_subtask, goal_end_date, goal_start_time, goal_end_time FROM goal where goal_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $goal_id);
$stmt->execute();
$goals = $stmt->get_result();

if ($goals->num_rows == 0) {
    header("Location: /user/index.php");
    exit();
}

$goal = $goals->fetch_assoc();
$owner = $goal['username'];
if($owner == $_SESSION['username']) {
    header("Location: /user/index.php");
    exit();   
}   

$goal_name = $goal['goal_name'];
$goal_description = empty($goal['goal_description']) ? "-" : $goal['goal_description'];
$goal_subtask = empty($goal['goal_subtask']) ? "-" : $goal['goal_subtask'];
$goal_end_date = $goal['goal_end_date'];
$goal_start_time = isset($goal['goal_start_time']) ? substr($goal['goal_start_time'], 0, 5) : "-";;
$goal_end_time = isset($goal['goal_end_time']) ? substr($goal['goal_end_time'], 0, 5) : "-";;
?>

<div id="form" class="mt-100">
    <h1><?php echo $title ?></h1>
    
    <form>
        <!-- hidden fields store data that need to send to backend -->
        <input type="hidden" name="goalID" value="<?php echo $goal_id ?>" readonly>
        <input type="hidden" name="owner" value="<?php echo $owner ?>" readonly>        
        <input type="hidden" name="goalName" value="<?php echo $goal_name ?>" readonly>

        <p>User: <?php echo $owner ?></p>
        <p>Goal name: <?php echo $goal_name ?></p>
        <p>Description: <?php echo $goal_description ?></p>
        <p>Subtask: <?php echo $goal_subtask ?></p>
        <p>End date: <?php echo $goal_end_date ?></p>
        <p>Start time: <?php echo $goal_start_time ?></p>
        <p>End time: <?php echo $goal_end_time ?></p>

        <label for="reason">Reason:</label><br>
        <textarea id="reason" name="reason" rows="4" required></textarea><br>

        <button type="button" id="report-goal-btn" class="submit-btn">Submit</button>
    </form>
    
    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
