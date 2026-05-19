<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../shared/config.php';

// Handle fee payment submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);
    $amount = floatval($_POST['amount']);
    $payment_method = $_POST['payment_method'];
    $term = $_POST['term'];
    $academic_year = $_POST['academic_year'];
    $payment_date = $_POST['payment_date'];
    
    $stmt = $conn->prepare("INSERT INTO fees (student_id, amount_paid, payment_method, term, academic_year, payment_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssss", $student_id, $amount, $payment_method, $term, $academic_year, $payment_date);
    
    if ($stmt->execute()) {
        $success_message = "Fee payment recorded successfully!";

        // Send email receipt to student
        require_once 'email_helper.php';
        $stu = $conn->query("SELECT username, email FROM students WHERE id = $student_id")->fetch_assoc();
        if ($stu && $stu['email']) {
            sendEmail($stu['email'], $stu['username'],
                'Fee Payment Receipt - Nyabikoni Secondary School',
                "<p>Dear <strong>{$stu['username']}</strong>,</p>
                <p>Your fee payment has been received and recorded.</p>
                <table style='border-collapse:collapse;margin:16px 0;'>
                    " . row('Amount Paid', 'UGX ' . number_format($amount, 0)) . "
                    " . row('Payment Method', $payment_method) . "
                    " . row('Term', $term) . "
                    " . row('Academic Year', $academic_year) . "
                    " . row('Date', $payment_date) . "
                </table>
                <p>Please keep this as your receipt.</p>
                <p>Best regards,<br><strong>Nyabikoni Secondary School Accounts</strong></p>"
            );
        }
    } else {
        $error_message = "Error recording payment: " . $conn->error;
    }
}

// Fetch all students with their class info
$students_query = "SELECT s.id, s.username, s.email, s.phone, c.class_name 
                   FROM students s 
                   LEFT JOIN classes c ON s.class_id = c.id 
                   WHERE s.usertype = 'student'
                   ORDER BY s.username ASC";
