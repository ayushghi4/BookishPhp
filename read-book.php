<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['login'])) {
    header('location:index.php');
    exit();
}

if (!isset($_GET['bookid'])) {
    header('location:browse-books.php');
    exit();
}

include('includes/config.php');

$bookid = intval($_GET['bookid']);

// Fetch book details
$sql = "SELECT tblbooks.BookName, tblbooks.bookImage, tblbooks.epub_file_path, tblcategory.CategoryName, tblauthors.AuthorName,
        tblbooks.ISBNNumber
        FROM tblbooks 
        JOIN tblcategory ON tblcategory.id=tblbooks.CatId 
        JOIN tblauthors ON tblauthors.id=tblbooks.AuthorId 
        WHERE tblbooks.id=:bookid";

$query = $dbh->prepare($sql);
$query->bindParam(':bookid', $bookid, PDO::PARAM_INT);
$query->execute();
$book = $query->fetch(PDO::FETCH_OBJ);

if (!$book) {
    header('location:browse-books.php');
    exit();
}

// Record reading history
$reader = $_SESSION['login'];
$sql = "INSERT INTO tblreadinghistory(BookId, ReaderId, ReadingDate) VALUES(:bookid, :readerid, NOW())";
$query = $dbh->prepare($sql);
$query->bindParam(':bookid', $bookid, PDO::PARAM_INT);
$query->bindParam(':readerid', $reader, PDO::PARAM_STR);
$query->execute();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Online Library Management System | <?php echo htmlentities($book->BookName);?></title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- EPUB.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/epubjs/dist/epub.min.js"></script>
    <style>
        body.reading-mode {
            background: #1a1a1a;
            color: #fff;
        }
        .book-container {
            background: #fff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            margin-bottom: 30px;
            position: relative;
        }
        #viewer {
            height: 70vh;
            margin: 20px 0;
            background: #fff;
            padding: 20px 40px;
            position: relative;
            transition: all 0.3s ease;
            font-family: "Georgia", serif;
            line-height: 1.6;
            font-size: 18px;
            color: #333;
        }
        #viewer.fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw !important;
            height: 100vh !important;
            margin: 0;
            padding: 60px;
            z-index: 9999;
            background: #fcf6e9;  /* Apple Books like cream color */
            overflow-y: auto;
        }
        .reader-controls {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            padding: 10px 20px;
            border-radius: 25px;
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 15px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .fullscreen .reader-controls {
            opacity: 0.9;
        }
        #viewer:hover + .reader-controls,
        .reader-controls:hover {
            opacity: 0.9;
        }
        .reader-controls button {
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px 10px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }
        .reader-controls button:hover {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        .page-info {
            color: white;
            font-size: 14px;
            margin: 0 15px;
            min-width: 100px;
            text-align: center;
        }
        .fullscreen-btn {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 10001;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .fullscreen-btn:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.9);
        }
        .fullscreen-btn i {
            font-size: 16px;
        }
        .fullscreen-btn::after {
            content: "Fullscreen";
            display: inline-block;
        }
        /* Hide regular controls when in fullscreen */
        .fullscreen .controls {
            display: none;
        }
        /* Reading progress bar */
        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: rgba(0, 0, 0, 0.1);
            z-index: 10001;
        }
        .progress-bar .progress {
            height: 100%;
            background: #337ab7;
            width: 0;
            transition: width 0.3s ease;
        }
        /* Theme switcher */
        .theme-switch {
            position: fixed;
            top: 20px;
            right: 70px;
            z-index: 10001;
            display: none;
        }
        .fullscreen ~ .theme-switch {
            display: block;
        }
        .theme-switch button {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            padding: 8px;
            border-radius: 5px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s;
            margin-left: 5px;
        }
        .theme-switch button:hover {
            opacity: 1;
        }
        /* Themes */
        #viewer.theme-light {
            background: #fcf6e9;
            color: #333;
        }
        #viewer.theme-sepia {
            background: #f4ecd8;
            color: #5b4636;
        }
        #viewer.theme-dark {
            background: #1a1a1a;
            color: #dedede;
        }
        .book-header {
            border-bottom: 2px solid #f5f5f5;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .book-title {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .book-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .book-meta i {
            width: 20px;
            color: #337ab7;
        }
        .book-cover {
            max-width: 200px;
            max-height: 300px;
            margin: 0 auto 20px;
            display: block;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 4px;
            object-fit: contain;
            background: #fff;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php');?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Reading: <?php echo htmlentities($book->BookName);?></h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="book-container">
                        <div class="book-header">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <?php if($book->bookImage): ?>
                                        <img src="admin/bookimg/<?php echo htmlentities($book->bookImage);?>" 
                                             alt="<?php echo htmlentities($book->BookName);?> Cover"
                                             class="book-cover img-responsive">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-9">
                                    <h2 class="book-title"><?php echo htmlentities($book->BookName);?></h2>
                                    <div class="book-meta">
                                        <p><i class="fa fa-user"></i> <strong>Author:</strong> <?php echo htmlentities($book->AuthorName);?></p>
                                        <p><i class="fa fa-list"></i> <strong>Category:</strong> <?php echo htmlentities($book->CategoryName);?></p>
                                        <p><i class="fa fa-barcode"></i> <strong>ISBN:</strong> <?php echo htmlentities($book->ISBNNumber);?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="progress-bar">
                            <div class="progress"></div>
                        </div>
                        <div id="viewer">
                            <button class="fullscreen-btn" onclick="toggleFullscreen()" title="Toggle Fullscreen (Press 'F')">
                                <i class="fa fa-expand"></i>
                            </button>
                        </div>
                        <div class="theme-switch">
                            <button onclick="setTheme('light')" title="Light theme">
                                <i class="fa fa-sun-o"></i>
                            </button>
                            <button onclick="setTheme('sepia')" title="Sepia theme">
                                <i class="fa fa-book"></i>
                            </button>
                            <button onclick="setTheme('dark')" title="Dark theme">
                                <i class="fa fa-moon-o"></i>
                            </button>
                        </div>
                        <div class="reader-controls" style="display: none;">
                            <button onclick="prevPage()">
                                <i class="fa fa-chevron-left"></i>
                                Previous
                            </button>
                            <span class="page-info">Page <span id="current-page">0</span> of <span id="total-pages">0</span></span>
                            <button onclick="nextPage()">
                                Next
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        var book = ePub("admin/epubfiles/<?php echo htmlentities($book->epub_file_path);?>");
        var rendition = book.renderTo("viewer", {
            width: "100%",
            height: "100%",
            spread: "none",
            flow: "paginated",
            minSpreadWidth: 800
        });

        var displayed = rendition.display();
        var currentPage = 0;
        var totalPages = 0;

        function setTheme(theme) {
            var viewer = document.getElementById('viewer');
            viewer.classList.remove('theme-light', 'theme-sepia', 'theme-dark');
            viewer.classList.add('theme-' + theme);
            
            // Save theme preference
            localStorage.setItem('reader-theme', theme);
        }

        // Load saved theme
        var savedTheme = localStorage.getItem('reader-theme') || 'light';
        setTheme(savedTheme);

        function toggleFullscreen() {
            var viewer = document.getElementById('viewer');
            var controls = document.querySelector('.reader-controls');
            
            if (!viewer.classList.contains('fullscreen')) {
                viewer.classList.add('fullscreen');
                controls.style.display = 'flex';
                document.body.classList.add('reading-mode');
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                }
            } else {
                viewer.classList.remove('fullscreen');
                controls.style.display = 'none';
                document.body.classList.remove('reading-mode');
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Update progress bar
        function updateProgress(location) {
            var progress = (location.start.percentage * 100).toFixed(2);
            document.querySelector('.progress-bar .progress').style.width = progress + '%';
        }

        rendition.on('relocated', function(location) {
            var currentPage = location.start.displayed.page;
            var totalPages = location.start.displayed.total;
            
            document.getElementById('current-page').textContent = currentPage;
            document.getElementById('total-pages').textContent = totalPages;
            
            updateProgress(location);
        });

        // Navigation functions
        function prevPage() {
            rendition.prev();
        }

        function nextPage() {
            rendition.next();
        }

        // Keyboard navigation
        document.addEventListener('keyup', function(e) {
            if (e.key === 'ArrowLeft') prevPage();
            if (e.key === 'ArrowRight') nextPage();
            if (e.key === 'f') toggleFullscreen();
            if (e.key === 'Escape' && document.getElementById('viewer').classList.contains('fullscreen')) {
                toggleFullscreen();
            }
        });

        // Touch events for mobile
        var touchStart = null;
        document.getElementById('viewer').addEventListener('touchstart', function(e) {
            touchStart = e.touches[0].pageX;
        });

        document.getElementById('viewer').addEventListener('touchend', function(e) {
            if (!touchStart) return;
            var touchEnd = e.changedTouches[0].pageX;
            var diff = touchStart - touchEnd;

            if (Math.abs(diff) < 50) return; // Ignore small swipes
            if (diff > 0) nextPage();
            else prevPage();
            
            touchStart = null;
        });

        // Auto-hide controls
        var controlsTimeout;
        document.addEventListener('mousemove', function() {
            var controls = document.querySelector('.reader-controls');
            var fullscreenBtn = document.querySelector('.fullscreen-btn');
            var themeSwitch = document.querySelector('.theme-switch');
            
            if (controls) controls.style.opacity = '0.9';
            if (fullscreenBtn) fullscreenBtn.style.opacity = '0.7';
            if (themeSwitch) themeSwitch.style.opacity = '0.7';
            
            clearTimeout(controlsTimeout);
            controlsTimeout = setTimeout(function() {
                if (controls) controls.style.opacity = '0';
                if (fullscreenBtn) fullscreenBtn.style.opacity = '0';
                if (themeSwitch) themeSwitch.style.opacity = '0';
            }, 2000);
        });
    </script>

    <?php include('includes/footer.php');?>
    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>