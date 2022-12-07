<!--display one's profile when user click the view profile button-->

<?php
$title = "Profile";
$css = ["profile.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';

$userID = isset($_GET["user_id"]) ? $_GET["user_id"] : $_SESSION['user_id'];

//fetch information of the user who logged in the system from the database
$sql = "Select * FROM user Where user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $userID); 
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!isset($user)) {
    header("Location: /user/profile.php");
    exit();
}

$username = $user['username'];
$email = $user['email'];
$firstName = isset($user['first_name']) ? $user["first_name"] : "";
$lastName = isset($user['last_name']) ? $user["last_name"] : "";
$welcomeMsg = isset($user['welcome_message']) ? $user["welcome_message"] : "";

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<h5 id="msg" class='text-success m-2'><?php echo $msg ?></h5>

<!-- display users' first name, last name, profile picture and welcoming message in their profile-->
<div id="profile-card">
    <img id="avatar" src="/public/avatar/<?php echo $user['avatar'] ?>" height="150" alt="avatar">

    <!-- display users' first name, last name, profile picture and welcoming message in their profile-->
    <div class="main-container">
        <h3><?php echo $username ?></h3>
        <p><i class="fa fa-envelope info"></i>Email: <?php echo $email ?></p>
        <p><i class="fa fa-star info"></i>First name: <?php echo $firstName ?></p>
        <p><i class="fa fa-star-o info"></i>Last name: <?php echo $lastName ?></p>
        <p><i class="fa fa-heart info"></i>Welcoming message: <br><?php echo $welcomeMsg ?></p>

        <?php if ($userID == $_SESSION['user_id']) { ?>
            <hr>
            <button type="button" onclick="location.href='/user/edit_profile.php';" class="edit-profile-btn">Edit your profile</button>
        <?php } ?>

        <?php
        //display success message after user edit their profile and being redirect back to the display profile page
        if (isset($_GET['profile'])){    //use $_GET to check the url
            if ($_GET['profile'] == "profileupdated") {
            echo '<p class="success">Your profile is updated!</p>';
        }
        }
        
        //display error messages according to users invalid actions when uploading their profile picture 
        if (isset($_GET['error'])){  
            if ($_GET['error'] == "wrongtype") {
                echo '<p class="wrong">You cannot upload files of this type!</p>';
            }
            else if ($_GET['error'] == "filetoobig") {
                echo '<p class="wrong"> Your file is too big!</p>';
            }
            else if ($_GET['error'] == "error") {
                echo '<p class="wrong">There was an error uploading your file!</p>';
            }
        }
        
        //display success message after user successfully update their proilfe picture 
        if (isset($_GET['upload'])){ 
            if ($_GET['upload'] == "success"){
                echo '<p class="success">Your profile picture is uploaded!</p>';
            }
        }
    ?>
    </div>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
