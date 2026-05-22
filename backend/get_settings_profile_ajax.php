<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$username = $_SESSION['username'] ?? '';
$user = ['username' => $username, 'email' => '', 'phone' => ''];
if ($username) {
    $stmt = $conn->prepare("SELECT username, email, phone FROM admins WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $row = $res->fetch_assoc()) {
        $user = $row;
        $user['name'] = $row['username'];
    }
}
echo json_encode(['user' => $user]);
