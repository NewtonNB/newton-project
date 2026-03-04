<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}
$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Missing ID.']);
    exit;
}
require_once 'config.php';
$stmt = $conn->prepare('DELETE FROM attendance WHERE id = ?');
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
    exit;
}
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Delete failed.']);
}
$stmt->close(); 