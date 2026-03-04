<?php

session_start();
$host = "localhost";
$user = "root";
$password = "1234";
$db = "schoolproject"; 

$data = mysqli_connect($host, $user, $password, $db);

if ($data === false) {
    die("Connection error: " . mysqli_connect_error());
}

if (
    isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone']) &&
    isset($_POST['dob']) && isset($_POST['gender']) && isset($_POST['address']) &&
    isset($_POST['nationality']) && isset($_POST['religion']) && isset($_POST['parent_name']) && isset($_POST['parent_phone']) && isset($_POST['class_applying'])
) {
    // Sanitize user input
    $data_name = mysqli_real_escape_string($data, $_POST['name']);
    $data_dob = mysqli_real_escape_string($data, $_POST['dob']);
    $data_gender = mysqli_real_escape_string($data, $_POST['gender']);
    $data_address = mysqli_real_escape_string($data, $_POST['address']);
    $data_nationality = mysqli_real_escape_string($data, $_POST['nationality']);
    $data_religion = mysqli_real_escape_string($data, $_POST['religion']);
    $data_prev_school = isset($_POST['previous_school']) ? mysqli_real_escape_string($data, $_POST['previous_school']) : '';
    $data_class = mysqli_real_escape_string($data, $_POST['class_applying']);
    $data_parent_name = mysqli_real_escape_string($data, $_POST['parent_name']);
    $data_parent_phone = mysqli_real_escape_string($data, $_POST['parent_phone']);
    $data_email = mysqli_real_escape_string($data, $_POST['email']);
    $data_phone = mysqli_real_escape_string($data, $_POST['phone']);
    $data_message = isset($_POST['message']) ? mysqli_real_escape_string($data, $_POST['message']) : '';

    // Handle passport photo upload
    $passport_photo_path = '';
    if (isset($_FILES['passport_photo']) && $_FILES['passport_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/passport_photos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_tmp = $_FILES['passport_photo']['tmp_name'];
        $file_name = basename($_FILES['passport_photo']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_exts)) {
            $new_file_name = uniqid('passport_', true) . '.' . $file_ext;
            $target_path = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp, $target_path)) {
                $passport_photo_path = $target_path;
            } else {
                echo "Error: Failed to upload passport photo.";
                exit;
            }
        } else {
            echo "Error: Invalid passport photo file type.";
            exit;
        }
    }

    $sql = "INSERT INTO admission (name, dob, gender, address, nationality, religion, previous_school, class_applying, parent_name, parent_phone, email, phone, passport_photo, message)
            VALUES ('$data_name', '$data_dob', '$data_gender', '$data_address', '$data_nationality', '$data_religion', '$data_prev_school', '$data_class', '$data_parent_name', '$data_parent_phone', '$data_email', '$data_phone', '$passport_photo_path', '$data_message')";

    $result = mysqli_query($data, $sql);

    if ($result) {
        // Send email notification to admin and confirmation to applicant
        require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
        require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';
        $admin_email = 'admin@nyabikonischool.com'; // Change to your admin email
        $school_name = 'Nyabikoni Secondary School';
        $mail_success = true;
        // --- GMAIL SMTP CONFIGURATION ---
        $gmail_address = 'tukamuhebwanewton@gmail.com';
        $gmail_app_password = 'qeeuyrvmzserzdfe';
        // ---
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
            $mail->addAddress($admin_email);
            $mail->Subject = 'New Admission Application Received';
            $body = "<h3>New Admission Application</h3>"
                . "<b>Name:</b> $data_name<br>"
                . "<b>Date of Birth:</b> $data_dob<br>"
                . "<b>Gender:</b> $data_gender<br>"
                . "<b>Address:</b> $data_address<br>"
                . "<b>Nationality:</b> $data_nationality<br>"
                . "<b>Religion:</b> $data_religion<br>"
                . "<b>Previous School:</b> $data_prev_school<br>"
                . "<b>Class Applying For:</b> $data_class<br>"
                . "<b>Parent/Guardian Name:</b> $data_parent_name<br>"
                . "<b>Parent/Guardian Phone:</b> $data_parent_phone<br>"
                . "<b>Email:</b> $data_email<br>"
                . "<b>Phone:</b> $data_phone<br>"
                . "<b>Message:</b> $data_message<br>";
            if ($passport_photo_path) {
                $body .= "<b>Passport Photo:</b> <a href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $passport_photo_path . "'>View Photo</a><br>";
            }
            $mail->isHTML(true);
            $mail->Body = $body;
            $mail->send();
        } catch (Exception $e) {
            $mail_success = false;
        }
        // Confirmation to applicant
        try {
            $mail2 = new PHPMailer\PHPMailer\PHPMailer();
            $mail2->isSMTP();
            $mail2->Host = 'smtp.gmail.com';
            $mail2->SMTPAuth = true;
            $mail2->Username = $gmail_address;
            $mail2->Password = $gmail_app_password;
            $mail2->SMTPSecure = 'tls';
            $mail2->Port = 587;
            $mail2->setFrom($gmail_address, $school_name);
            $mail2->addAddress($data_email);
            $mail2->Subject = 'Your Admission Application Received';
            $mail2->isHTML(true);
            $mail2->Body = "<p>Dear $data_name,</p><p>Thank you for applying to $school_name. We have received your application and will contact you soon.</p><p>Best regards,<br>$school_name Admissions</p>";
            $mail2->send();
        } catch (Exception $e) {
            // Ignore applicant email failure
        }
        echo "Success: Your application was sent successfully!";
    } else {
        echo "Apply Failed: Could not process your application.";
    }
} else {
    echo "Error: Invalid form submission.";
}

?>