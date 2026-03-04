<?php

session_start();

if(!isset($_SESSION['username']))
{
    header("location:login.php");
}
elseif($_SESSION['usertype']=='student'){
        header("location:login.php");
}

// Use centralized database connection
require 'config.php';

// Fetch all classes for dropdown
$class_options = [];
$classes = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name ASC");
if ($classes) {
    while ($c = $classes->fetch_assoc()) {
        $class_options[$c['id']] = $c['class_name'];
    }
}

// Handle class assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_student_id'], $_POST['assign_class_id'])) {
    $student_id = intval($_POST['assign_student_id']);
    $class_id = intval($_POST['assign_class_id']);
    $conn->query("UPDATE students SET class_id = $class_id WHERE id = $student_id");
    $assign_success = true;
}

// Handle class filter
$class_filter = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$level_filter = isset($_GET['level']) ? $_GET['level'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$class_filter_sql = $class_filter ? " AND class_id = $class_filter" : '';
$level_filter_sql = $level_filter ? " AND class_id IN (SELECT id FROM classes WHERE level = '" . $conn->real_escape_string($level_filter) . "')" : '';
$status_filter_sql = $status_filter ? " AND status = '" . $conn->real_escape_string($status_filter) . "'" : '';

// Pagination setup
$studentsPerPage = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Count total students for current filters
$count_sql = "SELECT COUNT(*) as total FROM students WHERE 1 $class_filter_sql $level_filter_sql $status_filter_sql";
$count_result = $conn->query($count_sql);
$totalStudents = $count_result ? (int)$count_result->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalStudents / $studentsPerPage);
$offset = ($page - 1) * $studentsPerPage;

