<?php
// Database configuration
$host     = 'localhost';
$dbname   = 'contact_db';
$user = 'root';        // Change to your DB username
$pass = '';            // Change to your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // For production, log the error instead of showing a message
    die('Database connection failed.');
}
