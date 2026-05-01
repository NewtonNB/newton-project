<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require 'config.php';

$id = intval($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID is required']);
    exit();
}

// Prevent deleting yourself
$check = $conn->prepare("SELECT username FROM admins WHERE id = ?");
$check->bind_param('i', $id);
$check->execute();
$result = $check->get_result();
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    if ($admin['username'] == $_SESSION['username']) {
        echo json_encode(['success' => false, 'error' => 'You cannot delete yourself']);
        exit();
    }
}

// Delete admin
$stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
