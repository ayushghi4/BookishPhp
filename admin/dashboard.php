<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    ?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Online Library Management System | Admin Dashboard</title>
        <!-- BOOTSTRAP CORE STYLE  -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- FONT AWESOME STYLE  -->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLE  -->
        <link href="assets/css/style.css" rel="stylesheet" />
        <!-- GOOGLE FONT -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <style>
            .widget-link {
                display: block;
                text-decoration: none;
                color: inherit;
            }

            .widget-link:hover {
                text-decoration: none;
                color: inherit;
                opacity: 0.9;
            }
        </style>
    </head>

    <body>
        <!------MENU SECTION START-->
        <?php include('includes/header.php'); ?>
        <!-- MENU SECTION END-->
        <div class="content-wrapper">
            <div class="container">
                <div class="row pad-botm">
                    <div class="col-md-12">
                        <h4 class="header-line">ADMIN DASHBOARD</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <a href="manage-books.php" class="widget-link">
                            <div class="alert alert-success back-widget-set text-center">
                                <i class="fa fa-book fa-5x"></i>
                                <?php
                                try {
                                    $sql = "SELECT id from tblbooks";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $listdbooks = $query->rowCount();
                                    echo "<h3>" . htmlentities($listdbooks) . "</h3>";
                                } catch (PDOException $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                                ?>
                                Books Listed
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <a href="reg-users.php" class="widget-link">
                            <div class="alert alert-info back-widget-set text-center">
                                <i class="fa fa-users fa-5x"></i>
                                <?php
                                try {
                                    $sql1 = "SELECT id from tblusers";
                                    $query1 = $dbh->prepare($sql1);
                                    $query1->execute();
                                    $regstds = $query1->rowCount();
                                    echo "<h3>" . htmlentities($regstds) . "</h3>";
                                } catch (PDOException $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                                ?>
                                Registered Users
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <a href="manage-authors.php" class="widget-link">
                            <div class="alert alert-warning back-widget-set text-center">
                                <i class="fa fa-user fa-5x"></i>
                                <?php
                                try {
                                    $sql2 = "SELECT id from tblauthors";
                                    $query2 = $dbh->prepare($sql2);
                                    $query2->execute();
                                    $listdathrs = $query2->rowCount();
                                    echo "<h3>" . htmlentities($listdathrs) . "</h3>";
                                } catch (PDOException $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                                ?>
                                Authors Listed
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <a href="manage-categories.php" class="widget-link">
                            <div class="alert alert-danger back-widget-set text-center">
                                <i class="fa fa-list fa-5x"></i>
                                <?php
                                try {
                                    $sql3 = "SELECT id from tblcategory";
                                    $query3 = $dbh->prepare($sql3);
                                    $query3->execute();
                                    $listdcats = $query3->rowCount();
                                    echo "<h3>" . htmlentities($listdcats) . "</h3>";
                                } catch (PDOException $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                                ?>
                                Listed Categories
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- CONTENT-WRAPPER SECTION END-->
        <?php include('includes/footer.php'); ?>
        <!-- FOOTER SECTION END-->
        <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
        <!-- CORE JQUERY  -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <!-- BOOTSTRAP SCRIPTS  -->
        <script src="assets/js/bootstrap.js"></script>
        <!-- CUSTOM SCRIPTS  -->
        <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>