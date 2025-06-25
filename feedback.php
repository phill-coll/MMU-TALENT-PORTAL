<?php 
include 'header.inc.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle feedback submission 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback'])) {
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);

    $sql = "INSERT INTO feedback (user_id, feedback_content, feedback_submission_date)
            VALUES ('$user_id', '$feedback', NOW())";

    $message = mysqli_query($conn, $sql)
        ? "<div class='success'>Thank you for your feedback!</div>"
        : "<div class='error'>Error submitting feedback.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feedback | MMU Talent Showcase</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="css/feedback.css"> 
</head>
<body>

<div class="container">
  <h2>Submit Feedback</h2>

  <?= $message ?>

  <form method="POST" onsubmit="return validateFeedback()">
    <label for="feedback">Your Feedback:</label>
    <textarea name="feedback" id="feedback" placeholder="Enter your feedback here..." required></textarea><br>
    <small id="word-count-warning" class="error" style="display:none;"></small>
    <button type="submit">Submit</button>
  </form>
</div>

<script>
// Handle validation word counter
function validateFeedback() {
  const textarea = document.getElementById('feedback');
  const warning = document.getElementById('word-count-warning');
  const words = textarea.value.trim().split(/\s+/).filter(Boolean);

  if (words.length < 10) {
    warning.textContent = "Please enter at least 10 words.";
    warning.style.display = "block";
    textarea.classList.add('invalid');
    return false;
  }

  // Clear error if passed
  warning.style.display = "none";
  textarea.classList.remove('invalid');
  return true;
}
</script>

<?php include 'footer.inc.php'; ?>
</body>
</html>
