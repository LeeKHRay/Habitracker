<?php
$title = "Goal Reports";
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

<h1 class="text-center mt-4"><?php echo $title ?></h1>

<?php
function printTableRows($reports, $resolved) {
    global $mysqli;

    while ($report = $reports->fetch_assoc()) {
        $deleted = !isset($report['goal_name']);

        echo '<tr class="'.($resolved ? 'table-primary' : 'table-success').' text-center">';
        echo '<td class="align-middle">'.$report['report_time'].'</td>';
        echo '<td class="align-middle">'.$report['reporter'].'</td>';
        echo '<td class="align-middle">'.$report['owner'].'</td>';
        echo '<td class="text-left align-middle">'.$report['report_goal_name'].'</td>';
        echo '<td class="text-left align-middle text-wrap">'.$report['reason'].'</td>';
        
        if ($resolved) {
            if ($deleted) {
                echo '<td class="align-middle"><button class="btn btn-link align-middle disabled">Deleted</button></td>';
                echo '<td class="align-middle"><button class="btn btn-link align-middle disabled">Dismiss</button></td>';
            }
            else {
                echo '<td class="align-middle"><button class="btn btn-link align-middle disabled">Delete</button></td>';
                echo '<td class="align-middle"><button class="btn btn-link align-middle disabled">Dismissed</button></td>';
            }
            echo '<td class="align-middle"><button class="btn btn-link align-middle disabled">Resolved</button></td>';
        }
        else {
            if (!$deleted && !$report['dismissed']) {
                echo '<td class="align-middle"><button class="deleteGoal btn btn-link" data-id="'.$report['goal_id'].'" data-username="'.$report['owner'].'">Delete</button></td>';
                echo '<td class="align-middle"><button class="dismiss btn btn-link" data-id="'.$report['report_id'].'">Dismiss</button></td>';
                echo '<td class="align-middle"><button class="btn btn-link disabled">Resolve</button></td>';
            }
            else {
                echo '<td class="align-middle"><button class="btn btn-link disabled">'.($deleted ? 'Deleted' : 'Delete').'</button></td>';
                echo '<td class="align-middle"><button class="btn btn-link disabled">'.($deleted? 'Dismiss' : 'Dismissed').'</button></td>';
                echo '<td class="align-middle"><button class="resolve btn btn-link" data-id="'.$report['report_id'].'">Resolve</button></td>';
            }
        }
        
        echo '</tr>';
    }
}

$sql = "SELECT r.report_id, r.report_time, r.reporter, r.owner, r.goal_id, 
            r.goal_name AS report_goal_name, g.goal_name, r.reason, r.dismissed
        FROM report r
        LEFT JOIN goal g
        ON r.goal_id = g.goal_id
        WHERE r.resolved = FALSE AND r.report_type = 'goal'
        ORDER BY r.report_time";
$unresolved_reports = $mysqli->query($sql);

$sql = "SELECT r.report_id, r.report_time, r.reporter, r.owner, r.goal_id AS goal_id, 
            r.goal_name AS report_goal_name, g.goal_name, r.reason, r.dismissed
        FROM report r 
        LEFT JOIN goal g 
        ON r.goal_id = g.goal_id
        WHERE r.resolved = TRUE AND r.report_type = 'goal'
        ORDER BY r.report_time";
$resolved_reports = $mysqli->query($sql);

if ($unresolved_reports->num_rows + $resolved_reports->num_rows > 0) {
    echo '<table class="table table-bordered table-hover table-sm">
            <thead class="thead-light text-center">
                <tr>
                    <th>Report Time</th>
                    <th>Reporter</th>
                    <th>Goal Owner</th>
                    <th>Goal Name</th>
                    <th>Reason</th>
                    <th>Delete Goal</th>
                    <th>Dismiss Report</th>
                    <th>Resolve Report</th>
                </tr>
            </thead>
            <tbody>';
    
    printTableRows($unresolved_reports, false);
    printTableRows($resolved_reports, true);
    echo '</tbody></table>';
}
else {
    echo '<h3 class="text-center">No reports</h3>';
}

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php';
?>
