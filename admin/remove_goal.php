<?php
$title = "Remove Goal";
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

<h1 class="text-center mt-4">Goal List</h1>

<?php
$sql = "SELECT goal_id, username, goal_name, goal_description, goal_subtask FROM goal";
$result = $mysqli->query($sql);

// list all goals in a table
if($result->num_rows > 0) {
    echo '<table class="table table-bordered table-hover table-sm">
            <thead class="thead-light text-center">
                <tr>
                    <th>Goal Owner</th>
                    <th>Goal Name</th>
                    <th>Description</th>
                    <th>Subtask</th>
                    <th>Delete Goal</th>
                </tr>
            </thead>
        <tbody>';
    
    while($goal = $result->fetch_assoc()) { 
        echo '<tr class="table-success text-center">
            <td class="align-middle">'.$goal['username'].'</td>
            <td class="align-middle">'.$goal['goal_name'].'</td>
            <td class="text-left align-middle text-wrap">'.$goal['goal_description'].'</td>
            <td class="text-left align-middle text-wrap">'.$goal['goal_subtask'].'</td>
            <td class="align-middle"><button class="deleteGoal btn btn-link" data-id="'.$goal['goal_id'].'" data-username="'.$goal['username'].'">Delete</button></td>
            </tr>';
    }
    echo '</tbody></table>';
}
else {
    echo '<h3 class="text-center">No goals</h3>';
}
    
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php';
?>
