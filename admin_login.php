<?php 
$title = "Login as Admin";
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/login_header.php';
?>

<div id="form">
    <img src="/public/img/login_avatar1.png" class="avatar">
    <h1><?php echo $title ?></h1>

    <form>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter Username">

        <label for="pwd">Password</label>
        <input type="password" id="pwd" name="pwd" placeholder="Enter Password">

        <button id="admin-login-btn" type="submit">Login</button>
    </form>

    <a href="/login.php">Login as user</a>
    
    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
