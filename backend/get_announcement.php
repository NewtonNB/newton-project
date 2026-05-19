<?php
// Disable error reporting to prevent HTML output before JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../shared/config.php';

// Set JSON header and prevent any output buffering issues
header('Content-Type: application/json');
ob_clean(); // Clear any previous output

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'No ID provided']);
    exit();
}

$stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $announcement = $result->fetch_assoc();
    
    // Safely decode speakers from JSON
    $speakers_decoded = !empty($announcement['speakers']) ? json_decode($announcement['speakers'], true) : [];
    $announcement['speakers'] = is_array($speakers_decoded) ? implode(', ', $speakers_decoded) : '';

    // Safely decode gallery from JSON
    $gallery_decoded = !empty($announcement['gallery']) ? json_decode($announcement['gallery'], true) : [];
    $announcement['gallery'] = is_array($gallery_decoded) ? array_values($gallery_decoded) : [];
    
    echo json_encode(['success' => true, 'data' => $announcement]);
} else {
    echo json_encode(['success' => false, 'error' => 'Announcement not found']);
}

$stmt->close();
$conn->close(); 