// Fetch paginated students
$sql = "SELECT * FROM students WHERE 1 $class_filter_sql $level_filter_sql $status_filter_sql LIMIT $studentsPerPage OFFSET $offset";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <?php include 'admin_css.php'; ?>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    * {
        font-family: 'Inter', sans-serif;
    }
    
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }
    
    .content {
        margin-top: 40px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 80vh;
        padding: 0 20px;
    }
    
    .modern-table-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.1),
            0 8px 16px rgba(0, 0, 0, 0.05),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        padding: 48px 40px 40px 40px;
        width: 100%;
        max-width: 1200px;
        margin-top: 0;
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .modern-table-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
        border-radius: 24px 24px 0 0;
    }
    
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        position: relative;
    }
    
    .modern-table-container h2 {
        font-size: 2.4rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 16px;
        letter-spacing: -0.5px;
    }
    
    .table-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 2.6rem;
        filter: drop-shadow(0 4px 8px rgba(102, 126, 234, 0.3));
    }
    
    .add-student-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 16px 32px;
        border-radius: 16px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 
            0 8px 25px rgba(102, 126, 234, 0.3),
            0 4px 10px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .add-student-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .add-student-btn:hover::before {
        left: 100%;
    }
    
    .add-student-btn:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 
            0 12px 35px rgba(102, 126, 234, 0.4),
            0 6px 15px rgba(0, 0, 0, 0.15);
    }
    
    .add-student-btn:active {
        transform: translateY(-2px) scale(1.01);
    }
    
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        margin-bottom: 24px;
    }
    
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: transparent;
        margin-bottom: 0;
    }
    
    .table_th, .modern-table th {
        padding: 20px 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
        text-align: left;
        border: none;
        position: relative;
        letter-spacing: 0.5px;
    }
    
    .modern-table th:first-child {
        border-top-left-radius: 16px;
    }
    
    .modern-table th:last-child {
        border-top-right-radius: 16px;
    }
    
    .table_td, .modern-table td {
        padding: 18px 24px;
        background: rgba(255, 255, 255, 0.8);
        font-size: 1rem;
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        color: #2d3748;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .modern-table tr:last-child td:first-child {
        border-bottom-left-radius: 16px;
    }
    
    .modern-table tr:last-child td:last-child {
        border-bottom-right-radius: 16px;
    }
    
    .modern-table tr:hover td {
        background: rgba(102, 126, 234, 0.05);
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }
    
    .btn-action {
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        border: none;
        outline: none;
        cursor: pointer;
        margin-right: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        position: relative;
        overflow: hidden;
    }
    
    .btn-edit { 
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
    }
    
    .btn-edit:hover { 
        background: linear-gradient(135deg, #e91e63 0%, #f44336 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(240, 147, 251, 0.4);
    }
    
    .btn-delete { 
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    }
    
    .btn-delete:hover { 
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    }
    
    .btn-action::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn-action:hover::before {
        left: 100%;
    }
    
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0; width: 100vw; height: 100vh;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(8px);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      animation: fadeInBg 0.3s;
    }
    
    .modal.active {
      display: flex;
    }
    
    @keyframes fadeInBg {
      from { 
        background: rgba(0, 0, 0, 0);
        backdrop-filter: blur(0px);
      }
      to { 
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
      }
    }
    
    .modal .modal-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      max-width: 480px;
      width: 92vw;
      margin: auto;
      padding: 40px 32px 32px 32px;
      position: relative;
      box-shadow: 
          0 25px 50px rgba(0, 0, 0, 0.25),
          0 10px 20px rgba(0, 0, 0, 0.1);
      animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      overflow-y: auto;
      max-height: 90vh;
    }
    
    @keyframes popIn {
      from { 
        transform: scale(0.8) translateY(60px); 
        opacity: 0; 
      }
      to { 
        transform: scale(1) translateY(0); 
        opacity: 1; 
      }
    }
    
    .modal h2 {
      margin-bottom: 24px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-size: 1.8rem;
      font-weight: 700;
      text-align: center;
      letter-spacing: -0.5px;
    }
    
    .modal .close-x {
      position: absolute;
      top: 20px; right: 24px;
      font-size: 1.8rem;
      color: #a0aec0;
      background: none;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      z-index: 2;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .modal .close-x:hover {
      color: #e74c3c;
      background: rgba(231, 76, 60, 0.1);
      transform: rotate(90deg);
    }
    
    .modal label {
      font-weight: 600;
      color: #2d3748;
      margin-bottom: 8px;
      display: block;
      font-size: 0.95rem;
      letter-spacing: 0.3px;
    }
    
    .modal input, .modal select {
      width: 100%;
      padding: 14px 18px;
      border: 2px solid rgba(102, 126, 234, 0.2);
      border-radius: 12px;
      font-size: 1rem;
      background: rgba(255, 255, 255, 0.8);
      color: #2d3748;
      outline: none;
      margin-bottom: 18px;
      transition: all 0.3s ease;
      font-weight: 500;
    }
    
    .modal input:focus, .modal select:focus {
      border-color: #667eea;
      background: #fff;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
      transform: translateY(-2px);
    }
    
    .modal .submit-btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      padding: 16px 0;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      margin-bottom: 12px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
      position: relative;
      overflow: hidden;
    }
    
    .modal .submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .modal .submit-btn:hover::before {
        left: 100%;
    }
    
    .modal .submit-btn:hover {
      background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    }
    
    .modal .cancel-btn {
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
      color: #fff;
      border: none;
      padding: 16px 0;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      margin-bottom: 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
    }
    
    .modal .cancel-btn:hover {
      background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(255, 107, 107, 0.4);
    }
    
    .modal .success-msg {
      margin-bottom: 16px;
      color: #38a169;
      font-weight: 600;
      text-align: center;
      padding: 12px;
      background: rgba(56, 161, 105, 0.1);
      border-radius: 8px;
      border: 1px solid rgba(56, 161, 105, 0.2);
    }
    
    .modal .error-msg {
      margin-bottom: 16px;
      color: #e74c3c;
      font-weight: 600;
      text-align: center;
      padding: 12px;
      background: rgba(231, 76, 60, 0.1);
      border-radius: 8px;
      border: 1px solid rgba(231, 76, 60, 0.2);
    }
    
    /* Stats Cards */
    .stats-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    
    .stat-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
    }
    
    .stat-label {
        color: #4a5568;
        font-weight: 600;
        font-size: 0.95rem;
    }
    
    @media (max-width: 1200px) {
        .modern-table-container { 
            padding: 32px 20px; 
            max-width: 98vw; 
        }
    }
    
    @media (max-width: 700px) {
        .modern-table-container { 
            padding: 24px 16px; 
        }
        .content { 
            margin-top: 20px; 
            padding: 0 10px;
        }
        .modern-table th, .modern-table td { 
            font-size: 0.9rem; 
            padding: 12px 16px; 
        }
        .modern-table-container h2 { 
            font-size: 1.8rem; 
        }
        .header-section { 
            flex-direction: column; 
            gap: 20px; 
            align-items: stretch; 
        }
        .add-student-btn { 
            justify-content: center; 
        }
        .stats-section {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 600px) {
        .modal .modal-card {
            padding: 24px 20px;
        }
        .modal h2 {
            font-size: 1.4rem;
        }
        .btn-action {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
    }
    .modern-table th, .modern-table td {
      text-align: center;
      vertical-align: middle;
      padding: 18px 16px;
    }
    .modern-table th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      font-size: 1.08rem;
      font-weight: 700;
      border: none;
    }
    .modern-table td {
      background: rgba(255,255,255,0.92);
      font-size: 1.01rem;
      color: #2d3748;
      font-weight: 500;
      border-bottom: 1px solid #e2e8f0;
    }
    .table-responsive {
      width: 100%;
      overflow-x: auto;
      border-radius: 16px;
      background: rgba(255,255,255,0.5);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.3);
      margin-bottom: 24px;
    }
    @media (max-width: 900px) {
      .modern-table th, .modern-table td {
        padding: 12px 6px;
        font-size: 0.97rem;
      }
    }
    .filters-bar {
      display: flex;
      align-items: center;
      gap: 18px;
      flex-wrap: wrap;
      background: linear-gradient(120deg, #fff 80%, #e3eafc 100%);
      border-radius: 18px;
      box-shadow: 0 4px 18px rgba(102,126,234,0.10);
      padding: 18px 24px;
      margin: 0 auto 32px auto;
      max-width: 1100px;
      width: 98%;
      position: relative;
      animation: fadeInBar 0.7s cubic-bezier(.77,0,.18,1);
    }
    @keyframes fadeInBar {
      from { opacity: 0; transform: translateY(-18px) scale(0.98); }
      to { opacity: 1; transform: none; }
    }
    .filters-bar .dropdown-icon {
      margin-right: 7px;
      color: #667eea;
      font-size: 1.15em;
      vertical-align: middle;
    }
    .filters-bar select {
      padding-left: 34px;
      position: relative;
    }
    .filters-bar .filter-group {
      position: relative;
      display: flex;
      align-items: center;
      gap: 7px;
      margin-right: 18px;
    }
    .filters-bar .filter-group label {
      margin: 0 0 0 0;
      display: flex;
      align-items: center;
      font-weight: 600;
      color: #764ba2;
    }
    .filters-bar .filter-group .dropdown-icon {
      position: static;
      margin-left: 0;
      margin-right: 5px;
      font-size: 1.15em;
      color: #667eea;
      vertical-align: middle;
    }
    .filters-bar select {
      padding-left: 16px;
    }
    .filters-bar .filter-group .dropdown-icon {
      position: absolute;
      left: 10px;
      pointer-events: none;
    }
    .filters-bar label {
      font-weight: 600;
      color: #764ba2;
      margin-right: 4px;
    }
    .filters-bar select {
      padding: 10px 18px;
      border-radius: 12px;
      border: 2px solid #667eea;
      font-size: 1.08rem;
      background: #f8f8ff;
      color: #333;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(102,126,234,0.07);
      transition: border-color 0.2s, box-shadow 0.2s;
      outline: none;
    }
    .filters-bar select:focus {
      border-color: #764ba2;
      box-shadow: 0 0 0 4px rgba(118,75,162,0.13);
    }
    .filters-bar .export-btn {
      background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 13px 28px;
      font-size: 1.08rem;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 2px 12px rgba(102,126,234,0.10);
      transition: background 0.22s, box-shadow 0.22s, transform 0.18s;
      margin-left: 6px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .filters-bar .export-btn:hover {
      background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
      transform: scale(1.04);
      box-shadow: 0 6px 24px rgba(102,126,234,0.18);
    }
    .filters-bar .fa-download {
      color: #fff;
      font-size: 1.2rem;
      margin-left: 0;
    }
    @media (max-width: 900px) {
      .filters-bar {
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
        padding: 12px 8px;
        }
    }
    </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
    <div class="modern-table-container">
        <div class="header-section">
            <h2><span class="table-icon"><i class="fas fa-users"></i></span> Student Management</h2>
            <div style="display:flex; gap:12px; align-items:center;">
            <button class="add-student-btn" id="addStudentBtn">
                <i class="fas fa-plus"></i> Add New Student
            </button>
              <button class="add-student-btn" id="bulkAssignBtn" style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);">
                  <i class="fas fa-layer-group"></i> Bulk Assign Class
              </button>
            </div>
        </div>
        
        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-number"><?php echo $result ? $result->num_rows : 0; ?></div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php 
                    $adminCount = $conn->query("SELECT COUNT(*) as count FROM students WHERE usertype='admin' AND status='Active'")->fetch_assoc()['count'];
                    echo $adminCount;
                ?></div>
                <div class="stat-label">Admin Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php 
                    $studentCount = $conn->query("SELECT COUNT(*) as count FROM students WHERE usertype='student' AND status='Active'")->fetch_assoc()['count'];
                    echo $studentCount;
                ?></div>
                <div class="stat-label">Regular Students</div>
            </div>
        </div>
        
        <div class="filters-bar">
  <form method="get" style="display:flex; gap:18px; align-items:center; flex-wrap:wrap; margin:0;">
    <span class="filter-group">
      <label for="class_id">Class:</label>
      <select name="class_id" id="class_id" onchange="this.form.submit()">
        <option value="0">All Classes</option>
        <?php
          $desired_classes = ['S1','S2','S3','S4','S5','S6'];
          $shown = [];
          foreach ($desired_classes as $dc) {
            foreach ($class_options as $cid => $cname) {
              if (strcasecmp($cname, $dc) === 0) {
                echo '<option value="' . $cid . '"' . ($class_filter == $cid ? ' selected' : '') . '>' . htmlspecialchars($cname) . '</option>';
                $shown[] = $cid;
                break;
              }
            }
          }
          if (count($shown) < 6) {
            foreach ($class_options as $cid => $cname) {
              if (!in_array($cid, $shown)) {
                echo '<option value="' . $cid . '"' . ($class_filter == $cid ? ' selected' : '') . '>' . htmlspecialchars($cname) . '</option>';
              }
            }
          }
        ?>
      </select>
    </span>
    <span class="filter-group">
      <label for="level">Level:</label>
      <select name="level" id="level" onchange="this.form.submit()">
        <option value="">All Levels</option>
        <option value="O-Level" <?php if(isset($_GET['level']) && $_GET['level']==='O-Level') echo 'selected'; ?>>O-Level</option>
        <option value="A-Level" <?php if(isset($_GET['level']) && $_GET['level']==='A-Level') echo 'selected'; ?>>A-Level</option>
      </select>
    </span>
    <span class="filter-group">
      <label for="status">Status:</label>
      <select name="status" id="status" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="Active" <?php if(isset($_GET['status']) && $_GET['status']==='Active') echo 'selected'; ?>>Active</option>
        <option value="Graduated" <?php if(isset($_GET['status']) && $_GET['status']==='Graduated') echo 'selected'; ?>>Graduated</option>
        <option value="Transferred" <?php if(isset($_GET['status']) && $_GET['status']==='Transferred') echo 'selected'; ?>>Transferred</option>
        <option value="Suspended" <?php if(isset($_GET['status']) && $_GET['status']==='Suspended') echo 'selected'; ?>>Suspended</option>
      </select>
    </span>
  </form>
  <form method="post" action="export_students.php" style="margin:0;">
    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_filter); ?>">
    <input type="hidden" name="level" value="<?php echo htmlspecialchars($level_filter); ?>">
    <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
    <button type="submit" class="export-btn"><i class="fas fa-download"></i> Export CSV</button>
  </form>
