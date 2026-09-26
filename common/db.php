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

// Run a prepared statement with optional bound params and return the statement
function db_query(string $sql, array $params = []): PDOStatement
{
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// Project CRUD methods

// Create
function create_project(string $title, string $description): void
{
    db_query(
        "INSERT INTO projects (title, description) VALUES (:title, :description)",
        [':title' => $title, ':description' => $description]
    );
}


// Read: all projects
function get_projects(): array
{
    return db_query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
}

// Read: single project
function get_project(int $id): ?array
{
    $project = db_query("SELECT * FROM projects WHERE id = :id", [':id' => $id])->fetch();
    return $project ?: null;
}


// Update
function update_project(int $id, string $title, string $description): void
{
    db_query(
        "UPDATE projects SET title = :title, description = :description, updated_at = NOW() WHERE id = :id",
        [':title' => $title, ':description' => $description, ':id' => $id]
    );
}

// Delete
function delete_project(int $id): void
{
    db_query("DELETE FROM projects WHERE id = :id", [':id' => $id]);
}
