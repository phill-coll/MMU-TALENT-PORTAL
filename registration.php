<?php
include 'header.inc.php';
include 'db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $question = $_POST['security_question'];
    $answer = $_POST['security_answer'];

    if (empty($username) || empty($email) || empty($password) || empty($question) || empty($answer)) {
        $message = "All fields are required.";
    } else {
        // Check for existing email
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE user_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $hashed_answer = password_hash($answer, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO user (user_username, user_email, user_password, security_question, security_answer) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $hashed_password, $question, $hashed_answer);

            if ($stmt->execute()) {
                $message = "Registration successful! <a href='login.php'>Login now</a>";
            } else {
                $message = "Error during registration. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MMU Talent Showcase</title>
    <link rel="stylesheet" href="css/form.css">

</head>
<body>
<div class="form-container">
    <h2>Register</h2>

    <form method="POST" action="">
        <label for="username">Full Name:</label>
        <input type="text" name="username" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <label for="security_question">Security Question:</label>
        <select name="security_question" required>
            <option value="">-- Select a question --</option>
            <option value="What is your favourite pet?">What is your favourite pet?</option>
            <option value="What is your favourite country?">What is your favourite country?</option>
            <option value="What is your high school best friend's name?">What is your high school best friend's name?</option>
        </select>

        <label for="security_answer">Your Answer:</label>
        <input type="text" name="security_answer" required>

        <button type="submit">Register</button>
    </form>

    <?php if (!empty($message)): ?>
        <div class="message <?= str_contains($message, 'success') ? 'success' : '' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <p style="text-align:center; margin-top: 15px;">Already have an account? <a href="login.php">Login</a></p>
</div>

<?php include 'footer.inc.php'; ?>
</body>
</html>
