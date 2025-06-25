<?php
include 'header.inc.php';
include 'db_connect.php';

$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $result = mysqli_query($conn, "SELECT news.*, admin.admin_username FROM news JOIN admin ON news.admin_id = admin.admin_id WHERE news_title LIKE '%$search_query%' ORDER BY news_publish_date DESC");
} else {
    $result = mysqli_query($conn, "SELECT news.*, admin.admin_username FROM news JOIN admin ON news.admin_id = admin.admin_id ORDER BY news_publish_date DESC");
}

if (!$result) {
    die("Error loading news threads: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>News & Announcements</title>
    <link rel="stylesheet" href="css/news.css">
</head>

<body>
    <div class="header">
        <h1>News & Announcements</h1>
        <form class="search-container" method="GET">
            <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">🔍</button>
        </form>
    </div>

    <div class="news-grid" id="newsGrid">
        <?php
        if (mysqli_num_rows($result) > 0) {
            $count = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $count++;
                $date = date('d/m/Y', strtotime($row['news_publish_date']));
                $content = htmlspecialchars($row['news_content']);

                $hiddenClass = ($count > 2) ? "hidden-announcement" : "";

                echo "<div class='news-card $hiddenClass'>";
                echo "<div class='news-header'>";
                echo "<div class='news-date'>" . $date . "</div>";
                echo "<div class='news-title'>" . htmlspecialchars($row['news_title']) . "</div>";
                echo "</div>";

                echo "<div class='news-content'>" . nl2br($content) . "</div>";
                echo "<div class='news-meta'>By: " . htmlspecialchars($row['admin_username']) . "</div>";
                echo "</div>";
            }
        } else {
            echo "<div class='no-news'>No announcements available at the moment.</div>";
        }

        if ($count > 2) {
            echo "<div class='view-more'><a href='#' id='viewMoreBtn'>View More</a></div>";
        }
        ?>
    </div>

    <script src="script/news.js"></script>
</body>

<?php
include 'footer.inc.php';
?>

</html>