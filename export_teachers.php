<?php
// export_teachers.php
require 'config.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=teachers_export_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, ['ID', 'Full Name', 'Email', 'Phone', 'Subject', 'Gender', 'Joined On']);

// Fetch all teachers from the database
$result = $conn->query("SELECT id, full_name, email, phone, subject, gender, joined_on FROM teachers ORDER BY full_name ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
$conn->close();
exit(); 