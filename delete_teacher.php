<?php
// delete_teacher.php
session_start();
require 'config.php';

$id = $_GET['id'] ?? 0;

// Simple validation
if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: view_teacher.php?msg=deleted");
        exit();
    } else {
        echo "Failed to delete teacher.";
    }
} else {
    echo "Invalid ID.";
}
?>
