<?php
include 'header.inc.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'], $_POST['content'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $sql = "INSERT INTO forum (user_id, forum_title, forum_content, forum_post_date) VALUES ('$user_id', '$title', '$content', NOW())";
    if (mysqli_query($conn, $sql)) {
        header("Location: forum.php");
        exit();
    } else {
        $message = "Error creating thread.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Start a New Thread</title>
</head>

<body>
    <h2>Start a New Forum Thread</h2>
    <form method="POST">
        Title: <input type="text" name="title" required><br>
        Content: <textarea name="content" required></textarea><br>
        <button type="submit">Post Thread</button>
    </form>
    <p><?php echo $message ?? ''; ?></p>
    <a href="forum.php">Back to Forum</a>
</body>
<?php
include 'footer.inc.php';
?>

</html>