<?php
// Public API endpoint for fetching events/announcements
// No authentication required - this is public data

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../shared/config.php';

try {
    // Ensure deleted_at column exists
    $conn->query("ALTER TABLE announcements ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
    
    // Get category filter if provided
    $category = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : '';
    
    // Build query
    $sql = "SELECT * FROM announcements WHERE deleted_at IS NULL";
    
    if ($category) {
        $sql .= " AND category = ?";
    }
    
    $sql .= " ORDER BY date DESC, created_at DESC";
    
    // Prepare and execute
    if ($category) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $category);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
    
    $events = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Decode JSON fields
            if (!empty($row['speakers'])) {
                $row['speakers_array'] = json_decode($row['speakers'], true);
            }
            if (!empty($row['gallery'])) {
                $row['gallery_array'] = json_decode($row['gallery'], true);
            }
            $events[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'events' => $events,
        'count' => count($events)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch events',
        'message' => $e->getMessage()
    ]);
}
?>
