<?php
require 'config.php';

// Drop fees table first due to foreign key constraint
$conn->query("DROP TABLE IF EXISTS fees");
// Now drop students table
if ($conn->query("DROP TABLE IF EXISTS students") === TRUE) {
    echo "Students table dropped successfully.";
} else {
    echo "Error dropping students table: " . $conn->error;
}
$conn->close(); 