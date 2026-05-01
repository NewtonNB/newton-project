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
        // Skip email sending for faster response
        // Email functionality can be added later with proper PHPMailer setup
        echo "Success: Your application was sent successfully!";
    } else {
        echo "Apply Failed: Could not process your application.";
    }
} else {
    echo "Error: Invalid form submission.";
}

?>