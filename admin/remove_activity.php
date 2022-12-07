<?php
$title = "Remove Activity";
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

<h1 class="text-center mt-4">Activity List</h1>

<?php
$sql = "SELECT activity_id, host, activity_name, activity_location, activity_time_remark, activity_remark FROM activity";
$activities = $mysqli->query($sql);

// list all activities in a table
if($activities->num_rows > 0) {
    echo '<table class="table table-bordered table-hover table-sm">
            <thead class="thead-light text-center">
                <tr>
                    <th>Activity Host</th>
                    <th>Activity Name</th>
                    <th>Location</th>
                    <th>Remark on date and time</th>
                    <th>General Remark</th>
                    <th>Delete Activity</th>
                </tr>
            </thead>
        <tbody>';
    
    while($activity = $activities->fetch_assoc()) { 
        echo '<tr class="table-success text-center">
            <td class="align-middle">'.$activity['host'].'</td>
            <td class="align-middle">'.$activity['activity_name'].'</td>
            <td class="align-middle">'.$activity['activity_location'].'</td>
            <td class="align-middle">'.$activity['activity_time_remark'].'</td>
            <td class="align-middle">'.$activity['activity_remark'].'</td>
            <td class="align-middle"><button class="deleteActivity btn btn-link" data-id="'.$activity['activity_id'].'" data-username="'.$activity['host'].'">Delete</button></td>
            </tr>';
    }
    echo '</tbody></table>';
}
else {
    echo '<h3 class="text-center">No activities</h3>';
}

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php';
?>
