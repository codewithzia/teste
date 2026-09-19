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
$name          = '';
$email         = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and trim inputs
    $name            = trim($_POST['name'] ?? '');
    $email           = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $statusMessage = 'Please fill in all required fields.';
        $statusType    = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $statusMessage = 'Please provide a valid email address.';
        $statusType    = 'error';
    } elseif (strlen($password) < 8) {
        $statusMessage = 'Password must be at least 8 characters long.';
        $statusType    = 'error';
    } elseif ($password !== $confirmPassword) {
        $statusMessage = 'Passwords do not match.';
        $statusType    = 'error';
    } else {
        try {
            // Check if email is already registered
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);

            if ($stmt->fetch()) {
                $statusMessage = 'An account with this email already exists.';
                $statusType    = 'error';
            } else {
                // Prepared statement prevents SQL injection; password is hashed
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
                $stmt->execute([
                    ':name'     => $name,
                    ':email'    => $email,
                    ':password' => password_hash($password, PASSWORD_DEFAULT),
                ]);

                header('Location: login.php?registered=1');
                exit;
            }
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
  <title>Developer Portfolio - Register</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include_once('common/header.php')?>
  <main>
    <h1>Create an Account</h1>
    <p>Register to access the admin dashboard.</p>

    <?php if (!empty($statusMessage)): ?>
        <div class="alert <?= htmlspecialchars($statusType) ?>">
            <?= htmlspecialchars($statusMessage) ?>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
      </div>
      <button type="submit" class="btn" style="border: none; cursor: pointer; margin-top: 1rem;">Register</button>
    </form>
    <p style="margin-top: 1rem;">Already have an account? <a href="login.php" style="color: var(--accent-color);">Log in</a></p>
  </main>
   <?php include_once('common/footer.php')?>
</body>
</html>
