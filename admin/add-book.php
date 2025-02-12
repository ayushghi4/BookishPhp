<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    // Create upload directories if they don't exist
    $upload_dirs = array('bookimg', 'epubfiles');
    foreach ($upload_dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    if (isset($_POST['add'])) {
        try {
            $bookname = $_POST['bookname'];
            $category = $_POST['category'];
            $author = $_POST['author'];
            $isbn = $_POST['isbn'];

            // Get file names
            $bookimg = $_FILES["bookpic"]["name"];
            $epubfile = $_FILES["epubfile"]["name"];

            // Basic validation
            if (empty($bookname) || empty($category) || empty($author) || empty($isbn)) {
                throw new Exception("Please fill all required fields");
            }

            if (empty($bookimg) || empty($epubfile)) {
                throw new Exception("Please upload both book cover and EPUB file");
            }

            // Validate image file
            $image_extension = strtolower(pathinfo($_FILES["bookpic"]["name"], PATHINFO_EXTENSION));
            $allowed_image_extensions = array("jpg", "jpeg", "png", "gif");
            if (!in_array($image_extension, $allowed_image_extensions)) {
                throw new Exception('Invalid image format. Only jpg/jpeg/png/gif allowed');
            }

            // Validate book file
            $book_extension = strtolower(pathinfo($_FILES["epubfile"]["name"], PATHINFO_EXTENSION));
            if ($book_extension !== "epub") {
                throw new Exception('Invalid book format. Only EPUB files allowed');
            }

            // Generate unique filenames
            $imgnewname = md5($bookimg . time()) . '.' . $image_extension;
            $epubnewname = md5($epubfile . time()) . '.' . $book_extension;

            // Create directories if they don't exist
            if (!file_exists("bookimg")) {
                mkdir("bookimg", 0777, true);
            }
            if (!file_exists("epubfiles")) {
                mkdir("epubfiles", 0777, true);
            }

            // Check if files were actually uploaded
            if ($_FILES["bookpic"]["error"] !== UPLOAD_ERR_OK) {
                throw new Exception("Error uploading book cover: " . $_FILES["bookpic"]["error"]);
            }
            if ($_FILES["epubfile"]["error"] !== UPLOAD_ERR_OK) {
                throw new Exception("Error uploading EPUB file: " . $_FILES["epubfile"]["error"]);
            }

            // Move uploaded files
            if (!move_uploaded_file($_FILES["bookpic"]["tmp_name"], "bookimg/" . $imgnewname)) {
                throw new Exception("Failed to move uploaded book image");
            }
            if (!move_uploaded_file($_FILES["epubfile"]["tmp_name"], "epubfiles/" . $epubnewname)) {
                throw new Exception("Failed to move uploaded book file");
            }

            // Insert into database
            $sql = "INSERT INTO tblbooks(BookName,CatId,AuthorId,ISBNNumber,bookImage,epub_file_path,RegDate) VALUES(:bookname,:category,:author,:isbn,:imgnewname,:epubnewname,NOW())";
            $query = $dbh->prepare($sql);
            $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
            $query->bindParam(':category', $category, PDO::PARAM_INT);
            $query->bindParam(':author', $author, PDO::PARAM_INT);
            $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
            $query->bindParam(':imgnewname', $imgnewname, PDO::PARAM_STR);
            $query->bindParam(':epubnewname', $epubnewname, PDO::PARAM_STR);

            if ($query->execute()) {
                echo "<script>alert('Book added successfully');</script>";
                echo "<script>window.location.href='manage-books.php'</script>";
            } else {
                throw new Exception("Error inserting into database");
            }
        } catch (Exception $e) {
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Add Book</title>
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
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Add Book</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">Book Info</div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Book Name<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="bookname" required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category<span style="color:red;">*</span></label>
                                        <select class="form-control" name="category" required>
                                            <option value=""> Select Category</option>
                                            <?php
                                            $sql = "SELECT * from tblcategory ORDER BY CategoryName";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) { ?>
                                                    <option value="<?php echo htmlentities($result->id); ?>">
                                                        <?php echo htmlentities($result->CategoryName); ?>
                                                    </option>
                                                <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Author<span style="color:red;">*</span></label>
                                        <select class="form-control" name="author" required>
                                            <option value=""> Select Author</option>
                                            <?php
                                            $sql = "SELECT * from tblauthors ORDER BY AuthorName";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) { ?>
                                                    <option value="<?php echo htmlentities($result->id); ?>">
                                                        <?php echo htmlentities($result->AuthorName); ?>
                                                    </option>
                                                <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ISBN Number<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="isbn" required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Book Cover Image<span style="color:red;">*</span></label>
                                        <input class="form-control" type="file" name="bookpic" accept="image/jpeg,image/jpg,image/png,image/gif" required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>EPUB File<span style="color:red;">*</span></label>
                                        <input class="form-control" type="file" name="epubfile" accept=".epub" required />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" name="add" class="btn btn-info">Add Book</button>
                                    <a href="manage-books.php" class="btn btn-default">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php');?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>