</div>
        <table class="modern-table">
        <thead>
    <tr>
        <th class="table_th">#</th>
        <th class="table_th">Username</th>
        <th class="table_th">Email</th>
        <th class="table_th">Phone</th>
        <th class="table_th">Class</th>
        <th class="table_th">User Type</th>
        <th class="table_th">Actions</th>
    </tr>
</thead>
<tbody>
<?php if ($result && $result->num_rows > 0): ?>
    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
        <?php if (!isset($row['class_id'])) $row['class_id'] = null; ?>
        <tr>
            <td class="table_td"><?php echo $i++; ?></td>
            <td class="table_td">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.9rem;">
                        <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                    </div>
                    <?php echo htmlspecialchars($row['username']); ?>
                </div>
            </td>
            <td class="table_td"><?php echo htmlspecialchars($row['email']); ?></td>
            <td class="table_td"><?php echo htmlspecialchars($row['phone']); ?></td>
            <td class="table_td"><?php echo isset($class_options[$row['class_id']]) ? htmlspecialchars($class_options[$row['class_id']]) : '<span style="color:#aaa;">-</span>'; ?></td>
            <td class="table_td">
                <span style="
                    padding: 6px 12px; 
                    border-radius: 20px; 
                    font-size: 0.85rem; 
                    font-weight: 600;
                    background: <?php echo $row['usertype'] == 'admin' ? 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; ?>;
                    color: white;
                    display: inline-block;
                ">
                    <?php echo ucfirst(htmlspecialchars($row['usertype'])); ?>
                </span>
            </td>
            <td class="table_td">
  <div style="display:flex; flex-direction:column; gap:8px; align-items:flex-start;">
    <form method="post" style="display:flex; gap:8px; align-items:center; width:100%;">
      <input type="hidden" name="assign_student_id" value="<?php echo $row['id']; ?>">
      <select name="assign_class_id" required style="padding:6px 10px; border-radius:8px; border:1.5px solid #667eea; font-size:0.95rem; background:#f8f8ff; color:#333; font-weight:500; min-width:90px;">
        <option value="">Class</option>
        <?php foreach ($class_options as $cid => $cname): ?>
          <option value="<?php echo $cid; ?>" <?php if ($row['class_id'] == $cid) echo 'selected'; ?>><?php echo htmlspecialchars($cname); ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn-action btn-edit" style="padding:7px 14px; min-width:90px;"><i class="fas fa-save"></i> Update</button>
    </form>
    <div style="display:flex; gap:8px; width:100%;">
                <button 
                    class="btn-action btn-edit btn-edit-student"
                    data-id="<?php echo $row['id']; ?>"
                    data-username="<?php echo htmlspecialchars($row['username']); ?>"
                    data-email="<?php echo htmlspecialchars($row['email']); ?>"
                    data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
                    data-usertype="<?php echo htmlspecialchars($row['usertype']); ?>"
        data-class_id="<?php echo htmlspecialchars($row['class_id']); ?>"
        style="flex:1; min-width:0;"
                >
                    <i class="fas fa-edit"></i> Edit
                </button>
      <a href="delete_student.php?id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this student?')" style="flex:1; min-width:0;">
                    <i class="fas fa-trash"></i> Delete
                </a>
    </div>
  </div>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="6" class="table_td" style="text-align: center; padding: 40px; color: #718096; font-style: italic;">
            <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 10px; display: block; color: #a0aec0;"></i>
            No students found. Add your first student to get started!
        </td>
    </tr>
