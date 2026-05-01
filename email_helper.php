<?php
/**
 * Central Email Helper
 * Usage: sendEmail($to, $toName, $subject, $htmlBody)
 */

require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('SCHOOL_EMAIL',  'tukamuhebwanewton@gmail.com');
define('SCHOOL_NAME',   'Nyabikoni Secondary School');
define('SMTP_PASS',     'qeeuyrvmzserzdfe');

function sendEmail($to, $toName, $subject, $htmlBody, $replyTo = null, $replyToName = null) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = SCHOOL_EMAIL;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom(SCHOOL_EMAIL, SCHOOL_NAME);
        $mail->addAddress($to, $toName);
        if ($replyTo) $mail->addReplyTo($replyTo, $replyToName ?? $replyTo);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = emailWrapper($subject, $htmlBody);
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email failed to $to: " . $e->getMessage());
        return false;
    }
}

// Wraps content in a branded HTML email template
function emailWrapper($title, $content) {
    return "
    <div style='font-family:Poppins,Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #e0e0e0;border-radius:12px;overflow:hidden;'>
        <div style='background:linear-gradient(135deg,#667eea,#764ba2);padding:24px;text-align:center;'>
            <h2 style='color:#fff;margin:0;font-size:1.4rem;'>$title</h2>
            <p style='color:#e0e0e0;margin:4px 0 0;font-size:0.9rem;'>Nyabikoni Secondary School</p>
        </div>
        <div style='padding:28px;background:#fff;line-height:1.7;color:#333;'>
            $content
        </div>
        <div style='background:#f8f8ff;padding:14px;text-align:center;'>
            <p style='color:#999;font-size:11px;margin:0;'>Kabale District, Kabale Municipality, Nyabikoni Ward, Uganda &nbsp;|&nbsp; +256 703 599 882</p>
        </div>
    </div>";
}

function row($label, $value) {
    return "<tr>
        <td style='padding:7px 0;color:#666;width:140px;font-size:14px;'>$label</td>
        <td style='padding:7px 0;font-weight:600;font-size:14px;'>$value</td>
    </tr>";
}
?>
