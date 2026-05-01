<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'config.php';

// Function to calculate overall grade
function calculateOverallGrade($avg) {
    if ($avg >= 80) return 'A';
    if ($avg >= 70) return 'B';
    if ($avg >= 60) return 'C';
    if ($avg >= 50) return 'D';
    return 'F';
}

// Get parameters
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$term = isset($_GET['term']) ? intval($_GET['term']) : 0;
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

if (!$class_id || !$term) {
    header('Location: report_cards.php');
    exit;
}

// Fetch class info
$class_query = $conn->prepare("SELECT class_name, level FROM classes WHERE id = ?");
$class_query->bind_param("i", $class_id);
$class_query->execute();
$class_result = $class_query->get_result();
$class_info = $class_result->fetch_assoc();

if (!$class_info) {
    die("Class not found");
}

// Fetch students in this class
$students_query = $conn->prepare("SELECT id, username, email, phone FROM students WHERE class_id = ? ORDER BY username ASC");
$students_query->bind_param("i", $class_id);
$students_query->execute();
$students = $students_query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Cards - <?php echo htmlspecialchars($class_info['class_name']); ?></title>
  <?php include 'admin_css.php'; ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { 
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
      min-height: 100vh; 
      font-family: 'Poppins', sans-serif; 
      margin: 0; 
      padding: 0; 
    }
    .content { 
      margin-top: 40px; 
      margin-left: 280px; 
      padding: 20px; 
      max-width: calc(100vw - 320px); 
    }
    .report-header {
      background: white;
      padding: 30px;
      border-radius: 16px;
      margin-bottom: 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .report-header h1 {
      color: #667eea;
      margin: 0 0 10px 0;
      font-size: 2rem;
    }
    .report-header p {
      color: #666;
      margin: 5px 0;
    }
    .student-report {
      background: white;
      padding: 30px;
      border-radius: 16px;
      margin-bottom: 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      page-break-after: always;
    }
    .student-header {
      border-bottom: 3px solid #667eea;
      padding-bottom: 15px;
      margin-bottom: 20px;
    }
    .student-header h2 {
      color: #764ba2;
      margin: 0 0 10px 0;
    }
    .student-info {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      color: #666;
    }
    .marks-table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }
    .marks-table th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 12px;
      text-align: left;
    }
    .marks-table td {
      padding: 12px;
      border-bottom: 1px solid #e0e0e0;
    }
    .marks-table tr:hover {
      background: #f8f9ff;
    }
    .summary {
      background: #f0f4ff;
      padding: 20px;
      border-radius: 12px;
      margin-top: 20px;
    }
    .summary h3 {
      color: #667eea;
      margin-top: 0;
    }
    .grade {
      font-weight: 700;
      font-size: 1.2rem;
    }
    .grade.A { color: #10b981; }
    .grade.B { color: #3b82f6; }
    .grade.C { color: #f59e0b; }
    .grade.D { color: #ef4444; }
    .grade.F { color: #dc2626; }
    .actions {
      margin-bottom: 20px;
    }
    .btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      margin-right: 10px;
    }
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    .btn-secondary {
      background: #6c757d;
    }
    @media print {
      body { background: white; }
      .content { margin-left: 0; max-width: 100%; }
      .actions, .report-header { display: none; }
      .student-report { box-shadow: none; }
    }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <div class="report-header">
    <h1><i class="fas fa-file-alt"></i> Report Cards</h1>
    <p><strong>Class:</strong> <?php echo htmlspecialchars($class_info['class_name']); ?> (<?php echo htmlspecialchars($class_info['level']); ?>)</p>
    <p><strong>Term:</strong> <?php echo $term; ?> | <strong>Year:</strong> <?php echo $year; ?></p>
  </div>
  
  <div class="actions">
    <button onclick="window.print()" class="btn">
      <i class="fas fa-print"></i> Print All Reports
    </button>
    <a href="report_cards.php" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>
  
  <?php if ($students->num_rows > 0): 
    while ($student = $students->fetch_assoc()): 
      $student_id = $student['id'];
      
      // Fetch marks for this student
      $marks_query = $conn->prepare("SELECT subject_name, marks, grade FROM marks WHERE student_id = ? AND class_id = ? AND term = ? AND year = ? ORDER BY subject_name ASC");
      $marks_query->bind_param("iiii", $student_id, $class_id, $term, $year);
      $marks_query->execute();
      $marks_result = $marks_query->get_result();
      
      $total_marks = 0;
      $total_subjects = 0;
      $marks_data = [];
      
      while ($mark = $marks_result->fetch_assoc()) {
        $marks_data[] = $mark;
        $total_marks += $mark['marks'];
        $total_subjects++;
      }
      
      $average = $total_subjects > 0 ? round($total_marks / $total_subjects, 2) : 0;
      $overall_grade = $total_subjects > 0 ? calculateOverallGrade($average) : '-';
  ?>
  <div class="student-report">
    <div class="student-header">
      <h2><?php echo htmlspecialchars($student['username']); ?></h2>
      <div class="student-info">
        <div><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></div>
        <div><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone']); ?></div>
        <div><strong>Class:</strong> <?php echo htmlspecialchars($class_info['class_name']); ?></div>
        <div><strong>Term:</strong> <?php echo $term; ?> / <?php echo $year; ?></div>
      </div>
    </div>
    
    <table class="marks-table">
      <thead>
        <tr>
          <th>Subject</th>
          <th>Marks</th>
          <th>Grade</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($marks_data) > 0): 
          foreach ($marks_data as $mark): ?>
        <tr>
          <td><?php echo htmlspecialchars($mark['subject_name']); ?></td>
          <td><?php echo $mark['marks']; ?></td>
          <td><span class="grade <?php echo $mark['grade']; ?>"><?php echo $mark['grade']; ?></span></td>
          <td>
            <?php 
              if ($mark['marks'] >= 80) echo 'Excellent';
              elseif ($mark['marks'] >= 70) echo 'Very Good';
              elseif ($mark['marks'] >= 60) echo 'Good';
              elseif ($mark['marks'] >= 50) echo 'Fair';
              else echo 'Needs Improvement';
            ?>
          </td>
        </tr>
        <?php endforeach; 
        else: ?>
        <tr>
          <td colspan="4" style="text-align: center; color: #999; padding: 40px;">
            <i class="fas fa-info-circle"></i> No marks entered yet for this term.<br>
            <small>Please enter marks in the "Enter Marks" section to generate complete report cards.</small>
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <div class="summary">
      <h3>Summary</h3>
      <p><strong>Total Marks:</strong> <?php echo $total_marks; ?> / <?php echo $total_subjects * 100; ?></p>
      <p><strong>Average:</strong> <?php echo $average; ?>%</p>
      <p><strong>Overall Grade:</strong> <span class="grade <?php echo $overall_grade; ?>"><?php echo $overall_grade; ?></span></p>
      <p><strong>Class Teacher's Comment:</strong> 
        <?php 
          if ($average >= 80) echo 'Outstanding performance! Keep up the excellent work.';
          elseif ($average >= 70) echo 'Very good work. Continue with the same effort.';
          elseif ($average >= 60) echo 'Good progress. Keep working hard.';
          elseif ($average >= 50) echo 'Fair performance. More effort needed.';
          else echo 'Needs significant improvement. Please seek extra help.';
        ?>
      </p>
    </div>
  </div>
  <?php endwhile; 
  else: ?>
  <div class="student-report">
    <p style="text-align: center; color: #999; padding: 40px;">
      <i class="fas fa-users-slash"></i><br>
      No students found in this class.
    </p>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
