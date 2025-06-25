<?php
include 'header.inc.php';
include 'db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember_me']);

    // Check if email exists in user table
    $stmt = $conn->prepare("SELECT * FROM user WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result && $user_result->num_rows === 1) {
        $user = $user_result->fetch_assoc();

        // Use password_verify instead of MD5
        if (password_verify($password, $user['user_password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_username'] = $user['user_username'];

            if ($remember) {
                setcookie("user_name", $user['user_username'], time() + (86400 * 7), "/");
            }

            header("Location: index.php");
            exit();
        }
    }

    // Admin login using MD5
    $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_email = ? AND admin_password = MD5(?)");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $admin_result = $stmt->get_result();

    if ($admin_result && $admin_result->num_rows === 1) {
        $admin = $admin_result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['admin_username'];

        if ($remember) {
            setcookie("admin_name", $admin['admin_username'], time() + (86400 * 7), "/");
        }

        header("Location: admin/dashboard.php");
        exit();
    }

    $message = "Invalid email or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - MMU Talent Showcase</title>
    <link rel="stylesheet" href="css/form.css">

</head>
<body>

<div class="form-container">
    <h2>Login MMU Talent Showcase</h2>

    <form method="POST" action="">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>

        <div class="password-label-row">
            <label for="password">Password:</label>
            <a href="forgot_password.php" class="forgot-link">Forgot password?</a>
        </div>
        <input type="password" name="password" id="password" required>
        <label class="checkbox-show">
            <input type="checkbox" onclick="togglePassword()"> Show Password
        </label><br>

        <label><input type="checkbox" name="remember_me"> Remember Me</label><br><br>

        <button type="submit">Login</button>
    </form>

    <p class="footer-note">Not yet a user? <a href="registration.php">Sign up</a></p>

    <?php if (!empty($message)): ?>
        <div class="login-error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
</div>

<script>
function togglePassword() {
    const pw = document.getElementById("password");
    pw.type = pw.type === "password" ? "text" : "password";
}
</script>

<?php include 'footer.inc.php'; ?>
</body>
</html>
