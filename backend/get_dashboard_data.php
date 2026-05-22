<?php
/**
 * get_dashboard_data.php
 * Returns all dashboard data as JSON for the HTML admin dashboard.
 * Requires an active admin session.
 */
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../shared/config.php';

$studentCount   = (int)($conn->query("SELECT COUNT(*) FROM students WHERE deleted_at IS NULL AND usertype='student'")->fetch_row()[0] ?? 0);
$teacherCount   = (int)($conn->query("SELECT COUNT(*) FROM teachers WHERE deleted_at IS NULL")->fetch_row()[0] ?? 0);
$classCount     = (int)($conn->query("SELECT COUNT(*) FROM classes")->fetch_row()[0] ?? 0);
$feeCollected   = (float)($conn->query("SELECT SUM(amount_paid) FROM fees")->fetch_row()[0] ?? 0);

$totalEvents              = (int)($conn->query("SELECT COUNT(*) FROM announcements WHERE category = 'Event'")->fetch_row()[0] ?? 0);
$totalEventRegistrations  = (int)($conn->query("SELECT COUNT(*) FROM event_registrations")->fetch_row()[0] ?? 0);
$upcomingEvents           = (int)($conn->query("SELECT COUNT(*) FROM announcements WHERE category = 'Event' AND date >= date('now')")->fetch_row()[0] ?? 0);
$trashCount               = (int)($conn->query("SELECT COUNT(*) FROM contact_messages WHERE deleted_at IS NOT NULL")->fetch_row()[0] ?? 0);

// Recent event registrations
$recentRegs = [];
$result = $conn->query("SELECT er.*, a.title as event_title FROM event_registrations er LEFT JOIN announcements a ON er.event_id = a.id ORDER BY er.created_at DESC LIMIT 10");
if ($result) while ($row = $result->fetch_assoc()) $recentRegs[] = $row;

// Popular events
$popularEvents = [];
$result = $conn->query("SELECT a.title, a.date, COUNT(er.id) as registration_count FROM announcements a LEFT JOIN event_registrations er ON a.id = er.event_id WHERE a.category = 'Event' GROUP BY a.id, a.title, a.date ORDER BY registration_count DESC, a.date DESC LIMIT 5");
if ($result) while ($row = $result->fetch_assoc()) $popularEvents[] = $row;

// Recent messages
$recentMessages = [];
$result = $conn->query("SELECT * FROM contact_messages WHERE deleted_at IS NULL ORDER BY submitted_at DESC LIMIT 5");
if ($result) while ($row = $result->fetch_assoc()) $recentMessages[] = $row;

echo json_encode([
    'studentCount'            => $studentCount,
    'teacherCount'            => $teacherCount,
    'classCount'              => $classCount,
    'feeCollected'            => $feeCollected,
    'totalEvents'             => $totalEvents,
    'totalEventRegistrations' => $totalEventRegistrations,
    'upcomingEvents'          => $upcomingEvents,
    'trashCount'              => $trashCount,
    'recentRegistrations'     => $recentRegs,
    'popularEvents'           => $popularEvents,
    'recentMessages'          => $recentMessages,
]);
