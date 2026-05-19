<?php
// Disable error reporting to prevent HTML output before JSON
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $filename = $data['filename'] ?? '';
    
    if (!$filename) {
        echo json_encode(['success' => false, 'error' => 'No filename provided']);
        exit();
    }
    
    $metaFile = 'gallery_captions.json';
    
    // Load current metadata
    $metadata = [];
    if (file_exists($metaFile)) {
        $metaContent = file_get_contents($metaFile);
        $metaData = json_decode($metaContent, true);
        if (isset($metaData['images']) && is_array($metaData['images'])) {
            $metadata = $metaData['images'];
        }
    }
    
    // Find and update the image
    $updated = false;
    foreach ($metadata as &$image) {
        if ($image['filename'] === $filename) {
            $image['views'] = ($image['views'] ?? 0) + 1;
            $image['last_viewed'] = date('Y-m-d H:i:s');
            $updated = true;
            break;
        }
    }
    
    // If image not found in metadata, add it
    if (!$updated) {
        $metadata[] = [
            'filename' => $filename,
            'caption' => $filename,
            'category' => 'others',
            'order' => count($metadata) + 1,
            'views' => 1,
            'likes' => 0,
            'last_viewed' => date('Y-m-d H:i:s')
        ];
    }
    
    // Save updated metadata
    $result = file_put_contents($metaFile, json_encode(['images' => $metadata], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    
    if ($result !== false) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save metadata']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>