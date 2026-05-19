<?php
// Disable error reporting to prevent HTML output before JSON
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $galleryDir = 'nyabzgallery/';
    $metaFile = 'gallery_captions.json';
    
    // Load metadata
    $metadata = [];
    if (file_exists($metaFile)) {
        $metaContent = file_get_contents($metaFile);
        $metaData = json_decode($metaContent, true);
        if (isset($metaData['images']) && is_array($metaData['images'])) {
            $metadata = $metaData['images'];
        }
    }
    
    // If no metadata, scan directory and create basic metadata
    if (empty($metadata)) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];
        $files = array_filter(scandir($galleryDir), function($file) use ($galleryDir, $allowedExts) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return is_file($galleryDir . $file) && in_array($ext, $allowedExts);
        });
        
        foreach ($files as $index => $file) {
            $category = determineCategory($file);
            $metadata[] = [
                'filename' => $file,
                'caption' => formatCaption($file),
                'category' => $category,
                'order' => $index + 1,
                'likes' => rand(0, 50),
                'views' => rand(100, 1000),
                'uploaded_at' => date('Y-m-d H:i:s', filemtime($galleryDir . $file))
            ];
        }
        
        // Save generated metadata
        file_put_contents($metaFile, json_encode(['images' => $metadata], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
    
    // Filter out non-existent files
    $validImages = array_filter($metadata, function($image) use ($galleryDir) {
        return file_exists($galleryDir . $image['filename']);
    });
    
    // Add additional metadata for each image
    $enrichedImages = array_map(function($image) use ($galleryDir) {
        $filePath = $galleryDir . $image['filename'];
        
        // Get file info
        $fileInfo = [
            'size' => file_exists($filePath) ? filesize($filePath) : 0,
            'modified' => file_exists($filePath) ? date('Y-m-d H:i:s', filemtime($filePath)) : null
        ];
        
        // Get image dimensions
        if (file_exists($filePath)) {
            $imageInfo = getimagesize($filePath);
            if ($imageInfo) {
                $fileInfo['width'] = $imageInfo[0];
                $fileInfo['height'] = $imageInfo[1];
                $fileInfo['aspect_ratio'] = round($imageInfo[0] / $imageInfo[1], 2);
            }
        }
        
        // Merge with existing data
        return array_merge($image, $fileInfo, [
            'description' => getImageDescription($image['category'], $image['caption']),
            'tags' => generateTags($image['caption'], $image['category']),
            'thumbnail' => 'nyabzgallery/' . $image['filename'], // Could be optimized with actual thumbnails
            'full_url' => 'nyabzgallery/' . $image['filename']
        ]);
    }, $validImages);
    
    // Sort by order
    usort($enrichedImages, function($a, $b) {
        return ($b['order'] ?? 0) - ($a['order'] ?? 0);
    });
    
    // Return response
    echo json_encode([
        'success' => true,
        'images' => array_values($enrichedImages),
        'total' => count($enrichedImages),
        'categories' => getCategories($enrichedImages),
        'stats' => getGalleryStats($enrichedImages)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load gallery images',
        'message' => $e->getMessage()
    ]);
}

// Helper functions
function determineCategory($filename) {
    $name = strtolower($filename);
    
    // Teachers
    if (strpos($name, 'teacher') !== false || 
        strpos($name, 'headmaster') !== false || 
        strpos($name, 'deputy') !== false ||
        strpos($name, 'hm') !== false) {
        return 'teachers';
    }
    
    // Non-teachers/Staff
    if (strpos($name, 'cook') !== false || 
        strpos($name, 'cleaner') !== false ||
        strpos($name, 'secretary') !== false ||
        strpos($name, 'burser') !== false ||
        strpos($name, 'librarian') !== false ||
        strpos($name, 'lab') !== false) {
        require_once '../shared/config.php';
        return 'nonteachers';
    }
    
    // Sports
    if (strpos($name, 'sport') !== false || 
        strpos($name, 'game') !== false ||
        strpos($name, 'athletics') !== false ||
        strpos($name, 'football') !== false ||
        strpos($name, 'basketball') !== false) {
        return 'sports';
    }
    
    // Clubs
    if (strpos($name, 'club') !== false || 
        strpos($name, 'debate') !== false ||
        strpos($name, 'drama') !== false ||
        strpos($name, 'music') !== false ||
        strpos($name, 'interact') !== false ||
        strpos($name, 'writers') !== false ||
        strpos($name, 'ceremony') !== false) {
        return 'clubs';
    }
    
    // Buildings
    if (strpos($name, 'building') !== false || 
        strpos($name, 'block') !== false ||
        strpos($name, 'wing') !== false ||
        strpos($name, 'chapel') !== false ||
        strpos($name, 'library') !== false ||
        strpos($name, 'hall') !== false ||
        strpos($name, 'admin') !== false ||
        strpos($name, 'current') !== false ||
        strpos($name, 'eastern') !== false ||
        strpos($name, 'western') !== false) {
        return 'buildings';
    }
    
    return 'others';
}

function formatCaption($filename) {
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $name = str_replace(['_', '-'], ' ', $name);
    $name = ucwords($name);
    
    // Special formatting
    $titleMap = [
        'current' => 'Main Campus View',
        'alevelstudents' => 'A-Level Students',
        'estern block' => 'Eastern Block',
        'esternblock' => 'Eastern Block',
        'boyswing' => 'Boys Wing',
        'chapel' => 'School Chapel',
        'sports' => 'Sports Activities',
        'admin' => 'Administration Block',
        'hm' => 'Headmaster',
        'deputy hm' => 'Deputy Headmaster',
        'harvad' => 'Harvard View',
        'havard' => 'Harvard View'
    ];
    
    $lowerName = strtolower($name);
    foreach ($titleMap as $key => $title) {
        if (strpos($lowerName, $key) !== false) {
            return $title;
        }
    }
    
    return $name;
}

function getImageDescription($category, $caption) {
    $descriptions = [
        'teachers' => 'Our dedicated teaching staff committed to excellence in education and student development.',
        'nonteachers' => 'Support staff members who help make our school operations run smoothly and efficiently.',
        'sports' => 'Athletic activities, sports competitions, and physical education programs at our school.',
        'clubs' => 'Student clubs, extracurricular activities, and special interest groups.',
        'buildings' => 'School facilities, infrastructure, and campus buildings that support our educational mission.',
        'others' => 'Various aspects of school life, events, and community activities.'
    ];
    
    $baseDescription = $descriptions[$category] ?? 'School gallery image showcasing our vibrant community.';
    
    // Add specific details based on caption
    if (strpos(strtolower($caption), 'ceremony') !== false) {
        return 'Special ceremony and celebration events at our school.';
    } elseif (strpos(strtolower($caption), 'graduation') !== false) {
        return 'Graduation ceremonies celebrating student achievements and milestones.';
    } elseif (strpos(strtolower($caption), 'competition') !== false) {
        return 'Academic and sports competitions showcasing student talents.';
    }
    
    return $baseDescription;
}

function generateTags($caption, $category) {
    $tags = [$category];
    
    $caption = strtolower($caption);
    
    // Add relevant tags based on content
    $tagMap = [
        'student' => ['students', 'education', 'learning'],
        'sport' => ['athletics', 'competition', 'fitness'],
        'building' => ['infrastructure', 'facilities', 'campus'],
        'ceremony' => ['event', 'celebration', 'formal'],
        'club' => ['extracurricular', 'activities', 'groups'],
        'teacher' => ['staff', 'education', 'faculty'],
        'chapel' => ['religious', 'spiritual', 'worship']
    ];
    
    foreach ($tagMap as $keyword => $relatedTags) {
        if (strpos($caption, $keyword) !== false) {
            $tags = array_merge($tags, $relatedTags);
        }
    }
    
    return array_unique($tags);
}

function getCategories($images) {
    $categories = [];
    foreach ($images as $image) {
        $cat = $image['category'];
        if (!isset($categories[$cat])) {
            $categories[$cat] = 0;
        }
        $categories[$cat]++;
    }
    return $categories;
}

function getGalleryStats($images) {
    $totalLikes = array_sum(array_column($images, 'likes'));
    $totalViews = array_sum(array_column($images, 'views'));
    $avgLikes = count($images) > 0 ? round($totalLikes / count($images), 1) : 0;
    
    return [
        'total_images' => count($images),
        'total_likes' => $totalLikes,
        'total_views' => $totalViews,
        'average_likes' => $avgLikes,
        'most_popular' => getMostPopularImage($images),
        'recent_uploads' => getRecentUploads($images, 5)
    ];
}

function getMostPopularImage($images) {
    if (empty($images)) return null;
    
    usort($images, function($a, $b) {
        return ($b['likes'] ?? 0) - ($a['likes'] ?? 0);
    });
    
    return $images[0];
}

function getRecentUploads($images, $limit = 5) {
    usort($images, function($a, $b) {
        $aTime = strtotime($a['uploaded_at'] ?? $a['modified'] ?? '1970-01-01');
        $bTime = strtotime($b['uploaded_at'] ?? $b['modified'] ?? '1970-01-01');
        return $bTime - $aTime;
    });
    
    return array_slice($images, 0, $limit);
}
?>