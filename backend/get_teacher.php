<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin']) && !isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

require_once '../shared/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid teacher ID']);
    exit();
}

$stmt = $conn->prepare('SELECT * FROM teachers WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'teacher' => $row]);
} else {
    echo json_encode(['success' => false, 'error' => 'Teacher not found']);
}
$stmt->close();
$conn->close(); 