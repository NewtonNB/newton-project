<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header('Location: ../frontend/login.html');
    exit;
}

require_once '../shared/config.php';

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("ALTER TABLE exam_schedule ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
    $conn->query("UPDATE exam_schedule SET deleted_at = NOW() WHERE id = $delete_id");
}

header('Location: ../frontend/exam_schedule.html');
exit;
