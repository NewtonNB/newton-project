<?php
session_start();
if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$galleryDir = 'nyabzgallery/';
$metaFile = 'gallery_captions.json';

if (!isset($_FILES['add_image']) || $_FILES['add_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No image uploaded']);
    exit();
}

$allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
$fileTmpPath = $_FILES['add_image']['tmp_name'];
$fileName = $_FILES['add_image']['name'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
if (!in_array($fileExt, $allowedExts)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type']);
    exit();
}
$newFileName = uniqid('gallery_', true) . '.' . $fileExt;
$destPath = $galleryDir . $newFileName;
if (!move_uploaded_file($fileTmpPath, $destPath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to save image']);
    exit();
}

$caption = trim($_POST['add_caption'] ?? '');
$category = $_POST['add_category'] ?? 'others';

// Load and update metadata
$meta = file_exists($metaFile) ? json_decode(file_get_contents($metaFile), true) : ['images' => []];
$order = 1;
if (!empty($meta['images'])) {
    $order = max(array_column($meta['images'], 'order')) + 1;
}
$meta['images'][] = [
    'filename' => $newFileName,
    'category' => $category,
    'caption' => $caption ?: $newFileName,
    'order' => $order
];
file_put_contents($metaFile, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo json_encode(['success' => true]); 