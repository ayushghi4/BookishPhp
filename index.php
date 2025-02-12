<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Home</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Latest Books</h4>
                </div>
            </div>
            <div class="row">
                <?php
                $sql = "SELECT tblbooks.BookName,tblbooks.id as bookid,tblcategory.CategoryName,tblauthors.AuthorName,tblbooks.bookImage,tblbooks.ISBNNumber 
                        FROM tblbooks 
                        JOIN tblcategory ON tblcategory.id=tblbooks.CatId 
                        JOIN tblauthors ON tblauthors.id=tblbooks.AuthorId
                        ORDER BY tblbooks.id DESC";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results=$query->fetchAll(PDO::FETCH_OBJ);
                if($query->rowCount() > 0) {
                    foreach($results as $result) { ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <?php echo htmlentities($result->CategoryName);?>
                                </div>
                                <div class="panel-body">
                                    <img src="admin/bookimg/<?php echo htmlentities($result->bookImage);?>" class="img-responsive" style="height:200px;width:100%" />
                                    <h4 style="margin-top:10px"><?php echo htmlentities($result->BookName);?></h4>
                                    <p class="text-muted">By: <?php echo htmlentities($result->AuthorName);?></p>
                                    <p><b>ISBN: </b><?php echo htmlentities($result->ISBNNumber);?></p>
                                    <?php if($_SESSION['login']) { ?>
                                        <a href="read-book.php?bookid=<?php echo htmlentities($result->bookid);?>" class="btn btn-primary">Read Book</a>
                                    <?php } else { ?>
                                        <a href="login.php" class="btn btn-danger">Login to Read</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="col-md-12">
                        <div class="alert alert-info">No books available at the moment.</div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- CONTENT-WRAPPER SECTION END-->
    <?php include('includes/footer.php');?>
    <!-- FOOTER SECTION END-->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
    <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>
</body>
</html>
