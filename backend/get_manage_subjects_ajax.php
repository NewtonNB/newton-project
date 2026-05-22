<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$olevel = [];
$alevel = [];
$or = $conn->query("SELECT * FROM olevel_subjects ORDER BY id DESC");
if ($or) while ($row = $or->fetch_assoc()) {
    $olevel[] = ['id' => (int)$row['id'], 'subject_name' => $row['subject_name']];
}
$ar = $conn->query("SELECT * FROM alevel_subjects ORDER BY id DESC");
if ($ar) while ($row = $ar->fetch_assoc()) {
    $alevel[] = ['id' => (int)$row['id'], 'subject_name' => $row['subject_name']];
}

echo json_encode(['olevel' => $olevel, 'alevel' => $alevel]);
