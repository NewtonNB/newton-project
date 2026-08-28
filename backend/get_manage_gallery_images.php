<?php
/**
 * Get Gallery Images for Admin Management
 * Returns all gallery images with metadata for admin interface
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
    $galleryDir = __DIR__ . '/../frontend/nyabzgallery/';
    $metaFile = __DIR__ . '/../frontend/gallery_captions.json';

    // Load metadata
    $metadata = [];
    if (file_exists($metaFile)) {
        $content = file_get_contents($metaFile);
        $decoded = json_decode($content, true);
        if (isset($decoded['images']) && is_array($decoded['images'])) {
            $metadata = $decoded['images'];
        } elseif (is_array($decoded)) {
            $metadata = $decoded;
        }
    }

    // Get all image files from directory
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];
    $files = [];

    if (is_dir($galleryDir)) {
        $dirContents = scandir($galleryDir);
        foreach ($dirContents as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $filePath = $galleryDir . $file;
            
            if (is_file($filePath) && in_array($ext, $allowedExts)) {
                $files[] = $file;
            }
        }
    }

    // Merge files with metadata
    $images = [];
    $metadataMap = [];

    // Create map for quick lookup
    foreach ($metadata as $item) {
        $metadataMap[$item['filename']] = $item;
    }

    // Process each file
    foreach ($files as $file) {
        $filePath = $galleryDir . $file;
        
        if (isset($metadataMap[$file])) {
            // Use existing metadata
            $imageData = $metadataMap[$file];
        } else {
            // Create default metadata
            $imageData = [
                'filename' => $file,
                'caption' => pathinfo($file, PATHINFO_FILENAME),
                'category' => 'others',
                'order' => 999,
                'likes' => 0,
                'views' => 0,
                'uploaded_at' => date('Y-m-d H:i:s', filemtime($filePath))
            ];
        }

        // Add file info
        $imageData['size'] = filesize($filePath);
        $imageData['size_formatted'] = formatBytes(filesize($filePath));
        $imageData['modified'] = date('Y-m-d H:i:s', filemtime($filePath));
        $imageData['url'] = 'nyabzgallery/' . $file;

        $images[] = $imageData;
    }

    // Sort by order, then by filename
    usort($images, function($a, $b) {
        if ($a['order'] === $b['order']) {
            return strcmp($a['filename'], $b['filename']);
        }
        return $a['order'] - $b['order'];
    });

    // Group by category
    $grouped = [
        'teachers' => [],
        'nonteachers' => [],
        'sports' => [],
        'clubs' => [],
        'buildings' => [],
        'others' => []
    ];

    foreach ($images as $image) {
        $category = $image['category'] ?? 'others';
        if (!isset($grouped[$category])) {
            $category = 'others';
        }
        $grouped[$category][] = $image;
    }

    echo json_encode([
        'success' => true,
        'images' => $images,
        'grouped' => $grouped,
        'total' => count($images),
        'categories' => [
            'teachers' => count($grouped['teachers']),
            'nonteachers' => count($grouped['nonteachers']),
            'sports' => count($grouped['sports']),
            'clubs' => count($grouped['clubs']),
            'buildings' => count($grouped['buildings']),
            'others' => count($grouped['others'])
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>
