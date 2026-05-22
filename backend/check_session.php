<?php
/**
 * check_session.php
 * Returns JSON indicating whether a user is logged in.
 * Used by HTML frontend to gate admin pages.
 */
require_once __DIR__ . '/cors.php';
nyabz_cors_preflight();
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['admin']) || (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'admin')) {
    echo json_encode([
        'loggedIn' => true,
        'user' => htmlspecialchars($_SESSION['username'] ?? ''),
        'usertype' => $_SESSION['usertype'] ?? 'admin'
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
