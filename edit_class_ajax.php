<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}
require 'config.php';
$id = intval($_POST['id'] ?? 0);
$class_name = trim($_POST['class_name'] ?? '');
$level = trim($_POST['level'] ?? '');
if (!$id || !$class_name || !$level) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit();
}
$stmt = $conn->prepare('UPDATE classes SET class_name=?, level=? WHERE id=?');
$stmt->bind_param('ssi', $class_name, $level, $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
}
$stmt->close();
$conn->close(); 