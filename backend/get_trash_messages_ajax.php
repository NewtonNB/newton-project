<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$conn->query("ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
$messages = [];
$r = $conn->query("SELECT * FROM contact_messages WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
if ($r) while ($row = $r->fetch_assoc()) $messages[] = $row;

echo json_encode(['messages' => $messages, 'count' => count($messages)]);
