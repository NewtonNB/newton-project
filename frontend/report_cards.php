<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../shared/config.php';

// Fetch classes
$classes = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Cards - Nyabikoni Secondary School</title>
  <?php include 'admin_css.php'; ?>
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
      padding: 48px 40px; 
      width: 100%; 
      border: 1px solid rgba(255,255,255,0.2); 
    }
    .page-header {
      text-align: center;
      margin-bottom: 40px;
    }
    .page-header h1 {
      font-size: 2.5rem;
      font-weight: 800;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 10px;
    }
    .page-header p {
      color: #666;
      font-size: 1.1rem;
    }
    .form-section {
      max-width: 600px;
      margin: 0 auto;
    }
    .form-group {
      margin-bottom: 24px;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      color: #764ba2;
      margin-bottom: 8px;
    }
    .form-group select,
    .form-group input {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s;
    }
    .form-group select:focus,
    .form-group input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 14px 32px;
      border: none;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      width: 100%;
    }
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }
    .info-box {
      background: #f0f4ff;
      border-left: 4px solid #667eea;
      padding: 20px;
      border-radius: 12px;
      margin-top: 30px;
    }
    .info-box h3 {
      color: #667eea;
      margin-top: 0;
    }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <div class="modern-container">
    <div class="page-header">
      <h1><i class="fas fa-file-alt"></i> Report Cards</h1>
      <p>Generate and view student report cards</p>
    </div>
    
    <div class="form-section">
      <form method="get" action="generate_report.php">
        <div class="form-group">
          <label for="class_id">Select Class</label>
          <select name="class_id" id="class_id" required>
            <option value="">Choose a class...</option>
            <?php if ($classes && $classes->num_rows > 0): 
              while ($class = $classes->fetch_assoc()): ?>
                <option value="<?php echo $class['id']; ?>">
                  <?php echo htmlspecialchars($class['class_name']); ?>
                </option>
              <?php endwhile; 
            endif; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label for="term">Select Term</label>
          <select name="term" id="term" required>
            <option value="">Choose a term...</option>
            <option value="1">Term 1</option>
            <option value="2">Term 2</option>
            <option value="3">Term 3</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="year">Academic Year</label>
          <input type="number" name="year" id="year" value="<?php echo date('Y'); ?>" min="2020" max="2030" required>
        </div>
        
        <button type="submit" class="btn">
          <i class="fas fa-file-pdf"></i> Generate Report Cards
        </button>
      </form>
      
      <div class="info-box">
        <h3><i class="fas fa-info-circle"></i> Information</h3>
        <p>Report cards will be generated based on:</p>
        <ul>
          <li>Student marks entered in the system</li>
          <li>Attendance records</li>
          <li>Teacher comments (if available)</li>
        </ul>
        <p><strong>Note:</strong> Make sure all marks have been entered before generating report cards.</p>
      </div>
    </div>
  </div>
</div>
</body>
</html>
