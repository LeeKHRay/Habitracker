<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?php echo isset($title) ? $title : "Habitracker" ?></title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Raleway&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/public/css/index.css">

        <script src="https://code.jquery.com/jquery-3.4.1.min.js" defer integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" defer integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" defer integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        
        <?php
        if (isset($carousel)) {
            echo '<script src="/public/js/carousel.js" defer></script>';
        }
        ?>
    </head>

    <body>
        <header>
            <nav class="navbar navbar-expand-lg navbar-light">
                <img id="logo" src="/public/img/logo.png" alt="Habitracker Logo" width="200px" height="52px">
                <!--button in mobile only-->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Home</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/functions.php">Functions</a>
                        </li>
                        <li class="nav-item">
                            <a id='signup-btn' class="nav-link" href="/signup.php">Register</a>
                        </li>
                        <li class="nav-item">
                            <a id='login-btn' class="nav-link" href="/login.php">Login</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>