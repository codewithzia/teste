<?php
session_start();
require_once 'common/db.php';

// Restrict access to logged-in users
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch contact messages for the dashboard
$messages = [];
try {
    $messages = $pdo->query("SELECT name, email, message, created_at FROM messages ORDER BY created_at DESC")->fetchAll();
} catch (PDOException $e) {
    // Table may not exist yet; show the dashboard anyway
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Developer Portfolio - Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include_once('common/header.php')?>
  <main>
    <h1>Admin Dashboard</h1>
    <p>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>. You are logged in.</p>

    <h2 style="margin-bottom: 1rem;">Contact Messages</h2>
    <?php if (empty($messages)): ?>
        <p>No messages yet.</p>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
            <div style="background: var(--card-bg); border: 1px solid #334155; border-radius: 0.375rem; padding: 1rem; margin-bottom: 1rem;">
                <p style="margin-bottom: 0.25rem;"><strong><?= htmlspecialchars($msg['name']) ?></strong> &lt;<?= htmlspecialchars($msg['email']) ?>&gt;</p>
                <p style="margin-bottom: 0.25rem;"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                <p style="margin-bottom: 0; font-size: 0.8rem;"><?= htmlspecialchars($msg['created_at']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
  </main>
   <?php include_once('common/footer.php')?>
</body>
</html>
