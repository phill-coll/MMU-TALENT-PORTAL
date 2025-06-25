<?php 
include 'header.inc.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = "";

// Fetch user and profile data
$userData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE user_id = $userId"));
$profileResult = mysqli_query($conn, "SELECT * FROM profile WHERE user_id = $userId");
$profile = ($profileResult && mysqli_num_rows($profileResult) > 0) ? mysqli_fetch_assoc($profileResult) : [];

// Handle profile photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    if ($_FILES['photo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['photo']['size'] <= 500000) {
            $photo_data = addslashes(file_get_contents($_FILES['photo']['tmp_name']));
            $check = mysqli_query($conn, "SELECT * FROM profile WHERE user_id='$userId'");
            if (mysqli_num_rows($check) > 0) {
                $sql = "UPDATE profile SET user_profile_photo='$photo_data' WHERE user_id='$userId'";
            } else {
                $sql = "INSERT INTO profile (user_id, user_profile_photo) VALUES ('$userId', '$photo_data')";
            }
            $message .= mysqli_query($conn, $sql) ? "<div class='success'>Profile picture updated.</div>" : "<div class='error'>Error uploading photo.</div>";
        } else {
            $message .= "<div class='error'>Invalid file type or size too large.</div>";
        }
    }
}

// Handle portfolio upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['portfolio_title'])) {
    if (isset($_FILES['portfolio_file']) && $_FILES['portfolio_file']['error'] === 0) {
        $allowed = ['pdf'];
        $ext = strtolower(pathinfo($_FILES['portfolio_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['portfolio_file']['size'] <= 5000000) {
            $title = mysqli_real_escape_string($conn, $_POST['portfolio_title']);
            $desc = mysqli_real_escape_string($conn, $_POST['portfolio_description']);
            $file_data = addslashes(file_get_contents($_FILES['portfolio_file']['tmp_name']));

            $check = mysqli_query($conn, "SELECT * FROM portfolio WHERE user_id='$userId'");
            if (mysqli_num_rows($check) > 0) {
                $sql = "UPDATE portfolio SET portfolio_title='$title', portfolio_description='$desc', portfolio_file='$file_data' WHERE user_id='$userId'";
            } else {
                $sql = "INSERT INTO portfolio (user_id, portfolio_title, portfolio_description, portfolio_file) VALUES ('$userId', '$title', '$desc', '$file_data')";
            }

            $message .= mysqli_query($conn, $sql)
                ? "<div class='success'>Portfolio uploaded successfully.</div>"
                : "<div class='error'>Error uploading portfolio.</div>";
        } else {
            $message .= "<div class='error'>Invalid portfolio file or too large.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile | MMU Talent Showcase</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="css/profile.css">
</head>

<body>
<div class="container profile">
    <?= $message ?>

    <!-- Profile Picture Form -->
    <form method="POST" enctype="multipart/form-data">
        <div class="profile-pic">
            <?php if (!empty($profile['user_profile_photo'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($profile['user_profile_photo']) ?>" class="pic" alt="Profile Picture">
            <?php else: ?>
                <img src="images/default-pic.png" class="pic" alt="Default Picture">
            <?php endif; ?>
            <label for="photo" class="edit-icon">
                <img src="images/pencil-icon.png" alt="Edit Icon">
            </label>
            <input type="file" name="photo" id="photo" accept=".jpg,.jpeg,.png" style="display:none;" onchange="this.form.submit()">
        </div>
    </form>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-label">Username</div>
        <div class="info-value"><?= htmlspecialchars($userData['user_username']) ?></div>

        <div class="info-label">Email</div>
        <div class="info-value"><?= htmlspecialchars($userData['user_email']) ?></div>

        <div class="info-label">Password</div>
        <div class="info-value">********</div>

        <!-- Modal Trigger -->
        <div class="btn-block">
            <button type="button" class="upload-btn" onclick="document.getElementById('portfolioModal').style.display='block'">
                Upload Portfolio
            </button>
            <a href="logout.php"><button class="logout-btn">Logout</button></a>
        </div>
    </div>
</div>

<!-- Modal Popup Form -->
<div id="portfolioModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('portfolioModal').style.display='none'">&times;</span>
    <form method="POST" enctype="multipart/form-data">
        <h3>Upload Portfolio</h3>
        <input type="text" name="portfolio_title" placeholder="Portfolio Title" required><br>
        <textarea name="portfolio_description" placeholder="Portfolio Description" required></textarea><br>
        <input type="file" name="portfolio_file" accept=".pdf" required><br><br>
        <button type="submit">Save Portfolio</button>
    </form>
  </div>
</div>

<script>
    // Close modal when clicking outside content
    window.onclick = function(event) {
        const modal = document.getElementById('portfolioModal');
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>

<script>
  const modal = document.getElementById("portfolioModal");
  const modalContent = modal.querySelector(".modal-content");

  let isDragging = false;
  let offsetX, offsetY;

  modalContent.style.position = "absolute"; // Make it moveable
  modalContent.style.cursor = "move";

  modalContent.addEventListener("mousedown", function(e) {
    isDragging = true;
    offsetX = e.clientX - modalContent.getBoundingClientRect().left;
    offsetY = e.clientY - modalContent.getBoundingClientRect().top;
    document.body.style.userSelect = "none";
  });

  document.addEventListener("mousemove", function(e) {
    if (isDragging) {
      const x = e.clientX - offsetX;
      const y = Math.max(e.clientY - offsetY, 60); // prevent overlap with header
      modalContent.style.left = `${x}px`;
      modalContent.style.top = `${y}px`;
    }
  });

  document.addEventListener("mouseup", function() {
    isDragging = false;
    document.body.style.userSelect = "";
  });

  // Reset modal position when opened
  function resetModalPosition() {
    modalContent.style.left = "0";
    modalContent.style.top = "0";
  }

  document.querySelector(".upload-btn").addEventListener("click", () => {
    resetModalPosition();
    modal.style.display = "block";
  });
</script>


<?php include 'footer.inc.php'; ?>
</body>
</html>
