<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}
require '../shared/config.php';
include '../frontend/admin_sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { echo '<div class="modern-content">Invalid announcement ID.</div>'; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo '<div class="modern-content" style="color:green;">Announcement deleted! <a href="announcements.php">Back to Announcements</a></div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Announcement/Event</title>
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/admin.css">
    <style>body { background: #f4f8fb; font-family: 'Poppins', Arial, sans-serif; font-size: 1rem; } .modern-content { font-size: 1rem; } h2 { font-size: 2.1rem; font-weight: 900; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 8px; text-align: center; letter-spacing: -1px; line-height: 1.1; } </style>
</head>
<body>
<div class="content">
<div class="modern-content">
    <h2>Delete Announcement/Event</h2>
    <p>Are you sure you want to delete this announcement/event?</p>
    <form method="post">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
        <a href="announcements.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</div>
</body>
</html> 