<?php endif; ?>
<?php if (!empty($assign_success)): ?><div class="success-msg">Student class updated successfully!</div><?php endif; ?>
</tbody>
        </table>
        <!-- Pagination Controls -->
        <div style="margin-top: 32px; display: flex; justify-content: center; align-items: center; gap: 8px;">
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Students pagination">
                    <ul style="list-style: none; display: flex; gap: 6px; padding: 0; margin: 0;">
                        <?php if ($page > 1): ?>
                            <li><a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page-1])); ?>" class="btn-action btn-edit" style="padding: 6px 14px; font-size: 1rem;">&laquo; Prev</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="btn-action <?php echo $i == $page ? 'btn-delete' : 'btn-edit'; ?>" style="padding: 6px 14px; font-size: 1rem; <?php if($i == $page) echo 'pointer-events:none; opacity:0.85;'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <li><a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page+1])); ?>" class="btn-action btn-edit" style="padding: 6px 14px; font-size: 1rem;">Next &raquo;</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal" id="addStudentModal">
  <div class="modal-card">
    <button class="close-x" id="closeAddModalX" title="Close">&times;</button>
    <h2>Add New Student</h2>
    <form id="addStudentForm">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" id="addStudentUsername" required placeholder="Enter username">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" id="addStudentEmail" required placeholder="Enter email address">
      </div>
      <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" id="addStudentPhone" required placeholder="Enter phone number">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" id="addStudentPassword" required placeholder="Enter password">
      </div>
      <div class="form-group">
        <label>User Type</label>
        <select name="usertype" id="addStudentUsertype">
          <option value="student">Student</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div class="form-group">
        <label>Class</label>
        <select name="class_id" id="addStudentClassId" required>
          <option value="">Select Class</option>
          <?php
            $desired_classes = ['S1','S2','S3','S4','S5','S6'];
            $shown = [];
            foreach ($desired_classes as $dc) {
              foreach ($class_options as $cid => $cname) {
                if (strcasecmp($cname, $dc) === 0) {
                  echo '<option value="' . $cid . '">' . htmlspecialchars($cname) . '</option>';
                  $shown[] = $cid;
                  break;
                }
              }
            }
            if (count($shown) < 6) {
              foreach ($class_options as $cid => $cname) {
                if (!in_array($cid, $shown)) {
                  echo '<option value="' . $cid . '">' . htmlspecialchars($cname) . '</option>';
                }
              }
            }
          ?>
        </select>
      </div>
      <div id="addStudentMsg"></div>
      <button type="submit" class="submit-btn">
        <i class="fas fa-plus"></i> Add Student
      </button>
      <button type="button" id="closeAddModal" class="cancel-btn">
        <i class="fas fa-times"></i> Cancel
      </button>
    </form>
  </div>
