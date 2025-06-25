<?php
include 'header.inc.php';
include 'db_connect.php';


$message = '';

// Handle question submission
if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['question'])) {
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $sql = "INSERT INTO faq (admin_id, faq_question, faq_answer) VALUES (NULL, '$question', NULL)";
    if (mysqli_query($conn, $sql)) {
        $message = "✅ Your question has been submitted. Admin will review it.";
    } else {
        $message = "❌ Failed to submit your question.";
    }
}

// Fetch answered FAQs
$result = mysqli_query($conn, "SELECT * FROM faq WHERE faq_answer IS NOT NULL");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FAQ</title>
    <link rel="stylesheet" href="css/faq.css">
</head>
<body>
    <div class="container">
        <h2>Frequently Asked Questions</h2>
        <script src="script/faq.js"></script>

        <!-- Search Input -->
        <input type="text" id="faqSearch" class="faq-search" placeholder="Search FAQs..." onkeyup="filterFAQs()">

        <!-- FAQ Display -->
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="faq-item">
                <p class="faq-question"><b>Q:</b> <?= htmlspecialchars($row['faq_question']) ?></p>
                <p class="faq-answer"><b>A:</b> <?= htmlspecialchars($row['faq_answer']) ?></p>
                <hr>
            </div>
        <?php endwhile; ?>

        <!-- Submission Form -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <h3>Submit a New Question</h3>
            <form method="POST" class="faq-form">
                <textarea name="question" placeholder="Your question here..." required></textarea><br>
                <button type="submit">Submit Question</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Login</a> to submit a question.</p>
        <?php endif; ?>

        <!-- Message -->
        <?php if (!empty($message)): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>
    </div>

</body>

<?php include 'footer.inc.php'; ?>
</html>
