<?php

error_reporting(0);
session_start();

// Use centralized database connection
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['username'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM students WHERE username = ? AND password = ? AND status = 'Active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($row["usertype"] == "student") {
            $_SESSION['username'] = $name;
            $_SESSION['usertype'] = "student";
            header("location: studenthome.php");
            exit();
        } elseif ($row["usertype"] == "admin") {
            $_SESSION['username'] = $name;
            $_SESSION['usertype'] = "admin";
            $_SESSION['admin'] = true;
            header("location: dashboard.php");
            exit();
        }
    } else {
        $message = "Username or password do not match";
        $_SESSION['loginMessage'] = $message;
        header("location: login.php");
        exit();
    }
    
    $stmt->close();
} else {
    // If not POST request, redirect to login
    header("location: login.php");
    exit();
}
?>
