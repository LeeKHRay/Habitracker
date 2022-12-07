<?php
$title = "Edit Goal";
$js = "user_goal.js";
$css = ['user_form.css'];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';

if (!isset($_GET['goal_id'])) {
    header("Location: /user/goal/my_goals.php");
    exit();
}
$goal_id = $_GET['goal_id'];

//retrieve information of the corresponding goal
$sql = "SELECT * FROM goal WHERE goal_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $goal_id);
$stmt->execute();
$goals = $stmt->get_result();

if ($goals->num_rows == 0) {
    header("Location: /user/goal/my_goals.php");
    exit();
}

$goal = $goals->fetch_assoc();
if ($goal['username'] != $_SESSION['username']) {
    header("Location: /user/goal/my_goals.php");
    exit();
}
?>

<div id="form" class="mt-100">
    <h1><?php echo $title ?></h1>

    <form>
        <input type="hidden" name="goalID" value="<?php echo $goal['goal_id'] ?>" readonly>

        <label for="goal-name">* Goal name:</label>
        <input type="text" id="goal-name" name="goalName" value="<?php echo $goal['goal_name'] ?>" required>

        <label for="goal-description">Description (Optional):</label>
        <input type="text" id="goal-description" name="goalDescription" value="<?php echo $goal['goal_description'] ?>">

        <label for="goal-subtask">Subtask (Optional):</label>
        <input type="text" id="goal-subtask" name="goalSubtask" value="<?php echo $goal['goal_subtask'] ?>">

        <?php 
            $daysToEnd = ceil((strtotime($goal['goal_end_date']) - time()) / 60 / 60 / 24) + 1;
        ?>
        <label for="duration">* Day(s) (Current end date: <?php echo $goal['goal_end_date'] ?>)<br>
        <input type="number" id="duration" name="duration" min="1" value="<?php echo $daysToEnd;?>" required>

        <p>Time (Optional):</p>
        <label for="goalStartTime">from</label>
        <input type="time" id="goal-start-time" name="goalStartTime" value="<?php echo $goal['goal_start_time'] ?>">
        <label for="goalEndTime">to</label>
        <input type="time" id="goal-end-time" name="goalEndTime" value="<?php echo $goal['goal_end_time'] ?>">
        <button type="button" id="reset-time-btn">Reset Time</button><br>
        
        <input type="checkbox" id="goal-public" name="goalPublic" <?php echo $goal['goal_public'] ? "checked" : "" ?>>
        <label for="goal-public">Allow other users to view this goal</label><br>

        <button type='submit' id="edit-goal-btn" class="submit-btn">Update</button>
    </form>

    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
