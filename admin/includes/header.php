<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(0);
$current_page = basename($_SERVER['PHP_SELF']);
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
        display: flex;
        align-items: center;
    }
    .navbar-brand small {
        display: block;
        font-size: 12px;
        color: #666;
        font-weight: 400;
    }
    .admin-badge {
        background: #337ab7;
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        margin-left: 10px;
    }
    .user-info {
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .user-info .welcome-text {
        color: #666;
        font-size: 14px;
    }
    .user-info .admin-name {
        color: #333;
        font-weight: 600;
    }
    .btn-logout {
        background: transparent;
        color: #337ab7;
        border: 2px solid #337ab7;
        border-radius: 6px;
        font-weight: 500;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }
    .btn-logout:hover {
        background: #337ab7;
        color: white;
        text-decoration: none;
    }
    .admin-menu {
        background: #f8f9fa;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }
    .admin-menu .nav > li > a {
        color: #555;
        font-weight: 500;
        padding: 10px 15px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    .admin-menu .nav > li > a:hover,
    .admin-menu .nav > li > a:focus {
        background: #eee;
        color: #337ab7;
    }
    .admin-menu .nav > li.active > a {
        background: #337ab7;
        color: white;
    }
    .dropdown-menu {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 6px;
        padding: 8px 0;
    }
    .dropdown-menu > li > a {
        padding: 8px 20px;
        color: #555;
        transition: all 0.2s ease;
    }
    .dropdown-menu > li > a:hover {
        background: #f8f9fa;
        color: #337ab7;
    }
    .dropdown-menu > li > a i {
        margin-right: 8px;
        color: #666;
    }
    .nav .open > a, 
    .nav .open > a:hover, 
    .nav .open > a:focus {
        background-color: #eee;
        border-color: transparent;
    }
    .dropdown:hover .dropdown-menu {
        display: block;
        margin-top: 0;
        animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @media (max-width: 768px) {
        .navbar-nav > li > a {
            padding: 10px 15px;
        }
        .user-info {
            padding: 10px 15px;
            flex-direction: column;
            align-items: flex-start;
        }
        .btn-logout {
            width: 100%;
            text-align: center;
            margin-top: 10px;
        }
        .dropdown-menu {
            width: 100%;
            margin-top: 0;
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
            <a class="navbar-brand" href="dashboard.php">
                BOOKISH
                <small>ADMIN PANEL</small>
            </a>
        </div>
        <div class="navbar-collapse collapse">
            <div class="nav navbar-nav navbar-right">
                <div class="user-info">
                    <span class="welcome-text">Welcome back,</span>
                    <span class="admin-name"><?php echo $_SESSION['alogin']; ?></span>
                    <a href="logout.php" class="btn-logout">LOGOUT</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admin-menu">
    <div class="container">
        <ul class="nav nav-pills">
            <li class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            
            <li class="dropdown <?php echo in_array($current_page, ['add-book.php', 'manage-books.php']) ? 'active' : ''; ?>">
                <a class="dropdown-toggle" href="#">
                    <i class="fa fa-book"></i> Books <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="add-book.php"><i class="fa fa-plus"></i> Add Book</a></li>
                    <li><a href="manage-books.php"><i class="fa fa-list"></i> Manage Books</a></li>
                </ul>
            </li>

            <li class="dropdown <?php echo in_array($current_page, ['add-category.php', 'manage-categories.php']) ? 'active' : ''; ?>">
                <a class="dropdown-toggle" href="#">
                    <i class="fa fa-folder"></i> Categories <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="add-category.php"><i class="fa fa-plus"></i> Add Category</a></li>
                    <li><a href="manage-categories.php"><i class="fa fa-list"></i> Manage Categories</a></li>
                </ul>
            </li>

            <li class="dropdown <?php echo in_array($current_page, ['add-author.php', 'manage-authors.php']) ? 'active' : ''; ?>">
                <a class="dropdown-toggle" href="#">
                    <i class="fa fa-users"></i> Authors <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="add-author.php"><i class="fa fa-plus"></i> Add Author</a></li>
                    <li><a href="manage-authors.php"><i class="fa fa-list"></i> Manage Authors</a></li>
                </ul>
            </li>

            <li class="<?php echo $current_page == 'reg-users.php' ? 'active' : ''; ?>">
                <a href="reg-users.php"><i class="fa fa-users"></i> Registered Users</a>
            </li>

            <li class="<?php echo $current_page == 'change-password.php' ? 'active' : ''; ?>">
                <a href="change-password.php"><i class="fa fa-lock"></i> Change Password</a>
            </li>
        </ul>
    </div>
</div>

<script>
$(document).ready(function(){
    // Remove data-toggle to prevent click requirement
    $('.dropdown-toggle').removeAttr('data-toggle');
    
    // Optional: Add touch support for mobile
    if('ontouchstart' in document.documentElement) {
        $('.dropdown-toggle').click(function(e) {
            e.preventDefault();
            $(this).parent().toggleClass('open');
        });
    }
});
</script>