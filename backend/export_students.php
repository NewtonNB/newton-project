<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    die('Unauthorized');
}
require_once '../shared/config.php';
$filename = 'students_export_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
$class_filter = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
$level_filter = isset($_POST['level']) ? $_POST['level'] : '';
$status_filter = isset($_POST['status']) ? $_POST['status'] : '';
$class_filter_sql = $class_filter ? " AND class_id = $class_filter" : '';
$level_filter_sql = $level_filter ? " AND class_id IN (SELECT id FROM classes WHERE level = '" . $conn->real_escape_string($level_filter) . "')" : '';
$status_filter_sql = $status_filter ? " AND status = '" . $conn->real_escape_string($status_filter) . "'" : '';
$sql = "SELECT s.*, c.class_name, c.level FROM students s LEFT JOIN classes c ON s.class_id = c.id WHERE 1 $class_filter_sql $level_filter_sql $status_filter_sql ORDER BY s.username ASC";
$result = $conn->query($sql);
$out = fopen('php://output', 'w');
fputcsv($out, ['Username', 'Full Name', 'Email', 'Phone', 'User Type', 'Status', 'Class', 'Level']);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($out, [
            $row['username'],
            $row['full_name'] ?? '',
            $row['email'],
            $row['phone'],
            $row['usertype'],
            $row['status'],
            $row['class_name'] ?? '',
            $row['level'] ?? ''
        ]);
    }
}
fclose($out);
$conn->close(); 