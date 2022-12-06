<?php
$title = "Search Goals";
$js = "user_goal.js";
$css = ["user_form.css", "user_table.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
};
?>

<h5 id="msg" class='text-success m-2'><?php echo $msg ?></h5>

<div id="form" class="mt-50">
    <h1><?php echo $title?></h1>

    <p>You can search public goals through keywords.</p>

    <form>
        <label for="keyword">Keyword: </label>
        <input id="keyword" type="text" name="keyword" required>

        <!-- sort by which criterion -->
        <label for="sortby">Sort by:</label>
        <select name="sortby" id="sortby">
            <option value="goal_name" selected>Goal name</option>
            <option value="goal_end_date">End date</option>
            <option value="goal_start_time">Start time</option>
            <option value="goal_end_time">End time</option>
        </select>

        <!-- asc or desc -->
        <label for="order">Order:</label>
        <select name="order" id="order">
            <option value="ASC" selected>Ascending</option>
            <option value="DESC">Descending</option>
        </select>

        <button type="submit" id="search-goal-btn" class="submit-btn">Search <i class="fa fa-search"></i></button>
    </form>
        
    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<div id="results"></div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
