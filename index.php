<!-- This is the html code for displaying homepage
    the htmls and related css are contibuted by Adrian Kwan 1155110979
    written on 19 April 2020
    it can connect to the related html files for more informations or direct to the login page
-->

<?php 
$carousel = true;
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/header.php';
?>

<section id='carouselIntro'>
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="/public/img/tracker.jpg" style="height: 500px; opacity: 0.5;">
                <div class = "carousel-caption" id="display0">
                    <p>Best Tracker</p>
                </div>
            </div>
            <div class="carousel-item" >
                <img class="d-block w-100" src="/public/img/studygroup.png" style="height: 500px; opacity: 0.5;">
                <div class = "carousel-caption" id="display1">
                    <p>Find Partners</p>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="/public/img/learn.jpg" style="height: 500px; opacity: 0.5;">
                <div class = "carousel-caption" id="display2">
                    <p>Learn Faster</p>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href='#myCarousel' data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href='#myCarousel' data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</section>

<section id='intro'>
    <div class='jumbotron'>
        <div class='container'>
            <div class='row'>
                <div class = "col-md-12 text-center">
                    <h1> The Best Tracker to Develop Habits </h1>
                    <p> Feeling bored ? Want to meet like-minded friends ?</p>
                    <p><a href="/signup.php">Register</a> Now!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id='features'>
    <div class='container'>
        <div class='row'>
            <div class='col-md-10 offset-md-1 text-center'>
                <h1>Features</h1>
                <div class='row'>
                    <div class='col-md-4 text-center'>
                        <div class='outer'>
                            <h3>Better experience</h3>
                            <div class='inner'>
                                <img src="/public/img/report.png">  
                                <p>Receive customized weekly report and daily notification!</p>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-4 text-center'>
                        <div class='outer'>
                            <h3>Chatroom</h3>
                            <div class='inner'>
                                <img src="/public/img/conversation.png">
                                <p>Share your experience and get advice from supportive users!</p>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-4 text-center'>
                        <div class='outer'>
                            <h3>Streaks</h3>
                            <div class='inner'>
                                <img src="/public/img/fire.png">
                                <p>Maintain your streaks and compete with other users!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/info_footer.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php';
?>
