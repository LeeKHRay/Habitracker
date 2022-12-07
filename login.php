<?php
$title = "Login";
$css = "login.css";
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/login_header.php';

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
};
?>

<div id="form">
    <img src="/public/img/login_avatar1.png" class="avatar">
    <h1>Login Here</h1> 

    <form>
        <label for="username-email">Username/E-mail</label> 
        <input type="text" id="username-email" name="usernameEmail" placeholder="Enter Username/E-mail" required>

        <label for="pwd">Password</label>
        <input type="password" id="pwd" name="pwd" placeholder="Enter Password" required>

        <button id="login-btn" type="submit">Login</button>
    </form>

    <?php
    //display success message after user has successfully created new password
    //users will be redirected back to the login page from the create password page
    //users create their new password if they have forgot their password             
    ?>

    <a href="/signup.php">Signup</a><br>
    <a href="/reset_password.php">Forgot password</a><br>
    <a href="/admin_login.php">Login as administrator</a>

    <p id="err-msg" class="text-danger mt-3"></p>
    <p id="msg" class="text-success mt-3"><?php echo $msg ?></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
