<?php
/**
 * Delete Gallery Image
 * Handles image deletion from gallery
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
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $filename = $input['filename'] ?? '';

    if (empty($filename)) {
        throw new Exception('Filename is required');
    }

    // Sanitize filename
    $filename = basename($filename);

    // Set paths
    $imagePath = __DIR__ . '/../frontend/nyabzgallery/' . $filename;
    $metaFile = __DIR__ . '/../frontend/gallery_captions.json';

    // Check if file exists
    if (!file_exists($imagePath)) {
        throw new Exception('Image file not found');
    }

    // Delete the image file
    if (!unlink($imagePath)) {
        throw new Exception('Failed to delete image file');
    }

    // Update metadata file
    if (file_exists($metaFile)) {
        $content = file_get_contents($metaFile);
        $decoded = json_decode($content, true);
        
        $metadataWrapper = ['images' => []];
        if (isset($decoded['images']) && is_array($decoded['images'])) {
            $metadataWrapper = $decoded;
        } elseif (is_array($decoded)) {
            $metadataWrapper['images'] = $decoded;
        }

        // Remove the deleted image from metadata
        $metadataWrapper['images'] = array_filter($metadataWrapper['images'], function($item) use ($filename) {
            return $item['filename'] !== $filename;
        });

        // Re-index array
        $metadataWrapper['images'] = array_values($metadataWrapper['images']);

        // Save updated metadata
        file_put_contents($metaFile, json_encode($metadataWrapper, JSON_PRETTY_PRINT));
    }

    echo json_encode([
        'success' => true,
        'message' => 'Image deleted successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
