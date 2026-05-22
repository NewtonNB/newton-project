<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
if (!$student_id) {
    echo json_encode(['error' => 'student_id required']);
    exit;
}

$stmt = $conn->prepare("SELECT s.id, s.username, s.email, s.phone, c.class_name FROM students s LEFT JOIN classes c ON s.class_id = c.id WHERE s.id = ?");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) {
    echo json_encode(['error' => 'Student not found']);
    exit;
}

$payments = [];
$pstmt = $conn->prepare("SELECT * FROM fees WHERE student_id = ? ORDER BY payment_date DESC");
$pstmt->bind_param('i', $student_id);
$pstmt->execute();
$pres = $pstmt->get_result();
$total_paid = 0;
if ($pres) {
    while ($row = $pres->fetch_assoc()) {
        $total_paid += (float)($row['amount_paid'] ?? 0);
        $payments[] = $row;
    }
}

echo json_encode(['student' => $student, 'payments' => $payments, 'total_paid' => $total_paid]);
