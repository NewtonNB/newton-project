<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$classes = [];
$r = $conn->query("SELECT * FROM classes ORDER BY class_name ASC");
if ($r) while ($row = $r->fetch_assoc()) {
    $classes[] = ['id' => (int)$row['id'], 'class_name' => $row['class_name'], 'level' => $row['level'] ?? ''];
}
echo json_encode(['classes' => $classes]);
