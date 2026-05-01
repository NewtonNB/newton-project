<?php
header('Content-Type: application/json');
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$event_id = trim($_POST['event_id'] ?? '');
$website = trim($_POST['website'] ?? ''); // honeypot
if ($website) {
    echo json_encode(['success' => false, 'error' => 'Spam detected.']);
    exit;
}
if (!$name || !$email || !$phone || !$event_id) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address.']);
    exit;
}
if (!preg_match('/^\+?[0-9\-\s]{7,}$/', $phone)) {
    echo json_encode(['success' => false, 'error' => 'Invalid phone number.']);
    exit;
}
$stmt = $conn->prepare('INSERT INTO event_registrations (event_id, name, email, phone, created_at) VALUES (?, ?, ?, ?, datetime(\'now\'))');
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
    exit;
}
$stmt->bind_param('ssss', $event_id, $name, $email, $phone);
if ($stmt->execute()) {
    // Fetch event title
    $event_title = '';
    $event_stmt = $conn->prepare('SELECT title FROM announcements WHERE id = ?');
    if ($event_stmt) {
        $event_stmt->bind_param('s', $event_id);
        $event_stmt->execute();
        $event_stmt->bind_result($event_title);
        $event_stmt->fetch();
        $event_stmt->close();
    }
    // Send emails using PHPMailer
    require_once 'email_helper.php';

    // Notify admin
    sendEmail(SCHOOL_EMAIL, SCHOOL_NAME,
        "New Event Registration: $event_title",
        "<p>A new registration has been received.</p>
        <table style='border-collapse:collapse;margin:16px 0;'>
            " . row('Event', $event_title) . "
            " . row('Name', $name) . "
            " . row('Email', $email) . "
            " . row('Phone', $phone) . "
            " . row('Time', date('F j, Y g:i A')) . "
        </table>"
    );

    // Confirm to registrant
    sendEmail($email, $name,
        "Registration Confirmed: $event_title",
        "<p>Dear <strong>$name</strong>,</p>
        <p>You have successfully registered for:</p>
        <p style='font-size:1.1rem;font-weight:700;color:#764ba2;'>$event_title</p>
        <table style='border-collapse:collapse;margin:16px 0;'>
            " . row('Name', $name) . "
            " . row('Email', $email) . "
            " . row('Phone', $phone) . "
        </table>
        <p>We look forward to seeing you!</p>
        <p>Best regards,<br><strong>Nyabikoni Secondary School</strong></p>",
        SCHOOL_EMAIL, SCHOOL_NAME
    );

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Could not save registration.']);
}
$stmt->close(); 