<?php
$title = "Change Password";
$css = ["user_form.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
?>

<h5 id="msg" class='text-success m-2'></h5>

<div id="form" class="mt-100">
    <h1><?php echo $title ?></h1>

    <form>
        <input type="password" name="curPwd" placeholder="Current Password">
        <input type="password" name="newPwd" placeholder="New Password">
        <input type="password" name="confirmPwd" placeholder="Confirm Password">
        <button type="button" id="change-password-btn" class="submit-btn">Save</button>
    </form>
    
    <p id="err-msg" class="text-danger mt-3"></p>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
