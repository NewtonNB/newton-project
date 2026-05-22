<?php
/**
 * get_teachers_ajax.php
 * Returns paginated teacher list + stats for view_teacher.html
 */
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin']) && (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin')) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../shared/config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 15;

$conn->query("ALTER TABLE teachers ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");

$search_sql = '';
if ($search) {
    $sq = $conn->real_escape_string($search);
    $search_sql = " AND (full_name LIKE '%$sq%' OR email LIKE '%$sq%' OR subject LIKE '%$sq%' OR phone LIKE '%$sq%')";
}

$baseWhere = "deleted_at IS NULL $search_sql";

$count_result = $conn->query("SELECT COUNT(*) as total FROM teachers WHERE $baseWhere");
$total = $count_result ? (int)$count_result->fetch_assoc()['total'] : 0;
$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

$male_result = $conn->query("SELECT COUNT(*) as c FROM teachers WHERE deleted_at IS NULL AND gender='Male'");
$female_result = $conn->query("SELECT COUNT(*) as c FROM teachers WHERE deleted_at IS NULL AND gender='Female'");
$maleCount = $male_result ? (int)$male_result->fetch_assoc()['c'] : 0;
$femaleCount = $female_result ? (int)$female_result->fetch_assoc()['c'] : 0;

$sql = "SELECT * FROM teachers WHERE $baseWhere ORDER BY id DESC LIMIT $perPage OFFSET $offset";
$result = $conn->query($sql);

$teachers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $teachers[] = [
            'id' => (int)$row['id'],
            'full_name' => $row['full_name'] ?? '',
            'email' => $row['email'] ?? '',
            'phone' => $row['phone'] ?? '',
            'subject' => $row['subject'] ?? '',
            'gender' => $row['gender'] ?? '',
            'joined_on' => $row['joined_on'] ?? '',
            'photo' => $row['photo'] ?? '',
        ];
    }
}

echo json_encode([
    'teachers' => $teachers,
    'stats' => [
        'total' => $total,
        'male' => $maleCount,
        'female' => $femaleCount,
        'pageCount' => count($teachers),
    ],
    'pagination' => [
        'page' => $page,
        'totalPages' => $totalPages,
        'total' => $total,
        'perPage' => $perPage,
    ],
    'filters' => [
        'search' => $search,
    ],
]);
