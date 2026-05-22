<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$conn->query("ALTER TABLE announcements ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
$result = $conn->query("SELECT * FROM announcements WHERE deleted_at IS NULL ORDER BY created_at DESC");
$list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $list[] = $row;
    }
}
$trashCount = (int)($conn->query("SELECT COUNT(*) FROM announcements WHERE deleted_at IS NOT NULL")->fetch_row()[0] ?? 0);

echo json_encode(['announcements' => $list, 'trashCount' => $trashCount]);
