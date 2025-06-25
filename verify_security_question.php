<?php
include 'db_connect.php';

$email = $_POST['email'];

$stmt = $conn->prepare("SELECT security_question FROM user WHERE user_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $question = $row['security_question'];
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Answer Security Question</title>
        <link rel="stylesheet" href="css/form.css">
    </head>
    <body>
    <div class="form-container">
        <h2>Security Question</h2>
        <form action="reset_with_answer.php" method="POST">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <label><?= htmlspecialchars($question) ?></label>
            <input type="text" name="answer" required>
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
    </body>
    </html>
    <?php
} else {
    echo "<p>Email not found. <a href='forgot_password.php'>Try again</a></p>";
}
?>