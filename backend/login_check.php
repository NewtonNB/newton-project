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
    $name = $_POST['username'];
    $pass = $_POST['password'];

    // First check admins table
    $sql = "SELECT * FROM admins WHERE username = ? AND password = ? AND status = 'Active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Admin login
        $_SESSION['username'] = $name;
        $_SESSION['usertype'] = "admin";
        $_SESSION['admin'] = true;
        header("location: ../frontend/dashboard.html");
        exit();
    }
    
    $stmt->close();
    
    // If not admin, check students table
    $sql = "SELECT * FROM students WHERE username = ? AND password = ? AND status = 'Active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $name;
        $_SESSION['usertype'] = "student";
        header("location: ../frontend/studenthome.html");
        exit();
    }
    
    // Login failed - pass error as URL parameter
    header("location: ../frontend/login.html?error=1");
    exit();
    
    $stmt->close();
} else {
    // If not POST request, redirect to login
    header("location: ../frontend/login.html");
    exit();
}
?>
