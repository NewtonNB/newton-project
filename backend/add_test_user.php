<?php
require '../shared/config.php';

// Check if students table exists, if not create it
$create_table = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    usertype ENUM('student', 'admin') DEFAULT 'student',
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_table) === TRUE) {
    echo "✓ Students table created/verified successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Add a test admin user
$admin_username = "admin";
$admin_password = "admin123";
$admin_usertype = "admin";

// Check if admin user already exists
$check_sql = "SELECT id FROM students WHERE username = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $admin_username);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    // Insert admin user
    $insert_sql = "INSERT INTO students (username, password, usertype, status) VALUES (?, ?, ?, 'Active')";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sss", $admin_username, $admin_password, $admin_usertype);
    
    if ($insert_stmt->execute()) {
        echo "✓ Test admin user created successfully!<br>";
        echo "Username: <strong>$admin_username</strong><br>";
        echo "Password: <strong>$admin_password</strong><br>";
        echo "User Type: <strong>$admin_usertype</strong><br>";
    } else {
        echo "Error creating admin user: " . $insert_stmt->error . "<br>";
    }
    $insert_stmt->close();
} else {
    echo "✓ Admin user already exists<br>";
    echo "Username: <strong>$admin_username</strong><br>";
    echo "Password: <strong>$admin_password</strong><br>";
}

$check_stmt->close();
$conn->close();

echo "<br><a href='login.php' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login</a>";
?> 