<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$classes = [];
$r = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name ASC");
if ($r) while ($row = $r->fetch_assoc()) {
    $classes[] = ['id' => (int)$row['id'], 'class_name' => $row['class_name']];
}

$students = [];
$attendance_map = [];
$history = [];

if ($class_id) {
    $sr = $conn->query("SELECT id, username as full_name FROM students WHERE class_id = $class_id ORDER BY username ASC");
    if ($sr) while ($row = $sr->fetch_assoc()) {
        $students[] = ['id' => (int)$row['id'], 'full_name' => $row['full_name']];
    }
    $d = $conn->real_escape_string($date);
    $ar = $conn->query("SELECT student_id, status FROM attendance WHERE class_id = $class_id AND date = '$d'");
    if ($ar) while ($row = $ar->fetch_assoc()) {
        $attendance_map[(int)$row['student_id']] = $row['status'];
    }
    $hr = $conn->query("SELECT date, SUM(status='Present') as present, SUM(status='Absent') as absent, SUM(status='Late') as late, SUM(status='Excused') as excused FROM attendance WHERE class_id = $class_id GROUP BY date ORDER BY date DESC LIMIT 7");
    if ($hr) while ($row = $hr->fetch_assoc()) $history[] = $row;
}

echo json_encode([
    'classes' => $classes,
    'students' => $students,
    'attendance_map' => $attendance_map,
    'history' => $history,
    'filters' => ['class_id' => $class_id, 'date' => $date],
]);
