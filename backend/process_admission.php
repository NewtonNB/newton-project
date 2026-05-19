<?php
require_once '../shared/config.php';

function show_error_and_exit($msg) {
    echo "<script>alert('" . addslashes($msg) . "'); window.location.href='admission.php';</script>";
    exit();
}

if (isset($_POST['id']) && isset($_POST['action'])) {
    $id = intval($_POST['id']);
    if ($_POST['action'] === 'approve') {
        // Fetch applicant data
        $stmt = $conn->prepare("SELECT name, email, phone FROM admission WHERE id=?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($name, $email, $phone);
        if ($stmt->fetch()) {
            $stmt->close();
            $username = $name;
            $email = $email;
            $phone = $phone;
            $usertype = 'student';
            $password = password_hash('password123', PASSWORD_DEFAULT); // Default password
            $status = 'Active';
            // Try to assign class_id based on class_applying
            $class_id = null;
            if (!empty($student['class_applying'])) {
                $class_stmt = $conn->prepare("SELECT id FROM classes WHERE class_name = ? LIMIT 1");
                if ($class_stmt) {
                    $class_stmt->bind_param('s', $student['class_applying']);
                    $class_stmt->execute();
                    $class_stmt->bind_result($cid);
                    if ($class_stmt->fetch()) {
                        $class_id = $cid;
                    }
                    $class_stmt->close();
                }
            }
            // Check if student already exists in students table
            $check = $conn->prepare("SELECT * FROM students WHERE email=?");
            if (!$check) {
                die("Prepare failed: " . $conn->error);
            }
            $check->bind_param('s', $email);
            $check->execute();
            $check_result = $check->get_result();
            if ($check_result->num_rows > 0) {
                $check->close();
                show_error_and_exit('Student already exists in the system.');
            }
            $check->close();
            // Insert into students table
            if ($class_id) {
                $insert = $conn->prepare("INSERT INTO students (username, email, phone, usertype, password, status, class_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if (!$insert) {
                    die("Prepare failed: " . $conn->error);
                }
                $insert->bind_param('ssssssi', $username, $email, $phone, $usertype, $password, $status, $class_id);
            } else {
                $insert = $conn->prepare("INSERT INTO students (username, email, phone, usertype, password, status) VALUES (?, ?, ?, ?, ?, ?)");
                if (!$insert) {
                    die("Prepare failed: " . $conn->error);
                }
                $insert->bind_param('ssssss', $username, $email, $phone, $usertype, $password, $status);
            }
            if (!$insert->execute()) {
                show_error_and_exit('Failed to add student to students table: ' . $conn->error);
            }
            $insert->close();
            // Send welcome email to student
            require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
            require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
            require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';
            $gmail_address = 'tukamuhebwanewton@gmail.com';
            $gmail_app_password = 'qeeuyrvmzserzdfe';
            $school_name = 'Nyabikoni Secondary School';
            try {
                $mail = new PHPMailer\PHPMailer\PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $gmail_address;
                $mail->Password = $gmail_app_password;
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom($gmail_address, $school_name);
                $mail->addAddress($email);
                $mail->Subject = 'Welcome to Nyabikoni Secondary School';
                $mail->isHTML(true);
                $mail->Body = "<p>Dear $username,</p>"
                    . "<p>Your admission has been approved! You can now log in to the student portal.</p>"
                    . "<p><b>Login Details:</b><br>"
                    . "Username/Email: $email<br>"
                    . "Password: password123</p>"
                    . "<p><b>Important:</b> Please log in and change your password as soon as possible.</p>"
                    . "<p>Best regards,<br>$school_name Admissions</p>";
                $mail->send();
            } catch (Exception $e) {
                // Ignore email failure
            }
        } else {
            $stmt->close();
        }
        // Update status to approved
        $update = $conn->prepare("UPDATE admission SET status='approved' WHERE id=?");
        if (!$update) {
            die("Prepare failed: " . $conn->error);
        }
        $update->bind_param("i", $id);
        $update->execute();
        $update->close();
    } else {
        // Reject action
        $update = $conn->prepare("UPDATE admission SET status='rejected' WHERE id=?");
        if (!$update) {
            die("Prepare failed: " . $conn->error);
        }
        $update->bind_param("i", $id);
        $update->execute();
        $update->close();
    }
}
header("Location: admission.php");
exit(); 