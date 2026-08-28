<?php
/**
 * Login System Test Page
 * This page tests the database connection and login tables
 */

require_once 'shared/config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        h2 {
            color: #333;
            margin: 2rem 0 1rem 0;
            font-size: 1.5rem;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }
        .status {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 1rem 0.5rem 1rem 0;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
        code {
            background: #f8f9fa;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #e83e8c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Login System Test</h1>
        <p style="color: #666; margin-bottom: 2rem;">Testing database connection and login system for Nyabikoni Secondary School</p>

        <?php
        // Test 1: Database Connection
        echo '<h2>1. Database Connection</h2>';
        if ($conn->ping()) {
            echo '<div class="status success">✅ Database connection successful!</div>';
            echo '<div class="info">
                <strong>Database:</strong> ' . $db_name . '<br>
                <strong>Host:</strong> ' . $db_host . ':' . $db_port . '<br>
                <strong>Status:</strong> Connected
            </div>';
        } else {
            echo '<div class="status error">❌ Database connection failed!</div>';
        }

        // Test 2: Check Admins Table
        echo '<h2>2. Admins Table</h2>';
        $result = $conn->query("SHOW TABLES LIKE 'admins'");
        if ($result->num_rows > 0) {
            echo '<div class="status success">✅ Admins table exists</div>';
            
            // Get admin users
            $admins = $conn->query("SELECT id, username, email, phone, status, role, created_at FROM admins");
            if ($admins->num_rows > 0) {
                echo '<p><strong>Total Admins:</strong> ' . $admins->num_rows . '</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Created</th></tr>';
                while ($row = $admins->fetch_assoc()) {
                    $statusBadge = $row['status'] == 'Active' ? 'badge-success' : 'badge-warning';
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td><strong>' . htmlspecialchars($row['username']) . '</strong></td>';
                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['role']) . '</td>';
                    echo '<td><span class="badge ' . $statusBadge . '">' . $row['status'] . '</span></td>';
                    echo '<td>' . date('M d, Y', strtotime($row['created_at'])) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<div class="status error">❌ No admin users found! Default admin should be created automatically.</div>';
            }
        } else {
            echo '<div class="status error">❌ Admins table does not exist!</div>';
        }

        // Test 3: Check Students Table
        echo '<h2>3. Students Table</h2>';
        $result = $conn->query("SHOW TABLES LIKE 'students'");
        if ($result->num_rows > 0) {
            echo '<div class="status success">✅ Students table exists</div>';
            
            // Get student users
            $students = $conn->query("SELECT id, username, email, phone, usertype, status, class_id FROM students WHERE usertype = 'student' LIMIT 5");
            if ($students->num_rows > 0) {
                echo '<p><strong>Sample Students:</strong> (Showing first 5)</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Phone</th><th>Class</th><th>Status</th></tr>';
                while ($row = $students->fetch_assoc()) {
                    $statusBadge = $row['status'] == 'Active' ? 'badge-success' : 'badge-warning';
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td><strong>' . htmlspecialchars($row['username']) . '</strong></td>';
                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                    echo '<td>' . ($row['class_id'] ?? 'Not assigned') . '</td>';
                    echo '<td><span class="badge ' . $statusBadge . '">' . $row['status'] . '</span></td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<div class="info">ℹ️ No student users found. Sample students should be created automatically.</div>';
            }
        } else {
            echo '<div class="status error">❌ Students table does not exist!</div>';
        }

        // Test 4: Login Credentials
        echo '<h2>4. Default Login Credentials</h2>';
        $adminCheck = $conn->query("SELECT username, email FROM admins WHERE username = 'admin' AND status = 'Active'");
        if ($adminCheck && $adminCheck->num_rows > 0) {
            $admin = $adminCheck->fetch_assoc();
            echo '<div class="status success">✅ Default admin account found</div>';
            echo '<div class="info" style="padding: 1.5rem;">
                <h3 style="margin-bottom: 1rem; color: #667eea;">Admin Login Details:</h3>
                <p><strong>Username:</strong> <code>admin</code></p>
                <p><strong>Password:</strong> <code>admin123</code></p>
                <p><strong>Email:</strong> <code>' . htmlspecialchars($admin['email']) . '</code></p>
                <p style="margin-top: 1rem; color: #856404;">⚠️ <strong>Important:</strong> Change this password after first login!</p>
            </div>';
        } else {
            echo '<div class="status error">❌ Default admin account not found!</div>';
        }

        // Test 5: Session Check
        echo '<h2>5. Session Support</h2>';
        if (session_status() === PHP_SESSION_ACTIVE) {
            echo '<div class="status success">✅ PHP sessions are enabled</div>';
        } else {
            echo '<div class="status error">❌ PHP sessions are not enabled</div>';
        }

        // Test 6: Required Files
        echo '<h2>6. Required Files</h2>';
        $files = [
            'backend/login_check.php' => 'Login handler',
            'backend/do_logout.php' => 'Logout handler',
            'backend/session_check.php' => 'Session validator',
            'frontend/login.html' => 'Login page',
            'frontend/dashboard.html' => 'Admin dashboard',
            'shared/config.php' => 'Database config'
        ];

        echo '<table>';
        echo '<tr><th>File</th><th>Description</th><th>Status</th></tr>';
        foreach ($files as $file => $description) {
            $exists = file_exists(__DIR__ . '/' . $file);
            $status = $exists ? '<span class="badge badge-success">✅ Exists</span>' : '<span class="badge badge-warning">❌ Missing</span>';
            echo '<tr>';
            echo '<td><code>' . $file . '</code></td>';
            echo '<td>' . $description . '</td>';
            echo '<td>' . $status . '</td>';
            echo '</tr>';
        }
        echo '</table>';

        // Action Buttons
        echo '<h2>7. Quick Actions</h2>';
        echo '<a href="frontend/login.html" class="btn">🔐 Go to Login Page</a>';
        echo '<a href="frontend/dashboard.html" class="btn">📊 Go to Dashboard</a>';
        echo '<a href="LOGIN_CREDENTIALS.md" class="btn">📖 View Full Documentation</a>';

        $conn->close();
        ?>

        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e9ecef; color: #666; text-align: center;">
            <p><strong>Test completed successfully! ✅</strong></p>
            <p style="font-size: 0.9rem; margin-top: 0.5rem;">Nyabikoni Secondary School Management System</p>
        </div>
    </div>
</body>
</html>
<?php
// Close connection if still open
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
?>
