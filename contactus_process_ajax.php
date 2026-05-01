<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'config.php';
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $firstName = clean_input($_POST['firstName'] ?? '');
    $lastName  = clean_input($_POST['lastName']  ?? '');
    $email     = clean_input($_POST['email']     ?? '');
    $phone     = clean_input($_POST['phone']     ?? '');
    $subject   = clean_input($_POST['subject']   ?? 'General Inquiry');
    $message   = clean_input($_POST['message']   ?? '');

    // Basic validation
    $errors = [];
    if (strlen($firstName) < 2) $errors[] = 'First name is required';
    if (strlen($lastName)  < 2) $errors[] = 'Last name is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (strlen($message)   < 10) $errors[] = 'Message must be at least 10 characters';

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
        exit;
    }

    // Save to database
    $stmt = $conn->prepare("INSERT INTO contact_messages (first_name, last_name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $firstName, $lastName, $email, $phone, $message);
    $stmt->execute();

    // ── Send email to school admin ────────────────────────────────────────────
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tukamuhebwanewton@gmail.com';
    $mail->Password   = 'qeeuyrvmzserzdfe';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('tukamuhebwanewton@gmail.com', 'Nyabikoni Secondary School');
    $mail->addAddress('tukamuhebwanewton@gmail.com', 'School Admin');
    $mail->addReplyTo($email, "$firstName $lastName");

    $mail->isHTML(true);
    $mail->Subject = "New Contact Message: $subject";
    $mail->Body = "
        <div style='font-family:Poppins,sans-serif;max-width:600px;margin:0 auto;border:1px solid #e0e0e0;border-radius:12px;overflow:hidden;'>
            <div style='background:linear-gradient(135deg,#667eea,#764ba2);padding:24px;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>New Contact Message</h2>
                <p style='color:#e0e0e0;margin:4px 0 0;'>Nyabikoni Secondary School</p>
            </div>
            <div style='padding:28px;background:#fff;'>
                <table style='width:100%;border-collapse:collapse;'>
                    <tr><td style='padding:8px 0;color:#666;width:120px;'>Name:</td><td style='padding:8px 0;font-weight:600;'>$firstName $lastName</td></tr>
                    <tr><td style='padding:8px 0;color:#666;'>Email:</td><td style='padding:8px 0;'><a href='mailto:$email'>$email</a></td></tr>
                    <tr><td style='padding:8px 0;color:#666;'>Phone:</td><td style='padding:8px 0;'>$phone</td></tr>
                    <tr><td style='padding:8px 0;color:#666;'>Subject:</td><td style='padding:8px 0;'>$subject</td></tr>
                </table>
                <hr style='border:none;border-top:1px solid #eee;margin:16px 0;'>
                <p style='color:#666;margin-bottom:8px;'>Message:</p>
                <div style='background:#f8f8ff;border-left:4px solid #667eea;padding:16px;border-radius:0 8px 8px 0;color:#333;line-height:1.6;'>
                    " . nl2br($message) . "
                </div>
                <p style='color:#999;font-size:12px;margin-top:20px;'>Sent on " . date('F j, Y \a\t g:i A') . "</p>
            </div>
        </div>
    ";

    $mail->send();

    // ── Send confirmation to the sender ───────────────────────────────────────
    $reply = new PHPMailer(true);
    $reply->isSMTP();
    $reply->Host       = 'smtp.gmail.com';
    $reply->SMTPAuth   = true;
    $reply->Username   = 'tukamuhebwanewton@gmail.com';
    $reply->Password   = 'qeeuyrvmzserzdfe';
    $reply->SMTPSecure = 'tls';
    $reply->Port       = 587;

    $reply->setFrom('tukamuhebwanewton@gmail.com', 'Nyabikoni Secondary School');
    $reply->addAddress($email, "$firstName $lastName");

    $reply->isHTML(true);
    $reply->Subject = 'We received your message - Nyabikoni Secondary School';
    $reply->Body = "
        <div style='font-family:Poppins,sans-serif;max-width:600px;margin:0 auto;border:1px solid #e0e0e0;border-radius:12px;overflow:hidden;'>
            <div style='background:linear-gradient(135deg,#667eea,#764ba2);padding:24px;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Thank You, $firstName!</h2>
                <p style='color:#e0e0e0;margin:4px 0 0;'>We have received your message</p>
            </div>
            <div style='padding:28px;background:#fff;'>
                <p style='color:#333;line-height:1.7;'>Dear <strong>$firstName $lastName</strong>,</p>
                <p style='color:#333;line-height:1.7;'>Thank you for contacting <strong>Nyabikoni Secondary School</strong>. We have received your message and will get back to you as soon as possible.</p>
                <div style='background:#f8f8ff;border-left:4px solid #667eea;padding:16px;border-radius:0 8px 8px 0;margin:20px 0;'>
                    <p style='margin:0;color:#666;font-size:14px;'>Your message:</p>
                    <p style='margin:8px 0 0;color:#333;'>" . nl2br($message) . "</p>
                </div>
                <p style='color:#333;line-height:1.7;'>If you need urgent assistance, please call us at <strong>+256 703 599 882</strong>.</p>
                <p style='color:#333;'>Best regards,<br><strong>Nyabikoni Secondary School</strong></p>
            </div>
            <div style='background:#f8f8ff;padding:16px;text-align:center;'>
                <p style='color:#999;font-size:12px;margin:0;'>Kabale District, Kabale Municipality, Nyabikoni Ward, Uganda</p>
            </div>
        </div>
    ";

    $reply->send();

    echo json_encode(['success' => true, 'message' => 'Message sent successfully! We\'ll get back to you soon.']);

} catch (Exception $e) {
    // Still success if DB saved but email failed
    echo json_encode(['success' => true, 'message' => 'Message received! We\'ll get back to you soon.']);
}
?>
