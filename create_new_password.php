<?php
//allow users to create their new password after being redirected from the email
//users will receive an email which contain the link to this page if one forgets his password when they attempt to login
$title = "Create New Password";
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/login_header.php';
?>

<div id="form">
    <h1><?php echo $title ?></h1>
    
    <?php
    $selector = $_GET["selector"];
    $validator = $_GET["validator"];

    if(empty($selector) || empty($validator)) {
        header("Location: /reset_password.php");
        exit();
    }

    if (!ctype_xdigit($selector) || !ctype_xdigit($validator)) { // check if they are hex number 
        header("Location: /reset_password.php");
        exit();
    }
    ?>

    <form>  
        <input type="hidden" name="selector" value="<?php echo $selector ?>">
        <input type="hidden" name="validator" value="<?php echo $validator ?>">

        <label for="pwd">New password</label> 
        <input type="password" id="pwd" name="pwd" placeholder="Enter new Password">

        <label for="confirm-pwd">Confirm password</label> 
        <input type="password" id="confirm-pwd" name="confirmPwd" placeholder="Confirm password">

        <button type="button" id="reset-password-btn">Reset password</button>
    </form>

    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