$students = $conn->query($students_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fee Collection - Nyabikoni Secondary School</title>
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
    .form-section {
      max-width: 700px;
      margin: 30px auto;
    }
    .form-row {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-bottom: 20px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group.full-width {
      grid-column: 1 / -1;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      color: #764ba2;
      margin-bottom: 8px;
    }
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 12px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.3s;
    }
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 14px 32px;
      border: none;
      border-radius: 8px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s;
    }
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }
    .success-msg {
      background: #d4edda;
      color: #155724;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border: 1px solid #c3e6cb;
    }
    .error-msg {
      background: #f8d7da;
      color: #721c24;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border: 1px solid #f5c6cb;
    }
    .info-box {
      background: #f0f4ff;
      border-left: 4px solid #667eea;
      padding: 20px;
      border-radius: 8px;
      margin-top: 30px;
    }
    .info-box h3 {
      color: #667eea;
      margin-top: 0;
    }
    .validation-error {
      color: #dc3545;
      font-size: 0.9rem;
      margin-top: 5px;
      display: none;
      font-weight: 500;
    }
    .validation-error.show {
      display: block;
      animation: fadeIn 0.3s;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-5px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .form-group input.error,
    .form-group select.error {
      border-color: #dc3545;
      background-color: #fff5f5;
    }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <div class="modern-container">
    <div class="page-header">
      <h1><i class="fas fa-money-bill-wave"></i> Fee Collection</h1>
      <p>Record student fee payments</p>
    </div>
    
    <?php if ($success_message): ?>
    <div class="success-msg">
      <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="error-msg">
      <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
    
    <div class="form-section">
      <form method="post" novalidate>
        <div class="form-group">
          <label for="student_id">Select Student <span style="color: red;">*</span></label>
          <select name="student_id" id="student_id">
            <option value="">Choose a student...</option>
            <?php if ($students && $students->num_rows > 0): 
              while ($student = $students->fetch_assoc()): ?>
                <option value="<?php echo $student['id']; ?>">
                  <?php echo htmlspecialchars($student['username']); ?> 
                  <?php if ($student['class_name']): ?>
                    - <?php echo htmlspecialchars($student['class_name']); ?>
                  <?php endif; ?>
                </option>
              <?php endwhile; 
            endif; ?>
          </select>
          <div class="validation-error" id="student-error">Please select a student</div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="amount">Amount (UGX) <span style="color: red;">*</span></label>
            <input type="number" name="amount" id="amount" min="0" step="0.01" placeholder="Enter amount">
            <div class="validation-error" id="amount-error">Please enter a valid amount greater than 0</div>
          </div>
          
          <div class="form-group">
            <label for="payment_method">Payment Method <span style="color: red;">*</span></label>
            <select name="payment_method" id="payment_method">
              <option value="Cash">Cash</option>
              <option value="Mobile Money">Mobile Money</option>
              <option value="Bank Transfer">Bank Transfer</option>
              <option value="Cheque">Cheque</option>
            </select>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="term">Term <span style="color: red;">*</span></label>
            <select name="term" id="term">
              <option value="">Select term...</option>
              <option value="Term 1">Term 1</option>
              <option value="Term 2">Term 2</option>
              <option value="Term 3">Term 3</option>
            </select>
            <div class="validation-error" id="term-error">Please select a term</div>
          </div>
          
          <div class="form-group">
            <label for="academic_year">Academic Year <span style="color: red;">*</span></label>
            <input type="text" name="academic_year" id="academic_year" value="<?php echo date('Y'); ?>" placeholder="e.g., 2024">
            <div class="validation-error" id="year-error">Please enter a valid 4-digit year</div>
          </div>
        </div>
        
        <div class="form-group">
          <label for="payment_date">Payment Date <span style="color: red;">*</span></label>
          <input type="date" name="payment_date" id="payment_date" value="<?php echo date('Y-m-d'); ?>">
          <div class="validation-error" id="date-error">Payment date cannot be in the future</div>
        </div>
        
        <button type="submit" class="btn">
          <i class="fas fa-save"></i> Record Payment
        </button>
      </form>
      
      <div class="info-box">
        <h3><i class="fas fa-info-circle"></i> Quick Links</h3>
        <p>
          <a href="fee_status.php" style="color: #667eea; text-decoration: none; font-weight: 600;">
            <i class="fas fa-receipt"></i> View Fee Status & Payment History
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  const studentSelect = document.getElementById('student_id');
  const amountInput = document.getElementById('amount');
  const termSelect = document.getElementById('term');
  const academicYearInput = document.getElementById('academic_year');
  const paymentDateInput = document.getElementById('payment_date');
  
  // Error message elements
  const studentError = document.getElementById('student-error');
  const amountError = document.getElementById('amount-error');
  const termError = document.getElementById('term-error');
  const yearError = document.getElementById('year-error');
  const dateError = document.getElementById('date-error');
  
  // Helper function to show error
  function showError(input, errorElement, message) {
    input.classList.add('error');
    errorElement.textContent = message;
    errorElement.classList.add('show');
  }
  
  // Helper function to hide error
  function hideError(input, errorElement) {
    input.classList.remove('error');
    errorElement.classList.remove('show');
  }
  
  // Student validation
  studentSelect.addEventListener('change', function() {
    if (this.value) {
      hideError(this, studentError);
    } else {
      showError(this, studentError, 'Please select a student');
    }
  });
  
  // Amount validation
  amountInput.addEventListener('input', function() {
    const value = parseFloat(this.value);
    if (!this.value) {
      showError(this, amountError, 'Please enter the payment amount');
    } else if (value <= 0) {
      showError(this, amountError, 'Amount must be greater than 0');
    } else {
      hideError(this, amountError);
    }
  });
  
  // Term validation
  termSelect.addEventListener('change', function() {
    if (this.value) {
      hideError(this, termError);
    } else {
      showError(this, termError, 'Please select a term');
    }
  });
  
  // Academic year validation
  academicYearInput.addEventListener('input', function() {
    if (!this.value) {
      showError(this, yearError, 'Please enter the academic year');
    } else if (!/^\d{4}$/.test(this.value)) {
      showError(this, yearError, 'Please enter a valid 4-digit year');
    } else {
      hideError(this, yearError);
    }
  });
  
  // Payment date validation
  paymentDateInput.addEventListener('change', function() {
    if (!this.value) {
      showError(this, dateError, 'Please select a payment date');
    } else {
      const selectedDate = new Date(this.value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      if (selectedDate > today) {
        showError(this, dateError, 'Payment date cannot be in the future');
      } else {
        hideError(this, dateError);
      }
    }
  });
  
  // Form submission validation
  form.addEventListener('submit', function(e) {
    let isValid = true;
    
    // Validate student
    if (!studentSelect.value) {
      showError(studentSelect, studentError, 'Please select a student');
      isValid = false;
    }
    
    // Validate amount
    const amount = parseFloat(amountInput.value);
    if (!amountInput.value) {
      showError(amountInput, amountError, 'Please enter the payment amount');
      isValid = false;
    } else if (amount <= 0) {
      showError(amountInput, amountError, 'Amount must be greater than 0');
      isValid = false;
    }
    
    // Validate term
    if (!termSelect.value) {
      showError(termSelect, termError, 'Please select a term');
      isValid = false;
    }
    
    // Validate academic year
    if (!academicYearInput.value) {
      showError(academicYearInput, yearError, 'Please enter the academic year');
      isValid = false;
    } else if (!/^\d{4}$/.test(academicYearInput.value)) {
      showError(academicYearInput, yearError, 'Please enter a valid 4-digit year');
      isValid = false;
    }
    
    // Validate payment date
    if (!paymentDateInput.value) {
      showError(paymentDateInput, dateError, 'Please select a payment date');
      isValid = false;
    } else {
      const selectedDate = new Date(paymentDateInput.value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      if (selectedDate > today) {
        showError(paymentDateInput, dateError, 'Payment date cannot be in the future');
        isValid = false;
      }
    }
    
    if (!isValid) {
      e.preventDefault();
      // Scroll to first error
      const firstError = document.querySelector('.error');
      if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstError.focus();
      }
      return false;
    }
    
    // Confirm before submission
    const studentName = studentSelect.options[studentSelect.selectedIndex].text;
    const paymentMethod = document.getElementById('payment_method').value;
    const confirmMsg = `Please review the payment details:\n\n` +
                      `Student: ${studentName}\n` +
                      `Amount: UGX ${amount.toLocaleString()}\n` +
                      `Payment Method: ${paymentMethod}\n` +
                      `Term: ${termSelect.value}\n` +
                      `Academic Year: ${academicYearInput.value}\n` +
                      `Payment Date: ${paymentDateInput.value}\n\n` +
                      `Would you like to save this payment?`;
    
    showConfirmModal(
      `Student: ${studentName}\nAmount: UGX ${amount.toLocaleString()}\nMethod: ${paymentMethod}\nTerm: ${termSelect.value}\nYear: ${academicYearInput.value}\nDate: ${paymentDateInput.value}\n\nSave this payment?`,
      () => { form.submit(); },
      { title: 'Confirm Payment?', confirmText: 'Yes, Record', icon: 'fa-money-bill-wave', isWarning: false }
    );
    e.preventDefault();
    return false;
  });
});
</script>
<?php include 'delete_modal.php'; ?>
</body>
</html>
