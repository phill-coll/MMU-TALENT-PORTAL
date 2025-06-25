<?php
session_start();

// Clear cookies
setcookie("admin_name", "", time() - 3600, "/");
setcookie("user_name", "", time() - 3600, "/");

// Destroy session
session_unset();
session_destroy();

// Redirect
header("Location: index.php");
exit();
