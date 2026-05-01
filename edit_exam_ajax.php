<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $exam_name = trim($_POST['exam_name']);
    $class_id = intval($_POST['class_id']);
    $subject_name = trim($_POST['subject_name']);
    $exam_date = $_POST['exam_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room = trim($_POST['room']);
    $term = intval($_POST['term']);
    $year = intval($_POST['year']);
    $instructions = trim($_POST['instructions']);
    
    if ($exam_name && $class_id && $subject_name && $exam_date && $start_time && $end_time && $term && $year) {
        $stmt = $conn->prepare("UPDATE exam_schedule SET exam_name=?, class_id=?, subject_name=?, exam_date=?, start_time=?, end_time=?, room=?, term=?, year=?, instructions=? WHERE id=?");
        $stmt->bind_param('sisssssisi', $exam_name, $class_id, $subject_name, $exam_date, $start_time, $end_time, $room, $term, $year, $instructions, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'All required fields must be filled']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
