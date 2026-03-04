<?php
session_start();
header('Content-Type: application/json');
if(!isset($_SESSION['username'])) {
    echo json_encode(['success'=>false, 'error'=>'Not logged in.']);
    exit();
}
$host = "localhost";
$user = "root";
$password = "1234";
$db = "schoolproject";
$conn = mysqli_connect($host, $user, $password, $db);
if (!$conn) {
    echo json_encode(['success'=>false, 'error'=>'DB connection error.']);
    exit();
}
$id = intval($_POST['id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$usertype = trim($_POST['usertype'] ?? '');
$class_id = intval($_POST['class_id'] ?? 0);
if (!$id || !$username || !$email || !$phone || !$usertype || !$class_id) {
    echo json_encode(['success'=>false, 'error'=>'Missing fields.']);
    exit();
}
$sql = "UPDATE students SET username=?, email=?, phone=?, usertype=?, class_id=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $username, $email, $phone, $usertype, $class_id, $id);
if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false, 'error'=>'Update failed.']);
} 