<?php
/**
 * get_statistics.php
 * Returns JSON statistics for the homepage hero section.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../shared/config.php';

$totalStudents = 0;
$totalTeachers = 0;
$graduatedStudents = 0;

$studentQuery = $conn->query("SELECT COUNT(*) as count FROM students WHERE usertype='student' AND status='Active'");
if ($studentQuery) $totalStudents = $studentQuery->fetch_assoc()['count'];

$teacherQuery = $conn->query("SELECT COUNT(*) as count FROM teachers WHERE status='Active'");
if ($teacherQuery) $totalTeachers = $teacherQuery->fetch_assoc()['count'];

$graduatedQuery = $conn->query("SELECT COUNT(*) as count FROM students WHERE class_id IN (SELECT id FROM classes WHERE class_name='S6') AND status='Graduated'");
if ($graduatedQuery) $graduatedStudents = $graduatedQuery->fetch_assoc()['count'];

echo json_encode([
    'totalStudents'    => (int)$totalStudents,
    'totalTeachers'    => (int)$totalTeachers,
    'graduatedStudents'=> (int)$graduatedStudents,
    'activityCount'    => 12
]);
