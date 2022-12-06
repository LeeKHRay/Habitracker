<?php
$css = ["user_index.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
?>

<h4 class="text-center m-2">You can access different functions of Habitracker using links at the header. Explore your journey in Habitracker!</h4>

<!--displaying the layout of the index page -->
<main>
    <div class="content">
        <div class="kolom">
            <div class="atas">
                <a href="/user/goal/create_goal.php">
                    <img src="/public/img/target.png">
                </a>
            </div>
            <div class="tengah">
                <h2>GOALS</h2>
            </div>
            <div class="bawah">
                <p>Set up a goal to step out of your comfort zone and challenge yourself - you might discover something beyond your imagination!</p>
            </div>
        </div>
        <div class="kolom">
            <div class="atas">
                <a href="/user/activity/create_one_off.php">
                    <img src="/public/img/bicycling.png">
                </a>
            </div>
            <div class="tengah">
                <h2>ACTIVITIES</h2>
            </div>
            <div class="bawah">
                <p>Start an activity and find some hobby-buddies - meet new friends and be one another's support in this exploration!</p></div>
            </div>
        <div class="kolom">
            <div class="atas">
                <a href="/user/chat.php">
                    <img src="/public/img/conversation.png">
                </a>
            </div>
            <div class="tengah">
                <h2>CHAT</h2>
            </div>
            <div class="bawah">
                <p>Talk to friends when you are in doubt - share your experience or get some advice from the supportive user community!</p>
            </div>
        </div>
    </div>
</main>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
