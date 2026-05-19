<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../shared/config.php';

// Fetch classes
$classes = $conn->query("SELECT id, class_name, level FROM classes ORDER BY class_name ASC");

// Get selected filters
$selected_class = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$selected_term = isset($_GET['term']) ? intval($_GET['term']) : 0;
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$students = null;
$subjects = [];
$class_info = null;

if ($selected_class && $selected_term) {
    // Fetch class info
    $class_query = $conn->prepare("SELECT class_name, level FROM classes WHERE id = ?");
    $class_query->bind_param("i", $selected_class);
    $class_query->execute();
    $class_info = $class_query->get_result()->fetch_assoc();
    
    // Fetch students
    $students_query = $conn->prepare("SELECT id, username FROM students WHERE class_id = ? ORDER BY username ASC");
    $students_query->bind_param("i", $selected_class);
    $students_query->execute();
    $students = $students_query->get_result();
    
    // Fetch subjects based on level
    if ($class_info) {
        $level = $class_info['level'];
        $subject_table = ($level === 'O-Level') ? 'olevel_subjects' : 'alevel_subjects';
        $subjects_result = $conn->query("SELECT subject_name FROM $subject_table ORDER BY subject_name ASC");
        while ($row = $subjects_result->fetch_assoc()) {
            $subjects[] = $row['subject_name'];
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marks'])) {
    $marks_data = $_POST['marks'];
    $success_count = 0;
    
    foreach ($marks_data as $student_id => $subject_marks) {
        foreach ($subject_marks as $subject => $mark) {
            if ($mark !== '' && is_numeric($mark)) {
                $mark = floatval($mark);
                $grade = calculateGrade($mark);
                
                $stmt = $conn->prepare("INSERT INTO marks (student_id, class_id, subject_name, term, year, marks, grade) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?) 
                                       ON DUPLICATE KEY UPDATE marks = ?, grade = ?");
                $stmt->bind_param("iisisdsds", $student_id, $selected_class, $subject, $selected_term, $selected_year, $mark, $grade, $mark, $grade);
                if ($stmt->execute()) {
                    $success_count++;
                }
            }
        }
    }
    
    $success_message = "Successfully saved $success_count marks!";
}

function calculateGrade($mark) {
    if ($mark >= 80) return 'A';
    if ($mark >= 70) return 'B';
    if ($mark >= 60) return 'C';
    if ($mark >= 50) return 'D';
    return 'F';
}

// Fetch existing marks if viewing
$existing_marks = [];
if ($selected_class && $selected_term && $students) {
    $marks_query = $conn->prepare("SELECT student_id, subject_name, marks FROM marks WHERE class_id = ? AND term = ? AND year = ?");
    $marks_query->bind_param("iii", $selected_class, $selected_term, $selected_year);
    $marks_query->execute();
    $marks_result = $marks_query->get_result();
    while ($row = $marks_result->fetch_assoc()) {
        $existing_marks[$row['student_id']][$row['subject_name']] = $row['marks'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter Marks - Nyabikoni Secondary School</title>
  <?php include 'admin_css.php'; ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
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
    .modern-container { 
      background: rgba(255,255,255,0.97); 
      backdrop-filter: blur(20px); 
      border-radius: 24px; 
      box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
      padding: 40px; 
      width: 100%; 
    }
    .page-header h1 {
      font-size: 2.2rem;
      font-weight: 800;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 10px;
    }
    .filters {
      display: flex;
      gap: 15px;
      margin: 20px 0;
      flex-wrap: wrap;
    }
    .filter-group {
      flex: 1;
      min-width: 200px;
    }
    .filter-group label {
      display: block;
      font-weight: 600;
      color: #764ba2;
      margin-bottom: 5px;
    }
    .filter-group select,
    .filter-group input {
      width: 100%;
      padding: 10px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 1rem;
    }
    .marks-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      overflow-x: auto;
      display: block;
    }
    .marks-table thead {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    .marks-table th,
    .marks-table td {
      padding: 12px;
      text-align: left;
      border: 1px solid #e0e0e0;
    }
    .marks-table input {
      width: 80px;
      padding: 6px;
      border: 1px solid #ddd;
      border-radius: 4px;
      text-align: center;
    }
    .marks-table input:focus {
      outline: none;
      border-color: #667eea;
    }
    .btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      font-size: 1rem;
    }
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    .success-msg {
      background: #d4edda;
      color: #155724;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border: 1px solid #c3e6cb;
    }
    .info-box {
      background: #f0f4ff;
      padding: 15px;
      border-radius: 8px;
      margin: 20px 0;
      border-left: 4px solid #667eea;
    }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <div class="modern-container">
    <div class="page-header">
      <h1><i class="fas fa-pen-alt"></i> Enter Marks</h1>
      <p>Enter student marks for report card generation</p>
    </div>
    
    <?php if (isset($success_message)): ?>
    <div class="success-msg">
      <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
    </div>
    <?php endif; ?>
    
    <form method="get" class="filters">
      <div class="filter-group">
        <label>Class</label>
        <select name="class_id" required onchange="this.form.submit()">
          <option value="">Select Class</option>
          <?php if ($classes): while ($class = $classes->fetch_assoc()): ?>
            <option value="<?php echo $class['id']; ?>" <?php echo ($selected_class == $class['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($class['class_name']); ?>
            </option>
          <?php endwhile; endif; ?>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Term</label>
        <select name="term" required onchange="this.form.submit()">
          <option value="">Select Term</option>
          <option value="1" <?php echo ($selected_term == 1) ? 'selected' : ''; ?>>Term 1</option>
          <option value="2" <?php echo ($selected_term == 2) ? 'selected' : ''; ?>>Term 2</option>
          <option value="3" <?php echo ($selected_term == 3) ? 'selected' : ''; ?>>Term 3</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Year</label>
        <input type="number" name="year" value="<?php echo $selected_year; ?>" min="2020" max="2030" required onchange="this.form.submit()">
      </div>
    </form>
    
    <?php if ($students && $students->num_rows > 0 && count($subjects) > 0): ?>
    <div class="info-box">
      <strong>Class:</strong> <?php echo htmlspecialchars($class_info['class_name']); ?> (<?php echo htmlspecialchars($class_info['level']); ?>) | 
      <strong>Term:</strong> <?php echo $selected_term; ?> | 
      <strong>Year:</strong> <?php echo $selected_year; ?>
    </div>
    
    <form method="post">
      <table class="marks-table">
        <thead>
          <tr>
            <th>Student</th>
            <?php foreach ($subjects as $subject): ?>
              <th><?php echo htmlspecialchars($subject); ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php while ($student = $students->fetch_assoc()): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($student['username']); ?></strong></td>
            <?php foreach ($subjects as $subject): 
              $existing_mark = isset($existing_marks[$student['id']][$subject]) ? $existing_marks[$student['id']][$subject] : '';
            ?>
              <td>
                <input type="number" 
                       name="marks[<?php echo $student['id']; ?>][<?php echo htmlspecialchars($subject); ?>]" 
                       min="0" 
                       max="100" 
                       step="0.01"
                       value="<?php echo $existing_mark; ?>"
                       placeholder="0-100"
                       style="width:80px; padding:6px 8px; border-radius:8px; border:2px solid #e0e0e0; text-align:center; font-family:'Poppins',sans-serif;"
                       oninput="validateMark(this)">
              </td>
            <?php endforeach; ?>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      
      <div style="margin-top: 20px; text-align: center;">
        <button type="submit" class="btn">
          <i class="fas fa-save"></i> Save All Marks
        </button>
      </div>
    </form>
    
    <?php elseif ($selected_class && $selected_term): ?>
    <div class="info-box">
      <i class="fas fa-info-circle"></i> No students or subjects found for this class.
    </div>
    <?php else: ?>
    <div class="info-box">
      <i class="fas fa-arrow-up"></i> Please select a class, term, and year to begin entering marks.
    </div>
    <?php endif; ?>
  </div>
</div>
<script>
function validateMark(input) {
    const val = parseFloat(input.value);
    let tip = input.nextElementSibling;
    if (!tip || !tip.classList.contains('mark-err')) {
        tip = document.createElement('span');
        tip.className = 'mark-err';
        tip.style.cssText = 'color:#e74c3c;font-size:0.75rem;font-weight:600;display:block;';
        input.insertAdjacentElement('afterend', tip);
    }
    if (input.value !== '' && (isNaN(val) || val < 0 || val > 100)) {
        input.style.borderColor = '#e74c3c';
        input.style.background = '#fff5f5';
        tip.textContent = '0–100 only';
    } else {
        input.style.borderColor = val >= 0 && val <= 100 ? '#27ae60' : '#e0e0e0';
        input.style.background = '';
        tip.textContent = '';
    }
}

// Prevent form submit if any mark is invalid
document.querySelector('form[method="post"]') && document.querySelector('form[method="post"]').addEventListener('submit', function(e) {
    const invalids = document.querySelectorAll('input[type=number][min="0"][max="100"]');
    let hasError = false;
    invalids.forEach(input => {
        validateMark(input);
        if (input.style.borderColor === 'rgb(231, 76, 60)') hasError = true;
    });
    if (hasError) {
        e.preventDefault();
        alert('Please fix the invalid marks before saving.');
    }
});
</script>
</body>
</html>
