<?php
include 'header.inc.php';
include 'db_connect.php';

$user_logged_in = isset($_SESSION['user_id']);
$logged_in_user = $user_logged_in ? $_SESSION['user_id'] : null;

// Handle rating submission
if ($user_logged_in && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['portfolio_id'], $_POST['rating_value'], $_POST['rating_comment'])) {
    $portfolio_id = $_POST['portfolio_id'];
    $rating_value = intval($_POST['rating_value']);
    $rating_comment = mysqli_real_escape_string($conn, $_POST['rating_comment']);

    $check = mysqli_query($conn, "SELECT * FROM rating WHERE portfolio_id='$portfolio_id' AND user_id='$logged_in_user'");
    if (mysqli_num_rows($check) == 0) {
        $check_owner = mysqli_query($conn, "SELECT user_id FROM portfolio WHERE portfolio_id='$portfolio_id'");
        $owner = mysqli_fetch_assoc($check_owner)['user_id'];
        if ($owner != $logged_in_user) {
            $sql = "INSERT INTO rating (portfolio_id, user_id, rating_value, rating_comment, rating_date)
                    VALUES ('$portfolio_id', '$logged_in_user', '$rating_value', '$rating_comment', CURDATE())";
            mysqli_query($conn, $sql);
        } else {
            echo "<p style='color:red;'>You cannot rate your own portfolio.</p>";
        }
    } else {
        echo "<p style='color:red;'>You have already rated this portfolio.</p>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Portfolio Gallery</title>
    <link rel="stylesheet" href="css/portfolio.css">
</head>

<body>
    <div class="container">
        <h2>Portfolio Gallery</h2>

        <?php
        $result = mysqli_query($conn, "SELECT portfolio.*, user.user_username, profile.user_profile_photo 
                                   FROM portfolio 
                                   JOIN user ON portfolio.user_id = user.user_id 
                                   LEFT JOIN profile ON user.user_id = profile.user_id 
                                   ORDER BY portfolio_date DESC");

        while ($row = mysqli_fetch_assoc($result)) {
            $portfolio_id = $row['portfolio_id'];
            echo "<div class='portfolio-card'>";

            // Avatar
            if (!empty($row['user_profile_photo'])) {
                $base64 = base64_encode($row['user_profile_photo']);
                $photo = "data:image/png;base64,$base64";
            } else {
                $photo = 'images/default-pic.png';
            }

            echo "<div class='portfolio-avatar'><img src='$photo' alt='User Avatar'></div>";
            echo "<div class='portfolio-content'>";
            echo "<h3 class='portfolio-title'>" . htmlspecialchars($row['portfolio_title']) . "</h3>";
            echo "<p class='portfolio-description'>" . nl2br(htmlspecialchars($row['portfolio_description'])) . "</p>";
            echo "<p class='portfolio-quote'><a href='download_portfolio.php?id=$portfolio_id' target='_blank'>Download Portfolio</a></p>";
            echo "<p class='portfolio-by'>By: " . htmlspecialchars($row['user_username']) . " | Date: " . $row['portfolio_date'] . "</p>";

            // Rating
            if ($user_logged_in && $row['user_id'] != $logged_in_user) {
                $check = mysqli_query($conn, "SELECT * FROM rating WHERE portfolio_id='$portfolio_id' AND user_id='$logged_in_user'");
                if (mysqli_num_rows($check) == 0) {
                    echo "<form method='POST' class='rating-form'>
                    <input type='hidden' name='portfolio_id' value='$portfolio_id'>
                    <div class='stars'>
                        <input type='radio' id='star5_$portfolio_id' name='rating_value' value='5'><label for='star5_$portfolio_id'>★</label>
                        <input type='radio' id='star4_$portfolio_id' name='rating_value' value='4'><label for='star4_$portfolio_id'>★</label>
                        <input type='radio' id='star3_$portfolio_id' name='rating_value' value='3'><label for='star3_$portfolio_id'>★</label>
                        <input type='radio' id='star2_$portfolio_id' name='rating_value' value='2'><label for='star2_$portfolio_id'>★</label>
                        <input type='radio' id='star1_$portfolio_id' name='rating_value' value='1'><label for='star1_$portfolio_id'>★</label>
                    </div>
                    <input type='text' name='rating_comment' placeholder='Add a comment'>
                    <div class='button-group'>
                        <button type='reset' class='discard'>DISCARD</button>
                        <button type='submit' class='rate'>RATE</button>
                    </div>
                </form>";
                } else {
                    echo "<p class='already-rated'>You have already rated this portfolio.</p>";
                }
            } elseif ($user_logged_in && $row['user_id'] == $logged_in_user) {
                echo "<p class='own-portfolio'>You cannot rate your own portfolio.</p>";
            }

            // Display existing ratings
            $ratings = mysqli_query($conn, "SELECT rating.*, user.user_username 
                                        FROM rating 
                                        JOIN user ON rating.user_id = user.user_id 
                                        WHERE portfolio_id='$portfolio_id'");
            while ($rate = mysqli_fetch_assoc($ratings)) {
                echo "<p class='rating-display'><b>{$rate['user_username']}</b> rated {$rate['rating_value']} - " . htmlspecialchars($rate['rating_comment']) . " on {$rate['rating_date']}</p>";
            }

            echo "</div></div><hr>";
        }

        if (!$user_logged_in) {
            echo '<p style="color:blue;">Please <a href="login.php">login</a> to upload new resources or rating portfolio.</p>';
        }

        ?>
    </div>

    <?php include 'footer.inc.php'; ?>
</body>

</html>