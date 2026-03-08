<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display, but log errors

session_start();
header('Content-Type: application/json');

// Log errors to a file instead of displaying
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/teacher_edit_errors.log');

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

require 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}
$id = intval($_POST['id'] ?? 0);
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$gender = $_POST['gender'] ?? '';
if (!$id || !$full_name || !$email || !$phone || !$subject || !$gender) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit();
}
// Fetch current teacher info
$stmt = $conn->prepare('SELECT * FROM teachers WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$teacher) {
    echo json_encode(['success' => false, 'error' => 'Teacher not found']);
    exit();
}
$photoFileName = $teacher['photo'];
// Handle new photo upload
if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== 4) {
    if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($fileExt, $allowedExts)) {
            $newFileName = uniqid('teacher_', true) . '.' . $fileExt;
            $destPath = 'nyabzgallery/' . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Delete old photo if not default and exists
                if ($photoFileName && $photoFileName !== 'default.png' && file_exists('nyabzgallery/' . $photoFileName)) {
                    @unlink('nyabzgallery/' . $photoFileName);
                }
                $photoFileName = $newFileName;
            }
        }
    }
}
// Update teacher in DB
$update = $conn->prepare('UPDATE teachers SET full_name=?, email=?, phone=?, subject=?, gender=?, photo=? WHERE id=?');
$update->bind_param('ssssssi', $full_name, $email, $phone, $subject, $gender, $photoFileName, $id);
if ($update->execute()) {
    echo json_encode(['success' => true, 'message' => 'Teacher updated successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Update failed: ' . $conn->error]);
}
$update->close();
$conn->close(); 