<?php
require_once 'common/db.php';

$statusMessage = '';
$statusType    = '';
$editProject   = null;

// Handle form submissions (create, update, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action      = $_POST['action'] ?? '';
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id          = (int) ($_POST['id'] ?? 0);

    try {
        if ($action === 'delete' && $id > 0) {
            delete_project($id);
            $statusMessage = 'Project deleted.';
            $statusType    = 'success';
        } elseif (in_array($action, ['create', 'update'], true)) {
            // Validation
            if (empty($title) || empty($description)) {
                $statusMessage = 'Title and description are required.';
                $statusType    = 'error';
            } elseif ($action === 'create') {
                create_project($title, $description);
                $statusMessage = 'Project created successfully.';
                $statusType    = 'success';
            } else {
                update_project($id, $title, $description);
                $statusMessage = 'Project updated successfully.';
                $statusType    = 'success';
            }
        }
    } catch (PDOException $e) {
        $statusMessage = 'Database error. Please try again later.';
        $statusType    = 'error';
    }
}

// Load a project for editing
$editId = (int) ($_GET['edit'] ?? 0);
if ($editId > 0) {
    try {
        $editProject = get_project($editId);
    } catch (PDOException $e) {
        // Table may not exist yet
    }
}

// Fetch all projects
$projects = [];
try {
    $projects = get_projects();
} catch (PDOException $e) {
    // Table may not exist yet; show the page anyway
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Developer Portfolio - Projects</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include_once('common/header.php')?>
  <main>
    <h1>Projects</h1>
    <p>A selection of things I've built.</p>

    <?php if (!empty($statusMessage)): ?>
        <div class="alert <?= htmlspecialchars($statusType) ?>">
            <?= htmlspecialchars($statusMessage) ?>
        </div>
    <?php endif; ?>

    <h2 style="margin-bottom: 1rem;"><?= $editProject ? 'Edit Project' : 'Add Project' ?></h2>
    <form action="projects.php" method="POST">
      <input type="hidden" name="action" value="<?= $editProject ? 'update' : 'create' ?>">
      <?php if ($editProject): ?>
        <input type="hidden" name="id" value="<?= (int) $editProject['id'] ?>">
      <?php endif; ?>
      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($editProject['title'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($editProject['description'] ?? '') ?></textarea>
      </div>
      <button type="submit" class="btn" style="border: none; cursor: pointer; margin-top: 1rem;"><?= $editProject ? 'Update Project' : 'Add Project' ?></button>
      <?php if ($editProject): ?>
        <a href="projects.php" class="btn" style="background: var(--text-muted);">Cancel</a>
      <?php endif; ?>
    </form>

    <h2 style="margin: 2rem 0 1rem;">All Projects</h2>
    <?php if (empty($projects)): ?>
        <p>No projects yet.</p>
    <?php else: ?>
        <?php foreach ($projects as $project): ?>
            <div style="background: var(--card-bg); border: 1px solid #334155; border-radius: 0.375rem; padding: 1rem; margin-bottom: 1rem;">
                <p style="margin-bottom: 0.25rem; color: var(--text-color);"><strong><?= htmlspecialchars($project['title']) ?></strong></p>
                <p style="margin-bottom: 0.5rem;"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                <a href="projects.php?edit=<?= (int) $project['id'] ?>" style="color: var(--accent-color); margin-right: 1rem;">Edit</a>
                <form action="projects.php" method="POST" style="display: inline;" onsubmit="return confirm('Delete this project?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">
                    <button type="submit" style="background: none; border: none; color: #f87171; cursor: pointer; font-size: inherit; padding: 0;">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
  </main>
   <?php include_once('common/footer.php')?>
</body>
</html>
