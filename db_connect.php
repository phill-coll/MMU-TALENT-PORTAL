<?php
// db_connect.php
$conn = mysqli_connect("localhost", "root", "", "mmu_talent_showcase");
date_default_timezone_set('Asia/Kuala_Lumpur');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
