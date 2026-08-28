<?php
/**
 * get_teachers_public.php
 * Public API to get teachers for staff page (no authentication required)
 */
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../shared/config.php';

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = isset($_GET['perPage']) && is_numeric($_GET['perPage']) ? (int)$_GET['perPage'] : 100;

    // Check if deleted_at column exists
    $columnsCheck = $conn->query("SHOW COLUMNS FROM teachers LIKE 'deleted_at'");
    $hasDeletedAt = $columnsCheck && $columnsCheck->num_rows > 0;

    $search_sql = '';
    if ($search) {
        $sq = $conn->real_escape_string($search);
        $search_sql = " AND (full_name LIKE '%$sq%' OR email LIKE '%$sq%' OR subject LIKE '%$sq%')";
    }

    // Build WHERE clause
    $baseWhere = "1=1";
    if ($hasDeletedAt) {
        $baseWhere .= " AND deleted_at IS NULL";
    }
    $baseWhere .= $search_sql;

    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM teachers WHERE $baseWhere";
    $countResult = $conn->query($countQuery);
    $totalTeachers = $countResult->fetch_assoc()['total'];

    // Get teachers (with pagination)
    $offset = ($page - 1) * $perPage;
    $query = "SELECT id, full_name, email, phone, subject, photo, created_at 
              FROM teachers 
              WHERE $baseWhere 
              ORDER BY created_at DESC 
              LIMIT $perPage OFFSET $offset";
    
    $result = $conn->query($query);
    
    $teachers = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $teachers[] = [
                'id' => $row['id'],
                'full_name' => $row['full_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'subject' => $row['subject'],
                'photo' => $row['photo'],
                'created_at' => $row['created_at']
            ];
        }
    }

    // Calculate pagination
    $totalPages = ceil($totalTeachers / $perPage);

    echo json_encode([
        'success' => true,
        'teachers' => $teachers,
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'totalRecords' => $totalTeachers
        ],
        'stats' => [
            'total' => $totalTeachers
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
