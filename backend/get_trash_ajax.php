<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$conn->query("ALTER TABLE students ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
$conn->query("ALTER TABLE teachers ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
$conn->query("ALTER TABLE announcements ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
$conn->query("ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");

$tab = $_GET['tab'] ?? 'students';
$fetch = function ($sql) use ($conn) {
    $items = [];
    $r = $conn->query($sql);
    if ($r) while ($row = $r->fetch_assoc()) $items[] = $row;
    return $items;
};

$data = [
    'students' => $fetch("SELECT id, username, email, class_id, deleted_at FROM students WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC"),
    'teachers' => $fetch("SELECT id, full_name, email, subject, deleted_at FROM teachers WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC"),
    'announcements' => $fetch("SELECT id, title, category, date, deleted_at FROM announcements WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC"),
    'messages' => $fetch("SELECT id, first_name, last_name, email, message, deleted_at FROM contact_messages WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC"),
];

$counts = [
    'students' => count($data['students']),
    'teachers' => count($data['teachers']),
    'announcements' => count($data['announcements']),
    'messages' => count($data['messages']),
];

echo json_encode([
    'tab' => $tab,
    'counts' => $counts,
    'total' => array_sum($counts),
    'items' => $data[$tab] ?? [],
]);
