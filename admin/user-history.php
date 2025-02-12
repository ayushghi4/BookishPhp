<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    // Get the user ID from URL
    $userid = isset($_GET['stdid']) ? $_GET['stdid'] : '';

    if (empty($userid)) {
        $_SESSION['error'] = "Invalid User ID";
        header('location:reg-users.php');
        exit();
    }
    ?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Online Library Management System | User History</title>
        <!-- BOOTSTRAP CORE STYLE  -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- FONT AWESOME STYLE  -->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- DATATABLE STYLE  -->
        <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
        <!-- CUSTOM STYLE  -->
        <link href="assets/css/style.css" rel="stylesheet" />
        <!-- GOOGLE FONT -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <style>
            .star-rating {
                color: #ffd700;
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
                        <h4 class="header-line">User History</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- User Info -->
                        <div class="panel panel-default">
                            <div class="panel-heading">User Information</div>
                            <div class="panel-body">
                                <?php
                                $sql = "SELECT * FROM tblusers WHERE UserId = :userid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':userid', $userid, PDO::PARAM_STR);
                                $query->execute();
                                $result = $query->fetch(PDO::FETCH_OBJ);
                                if ($query->rowCount() > 0) {
                                    ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>User ID:</strong> <?php echo htmlentities($result->UserId); ?></p>
                                            <p><strong>Full Name:</strong> <?php echo htmlentities($result->FullName); ?></p>
                                            <p><strong>Email:</strong> <?php echo htmlentities($result->EmailId); ?></p>
                                            <p><strong>Mobile Number:</strong>
                                                <?php echo htmlentities($result->MobileNumber); ?></p>
                                            <p><strong>Registration Date:</strong> <?php echo htmlentities($result->RegDate); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Books History -->
                        <div class="panel panel-default">
                            <div class="panel-heading">Book Usage History</div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="book-history">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Book Name</th>
                                                <th>ISBN</th>
                                                <th>View Date</th>
                                                <th>Rating</th>
                                                <th>Review</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT b.BookName, b.ISBNNumber, 
                                                    h.ViewDate, h.Rating, h.Review
                                                    FROM tblbookhistory h
                                                    JOIN tblbooks b ON b.id = h.BookId
                                                    WHERE h.UserId = :userid 
                                                    ORDER BY h.ViewDate DESC";

                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':userid', $userid, PDO::PARAM_STR);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_ASSOC);
                                            $cnt = 1;

                                            if (!empty($results)) {
                                                foreach ($results as $result) { ?>
                                                    <tr>
                                                        <td><?php echo $cnt; ?></td>
                                                        <td><?php echo htmlentities($result['BookName']); ?></td>
                                                        <td><?php echo htmlentities($result['ISBNNumber']); ?></td>
                                                        <td><?php echo htmlentities($result['ViewDate']); ?></td>
                                                        <td>
                                                            <?php
                                                            if ($result['Rating']) {
                                                                $rating = intval($result['Rating']);
                                                                echo '<span class="star-rating">';
                                                                for ($i = 1; $i <= 5; $i++) {
                                                                    echo $i <= $rating ? '★' : '☆';
                                                                }
                                                                echo '</span>';
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $result['Review'] ? htmlentities($result['Review']) : '-'; ?>
                                                        </td>
                                                    </tr>
                                                    <?php $cnt++;
                                                }
                                            } else { ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No book history available</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
        <!-- DATATABLE SCRIPTS  -->
        <script src="assets/js/dataTables/jquery.dataTables.js"></script>
        <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
        <script>
            $(document).ready(function () {
                // Disable DataTables warning messages
                $.fn.dataTable.ext.errMode = 'none';

                var table = $('#book-history').DataTable({
                    "bSort": true,
                    "pageLength": 10,
                    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    "order": [[3, "desc"]], // Sort by view date
                    "columnDefs": [{
                        "targets": 0,
                        "orderable": false
                    }]
                });
            });
        </script>
    </body>

    </html>
<?php } ?>