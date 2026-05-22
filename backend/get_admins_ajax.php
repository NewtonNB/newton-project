<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$admins = [];
$r = $conn->query("SELECT * FROM admins ORDER BY id DESC");
if ($r) while ($row = $r->fetch_assoc()) {
    $admins[] = [
        'id' => (int)$row['id'],
        'username' => $row['username'] ?? '',
        'email' => $row['email'] ?? '',
        'phone' => $row['phone'] ?? '',
        'status' => $row['status'] ?? '',
        'created_at' => $row['created_at'] ?? '',
    ];
}
echo json_encode(['admins' => $admins]);
