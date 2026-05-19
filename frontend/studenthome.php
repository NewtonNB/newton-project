<?php

session_start();

if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'student') {
    header("location:login.php");
    exit();
}

require '../shared/config.php'; // $conn = new mysqli(...);

// Fetch student info
$username = $_SESSION['username'];
$studentResult = $conn->query("SELECT * FROM students WHERE username='$username'");
$student = $studentResult && $studentResult->num_rows ? $studentResult->fetch_assoc() : [];
$profilePic = $student['profile_pic'] ?? "nyabzgallery/student3.jpg";

// Fetch classes/subjects
$classes = [];
$tableCheck = $conn->query("SHOW TABLES LIKE 'student_subjects'");
if ($tableCheck && $tableCheck->num_rows) {
    $classResult = $conn->query("SELECT subject_name FROM student_subjects WHERE student_username='$username'");
    if ($classResult) {
        while ($row = $classResult->fetch_assoc()) {
            $classes[] = $row['subject_name'];
        }
    }
}
if (!$classes) { $classes = ["Mathematics", "English", "Biology", "History"]; }

// Fetch upcoming assignments
$upcomingAssignments = [];
$tableCheck = $conn->query("SHOW TABLES LIKE 'assignments'");
if ($tableCheck && $tableCheck->num_rows) {
    $assignmentResult = $conn->query("SELECT subject, title, due_date FROM assignments WHERE student_username='$username' AND due_date >= date('now') ORDER BY due_date ASC LIMIT 5");
    if ($assignmentResult) {
        while ($row = $assignmentResult->fetch_assoc()) {
            $upcomingAssignments[] = [
                'subject' => $row['subject'],
                'title' => $row['title'],
                'due' => $row['due_date']
            ];
        }
    }
}
if (!$upcomingAssignments) {
    $upcomingAssignments = [
        ["subject" => "Mathematics", "title" => "Algebra Homework", "due" => "2024-07-10"],
        ["subject" => "Biology", "title" => "Lab Report", "due" => "2024-07-12"]
    ];
}

// Fetch recent grades
$recentGrades = [];
$tableCheck = $conn->query("SHOW TABLES LIKE 'grades'");
if ($tableCheck && $tableCheck->num_rows) {
    $gradesResult = $conn->query("SELECT subject, grade FROM grades WHERE student_username='$username' ORDER BY date_recorded DESC LIMIT 5");
    if ($gradesResult) {
        while ($row = $gradesResult->fetch_assoc()) {
            $recentGrades[] = [
                'subject' => $row['subject'],
                'grade' => $row['grade']
            ];
        }
    }
}
if (!$recentGrades) {
    $recentGrades = [
        ["subject" => "English", "grade" => "A"],
        ["subject" => "Mathematics", "grade" => "B+"]
    ];
}

// Fetch fee balance
$feeBalance = "UGX 0";
$tableCheck = $conn->query("SHOW TABLES LIKE 'fees'");
if ($tableCheck && $tableCheck->num_rows) {
    $feeRow = $conn->query("SELECT balance FROM fees WHERE student_username='$username' ORDER BY id DESC LIMIT 1");
    if ($feeRow && $feeRow->num_rows) {
        $feeRow = $feeRow->fetch_assoc();
        $feeBalance = $feeRow ? "UGX " . number_format($feeRow['balance']) : "UGX 0";
    }
} else {
    $feeBalance = "UGX 120,000";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="admin_css.php">
    <style>
        body { background: #f4f8fb; font-family: 'Poppins', sans-serif; }
        .student-dashboard { max-width: 1100px; margin: 40px auto; background: #fff; border-radius: 18px; box-shadow: 0 8px 32px rgba(52,152,219,0.10); padding: 36px; }
        .dashboard-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
        .student-info { display: flex; align-items: center; gap: 18px; }
        .student-avatar { width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 2px solid #3498db; }
        .student-name { font-size: 1.7rem; font-weight: 700; color: #3498db; }
        .logout-btn { background: #e74c3c; color: #fff; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .logout-btn:hover { background: #c0392b; }
        .dashboard-section { margin-bottom: 32px; }
        .section-title { font-size: 1.2rem; color: #2980b9; font-weight: 600; margin-bottom: 12px; }
        .card { background: #f7fbfd; border-radius: 12px; padding: 18px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(52,152,219,0.06); }
        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .list { list-style: none; padding: 0; margin: 0; }
        .list li { margin-bottom: 8px; }
        @media (max-width: 800px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .student-dashboard { padding: 16px; }
        }
    </style>
</head>
<body>
    <div class="student-dashboard">
        <div class="dashboard-header">
            <div class="student-info">
                <img src="<?php echo $profilePic; ?>" alt="Profile" class="student-avatar">
                <span class="student-name">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        <div class="dashboard-section">
            <div class="section-title">My Classes</div>
            <div class="card">
                <?php echo $classes ? implode(", ", $classes) : "No classes found."; ?>
            </div>
        </div>
        <div class="dashboard-grid">
            <div>
                <div class="dashboard-section">
                    <div class="section-title">Upcoming Assignments</div>
                    <div class="card">
                        <ul class="list">
                            <?php if ($upcomingAssignments): foreach ($upcomingAssignments as $a): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($a['subject']); ?>:</strong>
                                    <?php echo htmlspecialchars($a['title']); ?> (Due: <?php echo htmlspecialchars($a['due']); ?>)
                                </li>
                            <?php endforeach; else: ?>
                                <li>No upcoming assignments.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="dashboard-section">
                    <div class="section-title">Fee Balance</div>
                    <div class="card"><?php echo $feeBalance; ?></div>
                </div>
            </div>
            <div>
                <div class="dashboard-section">
                    <div class="section-title">Recent Grades</div>
                    <div class="card">
                        <ul class="list">
                            <?php if ($recentGrades): foreach ($recentGrades as $g): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($g['subject']); ?>:</strong>
                                    <?php echo htmlspecialchars($g['grade']); ?>
                                </li>
                            <?php endforeach; else: ?>
                                <li>No recent grades.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="dashboard-section">
                    <div class="section-title">Announcements</div>
                    <div class="card">No new announcements.</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>