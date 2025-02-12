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
    <title>Browse Books</title>
    <?php include('includes/header-styles.php');?>
    <style>
        body {
            background: #f8f9fa;
        }
        .page-container {
            padding: 30px 0;
        }
        .search-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .search-box {
            position: relative;
        }
        .search-box input {
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 15px 20px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
        }
        .search-box input:focus {
            border-color: #337ab7;
            box-shadow: 0 0 0 3px rgba(51,122,183,0.1);
            outline: none;
        }
        .search-box .fa-search {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }

        /* Filters Section */
        .filters-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .filters-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        .filter-group {
            margin-bottom: 20px;
        }
        .filter-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }
        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #eee;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
            transition: all 0.3s ease;
        }
        .filter-group select:focus {
            border-color: #337ab7;
            box-shadow: 0 0 0 3px rgba(51,122,183,0.1);
            outline: none;
        }
        .filter-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .btn-apply {
            background: #337ab7;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-apply:hover {
            background: #23527c;
        }
        .btn-clear {
            background: #f8f9fa;
            color: #666;
            border: 2px solid #eee;
            padding: 8px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-clear:hover {
            background: #eee;
        }

        /* Books Grid */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 25px;
            padding: 5px;
        }
        .book-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .book-cover-wrapper {
            position: relative;
            padding-top: 150%;  /* 3:2 Aspect Ratio */
            overflow: hidden;
        }
        .book-cover {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .book-card:hover .book-cover {
            transform: scale(1.05);
        }
        .book-info {
            padding: 15px;
            background: #fff;
        }
        .book-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin: 0 0 8px 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .book-author {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .book-category {
            font-size: 12px;
            color: #888;
            margin-bottom: 15px;
        }
        .read-btn {
            display: block;
            background: #337ab7;
            color: white;
            text-align: center;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .read-btn:hover {
            background: #23527c;
            color: white;
            text-decoration: none;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 50px 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .no-results i {
            color: #ddd;
            margin-bottom: 20px;
        }
        .no-results h3 {
            color: #333;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .no-results p {
            color: #666;
            font-size: 14px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .books-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 15px;
            }
            .book-title {
                font-size: 14px;
            }
            .book-author {
                font-size: 12px;
            }
            .search-box input {
                padding: 12px 15px;
                font-size: 14px;
            }
            .filters-section {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include('includes/header.php');?>
    <div class="page-container">
        <div class="container">
            <!-- Search Section -->
            <div class="search-section">
                <form method="GET" action="">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Search books by title, author, or ISBN..." 
                               value="<?php echo htmlentities($_GET['search'] ?? ''); ?>">
                        <i class="fa fa-search"></i>
                    </div>
                </form>
            </div>

            <div class="row">
                <!-- Filters -->
                <div class="col-md-3">
                    <div class="filters-section">
                        <h4 class="filters-title">Filters</h4>
                        <form method="GET" action="">
                            <?php if(isset($_GET['search'])) { ?>
                                <input type="hidden" name="search" value="<?php echo htmlentities($_GET['search']); ?>">
                            <?php } ?>
                            
                            <div class="filter-group">
                                <label>Categories</label>
                                <select name="category">
                                    <option value="">All Categories</option>
                                    <?php 
                                    $sql = "SELECT * FROM tblcategory ORDER BY CategoryName";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $categories = $query->fetchAll(PDO::FETCH_OBJ);
                                    if($query->rowCount() > 0) {
                                        foreach($categories as $category) {
                                            $selected = ($_GET['category'] == $category->id) ? 'selected' : '';
                                            echo "<option value='".$category->id."' ".$selected.">".$category->CategoryName."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label>Authors</label>
                                <select name="author">
                                    <option value="">All Authors</option>
                                    <?php 
                                    $sql = "SELECT * FROM tblauthors ORDER BY AuthorName";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $authors = $query->fetchAll(PDO::FETCH_OBJ);
                                    if($query->rowCount() > 0) {
                                        foreach($authors as $author) {
                                            $selected = ($_GET['author'] == $author->id) ? 'selected' : '';
                                            echo "<option value='".$author->id."' ".$selected.">".$author->AuthorName."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="filter-actions">
                                <button type="submit" class="btn btn-apply">Apply Filters</button>
                                <?php if(!empty($_GET)) { ?>
                                    <a href="books.php" class="btn btn-clear">Clear Filters</a>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Books Grid -->
                <div class="col-md-9">
                    <div class="books-grid">
                    <?php
                    $conditions = array();
                    $params = array();

                    $sql = "SELECT tblbooks.*, tblcategory.CategoryName, tblauthors.AuthorName 
                           FROM tblbooks 
                           LEFT JOIN tblcategory ON tblcategory.id=tblbooks.CatId 
                           LEFT JOIN tblauthors ON tblauthors.id=tblbooks.AuthorId 
                           WHERE 1=1";

                    if(isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = $_GET['search'];
                        $conditions[] = "(BookName LIKE :search OR ISBNNumber LIKE :search OR tblauthors.AuthorName LIKE :search)";
                        $params[':search'] = "%$search%";
                    }

                    if(isset($_GET['category']) && !empty($_GET['category'])) {
                        $conditions[] = "CatId = :category";
                        $params[':category'] = $_GET['category'];
                    }

                    if(isset($_GET['author']) && !empty($_GET['author'])) {
                        $conditions[] = "AuthorId = :author";
                        $params[':author'] = $_GET['author'];
                    }

                    if(!empty($conditions)) {
                        $sql .= " AND " . implode(" AND ", $conditions);
                    }

                    $sql .= " ORDER BY BookName";
                    $query = $dbh->prepare($sql);
                    $query->execute($params);
                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                    if($query->rowCount() > 0) {
                        foreach($results as $result) { ?>
                            <div class="book-card">
                                <div class="book-cover-wrapper">
                                    <img src="admin/bookimg/<?php echo htmlentities($result->bookImage);?>" 
                                         alt="<?php echo htmlentities($result->BookName);?>"
                                         class="book-cover">
                                </div>
                                <div class="book-info">
                                    <h3 class="book-title"><?php echo htmlentities($result->BookName);?></h3>
                                    <p class="book-author">by <?php echo htmlentities($result->AuthorName);?></p>
                                    <p class="book-category"><?php echo htmlentities($result->CategoryName);?></p>
                                    <a href="read-book.php?bookid=<?php echo htmlentities($result->id);?>" class="read-btn">
                                        Read Book
                                    </a>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="no-results">
                            <i class="fa fa-search fa-3x"></i>
                            <h3>No Books Found</h3>
                            <p>Try adjusting your search or filter criteria</p>
                        </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php');?>
    <script>
        // Add smooth transitions when applying filters
        document.querySelectorAll('.filter-group select').forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    </script>
</body>
</html>
