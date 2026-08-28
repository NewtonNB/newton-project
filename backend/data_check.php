<?php
/**
 * data_check.php
 * Handles student admission form submissions
 */
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');

require_once '../shared/config.php';

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. Please submit the form properly.');
    }

    // Check if POST data exists
    if (empty($_POST)) {
        throw new Exception('No form data received. Please submit the application form.');
    }

    // Get form data
    $name = trim($_POST['name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $parentName = trim($_POST['parent_name'] ?? '');
    $parentPhone = trim($_POST['parent_phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation
    if (empty($name) || empty($dob) || empty($gender) || empty($parentName) || empty($parentPhone)) {
        throw new Exception('Please fill in all required fields (Name, Date of Birth, Gender, Parent Name, Parent Phone)');
    }

    // Validate email if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }

    // Handle passport photo upload
    $photoPath = null;
    if (isset($_FILES['passport_photo']) && $_FILES['passport_photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        $fileType = $_FILES['passport_photo']['type'];
        $fileSize = $_FILES['passport_photo']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG and PNG allowed');
        }

        if ($fileSize > $maxSize) {
            throw new Exception('File too large. Maximum size is 2MB');
        }

        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../frontend/admission_photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($_FILES['passport_photo']['name'], PATHINFO_EXTENSION);
        $filename = 'admission_' . uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['passport_photo']['tmp_name'], $targetPath)) {
            $photoPath = 'admission_photos/' . $filename;
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO student_applications 
        (name, date_of_birth, gender, parent_name, parent_phone, email, phone, message, photo, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    
    $stmt->bind_param("sssssssss", 
        $name, $dob, $gender, $parentName, $parentPhone, $email, $phone, $message, $photoPath
    );

    if ($stmt->execute()) {
        $applicationId = $stmt->insert_id;
        
        // Send email notification (optional)
        // You can add email sending code here if needed
        
        echo json_encode([
            'success' => true,
            'message' => 'Application submitted successfully! We will contact you soon.',
            'application_id' => $applicationId
        ]);
    } else {
        throw new Exception('Failed to submit application. Please try again.');
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>
