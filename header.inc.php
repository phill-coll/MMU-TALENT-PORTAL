<?php
session_start();
$basePath = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html>

<head>

  <link rel="stylesheet" href="<?php echo $basePath; ?>css/header.css">
</head>

<header>
  <div class="logo-box">
    <a href="https://www.mmu.edu.my/" target="_blank" class="mmu-brand">
      <img src="https://www.mmu.edu.my/wp-content/themes/mmu2018/assets/images/logo-mmu2x.png" alt="MMU Logo">
    </a>
    <?php
    $portalLink = 'index.php';
    if (isset($_SESSION['admin_id'])) {
      $portalLink = 'admin/dashboard.php';
    }
    ?>
    <a href="<?php echo $basePath . $portalLink; ?>" class="mmu-brand">
      <div class="title-text">MMU TALENT SHOWCASE PORTAL</div>
    </a>

  </div>


  <nav>
    <?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])): ?>
      <a href="<?php echo $basePath; ?>login.php">Login</a>
      <a href="<?php echo $basePath; ?>registration.php">Register</a>
      <a href="<?php echo $basePath; ?>portfolio.php">Resources</a>
      <a href="<?php echo $basePath; ?>faq.php">FAQ</a>
      <a href="<?php echo $basePath; ?>news.php">News</a>
      <a href="<?php echo $basePath; ?>about_us.php">About Us</a>
    <?php elseif (isset($_SESSION['user_id'])): ?>
      <a href="<?php echo $basePath; ?>index.php">Home</a>
      <a href="<?php echo $basePath; ?>profile.php">My Profile</a>
      <a href="<?php echo $basePath; ?>cart.php">Shopping Cart</a>
      <a href="<?php echo $basePath; ?>feedback.php">Feedback</a>
      <a href="<?php echo $basePath; ?>forum.php">Forum</a>
      <a href="<?php echo $basePath; ?>faq.php">FAQ</a>
      <a href="<?php echo $basePath; ?>news.php">News</a>
      <a href="<?php echo $basePath; ?>portfolio.php">Portfolio</a>
      <a href="<?php echo $basePath; ?>logout.php">Logout</a>
    <?php elseif (isset($_SESSION['admin_id'])): ?>
      <a href="dashboard.php">Dashboard</a>
      <a href="forum_manage.php">Manage Forum</a>
      <a href="faq_manage.php">Manage FAQ</a>
      <a href="news_manage.php">Manage News</a>
      <a href="catalogue_manage.php">Manage Catalogue</a>
      <a href="reports.php">Reports</a>
      <a href="<?php echo $basePath; ?>logout.php">Logout</a>
    <?php endif; ?>
  </nav>

</header>
<div class="container">