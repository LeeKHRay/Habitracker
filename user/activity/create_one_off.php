<?php
$title = "Create One-off Activity";
$js = "user_activity.js";
$css = ["user_form.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
?>

<div id="form" class="mt-100">
    <h1><?php echo $title ?></h1>
    
    <p>You can create an one-off activity now and see if the activity mode and your new hobby-buddies suit you!</p>

    <form>
        <label for="activity-name">* Name: </label>
        <input type="text" id="activity-name" name="activityName" placeholder="Name of the activity" required>
        
        <label for="date">* Date: </label>
        <input type="date" id="date" name="date" placeholder="<?php echo date("Y-m-d") ?>" required><br>

        <label for="time">* Time: </label>
        <input type="time" id="time" name="time" required><br>

        <label for="location">Location: </label>
        <select name="location" id="location">
            <option selected="selected">Central & Western</option>
            <option>Eastern</option>
            <option>Islands</option>
            <option>Kowloon City</option>
            <option>Kwai Tsing</option>
            <option>Kwun Tong</option>
            <option>North</option>
            <option>Sai Kung</option>
            <option>Sha Tin</option>
            <option>Sham Shui Po</option>
            <option>Southern</option>
            <option>Tai Po</option>
            <option>Tsuen Wan</option>
            <option>Tuen Mun</option>
            <option>Wan Chai</option>
            <option>Wong Tai Sin</option>
            <option>Yau Tsim Mong</option>
            <option>Yuen Long</option>
            <option>Online</option>
            <option>Others</option>
        </select>

        <label for="time-remark">Remark on date and time (Optional): </label>
        <input type="text" id="time-remark" name="timeRemark" placeholder="Date/time remark of the activity">

        <label for="remark">General Remark (Optional): </label>
        <input type="text" id="remark" name="remark" placeholder="General remark of the activity">

        <button type="submit" id="create-one-off-btn" class="submit-btn">Create!</button>
    </form>

    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
