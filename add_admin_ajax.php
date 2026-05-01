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

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$status = $_POST['status'] ?? 'Active';

if (!$username || !$email || !$password) {
    echo json_encode(['success' => false, 'error' => 'Username, email and password are required']);
    exit();
}

// Check if username or email exists
$check = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
$check->bind_param('ss', $username, $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Username or email already exists']);
    exit();
}

// Insert new admin
$stmt = $conn->prepare("INSERT INTO admins (username, email, phone, password, status) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('sssss', $username, $email, $phone, $password, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
