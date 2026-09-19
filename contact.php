 <?php

 require_once 'common/db.php';

$statusMessage = '';
$statusType    = '';


 // Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and trim inputs
    $name    = trim($_POST['name'] ?? '');
    $email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        $statusMessage = 'Please fill in all required fields.';
        $statusType    = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $statusMessage = 'Please provide a valid email address.';
        $statusType    = 'error';
    } else {
        try {
            // Prepared statement prevents SQL injection
            $sql  = "INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':name'    => $name,
                ':email'   => $email,
                ':message' => $message,
            ]);

            $statusMessage = 'Thank you! Your message has been saved successfully.';
            $statusType    = 'success';

            // Clear inputs on success
            $name = $email = $message = '';

        } catch (PDOException $e) {
            // For production, log the error instead of showing raw $e->getMessage()
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
  <title>Developer Portfolio - Contact</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include_once('common/header.php')?>
  <main>
    <h1>Get In Touch</h1>
    <p>Have a project in mind or want to collaborate? Fill out the form below or drop me an email.</p>

    <?php if (!empty($statusMessage)): ?>
        <div class="alert <?= htmlspecialchars($statusType) ?>">
            <?= htmlspecialchars($statusMessage) ?>
        </div>
    <?php endif; ?>
    
    <form action="#" method="POST">
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" rows="5" required></textarea>
      </div>
      <button type="submit" class="btn" style="border: none; cursor: pointer; margin-top: 1rem;">Send Message</button>
    </form>
  </main>
   <?php include_once('common/footer.php')?>
</body>
</html>