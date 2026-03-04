<?php
require 'config.php';

echo "<h2>Users in Database:</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Username</th><th>Password</th><th>User Type</th><th>Status</th></tr>";

$sql = "SELECT id, username, password, usertype, status FROM students";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['password'] . "</td>";
        echo "<td>" . $row['usertype'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No users found</td></tr>";
}

echo "</table>";

// Check if the students table exists
$table_check = $conn->query("SHOW TABLES LIKE 'students'");
if ($table_check->num_rows == 0) {
    echo "<h3 style='color: red;'>ERROR: 'students' table does not exist!</h3>";
} else {
    echo "<h3 style='color: green;'>✓ 'students' table exists</h3>";
}

$conn->close();
?> 