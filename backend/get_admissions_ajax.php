<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$total = (int)($conn->query("SELECT COUNT(*) FROM admission")->fetch_row()[0] ?? 0);
$totalPages = max(1, (int)ceil($total / $perPage));

$sql = "SELECT * FROM admission WHERE status != 'approved' ORDER BY id DESC LIMIT $perPage OFFSET $offset";
$result = $conn->query($sql);
$applications = [];
if ($result) while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}

echo json_encode([
    'applications' => $applications,
    'total' => $total,
    'pagination' => ['page' => $page, 'totalPages' => $totalPages, 'perPage' => $perPage],
]);
