<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $portfolio_id = intval($_GET['id']);

    // Fetch the portfolio BLOB
    $result = mysqli_query($conn, "SELECT portfolio_file, portfolio_title FROM portfolio WHERE portfolio_id = $portfolio_id");
    if ($row = mysqli_fetch_assoc($result)) {
        $fileData = $row['portfolio_file'];
        $fileName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $row['portfolio_title']) . ".pdf";

        // Output headers to force download
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Content-Length: " . strlen($fileData));
        echo $fileData;
        exit;
    } else {
        echo "Portfolio file not found.";
    }
} else {
    echo "Invalid request.";
}
?>
