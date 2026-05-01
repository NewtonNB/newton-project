<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'config.php';

// Fetch classes
$classes = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name ASC");

// Fetch subjects from both O-Level and A-Level
$subjects = [];
$olevel_subjects = $conn->query("SELECT subject_name FROM olevel_subjects ORDER BY subject_name ASC");
if ($olevel_subjects) {
    while ($row = $olevel_subjects->fetch_assoc()) {
        $subjects[] = $row['subject_name'];
    }
}
$alevel_subjects = $conn->query("SELECT subject_name FROM alevel_subjects ORDER BY subject_name ASC");
if ($alevel_subjects) {
    while ($row = $alevel_subjects->fetch_assoc()) {
        if (!in_array($row['subject_name'], $subjects)) {
            $subjects[] = $row['subject_name'];
        }
    }
}
sort($subjects);

// Handle form submission
$success = false; $error = ''; $announcement_posted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_exam'])) {
    $exam_name = trim($_POST['exam_name']);
    $class_id = intval($_POST['class_id']);
    $subject_name = trim($_POST['subject_name']);
    $exam_date = $_POST['exam_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room = trim($_POST['room']);
    $term = intval($_POST['term']);
    $year = intval($_POST['year']);
    $instructions = trim($_POST['instructions']);
    $post_announcement = isset($_POST['post_announcement']) && $_POST['post_announcement'] == '1';
    
    if ($exam_name && $class_id && $subject_name && $exam_date && $start_time && $end_time && $term && $year) {
        $stmt = $conn->prepare("INSERT INTO exam_schedule (exam_name, class_id, subject_name, exam_date, start_time, end_time, room, term, year, instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sisssssiis', $exam_name, $class_id, $subject_name, $exam_date, $start_time, $end_time, $room, $term, $year, $instructions);
        if ($stmt->execute()) {
            $success = true;
            
            // Create announcement only if checkbox is checked
            if ($post_announcement) {
                // Get class name
                $class_result = $conn->query("SELECT class_name FROM classes WHERE id = $class_id");
                $class_row = $class_result->fetch_assoc();
                $class_name = $class_row['class_name'];
                
                // Create announcement for the exam
                $announcement_title = "$exam_name - $subject_name ($class_name)";
                $announcement_content = "Exam Schedule Notice\n\n";
                $announcement_content .= "Exam: $exam_name\n";
                $announcement_content .= "Class: $class_name\n";
                $announcement_content .= "Subject: $subject_name\n";
                $announcement_content .= "Date: " . date('l, F j, Y', strtotime($exam_date)) . "\n";
                $announcement_content .= "Time: " . date('h:i A', strtotime($start_time)) . " - " . date('h:i A', strtotime($end_time)) . "\n";
                if ($room) {
                    $announcement_content .= "Room: $room\n";
                }
                $announcement_content .= "Term: $term | Year: $year\n";
                if ($instructions) {
                    $announcement_content .= "\nInstructions:\n$instructions";
                }
                
                // Insert announcement
                $ann_stmt = $conn->prepare("INSERT INTO announcements (title, type, date, time, location, category, content) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $ann_type = 'exam';
                $ann_time = date('h:i A', strtotime($start_time)) . " - " . date('h:i A', strtotime($end_time));
                $ann_location = $room ?: 'TBA';
                $ann_category = 'Examinations';
                $ann_stmt->bind_param('sssssss', $announcement_title, $ann_type, $exam_date, $ann_time, $ann_location, $ann_category, $announcement_content);
                if ($ann_stmt->execute()) {
                    $announcement_posted = true;
                }
            }
        } else {
            $error = 'Failed to add exam schedule';
        }
    } else {
        $error = 'Please fill all required fields';
    }
}

// Handle delete - soft delete
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("ALTER TABLE exam_schedule ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
    $conn->query("UPDATE exam_schedule SET deleted_at = NOW() WHERE id = $delete_id");
    header('Location: exam_schedule.php');
    exit;
}

// Fetch exam schedules with filters
$class_filter = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$term_filter = isset($_GET['term']) ? intval($_GET['term']) : 0;
$year_filter = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$where = ['e.deleted_at IS NULL'];
if ($class_filter) $where[] = "e.class_id = $class_filter";
if ($term_filter) $where[] = "e.term = $term_filter";
if ($year_filter) $where[] = "e.year = $year_filter";
$where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$exams = $conn->query("SELECT e.*, c.class_name FROM exam_schedule e 
                       JOIN classes c ON e.class_id = c.id 
                       $where_sql 
                       ORDER BY e.exam_date ASC, e.start_time ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Exam Schedule</title>
  <?php include 'admin_css.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; font-family: 'Poppins', sans-serif; margin: 0; padding: 0; }
    .content { margin-top: 40px; margin-left: 280px; padding: 20px; max-width: calc(100vw - 320px); }
    .modern-container { background: rgba(255,255,255,0.97); backdrop-filter: blur(20px); border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); padding: 40px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.2); position: relative; overflow: hidden; }
    .modern-container::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c); border-radius: 24px 24px 0 0; }
    h2 { font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 18px; margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { font-weight: 600; color: #764ba2; margin-bottom: 6px; font-size: 0.95rem; }
    .form-control { padding: 12px 16px; border-radius: 10px; border: 2px solid #e0e0e0; font-size: 1rem; transition: all 0.3s; }
    .form-control:focus { border-color: #667eea; outline: none; box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
    .btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; border-radius: 10px; padding: 12px 28px; font-size: 1.05rem; font-weight: 700; cursor: pointer; transition: all 0.3s; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102,126,234,0.3); }
    .btn-danger { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); padding: 8px 16px; font-size: 0.9rem; }
    .btn-edit { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 8px 16px; font-size: 0.9rem; }
    .success-msg { color: #27ae60; font-weight: 600; margin-bottom: 16px; padding: 12px; background: #eafaf1; border-radius: 8px; }
    .error-msg { color: #e74c3c; font-weight: 600; margin-bottom: 16px; padding: 12px; background: #fdeaea; border-radius: 8px; }
    .exam-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 20px; }
    .exam-table th, .exam-table td { padding: 14px 12px; text-align: left; }
    .exam-table th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; font-weight: 600; }
    .exam-table tr:nth-child(even) td { background: #f8f8ff; }
    .exam-table td { background: rgba(255,255,255,0.8); color: #2d3748; }
    .filter-row { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: end; }
    .error-text { color: #e74c3c; font-size: 0.85rem; margin-top: 4px; display: block; min-height: 18px; }
    .form-control.error { border-color: #e74c3c; background-color: #fdeaea; }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <div class="modern-container">
    <h2><i class="fa-solid fa-calendar-days"></i> Add Exam Schedule</h2>
    <?php if ($success): ?>
      <div class="success-msg">
        <i class="fa-solid fa-check-circle"></i> 
        Exam schedule added successfully!
        <?php if ($announcement_posted): ?>
          <br><small>✓ Announcement posted successfully</small>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <?php if ($error): ?><div class="error-msg"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" id="addExamForm" novalidate>
      <input type="hidden" name="add_exam" value="1">
      <div class="form-grid">
        <div class="form-group">
          <label>Exam Name *</label>
          <select name="exam_name" id="exam_name" class="form-control">
            <option value="">Select Exam Type</option>
            <option value="Mid-Term Exam">Mid-Term Exam</option>
            <option value="End of Term Exam">End of Term Exam</option>
            <option value="Mock Exam">Mock Exam</option>
            <option value="Final Exam">Final Exam</option>
            <option value="Quiz">Quiz</option>
            <option value="Test">Test</option>
            <option value="UCE Exam">UCE Exam</option>
            <option value="UACE Exam">UACE Exam</option>
          </select>
          <span class="error-text" id="error-exam_name"></span>
        </div>
        <div class="form-group">
          <label>Class *</label>
          <select name="class_id" id="class_id" class="form-control">
            <option value="">Select Class</option>
            <?php if ($classes && $classes->num_rows > 0): foreach ($classes as $c): ?>
              <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['class_name']); ?></option>
            <?php endforeach; endif; ?>
          </select>
          <span class="error-text" id="error-class_id"></span>
        </div>
        <div class="form-group">
          <label>Subject *</label>
          <select name="subject_name" id="subject_name" class="form-control">
            <option value="">Select Subject</option>
            <?php foreach ($subjects as $subject): ?>
              <option value="<?php echo htmlspecialchars($subject); ?>"><?php echo htmlspecialchars($subject); ?></option>
            <?php endforeach; ?>
          </select>
          <span class="error-text" id="error-subject_name"></span>
        </div>
        <div class="form-group">
          <label>Exam Date *</label>
          <input type="date" name="exam_date" id="exam_date" class="form-control">
          <span class="error-text" id="error-exam_date"></span>
        </div>
        <div class="form-group">
          <label>Start Time *</label>
          <input type="time" name="start_time" id="start_time" class="form-control">
          <span class="error-text" id="error-start_time"></span>
        </div>
        <div class="form-group">
          <label>End Time *</label>
          <input type="time" name="end_time" id="end_time" class="form-control">
          <span class="error-text" id="error-end_time"></span>
        </div>
        <div class="form-group">
          <label>Room</label>
          <input type="text" name="room" id="room" class="form-control" placeholder="e.g. Room 101">
        </div>
        <div class="form-group">
          <label>Term *</label>
          <select name="term" id="term" class="form-control">
            <option value="">Select Term</option>
            <option value="1">Term 1</option>
            <option value="2">Term 2</option>
            <option value="3">Term 3</option>
          </select>
          <span class="error-text" id="error-term"></span>
        </div>
        <div class="form-group">
          <label>Year *</label>
          <input type="number" name="year" id="year" class="form-control" value="<?php echo date('Y'); ?>" min="2020" max="2099">
          <span class="error-text" id="error-year"></span>
        </div>
      </div>
      <div class="form-group" style="margin-bottom: 20px;">
        <label>Instructions</label>
        <textarea name="instructions" id="instructions" class="form-control" rows="3" placeholder="Any special instructions for this exam..."></textarea>
      </div>
      <div style="margin-bottom: 24px; padding: 16px; background: #f8f8ff; border-radius: 10px; border: 2px solid #e0e0e0;">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600; color: #764ba2; margin: 0;">
          <input type="checkbox" name="post_announcement" id="post_announcement" value="1" checked style="width: 20px; height: 20px; cursor: pointer; accent-color: #667eea;">
          <span style="font-size: 1rem;">Post this exam schedule as an announcement</span>
        </label>
        <small style="color: #666; margin-left: 30px; display: block; margin-top: 6px; font-size: 0.9rem;">
          <i class="fa-solid fa-info-circle"></i> Students and teachers will see this in the announcements section
        </small>
      </div>
      <button type="submit" class="btn"><i class="fa-solid fa-plus"></i> Add Exam Schedule</button>
    </form>
  </div>

  <div class="modern-container">
    <h2><i class="fa-solid fa-list"></i> Exam Schedules</h2>
    <form method="get" class="filter-row">
      <div class="form-group">
        <label>Filter by Class</label>
        <select name="class_id" class="form-control">
          <option value="">All Classes</option>
          <?php 
          $classes->data_seek(0);
          foreach ($classes as $c): ?>
            <option value="<?php echo $c['id']; ?>" <?php if ($class_filter == $c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['class_name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Filter by Term</label>
        <select name="term" class="form-control">
          <option value="">All Terms</option>
          <option value="1" <?php if ($term_filter == 1) echo 'selected'; ?>>Term 1</option>
          <option value="2" <?php if ($term_filter == 2) echo 'selected'; ?>>Term 2</option>
          <option value="3" <?php if ($term_filter == 3) echo 'selected'; ?>>Term 3</option>
        </select>
      </div>
      <div class="form-group">
        <label>Filter by Year</label>
        <input type="number" name="year" class="form-control" value="<?php echo $year_filter; ?>" min="2020" max="2099">
      </div>
      <div class="form-group">
        <label>&nbsp;</label>
        <button type="submit" class="btn">Apply Filters</button>
      </div>
    </form>
    
    <?php if ($exams && $exams->num_rows > 0): ?>
    <div style="overflow-x: auto;">
      <table class="exam-table">
        <thead>
          <tr>
            <th>Exam Name</th>
            <th>Class</th>
            <th>Subject</th>
            <th>Date</th>
            <th>Time</th>
            <th>Room</th>
            <th>Term</th>
            <th>Year</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($exam = $exams->fetch_assoc()): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($exam['exam_name']); ?></strong></td>
            <td><?php echo htmlspecialchars($exam['class_name']); ?></td>
            <td><?php echo htmlspecialchars($exam['subject_name']); ?></td>
            <td><?php echo date('M d, Y', strtotime($exam['exam_date'])); ?></td>
            <td><?php echo date('h:i A', strtotime($exam['start_time'])) . ' - ' . date('h:i A', strtotime($exam['end_time'])); ?></td>
            <td><?php echo htmlspecialchars($exam['room'] ?: '-'); ?></td>
            <td>Term <?php echo $exam['term']; ?></td>
            <td><?php echo $exam['year']; ?></td>
            <td>
              <button class="btn btn-edit btn-edit-exam" 
                      data-id="<?php echo $exam['id']; ?>"
                      data-exam_name="<?php echo htmlspecialchars($exam['exam_name']); ?>"
                      data-class_id="<?php echo $exam['class_id']; ?>"
                      data-subject_name="<?php echo htmlspecialchars($exam['subject_name']); ?>"
                      data-exam_date="<?php echo $exam['exam_date']; ?>"
                      data-start_time="<?php echo $exam['start_time']; ?>"
                      data-end_time="<?php echo $exam['end_time']; ?>"
                      data-room="<?php echo htmlspecialchars($exam['room']); ?>"
                      data-term="<?php echo $exam['term']; ?>"
                      data-year="<?php echo $exam['year']; ?>"
                      data-instructions="<?php echo htmlspecialchars($exam['instructions']); ?>">
                <i class="fa-solid fa-edit"></i> Edit
              </button>
              <button type="button" class="btn btn-danger" onclick="showDeleteModal('<?php echo htmlspecialchars(addslashes($exam['exam_name'] . ' - ' . $exam['subject_name'])); ?>', 'exam_schedule.php?delete=<?php echo $exam['id']; ?>')">
                <i class="fa-solid fa-trash"></i> Remove
              </button>
            </td>
          </tr>
          <?php if ($exam['instructions']): ?>
          <tr>
            <td colspan="9" style="background: #f0f0f0; padding: 10px; font-style: italic;">
              <strong>Instructions:</strong> <?php echo nl2br(htmlspecialchars($exam['instructions'])); ?>
            </td>
          </tr>
          <?php endif; ?>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 40px; color: #999;">
      <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; margin-bottom: 16px;"></i>
      <p>No exam schedules found. Add your first exam schedule above.</p>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="editExamModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modern-container" style="max-width:600px; max-height:90vh; overflow-y:auto; margin:20px;">
    <h2><i class="fa-solid fa-edit"></i> Edit Exam Schedule</h2>
    <form id="editExamForm">
      <input type="hidden" name="id" id="editExamId">
      <div class="form-grid">
        <div class="form-group">
          <label>Exam Name *</label>
          <select name="exam_name" id="editExamName" class="form-control" required>
            <option value="">Select Exam Type</option>
            <option value="Mid-Term Exam">Mid-Term Exam</option>
            <option value="End of Term Exam">End of Term Exam</option>
            <option value="Mock Exam">Mock Exam</option>
            <option value="Final Exam">Final Exam</option>
            <option value="Quiz">Quiz</option>
            <option value="Test">Test</option>
            <option value="UCE Exam">UCE Exam</option>
            <option value="UACE Exam">UACE Exam</option>
          </select>
        </div>
        <div class="form-group">
          <label>Class *</label>
          <select name="class_id" id="editClassId" class="form-control" required>
            <?php 
            $classes->data_seek(0);
            foreach ($classes as $c): ?>
              <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['class_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Subject *</label>
          <select name="subject_name" id="editSubjectName" class="form-control" required>
            <option value="">Select Subject</option>
            <?php foreach ($subjects as $subject): ?>
              <option value="<?php echo htmlspecialchars($subject); ?>"><?php echo htmlspecialchars($subject); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Exam Date *</label>
          <input type="date" name="exam_date" id="editExamDate" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Start Time *</label>
          <input type="time" name="start_time" id="editStartTime" class="form-control" required>
        </div>
        <div class="form-group">
          <label>End Time *</label>
          <input type="time" name="end_time" id="editEndTime" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Room</label>
          <input type="text" name="room" id="editRoom" class="form-control">
        </div>
        <div class="form-group">
          <label>Term *</label>
          <select name="term" id="editTerm" class="form-control" required>
            <option value="1">Term 1</option>
            <option value="2">Term 2</option>
            <option value="3">Term 3</option>
          </select>
        </div>
        <div class="form-group">
          <label>Year *</label>
          <input type="number" name="year" id="editYear" class="form-control" min="2020" max="2099" required>
        </div>
      </div>
      <div class="form-group" style="margin-bottom: 20px;">
        <label>Instructions</label>
        <textarea name="instructions" id="editInstructions" class="form-control" rows="3"></textarea>
      </div>
      <div id="editExamMsg"></div>
      <button type="submit" class="btn"><i class="fa-solid fa-save"></i> Save Changes</button>
      <button type="button" class="btn btn-danger" id="closeEditModal">Cancel</button>
    </form>
  </div>
</div>

<script>
// Custom Validation for Add Exam Form
const addExamForm = document.getElementById('addExamForm');
const fields = {
  exam_name: document.getElementById('exam_name'),
  class_id: document.getElementById('class_id'),
  subject_name: document.getElementById('subject_name'),
  exam_date: document.getElementById('exam_date'),
  start_time: document.getElementById('start_time'),
  end_time: document.getElementById('end_time'),
  term: document.getElementById('term'),
  year: document.getElementById('year')
};

function showError(fieldName, message) {
  const errorSpan = document.getElementById('error-' + fieldName);
  const field = fields[fieldName];
  if (errorSpan && field) {
    errorSpan.textContent = message;
    field.classList.add('error');
  }
}

function clearError(fieldName) {
  const errorSpan = document.getElementById('error-' + fieldName);
  const field = fields[fieldName];
  if (errorSpan && field) {
    errorSpan.textContent = '';
    field.classList.remove('error');
  }
}

function validateField(fieldName) {
  const field = fields[fieldName];
  if (!field) return true;
  
  clearError(fieldName);
  
  if (fieldName === 'exam_name') {
    if (!field.value.trim()) {
      showError(fieldName, 'Please enter exam name');
      return false;
    }
  }
  
  if (fieldName === 'class_id') {
    if (!field.value) {
      showError(fieldName, 'Please select a class');
      return false;
    }
  }
  
  if (fieldName === 'subject_name') {
    if (!field.value.trim()) {
      showError(fieldName, 'Please enter subject name');
      return false;
    }
  }
  
  if (fieldName === 'exam_date') {
    if (!field.value) {
      showError(fieldName, 'Please select exam date');
      return false;
    }
  }
  
  if (fieldName === 'start_time') {
    if (!field.value) {
      showError(fieldName, 'Please select start time');
      return false;
    }
  }
  
  if (fieldName === 'end_time') {
    if (!field.value) {
      showError(fieldName, 'Please select end time');
      return false;
    }
    // Check if end time is after start time
    if (fields.start_time.value && field.value <= fields.start_time.value) {
      showError(fieldName, 'End time must be after start time');
      return false;
    }
  }
  
  if (fieldName === 'term') {
    if (!field.value) {
      showError(fieldName, 'Please select a term');
      return false;
    }
  }
  
  if (fieldName === 'year') {
    if (!field.value) {
      showError(fieldName, 'Please enter year');
      return false;
    }
    const yearVal = parseInt(field.value);
    if (yearVal < 2020 || yearVal > 2099) {
      showError(fieldName, 'Year must be between 2020 and 2099');
      return false;
    }
  }
  
  return true;
}

// Real-time validation
Object.keys(fields).forEach(fieldName => {
  const field = fields[fieldName];
  if (field) {
    field.addEventListener('blur', () => validateField(fieldName));
    field.addEventListener('input', () => {
      if (field.classList.contains('error')) {
        validateField(fieldName);
      }
    });
  }
});

// Form submission
addExamForm.addEventListener('submit', function(e) {
  e.preventDefault();
  
  let isValid = true;
  Object.keys(fields).forEach(fieldName => {
    if (!validateField(fieldName)) {
      isValid = false;
    }
  });
  
    if (isValid) {
    // Show custom confirmation
    const examName = fields.exam_name.value;
    const className = fields.class_id.options[fields.class_id.selectedIndex].text;
    const subjectName = fields.subject_name.value;
    const examDate = fields.exam_date.value;
    const msg = `Exam: ${examName}\nClass: ${className}\nSubject: ${subjectName}\nDate: ${examDate}\n\nWould you like to save this exam schedule?`;
    showConfirmModal(msg, () => { addExamForm.submit(); }, { title: 'Save Exam Schedule?', confirmText: 'Yes, Save', icon: 'fa-calendar-check', isWarning: false });
  } else {
    // Scroll to first error
    const firstError = document.querySelector('.form-control.error');
    if (firstError) {
      firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstError.focus();
    }
  }
});

// Edit Modal
const editBtns = document.querySelectorAll('.btn-edit-exam');
const editModal = document.getElementById('editExamModal');
const editForm = document.getElementById('editExamForm');
const editMsg = document.getElementById('editExamMsg');
const closeEditModal = document.getElementById('closeEditModal');

editBtns.forEach(btn => {
  btn.addEventListener('click', function() {
    document.getElementById('editExamId').value = this.dataset.id;
    document.getElementById('editExamName').value = this.dataset.exam_name;
    document.getElementById('editClassId').value = this.dataset.class_id;
    document.getElementById('editSubjectName').value = this.dataset.subject_name;
    document.getElementById('editExamDate').value = this.dataset.exam_date;
    document.getElementById('editStartTime').value = this.dataset.start_time;
    document.getElementById('editEndTime').value = this.dataset.end_time;
    document.getElementById('editRoom').value = this.dataset.room;
    document.getElementById('editTerm').value = this.dataset.term;
    document.getElementById('editYear').value = this.dataset.year;
    document.getElementById('editInstructions').value = this.dataset.instructions;
    editMsg.textContent = '';
    editModal.style.display = 'flex';
  });
});

closeEditModal.onclick = () => { editModal.style.display = 'none'; };

editForm.onsubmit = function(e) {
  e.preventDefault();
  editMsg.textContent = '';
  const formData = new FormData(editForm);
  fetch('edit_exam_ajax.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      editMsg.textContent = 'Exam schedule updated! Reloading...';
      editMsg.style.color = '#27ae60';
      setTimeout(() => window.location.reload(), 1200);
    } else {
      editMsg.textContent = data.error || 'Update failed.';
      editMsg.style.color = '#e74c3c';
    }
  })
  .catch(() => {
    editMsg.textContent = 'Network error.';
    editMsg.style.color = '#e74c3c';
  });
};
</script>
<?php include 'delete_modal.php'; ?>
</body>
</html>
