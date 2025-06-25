<?php
include 'db_connect.php';

$email = $_POST['email'];
$answer = $_POST['answer'];
$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT security_answer FROM user WHERE user_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset Result</title>
    <link rel="stylesheet" href="css/form.css">

</head>
<body>

<div class="result-container">
<?php
if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $correct_hashed_answer = $row['security_answer'];

    if (password_verify($answer, $correct_hashed_answer)) {
        $stmt = $conn->prepare("UPDATE user SET user_password = ? WHERE user_email = ?");
        $stmt->bind_param("ss", $new_password, $email);
        $stmt->execute();

        echo "<p class='success'>Password reset successful! Redirecting to login...</p>";
        echo "<script>setTimeout(function() { window.location.href = 'login.php'; }, 3000);</script>";
    } else {
        echo "<p class='error'>Incorrect answer. <a href='forgot_password.php'>Try again</a></p>";
    }
} else {
    echo "<p class='error'>User not found. <a href='forgot_password.php'>Try again</a></p>";
}
?>
</div>

</body>
</html>