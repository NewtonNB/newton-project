<?php
// Suppress all errors and warnings to prevent HTML output before JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Set JSON header immediately
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $gender = $_POST['gender'];
    
    // Validate input
    if (empty($full_name) || empty($email) || empty($phone) || empty($subject)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit();
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email format']);
        exit();
    }
    
    // Check if email already exists
    $check_email = $conn->prepare("SELECT id FROM teachers WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $email_result = $check_email->get_result();
    
    if ($email_result->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Email already exists']);
        exit();
    }
    $check_email->close();
    
    // Handle file upload if photo is provided
    $photoFileName = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileSize = $_FILES['photo']['size'];
        $fileType = $_FILES['photo']['type'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($fileExt, $allowedExts)) {
            $newFileName = uniqid('teacher_', true) . '.' . $fileExt;
            $destPath = 'nyabzgallery/' . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $photoFileName = $newFileName;
            }
        }
    }
    
    // Insert new teacher
    $joined_on = date('Y-m-d');
    $photoField = $photoFileName ? $photoFileName : NULL;
    $insert_sql = "INSERT INTO teachers (full_name, email, phone, subject, gender, joined_on, photo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssssss", $full_name, $email, $phone, $subject, $gender, $joined_on, $photoField);
    
    if ($insert_stmt->execute()) {
        // Send welcome email to teacher
        require_once 'email_helper.php';
        sendEmail($email, $full_name,
            'Welcome to Nyabikoni Secondary School Staff',
            "<p>Dear <strong>$full_name</strong>,</p>
            <p>You have been added to the Nyabikoni Secondary School staff system.</p>
            <table style='border-collapse:collapse;margin:16px 0;'>
                " . row('Name', $full_name) . "
                " . row('Subject', $subject) . "
                " . row('Phone', $phone) . "
                " . row('Joined', $joined_on) . "
            </table>
            <p>Welcome to the team!</p>
            <p>Best regards,<br><strong>Nyabikoni Secondary School Administration</strong></p>"
        );
        // Notify admin
        sendEmail(SCHOOL_EMAIL, SCHOOL_NAME,
            'New Teacher Added: ' . $full_name,
            "<p>A new teacher has been added to the system.</p>
            <table style='border-collapse:collapse;margin:16px 0;'>
                " . row('Name', $full_name) . "
                " . row('Email', $email) . "
                " . row('Subject', $subject) . "
                " . row('Gender', $gender) . "
            </table>"
        );
        // --- STAFF.HTML SYNC LOGIC ---
        $photoForStaff = $photoFileName ? $photoFileName : 'default.png';
        $staffHtmlPath = 'staff.html';
        $staffHtml = file_get_contents($staffHtmlPath);
        if ($staffHtml) {
            // Remove MR., MS., MRS., DR., etc. for matching
            $name_clean = preg_replace('/^(MR\.|MS\.|MRS\.|DR\.|MISS)\s+/i', '', $full_name);
            $name_clean_lower = strtolower($name_clean);
            // Find all staff items
            $pattern = '/(<div class="staff-item[^"]*">\s*<img src="([^"]+)"[^>]*>\s*<h3>([^<]+)<\/h3>\s*<p>([^<]*)<\/p>\s*<\/div>)/i';
            $found = false;
            $newStaffHtml = preg_replace_callback($pattern, function($matches) use ($name_clean_lower, $photoForStaff, $full_name, $subject, &$found) {
                $staffName = preg_replace('/^(MR\.|MS\.|MRS\.|DR\.|MISS)\s+/i', '', $matches[3]);
                if (strtolower($staffName) === $name_clean_lower) {
                    $found = true;
                    // Update photo and subject
                    return '<div class="staff-item slow-fade" data-aos="fade-up" data-aos-delay="100">\n  <img src="nyabzgallery/' . htmlspecialchars($photoForStaff) . '" alt="' . htmlspecialchars($full_name) . '">\n  <h3>' . htmlspecialchars(strtoupper($full_name)) . '</h3>\n  <p>' . htmlspecialchars($subject) . '</p>\n</div>';
                }
                return $matches[0];
            }, $staffHtml);
            if (!$found) {
                // Insert new staff item before the closing tags of the gallery-container
                $newStaffItem = "    <div class=\"staff-item slow-fade\" data-aos=\"fade-up\" data-aos-delay=\"100\">\n      <img src=\"nyabzgallery/" . htmlspecialchars($photoForStaff) . "\" alt=\"" . htmlspecialchars($full_name) . "\">\n      <h3>" . htmlspecialchars(strtoupper($full_name)) . "</h3>\n      <p>" . htmlspecialchars($subject) . "</p>\n    </div>\n";
                $newStaffHtml = preg_replace('/(<\/div>\s*<\/section>)/i', $newStaffItem . '$1', $newStaffHtml, 1);
            }
            file_put_contents($staffHtmlPath, $newStaffHtml);
        }
        // --- END STAFF.HTML SYNC ---
        echo json_encode(['success' => true, 'message' => 'Teacher added successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $insert_stmt->error]);
    }
    
    $insert_stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn->close();
?> 