<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/student_add_errors.log');

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
$usertype = 'student'; // Always student
$class_id = intval($_POST['class_id'] ?? 0);

// Generate default password (username + 123)
$password = $username . '123';

// Validate required fields
if (!$username || !$email || !$phone || !$usertype || !$class_id) {
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

// Insert new student (password stored as plain text for now - should be hashed in production)
$stmt = $conn->prepare('INSERT INTO students (username, email, phone, password, usertype, class_id, status) VALUES (?, ?, ?, ?, ?, ?, "Active")');
$stmt->bind_param('sssssi', $username, $email, $phone, $password, $usertype, $class_id);
if ($stmt->execute()) {
    // Send welcome email to student
    require_once 'email_helper.php';
    sendEmail($email, $username,
        'Welcome to Nyabikoni Secondary School',
        "<p>Dear <strong>$username</strong>,</p>
        <p>Your student account has been created successfully.</p>
        <table style='border-collapse:collapse;margin:16px 0;'>
            " . row('Username', $username) . "
            " . row('Password', $password) . "
            " . row('Email', $email) . "
        </table>
        <p>Please log in and change your password after your first login.</p>
        <p>Best regards,<br><strong>Nyabikoni Secondary School</strong></p>"
    );
    // Also notify admin
    sendEmail(SCHOOL_EMAIL, SCHOOL_NAME,
        'New Student Account Created',
        "<p>A new student account has been created.</p>
        <table style='border-collapse:collapse;margin:16px 0;'>
            " . row('Username', $username) . "
            " . row('Email', $email) . "
            " . row('Phone', $phone) . "
        </table>"
    );
    echo json_encode([
        'success' => true,
        'message' => 'Student added successfully!',
        'password' => $password,
        'username' => $username
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
}
$stmt->close();
$conn->close(); 