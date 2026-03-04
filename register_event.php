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
    // Send email notification to admin
    $admin_email = 'tukamuhebwanewton@gmail.com';
    $subject = "New Event Registration: $event_title";
    $body = "A new registration has been received for the event: $event_title\n\n" .
            "Name: $name\nEmail: $email\nPhone: $phone\nTime: " . date('Y-m-d H:i:s') . "\n";
    @mail($admin_email, $subject, $body);
    // Send HTML confirmation email to registrant
    $user_subject = "Registration Confirmed: $event_title";
    $user_body = "<html><body style='font-family:Poppins,Arial,sans-serif;background:#f8f8ff;padding:24px;'><div style='max-width:520px;margin:auto;background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(102,126,234,0.10);padding:32px;'>"
        . "<h2 style='color:#667eea;'>Thank you for registering!</h2>"
        . "<p style='font-size:1.1rem;'>Dear <strong>$name</strong>,<br>"
        . "You have successfully registered for the event:<br><span style='color:#764ba2;font-weight:700;'>$event_title</span></p>"
        . "<table style='margin:18px 0 24px 0;font-size:1.05rem;'>"
        . "<tr><td style='padding:4px 12px 4px 0;color:#764ba2;'>Name:</td><td>" . htmlspecialchars($name) . "</td></tr>"
        . "<tr><td style='padding:4px 12px 4px 0;color:#764ba2;'>Email:</td><td>" . htmlspecialchars($email) . "</td></tr>"
        . "<tr><td style='padding:4px 12px 4px 0;color:#764ba2;'>Phone:</td><td>" . htmlspecialchars($phone) . "</td></tr>"
        . "</table>"
        . "<p style='font-size:1.05rem;'>We look forward to seeing you!<br><br>Best regards,<br><span style='color:#667eea;font-weight:700;'>Nyabikoni Secondary School</span></p>"
        . "</div></body></html>";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Nyabikoni Secondary School <no-reply@nyabikoni.ac.ug>\r\n";
    if (!@mail($email, $user_subject, $user_body, $headers)) {
        // Optionally log or handle email failure
        // error_log('Failed to send confirmation email to ' . $email);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Could not save registration.']);
}
$stmt->close(); 