</div>

<!-- Edit Student Modal -->
<div class="modal" id="editStudentModal">
  <div class="modal-card">
    <button class="close-x" id="closeEditModalX" title="Close">&times;</button>
    <h2>Edit Student</h2>
    <form id="editStudentForm">
      <input type="hidden" name="id" id="editStudentId">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" id="editStudentUsername" required placeholder="Enter username">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" id="editStudentEmail" required placeholder="Enter email address">
      </div>
      <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" id="editStudentPhone" required placeholder="Enter phone number">
      </div>
      <div class="form-group">
        <label>User Type</label>
        <select name="usertype" id="editStudentUsertype">
          <option value="student">Student</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div class="form-group">
        <label>Class</label>
        <select name="class_id" id="editStudentClassId" required>
          <option value="">Select Class</option>
          <?php
            $desired_classes = ['S1','S2','S3','S4','S5','S6'];
            $shown = [];
            foreach ($desired_classes as $dc) {
              foreach ($class_options as $cid => $cname) {
                if (strcasecmp($cname, $dc) === 0) {
                  echo '<option value="' . $cid . '">' . htmlspecialchars($cname) . '</option>';
                  $shown[] = $cid;
                  break;
                }
              }
            }
            if (count($shown) < 6) {
              foreach ($class_options as $cid => $cname) {
                if (!in_array($cid, $shown)) {
                  echo '<option value="' . $cid . '">' . htmlspecialchars($cname) . '</option>';
                }
              }
            }
          ?>
        </select>
      </div>
      <div id="editStudentMsg"></div>
      <button type="submit" class="submit-btn">
        <i class="fas fa-save"></i> Update Student
      </button>
      <button type="button" id="closeEditModal" class="cancel-btn">
        <i class="fas fa-times"></i> Cancel
      </button>
    </form>
  </div>
