<?php
$title = "Settings";
$css = ["user_form.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';

///retrieve preference of this user
$username = $_SESSION['username'];
$sql = "SELECT receive_daily_reminder, receive_weekly_report FROM user WHERE username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $username); 
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<h5 id="msg" class='text-success m-2'></h5>

<div id="form" class="mt-100">
    <h1><?php echo $title ?></h1>
    <p>Personalize services according to your needs</p>
    <form>
        <!-- the indication of whether to receive emails -->
        <input type="checkbox" id="receive-daily-reminder" name="receiveDailyReminder" <?php echo $user['receive_daily_reminder'] ? "checked" : "" ?>>
        <label for="receive-daily-reminder">Receive goal reminder daily via email</label><br>

        <input type="checkbox" id="receive-weekly-reminder" name="receiveWeeklyReport" <?php echo $user['receive_weekly_report'] ? "checked" : "" ?>>
        <label for="receive-weekly-reminder">Receive weekly report via email</label><br>

        <button type='button' id="change-settings-btn" class="submit-btn">Save Changes</button>
    </form>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>

