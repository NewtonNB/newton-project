<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }
require_once 'config.php';

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
$action = $_GET['action'] ?? $_POST['action'] ?? 'trash';

if (!$id) { echo json_encode(['success'=>false,'error'=>'Invalid ID']); exit; }

$conn->query("ALTER TABLE teachers ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");

if ($action === 'trash') {
    $conn->query("UPDATE teachers SET deleted_at = NOW() WHERE id = $id");
    echo json_encode(['success'=>true]);
} elseif ($action === 'restore') {
    $conn->query("UPDATE teachers SET deleted_at = NULL WHERE id = $id");
    echo json_encode(['success'=>true]);
} elseif ($action === 'permanent') {
    $conn->query("DELETE FROM teachers WHERE id = $id");
    echo json_encode(['success'=>true]);
} else {
    $conn->query("UPDATE teachers SET deleted_at = NOW() WHERE id = $id");
    header('Location: view_teacher.php');
    exit;
}
?>
