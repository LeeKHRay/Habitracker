<?php
    $sql_host="localhost";
    $sql_username="root";
    $sql_password='';
    $sql_database="habitracker";

    $mysqli = new mysqli($sql_host, $sql_username, $sql_password, $sql_database);
    if($mysqli -> connect_errno) {
        die("Connection failed: ".mysqli_connect_error());
    }
?>
