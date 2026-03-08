<?php
// view_teacher.php
session_start();

if(!isset($_SESSION['username']))
{
    header("location:login.php");
}
elseif($_SESSION['usertype']=='student'){
        header("location:login.php");
}

require 'config.php';

// Pagination setup
$teachersPerPage = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Count total teachers
$count_sql = "SELECT COUNT(*) as total FROM teachers";
$count_result = $conn->query($count_sql);
$totalTeachers = $count_result ? (int)$count_result->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalTeachers / $teachersPerPage);
$offset = ($page - 1) * $teachersPerPage;

// Fetch paginated teachers
$result = $conn->query("SELECT * FROM teachers ORDER BY id DESC LIMIT $teachersPerPage OFFSET $offset");

// Parse staff.html for teacher images
// (Removed unnecessary code that referenced staff.html)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management - NYABIKONI SECONDARY SCHOOL</title>
    <?php include 'admin_css.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    
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
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    @keyframes fadeInUp {
        from { 
            opacity: 0; 
            transform: translateY(40px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
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
    
    .add-teacher-btn {
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
    
    .add-teacher-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .add-teacher-btn:hover::before {
        left: 100%;
    }
    
    .add-teacher-btn:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 
            0 12px 35px rgba(102, 126, 234, 0.4),
            0 6px 15px rgba(0, 0, 0, 0.15);
    }
    
    .add-teacher-btn:active {
        transform: translateY(-2px) scale(1.01);
    }
    
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
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
      background: rgba(30, 41, 59, 0.55);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      transition: background 0.3s;
      animation: fadeInBg 0.3s;
    }
    
    .modal.active {
      display: flex;
    }
    
    @keyframes fadeInBg {
      from { background: rgba(30,41,59,0); }
      to { background: rgba(30,41,59,0.55); }
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
      max-height: 90vh; /* Make modal content scrollable */
      overflow-y: auto; /* Add vertical scrollbar when needed */
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
        .add-teacher-btn { 
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
    </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
    <div class="modern-table-container">
        <div class="header-section">
            <h2><span class="table-icon"><i class="fas fa-chalkboard-teacher"></i></span> Teacher Management</h2>
            <div style="display:flex; gap:12px; align-items:center;">
                <button class="add-teacher-btn" id="addTeacherBtn">
                    <i class="fas fa-plus"></i> Add New Teacher
                </button>
                <form method="post" action="export_teachers.php" style="margin:0;">
                    <button type="submit" class="add-teacher-btn" style="background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-number"><?php echo $result ? $result->num_rows : 0; ?></div>
                <div class="stat-label">Total Teachers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php 
                    $maleCount = $conn->query("SELECT COUNT(*) as count FROM teachers WHERE gender='Male'")->fetch_assoc()['count'];
                    echo $maleCount;
                ?></div>
                <div class="stat-label">Male Teachers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php 
                    $femaleCount = $conn->query("SELECT COUNT(*) as count FROM teachers WHERE gender='Female'")->fetch_assoc()['count'];
                    echo $femaleCount;
                ?></div>
                <div class="stat-label">Female Teachers</div>
            </div>
        </div>
        
        <div class="table-responsive">
        <table class="modern-table">
        <thead>
    <tr>
        <th class="table_th">#</th>
        <th class="table_th">Photo</th>
        <th class="table_th">Full Name</th>
        <th class="table_th">Email</th>
        <th class="table_th">Phone</th>
        <th class="table_th">Subject</th>
        <th class="table_th">Gender</th>
        <th class="table_th">Joined On</th>
        <th class="table_th">Actions</th>
    </tr>
</thead>
<tbody>
<?php if ($result && $result->num_rows > 0): ?>
    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td class="table_td"><?php echo $i++; ?></td>
            <td class="table_td">
                <?php
                // Use the photo field from the database for teacher photo
                $photo = !empty($row['photo']) ? 'nyabzgallery/' . htmlspecialchars($row['photo']) : 'nyabzgallery/default.svg';
                ?>
                <img src="<?php echo $photo; ?>" alt="Teacher Photo" style="width:48px; height:48px; object-fit:cover; border-radius:50%; border:2px solid #667eea; background:#f3f3f3;">
            </td>
            <td class="table_td">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.9rem;">
                        <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                    </div>
                    <?php echo htmlspecialchars($row['full_name']); ?>
                </div>
            </td>
            <td class="table_td"><?php echo htmlspecialchars($row['email']); ?></td>
            <td class="table_td"><?php echo htmlspecialchars($row['phone']); ?></td>
            <td class="table_td">
                <span style="
                    padding: 6px 12px; 
                    border-radius: 20px; 
                    font-size: 0.85rem; 
                    font-weight: 600;
                    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                    color: white;
                    display: inline-block;
                ">
                    <?php echo htmlspecialchars($row['subject']); ?>
                </span>
            </td>
            <td class="table_td">
                <span style="
                    padding: 6px 12px; 
                    border-radius: 20px; 
                    font-size: 0.85rem; 
                    font-weight: 600;
                    background: <?php echo $row['gender'] == 'Male' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'; ?>;
                    color: white;
                    display: inline-block;
                ">
                    <?php echo $row['gender']; ?>
                </span>
            </td>
            <td class="table_td"><?php echo $row['joined_on']; ?></td>
            <td class="table_td">
                <a href="#" class="btn-action btn-edit btn-edit-teacher" data-id="<?php echo $row['id']; ?>">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="delete_teacher.php?id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this teacher?')">
                    <i class="fas fa-trash"></i> Delete
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="8" class="table_td" style="text-align: center; padding: 40px; color: #718096; font-style: italic;">
            <i class="fas fa-chalkboard-teacher" style="font-size: 2rem; margin-bottom: 10px; display: block; color: #a0aec0;"></i>
            No teachers found. Add your first teacher to get started!
        </td>
    </tr>
<?php endif; ?>
</tbody>
        </table>
        <!-- Pagination Controls -->
        <div style="margin-top: 32px; display: flex; justify-content: center; align-items: center; gap: 8px;">
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Teachers pagination">
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

<!-- Add Teacher Modal -->
<div class="modal" id="addTeacherModal">
  <div class="modal-card">
    <button class="close-x" id="closeAddModalX" title="Close">&times;</button>
    <h2>Add New Teacher</h2>
    <form id="addTeacherForm" enctype="multipart/form-data">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" id="addTeacherName" required placeholder="Enter full name">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" id="addTeacherEmail" required placeholder="Enter email address">
      </div>
      <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" id="addTeacherPhone" required placeholder="Enter phone number">
      </div>
      <div class="form-group">
        <label>Subject/Position</label>
        <select name="subject" id="addTeacherSubject" required>
          <option value="">Select Subject/Position</option>
          <optgroup label="Administrative">
            <option value="Headmaster">Headmaster</option>
            <option value="Deputy Headmaster">Deputy Headmaster</option>
            <option value="Deputy Headmistress">Deputy Headmistress</option>
            <option value="Director of Studies">Director of Studies</option>
            <option value="Senior Woman Teacher">Senior Woman Teacher</option>
            <option value="Senior Man Teacher">Senior Man Teacher</option>
          </optgroup>
          <optgroup label="Sciences">
            <option value="Mathematics">Mathematics</option>
            <option value="Physics">Physics</option>
            <option value="Chemistry">Chemistry</option>
            <option value="Biology">Biology</option>
            <option value="Computer Science">Computer Science</option>
          </optgroup>
          <optgroup label="Languages">
            <option value="English">English</option>
            <option value="Literature">Literature</option>
            <option value="Kiswahili">Kiswahili</option>
            <option value="French">French</option>
          </optgroup>
          <optgroup label="Humanities">
            <option value="History">History</option>
            <option value="Geography">Geography</option>
            <option value="Economics">Economics</option>
            <option value="Entrepreneurship">Entrepreneurship</option>
            <option value="Commerce">Commerce</option>
          </optgroup>
          <optgroup label="Other Subjects">
            <option value="Agriculture">Agriculture</option>
            <option value="Christian Religious Education">Christian Religious Education</option>
            <option value="Islamic Religious Education">Islamic Religious Education</option>
            <option value="Fine Art">Fine Art</option>
            <option value="Music">Music</option>
            <option value="Physical Education">Physical Education</option>
            <option value="Technical Drawing">Technical Drawing</option>
          </optgroup>
        </select>
      </div>
      <div class="form-group">
        <label>Gender</label>
        <select name="gender" id="addTeacherGender">
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>
      <div class="form-group">
        <label>Photo (optional)</label>
        <input type="file" name="photo" id="addTeacherPhoto" accept="image/*">
      </div>
      <div id="addTeacherMsg"></div>
      <button type="submit" class="submit-btn">
        <i class="fas fa-plus"></i> Add Teacher
      </button>
      <button type="button" id="closeAddModal" class="cancel-btn">
        <i class="fas fa-times"></i> Cancel
      </button>
    </form>
  </div>
</div>

<!-- Edit Teacher Modal -->
<div class="modal" id="editTeacherModal">
  <div class="modal-card">
    <button class="close-x" id="closeEditModalX" title="Close">&times;</button>
    <h2>Edit Teacher</h2>
    <form id="editTeacherForm" enctype="multipart/form-data">
      <input type="hidden" name="id" id="editTeacherId">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" id="editTeacherName" required placeholder="Enter full name">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" id="editTeacherEmail" required placeholder="Enter email address">
      </div>
      <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" id="editTeacherPhone" required placeholder="Enter phone number">
      </div>
      <div class="form-group">
        <label>Subject/Position</label>
        <select name="subject" id="editTeacherSubject" required>
          <option value="">Select Subject/Position</option>
          <optgroup label="Administrative">
            <option value="Headmaster">Headmaster</option>
            <option value="Deputy Headmaster">Deputy Headmaster</option>
            <option value="Deputy Headmistress">Deputy Headmistress</option>
            <option value="Director of Studies">Director of Studies</option>
            <option value="Senior Woman Teacher">Senior Woman Teacher</option>
            <option value="Senior Man Teacher">Senior Man Teacher</option>
          </optgroup>
          <optgroup label="Sciences">
            <option value="Mathematics">Mathematics</option>
            <option value="Physics">Physics</option>
            <option value="Chemistry">Chemistry</option>
            <option value="Biology">Biology</option>
            <option value="Computer Science">Computer Science</option>
          </optgroup>
          <optgroup label="Languages">
            <option value="English">English</option>
            <option value="Literature">Literature</option>
            <option value="Kiswahili">Kiswahili</option>
            <option value="French">French</option>
          </optgroup>
          <optgroup label="Humanities">
            <option value="History">History</option>
            <option value="Geography">Geography</option>
            <option value="Economics">Economics</option>
            <option value="Entrepreneurship">Entrepreneurship</option>
            <option value="Commerce">Commerce</option>
          </optgroup>
          <optgroup label="Other Subjects">
            <option value="Agriculture">Agriculture</option>
            <option value="Christian Religious Education">Christian Religious Education</option>
            <option value="Islamic Religious Education">Islamic Religious Education</option>
            <option value="Fine Art">Fine Art</option>
            <option value="Music">Music</option>
            <option value="Physical Education">Physical Education</option>
            <option value="Technical Drawing">Technical Drawing</option>
          </optgroup>
        </select>
      </div>
      <div class="form-group">
        <label>Gender</label>
        <select name="gender" id="editTeacherGender">
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>
      <div class="form-group" style="display:flex; flex-direction:column; align-items:center;">
        <label style="font-weight:700; color:#333; margin-bottom:8px;">Current Photo</label>
        <div style="background:linear-gradient(135deg,#f3f3f3 60%,#e0e7ff 100%); border-radius:20px; padding:18px 18px 10px 18px; box-shadow:0 6px 24px rgba(102,126,234,0.13); display:inline-block; transition:box-shadow 0.3s, transform 0.3s;">
          <img id="editTeacherCurrentPhoto" src="nyabzgallery/default.svg" alt="Teacher Photo" style="width:120px; height:120px; object-fit:cover; border-radius:16px; border:4px solid #667eea; background:#f3f3f3; box-shadow:0 4px 16px rgba(102,126,234,0.15); margin-bottom:0; transition:box-shadow 0.3s, transform 0.3s; cursor:pointer;" onmouseover="this.style.boxShadow='0 8px 32px rgba(102,126,234,0.25)';this.style.transform='scale(1.04)';" onmouseout="this.style.boxShadow='0 4px 16px rgba(102,126,234,0.15)';this.style.transform='scale(1)';">
        </div>
        <div style="font-size:0.97rem; color:#555; margin-top:10px; text-align:center;">This is the current photo. To change, upload a new one below.</div>
      </div>
      <div class="form-group">
        <label>Change Photo (optional)</label>
        <input type="file" name="photo" id="editTeacherPhoto" accept="image/*">
      </div>
      <div id="editTeacherMsg"></div>
      <button type="submit" class="submit-btn">
        <i class="fas fa-save"></i> Update Teacher
      </button>
      <button type="button" id="closeEditModal" class="cancel-btn">
        <i class="fas fa-times"></i> Cancel
      </button>
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

// Add Teacher Modal
const addTeacherBtn = document.getElementById('addTeacherBtn');
const addTeacherModal = document.getElementById('addTeacherModal');
const addTeacherForm = document.getElementById('addTeacherForm');
const addTeacherMsg = document.getElementById('addTeacherMsg');
const closeAddModalX = document.getElementById('closeAddModalX');
const closeAddModal = document.getElementById('closeAddModal');

addTeacherBtn.addEventListener('click', () => {
  addTeacherForm.reset();
  addTeacherMsg.textContent = '';
  openModal('addTeacherModal');
});

closeAddModal.onclick = closeAddModalX.onclick = () => closeModal('addTeacherModal');

// Close modals when clicking outside
window.onclick = function(event) {
  if (event.target.classList.contains('modal')) {
    event.target.classList.remove('active');
    event.target.style.display = 'none';
  }
};

// Add Teacher Form Submission
addTeacherForm.onsubmit = function(e) {
  e.preventDefault();
  addTeacherMsg.textContent = '';
  const submitBtn = addTeacherForm.querySelector('.submit-btn');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Adding...';
  const formData = new FormData(addTeacherForm);
  
  fetch('add_teacher_ajax.php', {
    method: 'POST',
    body: formData
  })
  .then(res => {
    if (!res.ok) {
      throw new Error('HTTP error! status: ' + res.status);
    }
    return res.json();
  })
  .then(data => {
    if (data.success) {
      addTeacherMsg.textContent = 'Teacher added successfully! Reloading...';
      addTeacherMsg.className = 'success-msg';
      setTimeout(() => window.location.reload(), 1500);
    } else {
      addTeacherMsg.textContent = data.error || 'Failed to add teacher.';
      addTeacherMsg.className = 'error-msg';
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fas fa-plus"></i> Add Teacher';
    }
  })
  .catch((error) => { 
    console.error('Error:', error);
    addTeacherMsg.textContent = 'Network error: ' + error.message;
    addTeacherMsg.className = 'error-msg';
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-plus"></i> Add Teacher';
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

// Edit Teacher Modal Logic
const editButtons = document.querySelectorAll('.btn-edit-teacher');
const editTeacherModal = document.getElementById('editTeacherModal');
const editTeacherForm = document.getElementById('editTeacherForm');
const editTeacherMsg = document.getElementById('editTeacherMsg');
const closeEditModalX = document.getElementById('closeEditModalX');
const closeEditModal = document.getElementById('closeEditModal');
const editTeacherCurrentPhoto = document.getElementById('editTeacherCurrentPhoto');

editButtons.forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const teacherId = this.getAttribute('data-id');
    // Fetch teacher data via AJAX
    fetch('get_teacher.php?id=' + teacherId)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          document.getElementById('editTeacherId').value = data.teacher.id;
          document.getElementById('editTeacherName').value = data.teacher.full_name;
          document.getElementById('editTeacherEmail').value = data.teacher.email;
          document.getElementById('editTeacherPhone').value = data.teacher.phone;
          document.getElementById('editTeacherSubject').value = data.teacher.subject;
          document.getElementById('editTeacherGender').value = data.teacher.gender;
          editTeacherCurrentPhoto.src = data.teacher.photo ? ('nyabzgallery/' + data.teacher.photo + '?t=' + Date.now()) : 'nyabzgallery/default.svg';
          editTeacherMsg.textContent = '';
          openModal('editTeacherModal');
        } else {
          alert('Failed to fetch teacher data.');
        }
      })
      .catch(() => alert('Network error.'));
  });
});

closeEditModal.onclick = closeEditModalX.onclick = () => closeModal('editTeacherModal');

// Edit Teacher Form Submission
editTeacherForm.onsubmit = function(e) {
  e.preventDefault();
  editTeacherMsg.textContent = '';
  const formData = new FormData(editTeacherForm);
  fetch('edit_teacher_ajax.php', {
    method: 'POST',
    body: formData
  })
  .then(res => {
    if (!res.ok) {
      throw new Error('HTTP error! status: ' + res.status);
    }
    return res.json();
  })
  .then(data => {
    if (data.success) {
      editTeacherMsg.textContent = 'Teacher updated successfully! Reloading...';
      editTeacherMsg.className = 'success-msg';
      setTimeout(() => window.location.reload(), 1500);
    } else {
      editTeacherMsg.textContent = data.error || 'Failed to update teacher.';
      editTeacherMsg.className = 'error-msg';
    }
  })
  .catch((error) => {
    console.error('Error:', error);
    editTeacherMsg.textContent = 'Network error: ' + error.message;
    editTeacherMsg.className = 'error-msg';
  });
};
</script>
</body>
</html>
