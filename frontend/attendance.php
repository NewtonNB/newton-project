<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../shared/config.php';
// Fetch classes
$classes = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name ASC");
// Handle form submission
$success = false; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_id'], $_POST['date'], $_POST['attendance'])) {
    $class_id = intval($_POST['class_id']);
    $date = $_POST['date'];
    $attendance = $_POST['attendance']; // [student_id => status]
    foreach ($attendance as $student_id => $status) {
        $student_id = intval($student_id);
        $status = in_array($status, ['Present','Absent','Late','Excused']) ? $status : 'Absent';
        // Upsert attendance
        $stmt = $conn->prepare("REPLACE INTO attendance (student_id, class_id, date, status, marked_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iisss', $student_id, $class_id, $date, $status, $_SESSION['admin']);
        $stmt->execute();
    }
    $success = true;
}
// Fetch students for selected class
$selected_class = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$students = [];
if ($selected_class) {
    $students_res = $conn->query("SELECT id, username as full_name FROM students WHERE class_id = $selected_class ORDER BY username ASC");
    if ($students_res) {
        while ($row = $students_res->fetch_assoc()) {
            $students[] = $row;
        }
    }
}
// Fetch attendance records for selected class and date
$attendance_map = [];
// Fetch recent attendance history for the selected class
$history = [];
if ($selected_class) {
    $hist_res = $conn->query("SELECT date, 
        SUM(status='Present') as present, 
        SUM(status='Absent') as absent, 
        SUM(status='Late') as late, 
        SUM(status='Excused') as excused
        FROM attendance WHERE class_id = $selected_class
        GROUP BY date ORDER BY date DESC LIMIT 7");
    if ($hist_res) {
        while ($row = $hist_res->fetch_assoc()) $history[] = $row;
    }
    // Fetch attendance for this class and date
    $att_res = $conn->query("SELECT student_id, status FROM attendance WHERE class_id = $selected_class AND date = '" . $conn->real_escape_string($selected_date) . "'");
    if ($att_res) {
        while ($att_row = $att_res->fetch_assoc()) {
            $attendance_map[$att_row['student_id']] = $att_row['status'];
        }
    }
}
// Fetch all classes for display at the bottom
$all_classes = [];
$all_classes_res = $conn->query("SELECT id, class_name FROM classes ORDER BY id ASC");
if ($all_classes_res) {
    while ($row = $all_classes_res->fetch_assoc()) {
        $all_classes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Take Attendance</title>
  <?php include 'admin_css.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; font-family: 'Poppins', sans-serif; margin: 0; padding: 0; }
    .content { margin-top: 40px; margin-left: 280px; padding: 20px; max-width: calc(100vw - 320px); }
    .modern-table-container { background: rgba(255,255,255,0.97); backdrop-filter: blur(20px); border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1), 0 8px 16px rgba(0,0,0,0.05), inset 0 1px 0 rgba(255,255,255,0.8); padding: 48px 40px 40px 40px; width: 100%; max-width: 100%; margin-top: 0; border: 1px solid rgba(255,255,255,0.2); position: relative; overflow: hidden; }
    .modern-table-container::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c); border-radius: 24px 24px 0 0; }
    .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
    .modern-table-container h2 { font-size: 2.1rem; font-weight: 800; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 0; display: flex; align-items: center; gap: 16px; letter-spacing: -0.5px; }
    .table-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 2.2rem; filter: drop-shadow(0 4px 8px rgba(102, 126, 234, 0.3)); }
    .form-row { display: flex; gap: 18px; margin-bottom: 24px; }
    .form-row label { font-weight: 600; color: #764ba2; margin-right: 8px; }
    .form-select, .form-date { padding: 12px 18px; border-radius: 12px; border: 2px solid #667eea; font-size: 1.08rem; background: #f8f8ff; color: #333; font-weight: 500; box-shadow: 0 2px 8px rgba(102,126,234,0.07); outline: none; }
    .form-select:focus, .form-date:focus { border-color: #764ba2; box-shadow: 0 0 0 4px rgba(118,75,162,0.13); }
    .attendance-table { width: 100%; border-collapse: separate; border-spacing: 0; background: transparent; margin-bottom: 0; }
    .attendance-table th, .attendance-table td { padding: 14px 12px; font-size: 1rem; text-align: left; }
    .attendance-table th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; font-weight: 600; border: none; }
    .attendance-table tr:nth-child(even) td { background: #f8f8ff; }
    .attendance-table td { background: rgba(255,255,255,0.8); color: #2d3748; font-weight: 500; }
    .attendance-status { display: flex; gap: 8px; }
    .bulk-actions { margin-bottom: 18px; }
    .btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; border-radius: 10px; padding: 10px 24px; font-size: 1.05rem; font-weight: 700; cursor: pointer; box-shadow: 0 2px 12px rgba(102,126,234,0.10); transition: background 0.22s, box-shadow 0.22s, transform 0.18s; margin-right: 8px; }
    .btn:hover { background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%); transform: scale(1.04); }
    .success-msg { color: #27ae60; font-weight: 600; margin-bottom: 12px; }
    .error-msg { color: #e74c3c; font-weight: 600; margin-bottom: 12px; }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <div class="modern-table-container">
    <div class="header-section">
      <h2><span class="table-icon"><i class="fa-solid fa-clipboard-list"></i></span> Take Attendance</h2>
    </div>
    <?php if ($success): ?><div class="success-msg">Attendance saved successfully!</div><?php endif; ?>
    <?php if ($error): ?><div class="error-msg"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="get" class="form-row" id="class-date-form" style="margin-bottom:0;">
      <label for="class_id">Class:</label>
      <select name="class_id" id="class_id" class="form-select" required>
        <option value="">Select Class</option>
        <?php if ($classes && $classes->num_rows > 0): foreach ($classes as $c): ?>
          <option value="<?php echo $c['id']; ?>" <?php if ($selected_class == $c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['class_name']); ?></option>
        <?php endforeach; endif; ?>
      </select>
      <label for="date">Date:</label>
      <input type="date" name="date" id="date" class="form-date" value="<?php echo htmlspecialchars($selected_date); ?>">
    </form>
    <script>
      // Auto-submit form when class or date changes
      document.getElementById('class_id').addEventListener('change', function() {
        if (this.value) {
          document.getElementById('class-date-form').submit();
        }
      });
      document.getElementById('date').addEventListener('change', function() {
        const classId = document.getElementById('class_id').value;
        if (classId) {
          document.getElementById('class-date-form').submit();
        }
      });
    </script>
    <div id="attendance-section">
    <?php if ($selected_class && $students): ?>
    <form method="post" id="attendance-form">
      <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">
      <input type="hidden" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">
      <div class="bulk-actions">
        <button type="button" class="btn" onclick="markAll('Present')">Mark All Present</button>
        <button type="button" class="btn" onclick="markAll('Absent')">Mark All Absent</button>
      </div>
      <div style="margin-bottom:18px;">
        <input type="text" id="student-search" placeholder="Search students..." style="padding:10px 16px; border-radius:8px; border:1.5px solid #764ba2; width:100%; max-width:320px; font-size:1rem;">
      </div>
      <div style="margin-bottom:18px;">
        <button type="button" class="btn" id="export-excel-btn"><i class="fa-solid fa-file-excel"></i> Export Excel</button>
        <button type="button" class="btn" id="export-pdf-btn"><i class="fa-solid fa-file-pdf"></i> Export PDF</button>
        <button type="button" class="btn" id="print-btn"><i class="fa-solid fa-print"></i> Print</button>
      </div>
      <div id="attendance-summary" style="margin-bottom:18px; font-weight:600; color:#764ba2;"></div>
      <div class="table-responsive">
        <table class="attendance-table" id="attendance-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Student Name</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach ($students as $stu): ?>
            <tr data-student-name="<?php echo htmlspecialchars(strtolower($stu['full_name'])); ?>">
              <td><?php echo $i++; ?></td>
              <td><?php echo htmlspecialchars($stu['full_name']); ?></td>
              <td>
                <div class="attendance-status">
                  <label><input type="radio" name="attendance[<?php echo $stu['id']; ?>]" value="Present" required <?php if(isset($attendance_map[$stu['id']]) && $attendance_map[$stu['id']] == 'Present') echo 'checked'; ?>> Present</label>
                  <label><input type="radio" name="attendance[<?php echo $stu['id']; ?>]" value="Absent" <?php if(isset($attendance_map[$stu['id']]) && $attendance_map[$stu['id']] == 'Absent') echo 'checked'; ?>> Absent</label>
                  <label><input type="radio" name="attendance[<?php echo $stu['id']; ?>]" value="Late" <?php if(isset($attendance_map[$stu['id']]) && $attendance_map[$stu['id']] == 'Late') echo 'checked'; ?>> Late</label>
                  <label><input type="radio" name="attendance[<?php echo $stu['id']; ?>]" value="Excused" <?php if(isset($attendance_map[$stu['id']]) && $attendance_map[$stu['id']] == 'Excused') echo 'checked'; ?>> Excused</label>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div id="attendance-msg"></div>
      <div id="attendance-loading" style="display:none; color:#764ba2; font-weight:600; margin-top:10px;">Saving...</div>
      <button type="submit" class="btn" id="attendance-save-btn" style="margin-top:18px;">Save Attendance</button>
    </form>
    <div id="quick-add-student" style="margin-top:32px; background:#f8f8ff; border-radius:12px; padding:18px 24px; max-width:500px; box-shadow:0 2px 8px rgba(102,126,234,0.07);">
      <h4 style="color:#764ba2; margin-bottom:12px;">Quick Add Student to This Class</h4>
      <form id="quick-add-student-form">
        <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">
        <div style="margin-bottom:10px;">
          <input type="text" name="full_name" placeholder="Full Name" required style="width:100%;padding:8px 12px; border-radius:8px; border:1.5px solid #764ba2;">
        </div>
        <div style="margin-bottom:10px;">
          <input type="text" name="username" placeholder="Username" required style="width:100%;padding:8px 12px; border-radius:8px; border:1.5px solid #764ba2;">
        </div>
        <div style="margin-bottom:10px;">
          <input type="email" name="email" placeholder="Email (optional)" style="width:100%;padding:8px 12px; border-radius:8px; border:1.5px solid #764ba2;">
        </div>
        <div style="margin-bottom:10px;">
          <input type="text" name="phone" placeholder="Phone (optional)" style="width:100%;padding:8px 12px; border-radius:8px; border:1.5px solid #764ba2;">
        </div>
        <button type="submit" class="btn">Add Student</button>
        <span id="quick-add-student-msg" style="margin-left:12px;"></span>
      </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
    <script>
      function markAll(status) {
        document.querySelectorAll('.attendance-status').forEach(function(row) {
          row.querySelectorAll('input[type=radio]').forEach(function(radio) {
            radio.checked = (radio.value === status);
          });
        });
        updateSummaryAndColors();
      }
      // Search/filter students
      document.getElementById('student-search').addEventListener('input', function() {
        const search = this.value.trim().toLowerCase();
        document.querySelectorAll('#attendance-table tbody tr').forEach(function(row) {
          const name = row.getAttribute('data-student-name');
          row.style.display = (!search || name.includes(search)) ? '' : 'none';
        });
      });
      // Color coding and summary
      function updateSummaryAndColors() {
        const rows = document.querySelectorAll('#attendance-table tbody tr');
        let present=0, absent=0, late=0, excused=0;
        rows.forEach(function(row) {
          let status = '';
          row.querySelectorAll('input[type=radio]').forEach(function(radio) {
            if (radio.checked) status = radio.value;
          });
          row.classList.remove('present-row','absent-row','late-row','excused-row');
          if (status === 'Present') { present++; row.classList.add('present-row'); }
          else if (status === 'Absent') { absent++; row.classList.add('absent-row'); }
          else if (status === 'Late') { late++; row.classList.add('late-row'); }
          else if (status === 'Excused') { excused++; row.classList.add('excused-row'); }
        });
        document.getElementById('attendance-summary').innerHTML =
          `Present: <span style='color:#27ae60;'>${present}</span> &nbsp; | &nbsp; `+
          `Absent: <span style='color:#e74c3c;'>${absent}</span> &nbsp; | &nbsp; `+
          `Late: <span style='color:#f39c12;'>${late}</span> &nbsp; | &nbsp; `+
          `Excused: <span style='color:#2980b9;'>${excused}</span>`;
      }
      document.querySelectorAll('.attendance-status input[type=radio]').forEach(function(radio) {
        radio.addEventListener('change', updateSummaryAndColors);
      });
      // Initial update
      updateSummaryAndColors();
      // Color coding CSS
      const style = document.createElement('style');
      style.innerHTML = `
        .present-row { background: #eafaf1 !important; }
        .absent-row { background: #fdeaea !important; }
        .late-row { background: #fff7e6 !important; }
        .excused-row { background: #eaf4fb !important; }
      `;
      document.head.appendChild(style);
      // Export to Excel
      document.getElementById('export-excel-btn').addEventListener('click', function() {
        const table = document.getElementById('attendance-table');
        const wb = XLSX.utils.table_to_book(table, {sheet: 'Attendance'});
        XLSX.writeFile(wb, 'attendance.xlsx');
      });
      // Export to PDF
      document.getElementById('export-pdf-btn').addEventListener('click', function() {
        const table = document.getElementById('attendance-table');
        const rows = Array.from(table.querySelectorAll('tbody tr')).filter(row => row.style.display !== 'none');
        const data = rows.map(row => [
          row.cells[0].textContent,
          row.cells[1].textContent,
          Array.from(row.querySelectorAll('input[type=radio]')).find(r=>r.checked)?.value || ''
        ]);
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text('Attendance', 14, 14);
        doc.autoTable({
          head: [['#', 'Student Name', 'Status']],
          body: data,
          startY: 20
        });
        doc.save('attendance.pdf');
      });
      // Print
      document.getElementById('print-btn').addEventListener('click', function() {
        window.print();
      });
    </script>
    <?php elseif ($selected_class): ?>
      <div style="margin-top:24px; color:#888;">No students found for this class.</div>
    <?php endif; ?>
  </div>
    <?php if ($selected_class && count($history) > 0): ?>
    <div style="margin-top:40px;">
      <h3 style="color:#764ba2; font-size:1.2rem; font-weight:700; margin-bottom:12px;">Recent Attendance History</h3>
      <table style="width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(102,126,234,0.07);">
        <thead>
          <tr style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:#fff;">
            <th style="padding:10px 8px;">Date</th>
            <th style="padding:10px 8px;">Present</th>
            <th style="padding:10px 8px;">Absent</th>
            <th style="padding:10px 8px;">Late</th>
            <th style="padding:10px 8px;">Excused</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($history as $h): ?>
          <tr class="history-row" data-history-date="<?php echo htmlspecialchars($h['date']); ?>" style="cursor:pointer;">
            <td style="padding:8px 8px; color:#764ba2; font-weight:600;"><?php echo htmlspecialchars($h['date']); ?></td>
            <td style="padding:8px 8px; color:#27ae60; font-weight:600;"><?php echo $h['present']; ?></td>
            <td style="padding:8px 8px; color:#e74c3c; font-weight:600;"><?php echo $h['absent']; ?></td>
            <td style="padding:8px 8px; color:#f39c12; font-weight:600;"><?php echo $h['late']; ?></td>
            <td style="padding:8px 8px; color:#2980b9; font-weight:600;"><?php echo $h['excused']; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <script>
    // Click on history row loads that date via AJAX
    document.querySelectorAll('.history-row').forEach(function(row) {
      row.addEventListener('click', function() {
        const date = this.getAttribute('data-history-date');
        document.getElementById('date').value = date;
        // Trigger change event to load attendance for that date
        const event = new Event('change', { bubbles: true });
        document.getElementById('date').dispatchEvent(event);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });
    </script>
    <?php endif; ?>
    
    <?php if ($selected_class): ?>
    <!-- View All Attendance Records Section -->
    <div style="margin-top:50px; padding-top:30px; border-top:2px solid #e0e0e0;">
      <h3 style="color:#764ba2; font-size:1.3rem; font-weight:700; margin-bottom:20px;">
        <i class="fas fa-history"></i> All Attendance Records
      </h3>
      
      <?php
      // Fetch all attendance records for the selected class
      $all_records_query = "SELECT a.date, a.status, s.username, s.id as student_id
                           FROM attendance a
                           JOIN students s ON a.student_id = s.id
                           WHERE a.class_id = $selected_class
                           ORDER BY a.date DESC, s.username ASC";
      $all_records = $conn->query($all_records_query);
      
      // Group by date
      $records_by_date = [];
      if ($all_records && $all_records->num_rows > 0) {
        while ($record = $all_records->fetch_assoc()) {
          $records_by_date[$record['date']][] = $record;
        }
      }
      ?>
      
      <?php if (count($records_by_date) > 0): ?>
        <div style="background:#f8f9ff; padding:20px; border-radius:12px; margin-bottom:20px;">
          <p style="margin:0; color:#666;">
            <strong>Total Records:</strong> <?php echo count($records_by_date); ?> days | 
            <strong>Class:</strong> <?php 
              $class_name_query = $conn->query("SELECT class_name FROM classes WHERE id = $selected_class");
              $class_name_row = $class_name_query->fetch_assoc();
              echo htmlspecialchars($class_name_row['class_name']);
            ?>
          </p>
        </div>
        
        <div style="max-height:600px; overflow-y:auto; border:1px solid #e0e0e0; border-radius:12px;">
          <?php foreach ($records_by_date as $date => $records): 
            $present = $absent = $late = $excused = 0;
            foreach ($records as $r) {
              if ($r['status'] == 'Present') $present++;
              elseif ($r['status'] == 'Absent') $absent++;
              elseif ($r['status'] == 'Late') $late++;
              elseif ($r['status'] == 'Excused') $excused++;
            }
          ?>
          <div style="background:#fff; margin-bottom:15px; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
            <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:#fff; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
              <div>
                <strong style="font-size:1.1rem;"><?php echo date('l, F j, Y', strtotime($date)); ?></strong>
              </div>
              <div style="display:flex; gap:15px; font-size:0.9rem; flex-wrap:wrap;">
                <span><i class="fas fa-check-circle"></i> <?php echo $present; ?> Present</span>
                <span><i class="fas fa-times-circle"></i> <?php echo $absent; ?> Absent</span>
                <span><i class="fas fa-clock"></i> <?php echo $late; ?> Late</span>
                <span><i class="fas fa-user-check"></i> <?php echo $excused; ?> Excused</span>
              </div>
            </div>
            
            <div style="padding:15px 20px;">
              <table style="width:100%; border-collapse:collapse;">
                <thead>
                  <tr style="border-bottom:2px solid #e0e0e0;">
                    <th style="padding:8px; text-align:left; color:#764ba2;">Student</th>
                    <th style="padding:8px; text-align:center; color:#764ba2;">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($records as $record): 
                    $status_color = '#27ae60';
                    if ($record['status'] == 'Absent') $status_color = '#e74c3c';
                    elseif ($record['status'] == 'Late') $status_color = '#f39c12';
                    elseif ($record['status'] == 'Excused') $status_color = '#2980b9';
                  ?>
                  <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:8px;"><?php echo htmlspecialchars($record['username']); ?></td>
                    <td style="padding:8px; text-align:center;">
                      <span style="background:<?php echo $status_color; ?>; color:#fff; padding:4px 12px; border-radius:20px; font-size:0.85rem; font-weight:600;">
                        <?php echo $record['status']; ?>
                      </span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="text-align:center; padding:60px; background:#f8f9ff; border-radius:12px;">
          <i class="fas fa-calendar-times" style="font-size:3rem; color:#ccc; margin-bottom:15px;"></i>
          <p style="color:#999; font-size:1.1rem;">No attendance records found for this class yet.</p>
          <p style="color:#999;">Start taking attendance to see records here.</p>
        </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php if (count($all_classes) > 0): ?>
    <div style="margin:40px 0 20px 0;">
      <h3 style="color:#764ba2; font-size:1.1rem; font-weight:700; margin-bottom:10px;">All Classes (for reference)</h3>
      <table style="width:100%; max-width:400px; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(102,126,234,0.07);">
        <thead>
          <tr style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:#fff;">
            <th style="padding:8px 8px;">Class ID</th>
            <th style="padding:8px 8px;">Class Name</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($all_classes as $c): ?>
          <tr>
            <td style="padding:8px 8px; color:#764ba2; font-weight:600;"> <?php echo $c['id']; ?> </td>
            <td style="padding:8px 8px; color:#333; font-weight:500;"> <?php echo htmlspecialchars($c['class_name']); ?> </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html> 