</div>

<!-- Bulk Assign Class Modal -->
<div class="modal" id="bulkAssignModal">
  <div class="modal-card" style="max-width:520px;">
    <button class="close-x" id="closeBulkAssignX" title="Close">&times;</button>
    <h2>Bulk Assign Class</h2>
    <form id="bulkAssignForm">
      <div class="form-group">
        <label>Select Students</label>
        <select name="student_ids[]" id="bulkStudentSelect" multiple required style="min-height:120px;">
          <?php
            $studentRes = $conn->query("SELECT id, username, email FROM students WHERE status='Active' ORDER BY username ASC");
            while ($stu = $studentRes->fetch_assoc()) {
              echo '<option value="' . $stu['id'] . '">' . htmlspecialchars($stu['username']) . ' (' . htmlspecialchars($stu['email']) . ')</option>';
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label>Assign to Class</label>
        <select name="class_id" id="bulkClassSelect" required>
          <option value="">Select Class</option>
          <?php
            $desired_classes = ['S1','S2','S3','S4','S5','S6'];
            $shown = [];
            foreach ($desired_classes as $dc) {
              foreach ($class_options as $cid => $cname) {
                if (strcasecmp($cname, $dc) === 0) {
                  echo '<option value="' . $cid . '">' . htmlspecialchars($cname) . '</option>';
                  $shown[] = $cid;
                  break;
                }
              }
            }
            if (count($shown) < 6) {
              foreach ($class_options as $cid => $cname) {
                if (!in_array($cid, $shown)) {
                  echo '<option value="' . $cid . '">' . htmlspecialchars($cname) . '</option>';
                }
              }
            }
          ?>
        </select>
      </div>
      <div id="bulkAssignMsg"></div>
      <button type="submit" class="submit-btn"><i class="fas fa-layer-group"></i> Assign Class</button>
      <button type="button" id="closeBulkAssign" class="cancel-btn"><i class="fas fa-times"></i> Cancel</button>
    </form>
  </div>
</div>

<script>
// Modal functionality
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.classList.add('active');
  modal.style.display = 'flex';
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.classList.remove('active');
  modal.style.display = 'none';
}

