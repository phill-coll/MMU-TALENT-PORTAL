<?php
include 'header.inc.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Search functionality
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $result = mysqli_query($conn, "SELECT * FROM forum WHERE forum_title LIKE '%$search_query%' ORDER BY forum_post_date DESC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM forum ORDER BY forum_post_date DESC");
}

if (!$result) {
    die("Error loading forum threads: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>

<head>
    <title>Tenant Showcase Discussions</title>
    <link rel="stylesheet" href="css/forum.css">
</head>

<body>
    <div class="forum-container">
        <h2 class="forum-title">Tenant Showcase Discussions</h2>

        <form class="forum-search-form" method="GET">
            <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">🔍</button>
            <a href="new_thread.php" class="new-thread-btn">+</a>
        </form>

        <div class="forum-cards">
            <?php
            if (mysqli_num_rows($result) > 0) {
                $count = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $count++;
                    $forum_id = (int)$row['forum_id'];
                    $content = htmlspecialchars($row['forum_content']);
                    if (strlen($content) > 100) {
                        $content = substr($content, 0, 100) . "...";
                    }

                    $reply_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM forum_reply WHERE forum_id = $forum_id");
                    $reply_count = ($reply_result && mysqli_num_rows($reply_result) > 0) ? mysqli_fetch_assoc($reply_result)['count'] : 0;

                    $posted_time = strtotime($row['forum_post_date']);
                    $elapsed = time() - $posted_time;
                    if ($elapsed < 60) {
                        $time_display = $elapsed . " s ago";
                    } elseif ($elapsed < 3600) {
                        $time_display = floor($elapsed / 60) . " m ago";
                    } elseif ($elapsed < 86400) {
                        $time_display = floor($elapsed / 3600) . " h ago";
                    } else {
                        $time_display = floor($elapsed / 86400) . " d ago";
                    }

                    $extraClass = $count > 2 ? 'hidden-thread' : '';

                    echo "<div class='forum-card $extraClass' data-title='" . strtolower(htmlspecialchars($row['forum_title'])) . "' data-content='" . strtolower(htmlspecialchars($row['forum_content'])) . "'>";
                    echo "<h3 class='forum-card-title'>" . htmlspecialchars($row['forum_title']) . "</h3>";
                    echo "<p class='forum-card-content'>" . $content . "</p>";
                    echo "<div class='forum-card-meta'>";
                    echo "<span>💬 " . $reply_count . "</span>";
                    echo "<span>⏱ " . $time_display . "</span>";
                    echo "</div>";
                    echo "<a href='reply.php?forum_id=" . $forum_id . "' class='view-more'>View more</a>";
                    echo "</div>";
                }
            } else {
                echo "<div class='no-news'>No forum threads available at the moment.</div>";
            }
            ?>
        </div>

        <?php if ($count > 2): ?>
            <div class="view-other">
                <a href="#" id="loadMoreBtn">View other</a>
            </div>
        <?php endif; ?>

        <script src="script/forum.js"></script>
    </div>

</body>

</html>

<?php
include 'footer.inc.php';
?>