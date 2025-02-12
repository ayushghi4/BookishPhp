<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(0);
?>
<link href="assets/css/bootstrap.css" rel="stylesheet" />
<link href="assets/css/font-awesome.css" rel="stylesheet" />
<style>
    .navbar {
        background: #fff;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 0;
    }
    .navbar-brand {
        font-size: 24px;
        font-weight: 700;
        color: #333 !important;
        padding: 15px;
        height: auto;
    }
    .navbar-brand small {
        display: block;
        font-size: 12px;
        color: #666;
        font-weight: 400;
    }
    .navbar-nav > li > a {
        padding: 20px 15px;
        color: #555;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .navbar-nav > li > a:hover,
    .navbar-nav > li > a:focus {
        color: #337ab7;
        background: transparent;
    }
    .navbar-nav > .active > a,
    .navbar-nav > .active > a:hover,
    .navbar-nav > .active > a:focus {
        color: #337ab7;
        background: transparent;
        font-weight: 600;
    }
    .auth-buttons {
        padding: 15px;
    }
    .auth-buttons .btn {
        margin-left: 10px;
        border-radius: 6px;
        font-weight: 500;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }
    .btn-signup {
        background: #337ab7;
        color: white;
        border: none;
    }
    .btn-signup:hover {
        background: #23527c;
        color: white;
    }
    .btn-login {
        background: transparent;
        color: #337ab7;
        border: 2px solid #337ab7;
    }
    .btn-login:hover {
        background: #337ab7;
        color: white;
    }
    @media (max-width: 768px) {
        .navbar-nav > li > a {
            padding: 10px 15px;
        }
        .auth-buttons {
            padding: 10px 15px;
        }
        .auth-buttons .btn {
            display: block;
            margin: 5px 0;
            width: 100%;
        }
    }
</style>

<div class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">
                BOOKISH
                <small>ONLINE BOOK LIBRARY</small>
            </a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="books.php">BROWSE BOOKS</a></li>
                <li><a href="categories.php">CATEGORIES</a></li>
                <li><a href="authors.php">AUTHORS</a></li>
                <?php if(strlen($_SESSION['login']) > 0) { ?>
                    <li><a href="my-reading.php">MY READING</a></li>
                    <li><a href="my-profile.php">MY PROFILE</a></li>
                    <?php if($_SESSION['role'] == 'admin') { ?>
                        <li><a href="admin/">ADMIN</a></li>
                    <?php } ?>
                <?php } ?>
            </ul>
            <div class="nav navbar-nav navbar-right">
                <?php if(strlen($_SESSION['login'])==0) { ?>
                    <div class="auth-buttons">
                        <a href="login.php" class="btn btn-login">LOGIN</a>
                        <a href="signup.php" class="btn btn-signup">SIGN UP</a>
                    </div>
                <?php } else { ?>
                    <div class="auth-buttons">
                        <a href="logout.php" class="btn btn-login">LOGOUT</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>