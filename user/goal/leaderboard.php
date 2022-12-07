<?php
$title = "Leaderboard";
$css = ["user_table.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';
?>

<h1 class="text-center mt-4"><?php echo $title ?></h1>

<?php
//retrieve information of the users in descending order according to score
$sql = "SELECT user_id, username, score FROM user ORDER BY score DESC";
$users = $mysqli->query($sql);

$rank = 0;
$score = -1;

echo '<table class="content-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>User</th>
                <th>Score*</th>
            </tr>
        </thead>
        <tbody>';

// display the ranking from high to low
while ($user = $users->fetch_assoc()) {
    if (($score != $user['score'])) { // check if the score is different from the previous one
        
        $rank = $rank + 1;
    }
    $score = $user['score'];

    // display the details of each of the top 10 users
    if ($user['user_id'] == $_SESSION['user_id']) { // display the rank of the current user
        echo "<tr class='table-success'>";
        echo "<th>{$rank}</th>";
        echo "<th>{$user['username']}</th>";
        echo "<th>{$user['score']}</th>";
    }
    else if ($rank <= 10) {  // display the rank of other users
        echo "<tr>";
        echo "<td>{$rank}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['score']}</td>";
    }

    echo "</tr>";
}

echo "</tbody></table>";
?>

<p class="text-center"><strong>* Remark: The scores are updated at midnight every day.</strong></p>

</body>
