<?php
$title = "Create Goal";
$js = "user_goal.js";
$css = ["user_form.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
?>

<div id="form" class="mt-100">
    <h1><?php echo $title ?></h1>

    <p><?php echo $_SESSION['username']?>, keep going and create a goal now! (ง •̀_•́)ง</p>

    <form>
        <label for="goal-name">* Goal name:</label>
        <input type="text" id="goal-name" name="goalName" placeholder="Name of the goal" required>

        <label for="goal-description">Description (Optional):</label>
        <input type="text" id="goal-description" name="goalDescription" placeholder="Description of the goal">

        <label for="goal-subtask">Subtask (Optional):</label>
        <input type="text" id="goal-subtask" name="goalSubtask" placeholder="Subtask of the goal">

        <!-- starting from the day when the goal is created, so +20 days -->
        <label for="duration">* Day(s) (Recommended: 21 days, until <?php echo date("Y M d", strtotime("+20 days")) ?>):</label><br>
        <input type="number" id="duration" name="duration" min="1" value="1" required>

        <p>Time (Optional):</p>
        <label for="goal-start-time">from</label>
        <input type="time" id="goal-start-time" name="goalStartTime">
        <label for="goal-end-time">to</label>
        <input type="time" id="goal-end-time" name="goalEndTime">
        <button type="button" id="reset-time-btn">Reset Time</button><br>

        <input type="checkbox" id="goal-public" name="goalPublic">
        <label for="goal-public">Allow other users to see this goal</label><br>

        <button type='button' id="create-goal-btn" class="submit-btn">Create</button>
    </form>

    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
