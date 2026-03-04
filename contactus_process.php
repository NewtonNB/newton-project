<?php
// contactus_process.php
require_once 'config.php'; // Assumes this file sets up $conn (mysqli)
require 'vendor/autoload.php'; // Use Composer autoloader for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Helper function to sanitize input
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = clean_input($_POST['firstName'] ?? '');
    $lastName = clean_input($_POST['lastName'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $formMessage = clean_input($_POST['formMessage'] ?? '');

    $errors = [];
    if (empty($firstName)) $errors[] = 'First name is required.';
    if (empty($lastName)) $errors[] = 'Last name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (empty($phone) || !preg_match('/^[0-9]{10,15}$/', $phone)) $errors[] = 'Valid phone number is required.';
    if (empty($formMessage)) $errors[] = 'Message is required.';

    if (count($errors) === 0) {
        // 1. Send email
        $to = 'tukamuhebwanewton@gmail.com';
        $subject = 'New Contact Form Submission';
        $body = "You have received a new message from the contact form:\n\n" .
                "Name: $firstName $lastName\n" .
                "Email: $email\n" .
                "Phone: $phone\n" .
                "Message:\n$formMessage\n";
        $headers = "From: $email\r\nReply-To: $email\r\n";

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tukamuhebwanewton@gmail.com'; // Your Gmail address
            $mail->Password   = 'qeeuyrvmzserzdfe';    // App password (not your Gmail password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom($email, $firstName . ' ' . $lastName);
            $mail->addAddress('tukamuhebwanewton@gmail.com'); // Your receiving email

            // Content
            $mail->isHTML(false);
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body    = $body;

            $mail->send();
            // Email sent successfully

            // Send automatic reply to user
            $autoReply = new PHPMailer(true);
            try {
                $autoReply->isSMTP();
                $autoReply->Host       = 'smtp.gmail.com';
                $autoReply->SMTPAuth   = true;
                $autoReply->Username   = 'tukamuhebwanewton@gmail.com'; // Your Gmail address
                $autoReply->Password   = 'qeeuyrvmzserzdfe';    // App password
                $autoReply->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $autoReply->Port       = 587;

                $autoReply->setFrom('tukamuhebwanewton@gmail.com', 'Nyabikoni Secondary School');
                $autoReply->addAddress($email, $firstName . ' ' . $lastName);
                $autoReply->isHTML(false);
                $autoReply->Subject = 'Thank you for contacting us!';
                $autoReply->Body    = "Dear $firstName,\n\nThank you for contacting Nyabikoni Secondary School! We have received your message and will get back to you soon.\n\nBest regards,\nNyabikoni Secondary School";
                $autoReply->send();
            } catch (Exception $e) {
                // If auto-reply fails, do not block the process
            }

            // 2. Save to database
            $stmt = $conn->prepare("INSERT INTO contact_messages (first_name, last_name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $firstName, $lastName, $email, $phone, $formMessage);
            $stmt->execute();
            $stmt->close();

            // 3. Redirect or show success
            echo '<script>alert("Thank you for contacting us! Your message has been sent."); window.location.href = "contactus.php";</script>';
            exit();
        } catch (Exception $e) {
            // Handle error
            echo '<script>alert("Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '"); window.history.back();</script>';
            exit();
        }
    } else {
        // Show errors
        echo '<script>alert("' . implode('\\n', $errors) . '"); window.history.back();</script>';
        exit();
    }
} else {
    header('Location: contactus.php');
    exit();
} 