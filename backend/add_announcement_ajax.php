<?php
// Disable error reporting to prevent HTML output before JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../shared/config.php';

// Set JSON header and prevent any output buffering issues
header('Content-Type: application/json');
ob_clean(); // Clear any previous output
function debug_log($msg) {
    file_put_contents('announcement_debug.log', date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND);
}
if (!isset($_SESSION['admin'])) {
    debug_log('Unauthorized access. Session: ' . print_r($_SESSION, true));
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$date = $_POST['date'] ?? null;
$time = $_POST['time'] ?? null;
$location = trim($_POST['location'] ?? '');
$speakers = trim($_POST['speakers'] ?? '');
$category = trim($_POST['category'] ?? '');
$gallery = [];
$galleryDir = 'announcement_gallery/';
if (!is_dir($galleryDir)) mkdir($galleryDir, 0777, true);
if (isset($_FILES['gallery']) && is_array($_FILES['gallery']['name'])) {
    foreach ($_FILES['gallery']['name'] as $i => $name) {
        if ($_FILES['gallery']['error'][$i] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                $newName = uniqid('ann_', true) . '.' . $ext;
                $dest = $galleryDir . $newName;
                if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $dest)) {
                    $gallery[] = $dest;
                } else {
                    debug_log('Failed to move uploaded file: ' . $_FILES['gallery']['tmp_name'][$i] . ' to ' . $dest);
                }
            } else {
                debug_log('Invalid file type: ' . $ext);
            }
        } else {
            debug_log('File upload error: ' . $_FILES['gallery']['error'][$i]);
        }
    }
}
if (!$title || !$content) {
    debug_log('Missing title or content. POST: ' . print_r($_POST, true));
    echo json_encode(['success' => false, 'error' => 'Title and content are required.']);
    exit();
}

debug_log('Preparing SQL statement with data: ' . json_encode([
    'title' => $title,
    'content' => $content,
    'date' => $date,
    'time' => $time,
    'location' => $location,
    'speakers' => $speakers,
    'category' => $category,
    'gallery_count' => count($gallery)
]));

$sql = "INSERT INTO announcements (title, content, date, time, location, speakers, category, gallery, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$galleryJson = json_encode($gallery);
if (!$stmt) {
    debug_log('Prepare failed: ' . $conn->error);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]);
    exit();
}

debug_log('Binding parameters...');
$stmt->bind_param('ssssssss', $title, $content, $date, $time, $location, $speakers, $category, $galleryJson);
if ($stmt->execute()) {
    debug_log('Announcement added successfully. ID: ' . $conn->insert_id);
    echo json_encode(['success' => true, 'message' => 'Announcement added successfully']);
} else {
    debug_log('Execute failed: ' . $stmt->error . ' | POST: ' . print_r($_POST, true));
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
}
$stmt->close();
$conn->close(); 