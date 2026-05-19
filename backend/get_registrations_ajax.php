<?php
// Disable error reporting to prevent HTML output before JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../shared/config.php';

// Set JSON header and prevent any output buffering issues
header('Content-Type: application/json');
ob_clean();

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    // Get parameters
    $page = max(1, intval($_GET['page'] ?? 1));
    $perPage = max(1, min(100, intval($_GET['per_page'] ?? 15)));
    $eventId = intval($_GET['event_id'] ?? 0);
    $search = trim($_GET['search'] ?? '');
    $dateFrom = $_GET['dateFrom'] ?? '';
    $dateTo = $_GET['dateTo'] ?? '';
    $sortBy = $_GET['sortBy'] ?? 'created_at_desc';
    
    $offset = ($page - 1) * $perPage;
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    $types = '';
    
    if ($eventId > 0) {
        $whereConditions[] = "r.event_id = ?";
        $params[] = $eventId;
        $types .= 'i';
    }
    
    if (!empty($search)) {
        $whereConditions[] = "(r.name LIKE ? OR r.email LIKE ? OR r.phone LIKE ? OR a.title LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        $types .= 'ssss';
    }
    
    if (!empty($dateFrom)) {
        $whereConditions[] = "DATE(r.created_at) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $whereConditions[] = "DATE(r.created_at) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Build ORDER BY clause
    $orderBy = 'ORDER BY ';
    switch ($sortBy) {
        case 'created_at_asc':
            $orderBy .= 'r.created_at ASC';
            break;
        case 'name_asc':
            $orderBy .= 'r.name ASC';
            break;
        case 'name_desc':
            $orderBy .= 'r.name DESC';
            break;
        case 'event_title_asc':
            $orderBy .= 'a.title ASC';
            break;
        default:
            $orderBy .= 'r.created_at DESC';
    }
    
    // Get total count
    $countSql = "SELECT COUNT(*) FROM event_registrations r LEFT JOIN announcements a ON r.event_id = a.id $whereClause";
    $countStmt = $conn->prepare($countSql);
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_row()[0];
    $totalPages = ceil($total / $perPage);
    
    // Get registrations
    $sql = "SELECT r.id, r.name, r.email, r.phone, r.created_at, r.event_id, a.title AS event_title 
            FROM event_registrations r 
            LEFT JOIN announcements a ON r.event_id = a.id 
            $whereClause 
            $orderBy 
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    $allParams = array_merge($params, [$perPage, $offset]);
    $allTypes = $types . 'ii';
    $stmt->bind_param($allTypes, ...$allParams);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $registrations = [];
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
    
    // Get statistics
    $totalRegistrations = $conn->query("SELECT COUNT(*) FROM event_registrations")->fetch_row()[0];
    $totalEvents = $conn->query("SELECT COUNT(*) FROM announcements WHERE category = 'Event'")->fetch_row()[0];
    
    // Get most popular event
    $mostPopularQuery = $conn->query("SELECT a.title, COUNT(r.id) as cnt 
                                     FROM announcements a 
                                     LEFT JOIN event_registrations r ON a.id = r.event_id 
                                     WHERE a.category = 'Event' 
                                     GROUP BY a.id, a.title 
                                     ORDER BY cnt DESC 
                                     LIMIT 1");
    
    $mostPopularTitle = 'N/A';
    $mostPopularCount = 0;
    if ($mostPopularQuery && $mostPopularQuery->num_rows > 0) {
        $mostPopular = $mostPopularQuery->fetch_assoc();
        $mostPopularTitle = $mostPopular['title'];
        $mostPopularCount = $mostPopular['cnt'];
    }
    
    echo json_encode([
        'success' => true,
        'registrations' => $registrations,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $total,
            'per_page' => $perPage
        ],
        'stats' => [
            'total_registrations' => $totalRegistrations,
            'total_events' => $totalEvents,
            'most_popular_title' => $mostPopularTitle,
            'most_popular_count' => $mostPopularCount
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error occurred']);
}
?>