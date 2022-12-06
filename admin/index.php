<?php
$title = "Admin Home";
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/admin_header.php';
?>

<h2 class="m-2">Welcome <?php echo $_SESSION['admin_username'] ?></h2>
<h4 class="m-2">You can view the reports and remove inappropriate users, goals and activities.</h4>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
