<?php
// reply_contact.php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once 'config.php';
require 'vendor/autoload.php'; // For PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$contact = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare('SELECT * FROM contact_messages WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contact = $result->fetch_assoc();
    $stmt->close();
}
if (!$contact) {
    echo '<p style="color:red;">Contact not found.</p>';
    exit();
}

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $contact['email'];
    $name = $contact['first_name'] . ' ' . $contact['last_name'];
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if (!$subject || !$message) {
        $error = 'Subject and message are required.';
    } else {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tukamuhebwanewton@gmail.com';
            $mail->Password   = 'qeeuyrvmzserzdfe';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setFrom('tukamuhebwanewton@gmail.com', 'Nyabikoni Secondary School');
            $mail->addAddress($to, $name);
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $success = 'Reply sent successfully!';
        } catch (Exception $e) {
            $error = 'Failed to send reply: ' . $mail->ErrorInfo;
        }
    }
}

// Helper for avatar initials
function get_initials($first, $last) {
    return strtoupper(mb_substr($first,0,1).mb_substr($last,0,1));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Contact</title>
    <link rel="stylesheet" href="admin_css.php">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f4f8fb; font-family: 'Inter', 'Poppins', Arial, sans-serif; }
        .reply-card {
            max-width: 480px;
            margin: 48px auto;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(52,152,219,0.13), 0 1.5px 4px rgba(0,0,0,0.04);
            padding: 38px 32px 32px 32px;
            animation: fadeInUp 0.7s cubic-bezier(.4,2,.6,1);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .reply-header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 18px;
        }
        .avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db 0%, #6dd5fa 100%);
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(52,152,219,0.13);
            letter-spacing: 1px;
        }
        .contact-info {
            flex: 1;
        }
        .contact-info h2 {
            color: #3498db;
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 0 2px 0;
        }
        .contact-info p {
            margin: 0;
            color: #34495e;
            font-size: 0.98rem;
        }
        .msg-success {
            color: #27ae60;
            text-align: center;
            margin-bottom: 18px;
            font-weight: 600;
            font-size: 1.08rem;
        }
        .msg-error {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 18px;
            font-weight: 600;
            font-size: 1.08rem;
        }
        .reply-form {
            margin-top: 18px;
        }
        .form-group {
            margin-bottom: 22px;
            position: relative;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 16px 14px 16px 14px;
            border: 1.5px solid #e3eaf1;
            border-radius: 10px;
            font-size: 1.08rem;
            background-color: #f7fbfd;
            color: #222;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: inherit;
            resize: none;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: #3498db;
            background: #fff;
            box-shadow: 0 2px 8px rgba(52,152,219,0.10);
        }
        .form-group label {
            position: absolute;
            left: 18px;
            top: 14px;
            background: #fff;
            padding: 0 4px;
            color: #2980b9;
            font-size: 1rem;
            font-weight: 500;
            pointer-events: none;
            transition: 0.2s;
        }
        .form-group input:focus + label,
        .form-group input:not(:placeholder-shown) + label,
        .form-group textarea:focus + label,
        .form-group textarea:not(:placeholder-shown) + label {
            top: -13px;
            left: 10px;
            font-size: 0.92rem;
            color: #3498db;
            background: #fff;
        }
        .send-btn {
            background: linear-gradient(90deg, #3498db 0%, #6dd5fa 100%);
            color: white;
            border: none;
            padding: 15px 0;
            border-radius: 10px;
            font-size: 1.13rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 6px;
            transition: background 0.3s, transform 0.15s;
            box-shadow: 0 2px 8px rgba(52,152,219,0.08);
            letter-spacing: 0.5px;
        }
        .send-btn:hover {
            background: linear-gradient(90deg, #2980b9 0%, #3498db 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .back-link {
            margin-top: 22px;
            text-align: center;
        }
        .back-link a {
            color: #3498db;
            text-decoration: underline;
            font-size: 1rem;
        }
    </style>
</head>
<body>
<div class="reply-card">
    <div class="reply-header">
        <div class="avatar"><?= get_initials($contact['first_name'], $contact['last_name']) ?></div>
        <div class="contact-info">
            <h2><?= htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) ?></h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($contact['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($contact['phone']) ?></p>
        </div>
    </div>
    <?php if ($success): ?><div class="msg-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
    <form method="POST" class="reply-form" autocomplete="off">
        <div class="form-group">
            <input type="text" name="subject" id="subject" required placeholder=" " autocomplete="off">
            <label for="subject">Subject</label>
        </div>
        <div class="form-group">
            <textarea name="message" id="message" rows="6" required placeholder=" " autocomplete="off"></textarea>
            <label for="message">Message</label>
        </div>
        <button type="submit" class="send-btn">Send Reply</button>
    </form>
    <div class="back-link">
        <a href="admission.php">&larr; Back to Contact Messages</a>
    </div>
</div>
</body>
</html> 