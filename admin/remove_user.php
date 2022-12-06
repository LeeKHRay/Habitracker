<?php
$title = "Remove User";
$css = "table.css";
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/admin_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<h5 id="msg" class='text-success m-2'><?php echo $msg ?></h5>

<h1 class="text-center mt-4">User List</h1>

<?php
$sql = "SELECT user_id, username, first_name, last_name, email, avatar, last_activity FROM user";
$result = $mysqli->query($sql);

// list all users in a table
if($result->num_rows > 0) {
    echo '<table class="table table-bordered table-hover table-sm">
            <thead class="thead-light text-center">
                <tr>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Last Activty</th>
                    <th>Delete User</th>
                </tr>
            </thead>
        <tbody>';
    
    while($user = $result->fetch_assoc()) { 
        echo "<tr class='table-success text-center'>";
        echo "<td class='align-middle'>{$user['username']}</td>";
        echo "<td class='align-middle'>{$user['first_name']}</td>";
        echo "<td class='align-middle'>{$user['last_name']}</td>";
        echo "<td class='align-middle'>{$user['email']}</td>";
        echo "<td class='align-middle'>{$user['last_activity']}</td>";
        echo "<td class='align-middle'><button class='deleteUser btn btn-link' 
            data-id='{$user['user_id']}' data-username='{$user['username']}' data-email='{$user['email']}' data-avatar='{$user['avatar']}'>Delete</button></td>";
        echo "</tr>";
    }
    echo '</tbody></table>';
}
else {
    echo '<h3 class="text-center">No users</h3>';
}

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php';
?>
