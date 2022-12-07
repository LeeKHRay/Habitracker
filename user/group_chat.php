<?php
$title = "Chat with users of the same activities";
$jQueryUI = true;
$js = "user_group_chat.js";
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
?>

<h1 class="text-center mt-4"><?php echo $title ?></h1>

<div class="container table-responsive">
    <!-- display a list of users that the user can start chat with-->
    <div id="activities"></div>
</div>
<div id="dialog-boxes"></div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'?>
