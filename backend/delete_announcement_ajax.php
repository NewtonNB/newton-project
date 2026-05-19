<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin'])) { echo json_encode(['success'=>false]); exit; }
require_once '../shared/config.php';

$id = intval($_POST['id'] ?? 0);
$action = $_POST['action'] ?? 'trash';
if (!$id) { echo json_encode(['success'=>false]); exit; }

$conn->query("ALTER TABLE announcements ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");

if ($action === 'trash') {
    $conn->query("UPDATE announcements SET deleted_at = NOW() WHERE id = $id");
} elseif ($action === 'restore') {
    $conn->query("UPDATE announcements SET deleted_at = NULL WHERE id = $id");
} elseif ($action === 'permanent') {
    $conn->query("DELETE FROM announcements WHERE id = $id");
}
echo json_encode(['success' => true]);
?>
