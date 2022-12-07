<?php
// for users to sign up if they do not have an account previously
$title = "Sign Up";
$css = "login.css";
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/login_header.php';
?>

<div id="form">
    <h1>Signup Here</h1>

    <!--display the signup form for users to sign up and pass their input to the backend appplication-->
    <form action="/actions/user_actions.php?action=signup" method="post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter Username" required>

        <label for="email">Email</label> 
        <input type="text" id="email" name="email" placeholder="Enter E-mail" required>

        <label for="pwd">Password</label> 
        <input type="password" id="pwd" name="pwd" placeholder="Enter Password" required>

        <label for="confirm-pwd">Repeat your password</label> 
        <input type="password" id="confirm-pwd" name="confirmPwd" placeholder="Confirm password" required>

        <button id="signup-btn" type="button">Signup</button>
    </form>

    <a href="login.php">Click here to login if you have an account</a></br>
    <p id="err-msg" class="text-danger mt-3"></p>

    <?php
    //display success message after user has successfully registered one's account
    if (isset($_GET['signup'])) {
        if ($_GET['signup'] == "success") {
            echo '<p id="signup_success"> Signup successful</p>';
            echo '<a href="/login.php">Please click here to log in!</a></br>';
        }
    }
    else {
        echo '';
    }
    ?>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
