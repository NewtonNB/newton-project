<?php
// subscribe_newsletter.php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once 'vendor/phpmailer/phpmailer/src/SMTP.php';
require_once 'vendor/phpmailer/phpmailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email']) || !isset($_POST['name'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}
$email = trim($_POST['email']);
$name = trim($_POST['name']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($name) < 2) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid name and email address.']);
    exit;
}
// Create table if not exists (add name column)
$conn->query("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
// Check for duplicate
$stmt = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You are already subscribed!']);
    exit;
}
$stmt->close();
// Insert new subscriber
$stmt = $conn->prepare("INSERT INTO newsletter_subscribers (name, email) VALUES (?, ?)");
$stmt->bind_param('ss', $name, $email);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}
$stmt->close();
// Send welcome email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Change to your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com'; // Change to your email
    $mail->Password = 'your_email_password'; // Change to your email password or app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('your_email@gmail.com', 'Nyabikoni Secondary School');
    $mail->addAddress($email, $name);
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to Nyabikoni Secondary School Newsletter!';
    $mail->Body = '<div style="font-family:Poppins,sans-serif;max-width:600px;margin:auto;background:#f8fafc;padding:32px 24px;border-radius:18px;box-shadow:0 4px 24px rgba(44,90,160,0.07);">
    <div style="text-align:center;margin-bottom:18px;">
      <img src="https://nyabikoni.ac.ug/nyabzgallery/nyabzlogo.png" alt="Nyabikoni Logo" style="max-width:120px;margin-bottom:10px;">
    </div>
    <h2 style="color:#2c5aa0;">Welcome, ' . htmlspecialchars($name) . '!</h2>
    <p style="font-size:1.1em;color:#222;">Thank you for subscribing to the Nyabikoni Secondary School newsletter.<br>We are excited to have you as part of our community!</p>
    <ul style="color:#10b981;font-size:1.05em;margin:18px 0 18px 18px;">
      <li>School news and announcements</li>
      <li>Upcoming events and activities</li>
      <li>Exclusive updates for our subscribers</li>
    </ul>
    <p style="color:#444;">If you have any questions, feel free to reply to this email or contact us at <a href="mailto:info@nyabikoni.ac.ug">info@nyabikoni.ac.ug</a>.</p>
    <div style="margin-top:24px;text-align:center;color:#6b7280;font-size:0.98em;">Nyabikoni Secondary School<br>Kabale, Uganda<br><a href="https://nyabikoni.ac.ug" style="color:#2c5aa0;">nyabikoni.ac.ug</a></div>
    </div>';
    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Subscription successful! Please check your email for a welcome message.']);
} catch (Exception $e) {
    echo json_encode(['success' => true, 'message' => 'Subscribed, but failed to send welcome email.']);
} 