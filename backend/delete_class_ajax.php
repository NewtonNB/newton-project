<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}
require_once '../shared/config.php';
$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'No class ID provided.']);
    exit();
}
$stmt = $conn->prepare('DELETE FROM classes WHERE id=?');
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
}
$stmt->close();
$conn->close(); 