<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../shared/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$student_id = isset($_POST['assign_student_id']) ? intval($_POST['assign_student_id']) : 0;
$class_id = isset($_POST['assign_class_id']) ? intval($_POST['assign_class_id']) : 0;

if (!$student_id || !$class_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}

$ok = $conn->query("UPDATE students SET class_id = $class_id WHERE id = $student_id");
echo json_encode(['success' => (bool)$ok]);
