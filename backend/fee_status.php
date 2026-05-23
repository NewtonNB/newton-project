<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../shared/config.php';

// Get filter parameters
$class_filter = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$term_filter = isset($_GET['term']) ? $_GET['term'] : '';
$year_filter = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Fetch classes for filter
$classes = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name ASC");

// Build query
$where_clauses = ["s.usertype = 'student'"];
$params = [];
$types = '';

if ($class_filter) {
    $where_clauses[] = "s.class_id = ?";
    $params[] = $class_filter;
    $types .= 'i';
}

if ($term_filter) {
    $where_clauses[] = "f.term = ?";
    $params[] = $term_filter;
    $types .= 's';
}

if ($year_filter) {
    $where_clauses[] = "f.academic_year = ?";
    $params[] = $year_filter;
    $types .= 's';
}

$where_sql = implode(' AND ', $where_clauses);

// Fetch payment data
$query = "SELECT s.id, s.username, c.class_name, 
          COALESCE(SUM(f.amount_paid), 0) as total_paid,
          COUNT(f.id) as payment_count
          FROM students s
          LEFT JOIN classes c ON s.class_id = c.id
          LEFT JOIN fees f ON s.id = f.student_id";

if ($term_filter || $year_filter) {
    $query .= " AND " . implode(' AND ', array_slice($where_clauses, 1));
}

$query .= " WHERE " . $where_clauses[0];
if ($class_filter) {
    $query .= " AND s.class_id = ?";
}
$query .= " GROUP BY s.id, s.username, c.class_name ORDER BY s.username ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$students = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fee Status - Nyabikoni Secondary School</title>
  <?php include '../frontend/admin_css.php'; ?>
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
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin: 20px 0;
    }
    .stat-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px;
      border-radius: 12px;
      text-align: center;
    }
    .stat-card h3 {
      margin: 0;
      font-size: 2rem;
    }
    .stat-card p {
      margin: 5px 0 0 0;
      opacity: 0.9;
    }
    .table-container {
      overflow-x: auto;
      margin-top: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 12px;
      text-align: left;
      font-weight: 600;
    }
    td {
      padding: 12px;
      border-bottom: 1px solid #e0e0e0;
    }
    tr:hover {
      background: #f8f9ff;
    }
    .btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      text-decoration: none;
      display: inline-block;
      font-size: 0.9rem;
      cursor: pointer;
    }
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    .amount {
      font-weight: 700;
      color: #10b981;
      font-size: 1.1rem;
    }
  </style>
</head>
<body>
<?php include '../frontend/admin_sidebar.php'; ?>
<div class="content">
  <div class="modern-container">
    <div class="page-header">
      <h1><i class="fas fa-receipt"></i> Fee Status</h1>
      <p>View student payment history and status</p>
    </div>
    
    <form method="get" class="filters">
      <div class="filter-group">
        <label>Class</label>
        <select name="class_id" onchange="this.form.submit()">
          <option value="0">All Classes</option>
          <?php if ($classes): while ($class = $classes->fetch_assoc()): ?>
            <option value="<?php echo $class['id']; ?>" <?php echo ($class_filter == $class['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($class['class_name']); ?>
            </option>
          <?php endwhile; endif; ?>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Term</label>
        <select name="term" onchange="this.form.submit()">
          <option value="">All Terms</option>
          <option value="Term 1" <?php echo ($term_filter == 'Term 1') ? 'selected' : ''; ?>>Term 1</option>
          <option value="Term 2" <?php echo ($term_filter == 'Term 2') ? 'selected' : ''; ?>>Term 2</option>
          <option value="Term 3" <?php echo ($term_filter == 'Term 3') ? 'selected' : ''; ?>>Term 3</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Year</label>
        <input type="number" name="year" value="<?php echo $year_filter; ?>" min="2020" max="2030" onchange="this.form.submit()">
      </div>
    </form>
    
    <?php
    // Calculate statistics
    $total_collected = 0;
    $students_paid = 0;
    $total_students = 0;
    
    $students->data_seek(0);
    while ($row = $students->fetch_assoc()) {
      $total_students++;
      $total_collected += $row['total_paid'];
      if ($row['total_paid'] > 0) {
        $students_paid++;
      }
    }
    $students->data_seek(0);
    ?>
    
    <div class="stats-grid">
      <div class="stat-card">
        <h3>UGX <?php echo number_format($total_collected); ?></h3>
        <p>Total Collected</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $students_paid; ?> / <?php echo $total_students; ?></h3>
        <p>Students Paid</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $total_students > 0 ? round(($students_paid / $total_students) * 100) : 0; ?>%</h3>
        <p>Payment Rate</p>
      </div>
    </div>
    
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Class</th>
            <th>Total Paid</th>
            <th>Payments</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($students->num_rows > 0): 
            $i = 1;
            while ($student = $students->fetch_assoc()): ?>
          <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($student['username']); ?></td>
            <td><?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?></td>
            <td class="amount">UGX <?php echo number_format($student['total_paid']); ?></td>
            <td><?php echo $student['payment_count']; ?> payment(s)</td>
            <td>
              <a href="view_payment_details.php?student_id=<?php echo $student['id']; ?>" class="btn">
                <i class="fas fa-eye"></i> View Details
              </a>
            </td>
          </tr>
          <?php endwhile; 
          else: ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
              <i class="fas fa-inbox"></i><br>No payment records found
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
