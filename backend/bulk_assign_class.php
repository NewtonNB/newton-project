<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL); ini_set('display_errors', 1);
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit();
}
require_once '../shared/config.php';
$student_ids = $_POST['student_ids'] ?? [];
$class_id = intval($_POST['class_id'] ?? 0);
if (!$student_ids || !$class_id) {
    echo json_encode(['success' => false, 'error' => 'Please select students and a class.']);
    exit();
}
$ids = array_filter(array_map('intval', $student_ids));
if (empty($ids)) {
    echo json_encode(['success' => false, 'error' => 'No valid students selected.']);
    exit();
}
$id_placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "UPDATE students SET class_id=? WHERE id IN ($id_placeholders)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit();
}
$types = str_repeat('i', count($ids) + 1);
$params = array_merge([$class_id], $ids);
$bind_names[] = $types;
foreach ($params as $key => $value) {
    $bind_names[] = &$params[$key];
}
call_user_func_array([$stmt, 'bind_param'], $bind_names);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
}
$stmt->close();
$conn->close(); 