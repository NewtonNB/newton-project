<?php
// approve_student.php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../shared/config.php';

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function show_error_and_exit($msg) {
    echo "<script>alert('" . addslashes($msg) . "'); window.location.href='admission.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    // Approve from admission table
    $admission_id = intval($_GET['id']);
    $sql = "SELECT * FROM admission WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $admission_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if (!$student) {
        show_error_and_exit('Student not found in admission table.');
    }

    $username = $student['name'];
    $email = $student['email'];
    $phone = $student['phone'];
    $usertype = 'student';
    $password = password_hash('password123', PASSWORD_DEFAULT); // Default password
    $status = 'Active';

    // Check if student already exists in user table
    $check = $conn->prepare("SELECT * FROM user WHERE username=? OR email=?");
    $check->bind_param('ss', $username, $email);
    $check->execute();
    $check_result = $check->get_result();
    if ($check_result->num_rows > 0) {
        $check->close();
        show_error_and_exit('Student already exists in the system.');
    }
    $check->close();

    // Insert into user table
    $insert = $conn->prepare("INSERT INTO user (username, email, phone, usertype, password) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param('sssss', $username, $email, $phone, $usertype, $password);
    if (!$insert->execute()) {
        show_error_and_exit('Failed to add student to user table: ' . $conn->error);
    }
    $insert->close();

    // Insert into students table for statistics
    $student_insert = $conn->prepare("INSERT INTO students (username, email, phone, usertype, password, status) VALUES (?, ?, ?, ?, ?, ?)");
    $student_insert->bind_param('ssssss', $username, $email, $phone, $usertype, $password, $status);
    if (!$student_insert->execute()) {
        show_error_and_exit('Failed to add student to students table: ' . $conn->error);
    }
    $student_insert->close();

    // Delete from admission table
    $delete = $conn->prepare("DELETE FROM admission WHERE id=?");
    $delete->bind_param('i', $admission_id);
    $delete->execute();
    $delete->close();
    show_error_and_exit('Student approved and added successfully!');

} elseif (isset($_GET['contact_id'])) {
    // Approve from contact_messages table
    $contact_id = intval($_GET['contact_id']);
    $sql = "SELECT * FROM contact_messages WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $contact_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contact = $result->fetch_assoc();
    $stmt->close();

    if (!$contact) {
        show_error_and_exit('Contact not found.');
    }

    $username = trim($contact['first_name'] . ' ' . $contact['last_name']);
    $email = $contact['email'];
    $phone = $contact['phone'];
    $usertype = 'student';
    $password = password_hash('password123', PASSWORD_DEFAULT); // Default password
    $status = 'Active';

    // Check if student already exists in user table
    $check = $conn->prepare("SELECT * FROM user WHERE username=? OR email=?");
    $check->bind_param('ss', $username, $email);
    $check->execute();
    $check_result = $check->get_result();
    if ($check_result->num_rows > 0) {
        $check->close();
        show_error_and_exit('Student already exists in the system.');
    }
    $check->close();

    // Insert into user table
    $insert = $conn->prepare("INSERT INTO user (username, email, phone, usertype, password) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param('sssss', $username, $email, $phone, $usertype, $password);
    if (!$insert->execute()) {
        show_error_and_exit('Failed to add student to user table: ' . $conn->error);
    }
    $insert->close();

    // Insert into students table for statistics
    $student_insert = $conn->prepare("INSERT INTO students (username, email, phone, usertype, password, status) VALUES (?, ?, ?, ?, ?, ?)");
    $student_insert->bind_param('ssssss', $username, $email, $phone, $usertype, $password, $status);
    if (!$student_insert->execute()) {
        show_error_and_exit('Failed to add student to students table: ' . $conn->error);
    }
    $student_insert->close();

    show_error_and_exit('Contact approved and added as student successfully!');

} else {
    show_error_and_exit('No student ID provided.');
} 