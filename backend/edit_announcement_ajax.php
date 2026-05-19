<?php
// Disable error reporting to prevent HTML output before JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once '../shared/config.php';
session_start();

// Set JSON header and prevent any output buffering issues
header('Content-Type: application/json');
ob_clean(); // Clear any previous output

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit();
}

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Invalid announcement ID.']);
    exit();
}

$title = trim($_POST['title'] ?? '');
$date = trim($_POST['date'] ?? null);
$time = trim($_POST['time'] ?? null);
$location = trim($_POST['location'] ?? '');
$speakers_raw = trim($_POST['speakers'] ?? '');
$category = trim($_POST['category'] ?? 'General');
$content = trim($_POST['content'] ?? '');
$delete_images = $_POST['delete_images'] ?? [];

if (empty($title) || empty($content) || empty($category)) {
    echo json_encode(['success' => false, 'error' => 'Title, content, and category are required.']);
    exit();
}

// Fetch current gallery
$stmt = $conn->prepare("SELECT gallery FROM announcements WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$current_gallery = $result->fetch_assoc()['gallery'];
$gallery_array = !empty($current_gallery) ? json_decode($current_gallery, true) : [];
$stmt->close();

// Delete images marked for deletion
if (!empty($delete_images) && is_array($delete_images)) {
    foreach ($delete_images as $img_to_delete) {
        $key = array_search($img_to_delete, $gallery_array);
        if ($key !== false) {
            unset($gallery_array[$key]);
            if (file_exists($img_to_delete)) {
                @unlink($img_to_delete);
            }
        }
    }
    // Re-index the array
    $gallery_array = array_values($gallery_array);
}

// Handle new image uploads
if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
    $upload_dir = 'announcement_gallery/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp_name) {
        $file_name = uniqid() . '_' . basename($_FILES['gallery']['name'][$key]);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($tmp_name, $target_file)) {
            $gallery_array[] = $target_file;
        }
    }
}

$speakers = json_encode(array_map('trim', explode(',', $speakers_raw)));
$gallery_json = !empty($gallery_array) ? json_encode($gallery_array) : null;

$stmt = $conn->prepare("UPDATE announcements SET title=?, date=?, time=?, location=?, speakers=?, category=?, content=?, gallery=? WHERE id=?");
$stmt->bind_param("ssssssssi", $title, $date, $time, $location, $speakers, $category, $content, $gallery_json, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database update failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close(); 