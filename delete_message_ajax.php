<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin'])) { echo json_encode(['success' => false]); exit; }
require_once 'config.php';

$action = $_POST['action'] ?? 'trash';
$id = intval($_POST['id'] ?? 0);
if (!$id) { echo json_encode(['success' => false]); exit; }

if ($action === 'trash') {
    // Soft delete - move to trash
    $conn->query("UPDATE contact_messages SET deleted_at = NOW() WHERE id = $id");
    echo json_encode(['success' => true]);
} elseif ($action === 'restore') {
    // Restore from trash
    $conn->query("UPDATE contact_messages SET deleted_at = NULL WHERE id = $id");
    echo json_encode(['success' => true]);
} elseif ($action === 'permanent') {
    // Permanently delete
    $conn->query("DELETE FROM contact_messages WHERE id = $id");
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
