<?php
include 'header.inc.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$forum_id = isset($_GET['forum_id']) ? intval($_GET['forum_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_content'])) {
    $reply_content = mysqli_real_escape_string($conn, $_POST['reply_content']);
    $admin_id = $_SESSION['admin_id'] ?? 'NULL'; // Admin or user reply
    $sql = "INSERT INTO forum_reply (forum_id, admin_id, reply_content, reply_post_date) VALUES ('$forum_id', $admin_id, '$reply_content', NOW())";
    if (mysqli_query($conn, $sql)) {
        header("Location: forum.php");
        exit();
    } else {
        $message = "Error submitting reply.";
    }
}

// Get forum post
$post = mysqli_query($conn, "SELECT * FROM forum WHERE forum_id='$forum_id'");
$thread = mysqli_fetch_assoc($post);

// Get replies
$replies = mysqli_query($conn, "SELECT * FROM forum_reply WHERE forum_id='$forum_id'");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reply to Forum</title>
</head>

<body>
    <h2><?php echo htmlspecialchars($thread['forum_title'] ?? 'Unknown Thread'); ?></h2>
    <p><?php echo nl2br(htmlspecialchars($thread['forum_content'] ?? '')); ?></p>

    <h3>Replies:</h3>
    <?php while ($row = mysqli_fetch_assoc($replies)) {
        echo "<p>" . nl2br(htmlspecialchars($row['reply_content'])) . " (Posted on " . $row['reply_post_date'] . ")</p><hr>";
    } ?>

    <h3>Add Your Reply</h3>
    <form method="POST">
        <textarea name="reply_content" required></textarea><br>
        <button type="submit">Submit Reply</button>
    </form>
    <p><?php echo $message ?? ''; ?></p>
    <a href="forum.php">Back to Forum</a>
</body>
<?php
include 'footer.inc.php';
?>

</html>