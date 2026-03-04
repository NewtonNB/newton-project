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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $usertype = trim($_POST['usertype']);
    $sql = "UPDATE students SET username=?, email=?, phone=?, usertype=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $username, $email, $phone, $usertype, $id);
    if ($stmt->execute()) {
        $msg = "Student updated successfully.";
    } else {
        $msg = "Error updating student: " . $conn->error;
    }
}
// Fetch student data
$sql = "SELECT * FROM students WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
if (!$student) { die('Student not found.'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        body { background: #f4f8fb; }
        .edit-form-container {
            max-width: 500px;
            background: #fff;
            margin: 40px auto;
            padding: 36px 28px;
            box-shadow: 0 8px 32px rgba(52,152,219,0.10), 0 1.5px 4px rgba(0,0,0,0.04);
            border-radius: 18px;
        }
        .edit-form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 2.1rem;
            color: #3498db;
            font-weight: 700;
        }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 500; color: #34495e; }
        input, select {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e3eaf1;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f7fbfd;
            color: #222;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input:focus, select:focus {
            border-color: #3498db;
            background: #fff;
            box-shadow: 0 2px 8px rgba(52,152,219,0.10);
        }
        .submit-btn {
            background: linear-gradient(90deg, #3498db 0%, #6dd5fa 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s, transform 0.15s;
            box-shadow: 0 2px 8px rgba(52,152,219,0.08);
        }
        .submit-btn:hover {
            background: linear-gradient(90deg, #2980b9 0%, #3498db 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .msg { text-align: center; margin-bottom: 18px; color: #27ae60; font-weight: 600; }
    </style>
</head>
<body>
    <div class="edit-form-container">
        <h2>Edit Student</h2>
        <?php if (isset($msg)) echo '<div class="msg">' . $msg . '</div>'; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($student['username']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label>User Type</label>
                <select name="usertype">
                    <option value="student" <?php if($student['usertype']==='student') echo 'selected'; ?>>Student</option>
                    <option value="admin" <?php if($student['usertype']==='admin') echo 'selected'; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="submit-btn">Update Student</button>
        </form>
    </div>
</body>
</html> 