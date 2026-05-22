<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$studentCount = (int)($conn->query("SELECT COUNT(*) FROM students WHERE usertype='student'")->fetch_row()[0] ?? 0);
$teacherCount = (int)($conn->query("SELECT COUNT(*) FROM teachers")->fetch_row()[0] ?? 0);
$adminCount = (int)($conn->query("SELECT COUNT(*) FROM admins")->fetch_row()[0] ?? 0);

echo json_encode([
    'students' => $studentCount,
    'teachers' => $teacherCount,
    'admins' => $adminCount,
]);
