<?php
/**
 * Update Gallery Metadata
 * Handles caption updates, category changes, and image reordering
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
    $action = $input['action'] ?? '';

    if (empty($action)) {
        throw new Exception('Action is required');
    }

    // Load metadata
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
    
    $metadata = &$metadataWrapper['images'];

    switch ($action) {
        case 'update_caption':
            $filename = $input['filename'] ?? '';
            $caption = trim($input['caption'] ?? '');

            if (empty($filename)) {
                throw new Exception('Filename is required');
            }

            // Find and update the image
            $found = false;
            foreach ($metadata as &$item) {
                if ($item['filename'] === $filename) {
                    $item['caption'] = $caption;
                    $item['updated_at'] = date('Y-m-d H:i:s');
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // Add new entry if not found
                $metadata[] = [
                    'filename' => $filename,
                    'caption' => $caption,
                    'category' => 'others',
                    'order' => count($metadata) + 1,
                    'likes' => 0,
                    'views' => 0,
                    'uploaded_at' => date('Y-m-d H:i:s')
                ];
            }
            break;

        case 'update_category':
            $filename = $input['filename'] ?? '';
            $category = trim($input['category'] ?? '');

            if (empty($filename) || empty($category)) {
                throw new Exception('Filename and category are required');
            }

            // Validate category
            $validCategories = ['teachers', 'nonteachers', 'sports', 'clubs', 'buildings', 'others'];
            if (!in_array($category, $validCategories)) {
                throw new Exception('Invalid category');
            }

            // Find and update the image
            $found = false;
            foreach ($metadata as &$item) {
                if ($item['filename'] === $filename) {
                    $item['category'] = $category;
                    $item['updated_at'] = date('Y-m-d H:i:s');
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new Exception('Image not found in metadata');
            }
            break;

        case 'update_order':
            $order = $input['order'] ?? [];

            if (!is_array($order) || empty($order)) {
                throw new Exception('Order data is required');
            }

            // Update order for each image
            foreach ($metadata as &$item) {
                if (isset($order[$item['filename']])) {
                    $item['order'] = (int)$order[$item['filename']];
                    $item['updated_at'] = date('Y-m-d H:i:s');
                }
            }

            // Sort by order
            usort($metadata, function($a, $b) {
                return ($a['order'] ?? 999) - ($b['order'] ?? 999);
            });
            break;

        default:
            throw new Exception('Invalid action');
    }

    // Save updated metadata
    if (file_put_contents($metaFile, json_encode($metadataWrapper, JSON_PRETTY_PRINT))) {
        echo json_encode([
            'success' => true,
            'message' => 'Metadata updated successfully'
        ]);
    } else {
        throw new Exception('Failed to save metadata');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
