<?php
require_once 'admin_require.php';
student_require_json();
require_once '../shared/config.php';

$username = $_SESSION['username'];
$student = [];
$r = $conn->query("SELECT * FROM students WHERE username='" . $conn->real_escape_string($username) . "' LIMIT 1");
if ($r && $row = $r->fetch_assoc()) {
    $student = $row;
}

$profilePic = !empty($student['profile_pic']) ? $student['profile_pic'] : 'nyabzgallery/student3.jpg';

$classes = [];
$tc = $conn->query("SHOW TABLES LIKE 'student_subjects'");
if ($tc && $tc->num_rows) {
    $cr = $conn->query("SELECT subject_name FROM student_subjects WHERE student_username='" . $conn->real_escape_string($username) . "'");
    if ($cr) while ($row = $cr->fetch_assoc()) $classes[] = $row['subject_name'];
}
if (!$classes) $classes = ['Mathematics', 'English', 'Biology', 'History'];

$upcomingAssignments = [];
$tc = $conn->query("SHOW TABLES LIKE 'assignments'");
if ($tc && $tc->num_rows) {
    $ar = $conn->query("SELECT subject, title, due_date FROM assignments WHERE student_username='" . $conn->real_escape_string($username) . "' AND due_date >= date('now') ORDER BY due_date ASC LIMIT 5");
    if ($ar) while ($row = $ar->fetch_assoc()) {
        $upcomingAssignments[] = ['subject' => $row['subject'], 'title' => $row['title'], 'due' => $row['due_date']];
    }
}
if (!$upcomingAssignments) {
    $upcomingAssignments = [
        ['subject' => 'Mathematics', 'title' => 'Algebra Homework', 'due' => '2024-07-10'],
        ['subject' => 'Biology', 'title' => 'Lab Report', 'due' => '2024-07-12'],
    ];
}

$recentGrades = [];
$tc = $conn->query("SHOW TABLES LIKE 'grades'");
if ($tc && $tc->num_rows) {
    $gr = $conn->query("SELECT subject, grade FROM grades WHERE student_username='" . $conn->real_escape_string($username) . "' ORDER BY date_recorded DESC LIMIT 5");
    if ($gr) while ($row = $gr->fetch_assoc()) {
        $recentGrades[] = ['subject' => $row['subject'], 'grade' => $row['grade']];
    }
}
if (!$recentGrades) {
    $recentGrades = [
        ['subject' => 'English', 'grade' => 'A'],
        ['subject' => 'Mathematics', 'grade' => 'B+'],
    ];
}

$feeBalance = 'UGX 120,000';
$tc = $conn->query("SHOW TABLES LIKE 'fees'");
if ($tc && $tc->num_rows && !empty($student['id'])) {
    $fr = $conn->query("SELECT balance FROM fees WHERE student_id=" . (int)$student['id'] . " ORDER BY id DESC LIMIT 1");
    if ($fr && $fr->num_rows) {
        $feeBalance = 'UGX ' . number_format((float)$fr->fetch_assoc()['balance']);
    }
}

echo json_encode([
    'username' => $username,
    'full_name' => $student['full_name'] ?? $student['username'] ?? $username,
    'profilePic' => $profilePic,
    'classes' => $classes,
    'upcomingAssignments' => $upcomingAssignments,
    'recentGrades' => $recentGrades,
    'feeBalance' => $feeBalance,
]);
