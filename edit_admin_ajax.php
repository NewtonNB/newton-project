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
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$status = $_POST['status'] ?? 'Active';

if (!$id || !$username || !$email) {
    echo json_encode(['success' => false, 'error' => 'ID, username and email are required']);
    exit();
}

// Check if username or email exists for other admins
$check = $conn->prepare("SELECT id FROM admins WHERE (username = ? OR email = ?) AND id != ?");
$check->bind_param('ssi', $username, $email, $id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Username or email already exists']);
    exit();
}

// Update admin
if ($password) {
    $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ?, phone = ?, password = ?, status = ? WHERE id = ?");
    $stmt->bind_param('sssssi', $username, $email, $phone, $password, $status, $id);
} else {
    $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ?, phone = ?, status = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $username, $email, $phone, $status, $id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
