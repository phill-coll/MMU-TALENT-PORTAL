<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/form.css">
</head>
<body>
<div class="form-container">
    <h2>Forgot Password</h2>
    <form action="verify_security_question.php" method="POST">
        <label for="email">Enter your registered email:</label>
        <input type="email" name="email" required>
        <button type="submit">Next</button>
    </form>
    <p><a href="login.php">Back to login</a></p>
</div>
</body>
</html>