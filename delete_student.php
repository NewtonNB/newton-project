<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("location:login.php");
    exit();
}
$host = "localhost";
$user = "root";
$password = "1234";
$db = "schoolproject";
$conn = mysqli_connect($host, $user, $password, $db);
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

if (!isset($_GET['id'])) {
    die('No student ID provided.');
}
$id = intval($_GET['id']);
$sql = "DELETE FROM students WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
header("Location: view_student.php");
exit();
?> 