// Add Student Modal
const addStudentBtn = document.getElementById('addStudentBtn');
const addStudentModal = document.getElementById('addStudentModal');
const addStudentForm = document.getElementById('addStudentForm');
const addStudentMsg = document.getElementById('addStudentMsg');
const closeAddModalX = document.getElementById('closeAddModalX');
const closeAddModal = document.getElementById('closeAddModal');

addStudentBtn.addEventListener('click', () => {
  addStudentForm.reset();
  addStudentMsg.textContent = '';
  openModal('addStudentModal');
});

closeAddModal.onclick = closeAddModalX.onclick = () => closeModal('addStudentModal');

// Edit Student Modal
const editButtons = document.querySelectorAll('.btn-edit-student');
const editStudentModal = document.getElementById('editStudentModal');
const editStudentForm = document.getElementById('editStudentForm');
const editStudentMsg = document.getElementById('editStudentMsg');
const closeEditModalX = document.getElementById('closeEditModalX');
const closeEditModal = document.getElementById('closeEditModal');

editButtons.forEach(btn => {
  btn.addEventListener('click', function() {
    document.getElementById('editStudentId').value = this.dataset.id;
    document.getElementById('editStudentUsername').value = this.dataset.username;
    document.getElementById('editStudentEmail').value = this.dataset.email;
    document.getElementById('editStudentPhone').value = this.dataset.phone;
    document.getElementById('editStudentUsertype').value = this.dataset.usertype;
    document.getElementById('editStudentClassId').value = this.dataset.class_id;
    editStudentMsg.textContent = '';
    openModal('editStudentModal');
  });
});

