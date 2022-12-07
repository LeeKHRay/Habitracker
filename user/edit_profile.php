<?php
$title = "Edit Profile";
$css = ["user_form.css"];
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/user_header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_connect.php';

$username = $_SESSION["username"];
$sql = "SELECT email, first_name, last_name, welcome_message FROM user WHERE username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $username); 
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$firstName = isset($user['first_name']) ? $user['first_name'] : "";
$lastName = isset($user['last_name']) ? $user['last_name'] : "";
$welcomeMsg = isset($user['welcome_message']) ? $user['welcome_message'] : "";
?>

<div id="form" class="mt-100">
    <h1><?php echo $title ?></h1>

    <form enctype="multipart/form-data">
        <p>Username: <?php echo $username ?></p>
        <p>Email: <?php echo $user['email'] ?></p>  <!-- change to email -->

        <label for="first-name">First name:</label>
        <input type="text" id="first-name" name="firstName" type="text" value="<?php echo $firstName ?>" placeholder="Enter your first name">

        <label for="last-name">Last name:</label>
        <input type="text" id="last-name" name="lastName" type="text" value="<?php echo $lastName ?>" placeholder="Enter your last name">

        <label for="welcome-msg">Welcoming message:</label>
        <textarea id="welcome-msg" name="welcomeMsg" rows="4" placeholder="Enter your welcoming message"><?php echo $welcomeMsg ?></textarea>
        
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Upload avatar</span>
            </div>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="avatar-upload" name="avatar" accept=".png, .jpg, .jpeg">
                <label class="custom-file-label" id="avatar-label" for="avatar">Choose a jpg, jpeg or png file</label>
            </div>
        </div>
        <button type="button" id="edit-profile-btn" class="submit-btn">Save</button>
    </form>

    <p id="err-msg" class="text-danger mt-3"></p>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
