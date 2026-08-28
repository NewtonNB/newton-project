<?php
/**
 * Session Check - Protects admin pages
 * Include this file at the top of any admin page that requires authentication
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['usertype'])) {
    // Not logged in - redirect to login page
    header('Location: ../frontend/login.html?error=session');
    exit();
}

// Check if user is admin (for admin-only pages)
function requireAdmin() {
    if ($_SESSION['usertype'] !== 'admin') {
        header('Location: ../frontend/login.html?error=unauthorized');
        exit();
    }
}

// Check if user is student (for student-only pages)
function requireStudent() {
    if ($_SESSION['usertype'] !== 'student') {
        header('Location: ../frontend/login.html?error=unauthorized');
        exit();
    }
}

// Check session timeout (30 minutes of inactivity)
$timeout_duration = 1800; // 30 minutes in seconds

if (isset($_SESSION['login_time'])) {
    $elapsed_time = time() - $_SESSION['login_time'];
    
    if ($elapsed_time > $timeout_duration) {
        // Session expired
        session_unset();
        session_destroy();
        header('Location: ../frontend/login.html?error=session');
        exit();
    }
}

// Update last activity time
$_SESSION['login_time'] = time();
?>
