<?php
require_once __DIR__ . '/cors.php';
nyabz_cors_preflight();

function admin_require_json(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    header('Content-Type: application/json');
    if (!isset($_SESSION['admin']) && (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin')) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

function student_require_json(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    header('Content-Type: application/json');
    if (!isset($_SESSION['username']) || ($_SESSION['usertype'] ?? '') !== 'student') {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}
