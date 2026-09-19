<?php
session_start();
require_once 'common/db.php';

// Already logged in? Go to admin
if (!empty($_SESSION['user_id'])) {
    header('Location: admin.php');
    exit;
}

$statusMessage = '';
$statusType    = '';

if (isset($_GET['registered'])) {
    $statusMessage = 'Account created successfully. You can now log in.';
    $statusType    = 'success';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and trim inputs
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email) || empty($password)) {
        $statusMessage = 'Please fill in all required fields.';
        $statusType    = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Prevent session fixation, then log the user in
                session_regenerate_id(true);
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                header('Location: admin.php');
                exit;
            }

            $statusMessage = 'Invalid email or password.';
            $statusType    = 'error';
        } catch (PDOException $e) {
            $statusMessage = 'Database error. Please try again later.';
            $statusType    = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Developer Portfolio - Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include_once('common/header.php')?>
  <main>
    <h1>Login</h1>
    <p>Sign in to access the admin dashboard.</p>

    <?php if (!empty($statusMessage)): ?>
        <div class="alert <?= htmlspecialchars($statusType) ?>">
            <?= htmlspecialchars($statusMessage) ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn" style="border: none; cursor: pointer; margin-top: 1rem;">Log In</button>
    </form>
    <p style="margin-top: 1rem;">Don't have an account? <a href="register.php" style="color: var(--accent-color);">Register</a></p>
  </main>
   <?php include_once('common/footer.php')?>
</body>
</html>
