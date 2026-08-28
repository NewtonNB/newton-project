<?php
// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/cors.php';
nyabz_cors_preflight();
session_start();

// Use centralized database connection
require '../shared/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['username']);
    $pass = trim($_POST['password']);
    
    // Validate inputs
    if (empty($name) || empty($pass)) {
        header("location: ../frontend/login.html?error=empty");
        exit();
    }

    // First check admins table
    $sql = "SELECT id, username, email, password, role FROM admins WHERE username = ? AND status = 'Active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // Check password (direct comparison for now, should use password_hash/password_verify in production)
        if ($pass === $admin['password']) {
            // Admin login successful
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['email'] = $admin['email'];
            $_SESSION['usertype'] = "admin";
            $_SESSION['role'] = $admin['role'];
            $_SESSION['admin'] = true;
            $_SESSION['login_time'] = time();
            
            $stmt->close();
            $conn->close();
            
            header("location: ../frontend/dashboard.html");
            exit();
        }
    }
    
    $stmt->close();
    
    // If not admin, check students table
    $sql = "SELECT id, username, email, password, class_id FROM students WHERE username = ? AND status = 'Active' AND usertype = 'student'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        
        // Check password
        if ($pass === $student['password']) {
            // Student login successful
            $_SESSION['user_id'] = $student['id'];
            $_SESSION['username'] = $student['username'];
            $_SESSION['email'] = $student['email'];
            $_SESSION['usertype'] = "student";
            $_SESSION['class_id'] = $student['class_id'];
            $_SESSION['login_time'] = time();
            
            $stmt->close();
            $conn->close();
            
            header("location: ../frontend/studenthome.html");
            exit();
        }
    }
    
    $stmt->close();
    $conn->close();
    
    // Login failed - invalid credentials
    header("location: ../frontend/login.html?error=invalid");
    exit();
    
} else {
    // If not POST request, redirect to login
    header("location: ../frontend/login.html");
    exit();
}
?>