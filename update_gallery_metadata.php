<?php
session_start();
if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$metaFile = 'gallery_captions.json';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['action'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit();
}

// Load current metadata
$meta = file_exists($metaFile) ? json_decode(file_get_contents($metaFile), true) : ['images' => []];
if (!isset($meta['images']) || !is_array($meta['images'])) {
    $meta['images'] = [];
}

$changed = false;

if ($data['action'] === 'update_caption') {
    // Update caption for a specific image
    $filename = $data['filename'] ?? '';
    $caption = $data['caption'] ?? '';
    foreach ($meta['images'] as &$img) {
        if ($img['filename'] === $filename) {
            $img['caption'] = $caption;
            $changed = true;
            break;
        }
    }
} elseif ($data['action'] === 'update_category') {
    // Update category for a specific image
    $filename = $data['filename'] ?? '';
    $category = $data['category'] ?? '';
    foreach ($meta['images'] as &$img) {
        if ($img['filename'] === $filename) {
            $img['category'] = $category;
            $changed = true;
            break;
        }
    }
} elseif ($data['action'] === 'update_order') {
    // Update order for all images
    $orderArr = $data['order'] ?? [];
    foreach ($meta['images'] as &$img) {
        if (isset($orderArr[$img['filename']])) {
            $img['order'] = $orderArr[$img['filename']];
            $changed = true;
        }
    }
}

if ($changed) {
    // Sort by new order before saving
    usort($meta['images'], function($a, $b) {
        return ($a['order'] ?? 9999) - ($b['order'] ?? 9999);
    });
    file_put_contents($metaFile, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No changes made']);
} 