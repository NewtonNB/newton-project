<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success'=>false, 'error'=>'Unauthorized']);
    exit();
}
require_once '../shared/config.php';
$galleryDir = 'nyabzgallery/';
$captionsFile = 'gallery_captions.json';
$img = $_POST['edit_img'] ?? '';
$caption = trim($_POST['edit_caption'] ?? '');
if (!$img || !file_exists($galleryDir . $img)) {
    echo json_encode(['success'=>false, 'error'=>'Image not found.']);
    exit();
}
// Handle image replacement
if (isset($_FILES['edit_file']) && $_FILES['edit_file']['error'] === UPLOAD_ERR_OK) {
    $allowedExts = ['jpg','jpeg','png','gif'];
    $fileExt = strtolower(pathinfo($_FILES['edit_file']['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedExts)) {
        echo json_encode(['success'=>false, 'error'=>'Invalid file type.']);
        exit();
    }
    $destPath = $galleryDir . $img;
    if (!move_uploaded_file($_FILES['edit_file']['tmp_name'], $destPath)) {
        echo json_encode(['success'=>false, 'error'=>'Failed to replace image.']);
        exit();
    }
}
// Save caption
$captions = file_exists($captionsFile) ? json_decode(file_get_contents($captionsFile), true) : [];
$captions[$img] = $caption;
file_put_contents($captionsFile, json_encode($captions));
echo json_encode(['success'=>true]); 