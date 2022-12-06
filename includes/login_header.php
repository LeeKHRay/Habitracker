<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: /user/index.php");
    exit();
}
else if (isset($_SESSION['admin_username'])) {
    header("Location: /admin/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo isset($title) ? $title : "Habitracker" ?></title>
        
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="/public/css/login_header.css">
        <link rel="stylesheet" href="/public/css/login.css">

        <script src="https://code.jquery.com/jquery-3.4.1.min.js" defer integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="/public/js/login.js" defer></script>

        <?php
        if (isset($css)) {
            echo '<link rel="stylesheet" href="/public/css/'.$css.'">';
        }
        ?>
    </head>

    <body>
        <div class="bubbles">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
        </div>

        <a href="/"><img id="logo" src="/public/img/logo.png" alt="Habitracker" height="50"></a>
