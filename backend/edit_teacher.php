<?php
// edit_teacher.php
session_start();
require '../shared/config.php';

$id = $_GET['id'] ?? 0;

// Fetch teacher details
$stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    die("Teacher not found.");
}

$teacher = $result->fetch_assoc();
$success = $error = "";

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $subject   = trim($_POST['subject']);
    $gender    = $_POST['gender'];

    if ($full_name && $email && $phone && $subject && $gender) {
        $update = $conn->prepare("UPDATE teachers SET full_name=?, email=?, phone=?, subject=?, gender=? WHERE id=?");
        $update->bind_param("sssssi", $full_name, $email, $phone, $subject, $gender, $id);

        if ($update->execute()) {
            $success = "Teacher updated successfully.";
        } else {
            $error = "Update failed: " . $conn->error;
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teacher</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h3>Edit Teacher</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($teacher['full_name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($teacher['phone']); ?>" required>
        </div>
        <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" class="form-control" value="<?php echo htmlspecialchars($teacher['subject']); ?>" required>
        </div>
        <div class="form-group">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
                <option value="Male" <?php if ($teacher['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($teacher['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            </select>
        </div>
        <div class="form-group">
            <label>Current Photo</label><br>
            <?php if (!empty($teacher['photo'])): ?>
                <img src="nyabzgallery/<?php echo htmlspecialchars($teacher['photo']); ?>" alt="Teacher Photo" style="width:64px; height:64px; object-fit:cover; border-radius:50%; border:2px solid #667eea; background:#f3f3f3;">
            <?php else: ?>
                <img src="nyabzgallery/default.png" alt="No Photo" style="width:64px; height:64px; object-fit:cover; border-radius:50%; border:2px solid #667eea; background:#f3f3f3;">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Change Photo (optional)</label>
            <input type="file" name="photo" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Update Teacher</button>
        <a href="view_teacher.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
