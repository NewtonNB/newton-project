<?php
// Disable error reporting to prevent HTML output before JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../shared/config.php';

// Set JSON header and prevent any output buffering issues
header('Content-Type: application/json');
ob_clean();

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Invalid registration ID']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT r.*, a.title AS event_title, a.date AS event_date, a.time AS event_time, a.location AS event_location 
                           FROM event_registrations r 
                           LEFT JOIN announcements a ON r.event_id = a.id 
                           WHERE r.id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Registration not found']);
        exit;
    }
    
    $registration = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'registration' => $registration
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error occurred']);
}
?>