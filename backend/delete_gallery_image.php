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

require_once '../shared/config.php';
$data = json_decode(file_get_contents('php://input'), true);
$filename = $data['filename'] ?? '';
if (!$filename) {
    echo json_encode(['success' => false, 'error' => 'No filename provided']);
    exit();
}

$galleryDir = 'nyabzgallery/';
$metaFile = 'gallery_captions.json';
$imgPath = $galleryDir . $filename;

// Delete the image file
if (file_exists($imgPath)) {
    unlink($imgPath);
}

// Remove from metadata
$meta = file_exists($metaFile) ? json_decode(file_get_contents($metaFile), true) : ['images' => []];
if (isset($meta['images']) && is_array($meta['images'])) {
    $meta['images'] = array_values(array_filter($meta['images'], function($img) use ($filename) {
        return $img['filename'] !== $filename;
    }));
    file_put_contents($metaFile, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

echo json_encode(['success' => true]); 