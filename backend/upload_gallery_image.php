<?php
/**
 * Upload Gallery Image
 * Handles image upload for gallery management
 */

error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['admin']) || $_SESSION['usertype'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require '../shared/config.php';

try {
    // Check if file was uploaded
    if (!isset($_FILES['add_image']) || $_FILES['add_image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error');
    }

    $file = $_FILES['add_image'];
    $caption = trim($_POST['add_caption'] ?? '');
    $category = trim($_POST['add_category'] ?? 'others');

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, and GIF allowed');
    }

    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('File too large. Maximum size is 5MB');
    }

    // Generate unique filename
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('gallery_') . '.' . $extension;
    
    // Set upload directory
    $uploadDir = __DIR__ . '/../frontend/nyabzgallery/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $targetPath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to save uploaded file');
    }

    // Update metadata file
    $metaFile = __DIR__ . '/../frontend/gallery_captions.json';
    $metadataWrapper = ['images' => []];
    
    if (file_exists($metaFile)) {
        $content = file_get_contents($metaFile);
        $decoded = json_decode($content, true);
        if (isset($decoded['images']) && is_array($decoded['images'])) {
            $metadataWrapper = $decoded;
        } elseif (is_array($decoded)) {
            // Convert old format to new format
            $metadataWrapper['images'] = $decoded;
        }
    }

    // Add new image metadata
    $metadataWrapper['images'][] = [
        'filename' => $filename,
        'caption' => $caption ?: pathinfo($file['name'], PATHINFO_FILENAME),
        'category' => $category,
        'order' => count($metadataWrapper['images']) + 1,
        'likes' => 0,
        'views' => 0,
        'uploaded_at' => date('Y-m-d H:i:s'),
        'uploaded_by' => $_SESSION['username'] ?? 'admin'
    ];

    // Save metadata
    if (file_put_contents($metaFile, json_encode($metadataWrapper, JSON_PRETTY_PRINT))) {
        echo json_encode([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'filename' => $filename
        ]);
    } else {
        throw new Exception('Failed to update metadata');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
