<?php
//allow user to enter their email address for receiving an email which directs them to reset their password when they forget their password
$title = "Reset Password";
$css = "login.css";
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/login_header.php';
?>

<!-- display a form for user to enter their email address and submit to the backend application-->
<div id="form">
    <h1><?php echo $title ?></h1>
    <p>An email will be sent to you with instructions on how to reset your password.</p>

    <form>
        <input type="text" name="email" placeholder="Enter your email address" required>
        <button type="button" id="reset-password-request-btn">Receive new password by email</button>
    </form>

    <a href="/login.php">Click here to login if you have an account</a><br>
    <a href="/signup.php">Signup</a><br>

    <p id="err-msg" class="text-danger mt-3"></p>
    <p id="msg" class="text-success mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
