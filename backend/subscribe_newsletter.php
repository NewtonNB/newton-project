<?php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'email_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email'], $_POST['name'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$email = trim($_POST['email']);
$name  = trim($_POST['name']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($name) < 2) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid name and email address.']);
    exit;
}

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Check duplicate
$stmt = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You are already subscribed!']);
    exit;
}
$stmt->close();

// Save subscriber
$stmt = $conn->prepare("INSERT INTO newsletter_subscribers (name, email) VALUES (?, ?)");
$stmt->bind_param('ss', $name, $email);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    exit;
}
$stmt->close();

// Welcome email to subscriber
sendEmail($email, $name,
    'Welcome to Nyabikoni Secondary School Newsletter!',
    "<p>Dear <strong>$name</strong>,</p>
    <p>Thank you for subscribing to the <strong>Nyabikoni Secondary School</strong> newsletter. We're excited to have you!</p>
    <p>Here's what you'll receive:</p>
    <ul style='color:#333;line-height:2;'>
        <li>📢 School news and announcements</li>
        <li>📅 Upcoming events and activities</li>
        <li>🏆 Student achievements and highlights</li>
        <li>📚 Academic updates and exam schedules</li>
    </ul>
    <p>If you ever wish to unsubscribe, simply reply to this email.</p>
    <p>Best regards,<br><strong>Nyabikoni Secondary School</strong></p>"
);

// Notify admin of new subscriber
sendEmail(SCHOOL_EMAIL, SCHOOL_NAME,
    'New Newsletter Subscriber',
    "<p>A new subscriber has joined the newsletter.</p>
    <table style='border-collapse:collapse;margin:16px 0;'>
        " . row('Name', $name) . "
        " . row('Email', $email) . "
        " . row('Date', date('F j, Y g:i A')) . "
    </table>"
);

echo json_encode(['success' => true, 'message' => 'Subscribed successfully! Check your email for a welcome message.']);
?>