closeEditModal.onclick = closeEditModalX.onclick = () => closeModal('editStudentModal');

// Close modals when clicking outside
window.onclick = function(event) {
  if (event.target.classList.contains('modal')) {
    event.target.classList.remove('active');
    event.target.style.display = 'none';
  }
};

// Add Student Form Submission
addStudentForm.onsubmit = function(e) {
  e.preventDefault();
  addStudentMsg.textContent = '';
  
  const formData = new FormData(addStudentForm);
  
  fetch('add_student_ajax.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      addStudentMsg.textContent = 'Student added successfully! Reloading...';
      addStudentMsg.className = 'success-msg';
      setTimeout(() => window.location.reload(), 1500);
    } else {
      addStudentMsg.textContent = data.error || 'Failed to add student.';
      addStudentMsg.className = 'error-msg';
    }
  })
  .catch(() => { 
    addStudentMsg.textContent = 'Network error.';
    addStudentMsg.className = 'error-msg';
  });
};

// Edit Student Form Submission
editStudentForm.onsubmit = function(e) {
  e.preventDefault();
  editStudentMsg.textContent = '';
  
  const formData = new FormData(editStudentForm);
  
  fetch('edit_student_ajax.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      editStudentMsg.textContent = 'Student updated! Reloading...';
      editStudentMsg.className = 'success-msg';
      setTimeout(() => window.location.reload(), 1000);
    } else {
      editStudentMsg.textContent = data.error || 'Update failed.';
      editStudentMsg.className = 'error-msg';
    }
  })
  .catch(() => { 
    editStudentMsg.textContent = 'Network error.';
    editStudentMsg.className = 'error-msg';
  });
};

// Bulk Assign Class Modal
const bulkAssignBtn = document.getElementById('bulkAssignBtn');
const bulkAssignModal = document.getElementById('bulkAssignModal');
const bulkAssignForm = document.getElementById('bulkAssignForm');
const bulkAssignMsg = document.getElementById('bulkAssignMsg');
const closeBulkAssignX = document.getElementById('closeBulkAssignX');
const closeBulkAssign = document.getElementById('closeBulkAssign');

bulkAssignBtn.addEventListener('click', () => {
  bulkAssignForm.reset();
  bulkAssignMsg.textContent = '';
  openModal('bulkAssignModal');
});
closeBulkAssign.onclick = closeBulkAssignX.onclick = () => closeModal('bulkAssignModal');

bulkAssignForm.onsubmit = function(e) {
  e.preventDefault();
  bulkAssignMsg.textContent = '';
  const formData = new FormData(bulkAssignForm);
  fetch('bulk_assign_class.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      bulkAssignMsg.textContent = 'Class assigned to selected students! Reloading...';
      bulkAssignMsg.className = 'success-msg';
      setTimeout(() => window.location.reload(), 1200);
    } else {
      bulkAssignMsg.textContent = data.error || 'Failed to assign class.';
      bulkAssignMsg.className = 'error-msg';
    }
  })
  .catch(() => {
    bulkAssignMsg.textContent = 'Network error.';
    bulkAssignMsg.className = 'error-msg';
  });
};

// Add some nice animations on page load
document.addEventListener('DOMContentLoaded', function() {
  const cards = document.querySelectorAll('.stat-card');
  cards.forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    setTimeout(() => {
      card.style.transition = 'all 0.6s ease';
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, index * 200);
  });
});
</script>
</body>
</html>