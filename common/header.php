<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <a href="index.php" class="logo">DevPortfolio</a>
    <nav>
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="skills.php">Skills</a>
      <a href="contact.php">Contact</a>
      <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="admin.php">Admin</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </header>
