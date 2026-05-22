<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ../frontend/login.html');
    exit;
}
require_once '../shared/config.php';

$id = isset($_GET['delete']) ? (int)$_GET['delete'] : 0;
$level = $_GET['level'] ?? 'olevel';
if ($id && in_array($level, ['olevel', 'alevel'], true)) {
    $table = $level . '_subjects';
    $conn->query("DELETE FROM $table WHERE id = $id");
}
$tab = in_array($level, ['olevel', 'alevel'], true) ? $level : 'olevel';
header('Location: ../frontend/manage_subjects.html?tab=' . $tab);
exit;
