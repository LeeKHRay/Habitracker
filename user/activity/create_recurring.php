<?php
$title = "Create Recurring Activity";
$js = "user_activity.js";
$css= ["user_form.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
?>

<div id="form" class="mt-50">
    <h1><?php echo $title ?></h1>

    <p><?php echo $_SESSION['username']?>, create a recurring activity now and meet some new hobby-buddies! (ง •̀_•́)ง</p>
    <p class="font-weight-normal">A recurring activity happens 1 to 3 times per week. This can make you stick to an activity and form a new habit</p>
    
    <form action="/actions/user_activity_actions.php?action=createRecurring" method="POST">
        <label for="activity-name">* Name: </label>
        <input type="text" id="activity-name" name="activityName" placeholder="Name of the activity" required>

        <p>You can choose at most 3 days and its corresponding time that you want to host this activity. If
        you want to do something on a daily basis, <a href="/user/goal/create_goal.php">start a goal</a>.</p>
        
        <ul>
            <li>
                Sunday<br>            
                <label for="time0">Time: </label>
                <input type="time" id="time0" name="time0"><br>
            </li>
            <li>
                Monday<br>            
                <label for="time1">Time: </label>
                <input type="time" id="time1" name="time1"><br>
            </li>
            <li>
                Tuesday<br>            
                <label for="time2">Time: </label>
                <input type="time" id="time2" name="time2"><br>
            </li>
            <li>
                Wednesday<br>            
                <label for="time3">Time: </label>
                <input type="time" id="time3" name="time3"><br>
            </li>
            <li>
                Thursday<br>            
                <label for="time4">Time: </label>
                <input type="time" id="time4" name="time4"><br>
            </li>
            <li>
                Friday<br>            
                <label for="time5">Time: </label>
                <input type="time" id="time5" name="time5"><br>
            </li>
            <li>
                Saturday<br>            
                <label for="time6">Time: </label>
                <input type="time" id="time6" name="time6"><br>
            </li>
            <li type="none">
                <button type="button" id="reset-time-btn">Reset Time</button><br>
            </li>
        </ul>

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

        <label for="timeRemark">Remark on the date and time (Optional): </label>
        <input type="text" id="timeRemark" name="timeRemark" placeholder="Date/time remark of the activity">

        <label for="remark">General Remark (Optional): </label>
        <input type="text" id="remark" name="remark" placeholder="General remark of the activity">

        <button type="submit" id="create-recurring-btn" class="submit-btn">Create!</button>
    </form>

    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
