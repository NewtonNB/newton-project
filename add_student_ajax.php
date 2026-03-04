<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit();
}

require 'config.php';

// Collect and sanitize input
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$usertype = $_POST['usertype'] ?? 'student';
$class_id = intval($_POST['class_id'] ?? 0);

// Validate required fields
if (!$username || !$email || !$phone || !$password || !$usertype || !$class_id) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address.']);
    exit();
}

// Check for duplicate username or email
$stmt = $conn->prepare('SELECT id FROM students WHERE username = ? OR email = ?');
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Username or email already exists.']);
    $stmt->close();
    exit();
}
$stmt->close();

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert new student
$stmt = $conn->prepare('INSERT INTO students (username, email, phone, password, usertype, class_id, status) VALUES (?, ?, ?, ?, ?, ?, "Active")');
$stmt->bind_param('sssssi', $username, $email, $phone, $hashed_password, $usertype, $class_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
}
$stmt->close();
$conn->close(); 