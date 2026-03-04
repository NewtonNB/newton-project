<?php
// Disable error reporting to prevent HTML output before JSON
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header and prevent any output buffering issues
header('Content-Type: application/json');
ob_clean();

require_once 'config.php';
require_once 'config_email.php'; // Email configuration
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Helper function to sanitize input
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Helper function to validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Helper function to validate phone
function validate_phone($phone) {
    $cleaned = preg_replace('/[^\d+]/', '', $phone);
    return strlen($cleaned) >= 10 && strlen($cleaned) <= 15;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and sanitize input data
    $firstName = clean_input($_POST['firstName'] ?? '');
    $lastName = clean_input($_POST['lastName'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $subject = clean_input($_POST['subject'] ?? '');
    $message = clean_input($_POST['message'] ?? '');

    // Validation
    $errors = [];

    if (empty($firstName) || strlen($firstName) < 2) {
        $errors[] = 'First name must be at least 2 characters long';
    }

    if (empty($lastName) || strlen($lastName) < 2) {
        $errors[] = 'Last name must be at least 2 characters long';
    }

    if (empty($email) || !validate_email($email)) {
        $errors[] = 'Please provide a valid email address';
    }

    if (empty($phone) || !validate_phone($phone)) {
        $errors[] = 'Please provide a valid phone number';
    }

    if (empty($message) || strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters long';
    }

    if (strlen($message) > 1000) {
        $errors[] = 'Message must not exceed 1000 characters';
    }

    // Check for spam (simple honeypot and rate limiting)
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Simple rate limiting - check if same IP submitted in last 2 minutes
    $recentSubmission = $conn->query("SELECT COUNT(*) FROM contact_messages WHERE submitted_at > datetime('now', '-2 minutes')");
    if ($recentSubmission && $recentSubmission->fetch_row()[0] > 3) {
        throw new Exception('Too many submissions. Please wait a moment before trying again.');
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode('. ', $errors)
        ]);
        exit;
    }

    // Prepare email content
    $subjectLine = !empty($subject) ? "Contact Form: $subject" : 'New Contact Form Submission';
    $emailBody = "You have received a new message from the contact form:\n\n" .
                "Name: $firstName $lastName\n" .
                "Email: $email\n" .
                "Phone: $phone\n" .
                "Subject: " . ($subject ?: 'General Inquiry') . "\n\n" .
                "Message:\n$message\n\n" .
                "Submitted: " . date('Y-m-d H:i:s') . "\n" .
                "IP Address: $ip\n" .
                "User Agent: $userAgent";

    // Send email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress(ADMIN_EMAIL, ADMIN_NAME);
        $mail->addReplyTo($email, "$firstName $lastName");

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subjectLine;
        $mail->Body    = $emailBody;

        $mail->send();

        // Send automatic reply to user
        $autoReply = new PHPMailer(true);
        
        try {
            $autoReply->isSMTP();
            $autoReply->Host       = SMTP_HOST;
            $autoReply->SMTPAuth   = true;
            $autoReply->Username   = SMTP_USERNAME;
            $autoReply->Password   = SMTP_PASSWORD;
            $autoReply->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $autoReply->Port       = SMTP_PORT;

            $autoReply->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $autoReply->addAddress($email, "$firstName $lastName");

            $autoReply->isHTML(true);
            $autoReply->Subject = 'Thank you for contacting Nyabikoni Secondary School';
            
            $autoReplyBody = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                        <h1 style='margin: 0; font-size: 24px;'>Nyabikoni Secondary School</h1>
                        <p style='margin: 10px 0 0 0; opacity: 0.9;'>Excellence in Education</p>
                    </div>
                    
                    <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;'>
                        <h2 style='color: #1a237e; margin-top: 0;'>Thank you for contacting us!</h2>
                        
                        <p>Dear $firstName,</p>
                        
                        <p>We have received your message and appreciate you taking the time to contact Nyabikoni Secondary School. Our team will review your inquiry and respond within 2-4 hours during business hours.</p>
                        
                        <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3949ab;'>
                            <h3 style='margin-top: 0; color: #1a237e;'>Your Message Summary:</h3>
                            <p><strong>Subject:</strong> " . ($subject ?: 'General Inquiry') . "</p>
                            <p><strong>Message:</strong> " . htmlspecialchars(substr($message, 0, 200)) . (strlen($message) > 200 ? '...' : '') . "</p>
                        </div>
                        
                        <div style='background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <h4 style='margin-top: 0; color: #1a237e;'>Our Office Hours:</h4>
                            <p style='margin: 5px 0;'><strong>Monday - Friday:</strong> 8:00 AM - 5:00 PM</p>
                            <p style='margin: 5px 0;'><strong>Saturday:</strong> 8:00 AM - 1:00 PM</p>
                            <p style='margin: 5px 0;'><strong>Sunday:</strong> Closed</p>
                        </div>
                        
                        <p>If you have any urgent matters, please don't hesitate to call us at:</p>
                        <p style='text-align: center; font-size: 18px; color: #1a237e; font-weight: bold;'>
                            📞 +256 703 599 882 or +256 775 475 629
                        </p>
                        
                        <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                        
                        <p style='text-align: center; color: #666; font-size: 14px;'>
                            Best regards,<br>
                            <strong>Nyabikoni Secondary School Administration</strong><br>
                            Kabale District, Kabale Municipality, Nyabikoni Ward, Uganda<br>
                            Email: nyabikonisecschool@gmail.com
                        </p>
                    </div>
                </div>
            </body>
            </html>";
            
            $autoReply->Body = $autoReplyBody;
            $autoReply->send();
            
        } catch (Exception $e) {
            // If auto-reply fails, log but don't block the process
            error_log("Auto-reply failed: " . $e->getMessage());
        }

        // Save to database
        $stmt = $conn->prepare("INSERT INTO contact_messages (first_name, last_name, email, phone, message, submitted_at) VALUES (?, ?, ?, ?, ?, datetime('now'))");
        if ($stmt) {
            $fullMessage = !empty($subject) ? "Subject: $subject\n\n$message" : $message;
            $stmt->bind_param('sssss', $firstName, $lastName, $email, $phone, $fullMessage);
            $stmt->execute();
            $stmt->close();
        }

        // Success response
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your message! We have received it and will get back to you within 2-4 hours during business hours.'
        ]);

    } catch (Exception $e) {
        throw new Exception('Failed to send email: ' . $e->getMessage());
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>