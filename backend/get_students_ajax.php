<?php
/**
 * get_students_ajax.php
 * Returns paginated student list + class options for view_student.html
 */
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin']) && (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin')) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../shared/config.php';

$class_options = [];
$classes = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name ASC");
if ($classes) {
    while ($c = $classes->fetch_assoc()) {
        $class_options[] = ['id' => (int)$c['id'], 'name' => $c['class_name']];
    }
}

$class_filter = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$level_filter = isset($_GET['level']) ? $_GET['level'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 15;

$class_filter_sql = $class_filter ? " AND class_id = $class_filter" : '';
$level_filter_sql = '';
if ($level_filter) {
    $level_filter_sql = " AND class_id IN (SELECT id FROM classes WHERE level = '" . $conn->real_escape_string($level_filter) . "')";
}
$status_filter_sql = '';
if ($status_filter) {
    $status_filter_sql = " AND status = '" . $conn->real_escape_string($status_filter) . "'";
}
$search_filter_sql = '';
if ($search_query) {
    $sq = $conn->real_escape_string($search_query);
    $search_filter_sql = " AND (username LIKE '%$sq%' OR email LIKE '%$sq%' OR phone LIKE '%$sq%')";
}

$conn->query("ALTER TABLE students ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");

$count_sql = "SELECT COUNT(*) as total FROM students WHERE deleted_at IS NULL $class_filter_sql $level_filter_sql $status_filter_sql $search_filter_sql";
$count_result = $conn->query($count_sql);
$total = $count_result ? (int)$count_result->fetch_assoc()['total'] : 0;
$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

$sql = "SELECT * FROM students WHERE deleted_at IS NULL $class_filter_sql $level_filter_sql $status_filter_sql $search_filter_sql ORDER BY username ASC LIMIT $perPage OFFSET $offset";
$result = $conn->query($sql);

$students = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $className = null;
        foreach ($class_options as $co) {
            if ($co['id'] == (int)$row['class_id']) {
                $className = $co['name'];
                break;
            }
        }
        $students[] = [
            'id' => (int)$row['id'],
            'username' => $row['username'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'usertype' => $row['usertype'],
            'status' => $row['status'] ?? '',
            'class_id' => $row['class_id'] ? (int)$row['class_id'] : null,
            'class_name' => $className,
        ];
    }
}

echo json_encode([
    'students' => $students,
    'classes' => $class_options,
    'pagination' => [
        'page' => $page,
        'totalPages' => $totalPages,
        'total' => $total,
        'perPage' => $perPage,
    ],
    'filters' => [
        'class_id' => $class_filter,
        'level' => $level_filter,
        'status' => $status_filter,
        'search' => $search_query,
    ],
]);
