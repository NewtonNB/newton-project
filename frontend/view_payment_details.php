<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../shared/config.php';

// Handle delete payment (soft delete)
if (isset($_GET['delete_payment'])) {
    $payment_id = intval($_GET['delete_payment']);
    // Instead of deleting, we'll mark it as deleted by setting amount to negative
    // Or we can add a 'deleted' column. For now, let's just delete it.
    $delete_stmt = $conn->prepare("DELETE FROM fees WHERE id = ?");
    $delete_stmt->bind_param("i", $payment_id);
    if ($delete_stmt->execute()) {
        $success_message = "Payment removed successfully!";
    }
}

// Handle update payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment'])) {
    $payment_id = intval($_POST['payment_id']);
    $amount = floatval($_POST['amount']);
    $payment_method = $_POST['payment_method'];
    $term = $_POST['term'];
    $academic_year = $_POST['academic_year'];
    $payment_date = $_POST['payment_date'];
    
    $update_stmt = $conn->prepare("UPDATE fees SET amount_paid = ?, payment_method = ?, term = ?, academic_year = ?, payment_date = ? WHERE id = ?");
    $update_stmt->bind_param("dssssi", $amount, $payment_method, $term, $academic_year, $payment_date, $payment_id);
    
    if ($update_stmt->execute()) {
        $success_message = "Payment updated successfully!";
    } else {
        $error_message = "Error updating payment: " . $conn->error;
    }
}

$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

if (!$student_id) {
    header('Location: fee_status.php');
    exit;
}

// Fetch student info
$student_query = $conn->prepare("SELECT s.username, s.email, s.phone, c.class_name 
                                 FROM students s 
                                 LEFT JOIN classes c ON s.class_id = c.id 
                                 WHERE s.id = ?");
$student_query->bind_param("i", $student_id);
$student_query->execute();
$student = $student_query->get_result()->fetch_assoc();

if (!$student) {
    die("Student not found");
}

// Fetch payment history
$payments_query = $conn->prepare("SELECT * FROM fees WHERE student_id = ? ORDER BY payment_date DESC");
$payments_query->bind_param("i", $student_id);
$payments_query->execute();
$payments = $payments_query->get_result();

// Calculate totals
$total_paid = 0;
$payments->data_seek(0);
while ($payment = $payments->fetch_assoc()) {
    $total_paid += $payment['amount_paid'];
}
$payments->data_seek(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Details - <?php echo htmlspecialchars($student['username']); ?></title>
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
    .student-info {
      background: #f0f4ff;
      padding: 20px;
      border-radius: 12px;
      margin: 20px 0;
      border-left: 4px solid #667eea;
    }
    .student-info h3 {
      color: #667eea;
      margin-top: 0;
    }
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 15px;
    }
    .info-item {
      display: flex;
      flex-direction: column;
    }
    .info-label {
      font-weight: 600;
      color: #764ba2;
      font-size: 0.9rem;
    }
    .info-value {
      color: #333;
      font-size: 1.1rem;
    }
    .summary-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      margin: 20px 0;
    }
    .summary-card h2 {
      margin: 0;
      font-size: 3rem;
    }
    .summary-card p {
      margin: 10px 0 0 0;
      font-size: 1.2rem;
      opacity: 0.9;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
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
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      display: inline-block;
      margin-bottom: 20px;
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
    .badge {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }
    .badge-cash { background: #d4edda; color: #155724; }
    .badge-mobile { background: #cce5ff; color: #004085; }
    .badge-bank { background: #fff3cd; color: #856404; }
    .badge-cheque { background: #f8d7da; color: #721c24; }
    .action-btn {
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.85rem;
      margin-right: 5px;
      text-decoration: none;
      display: inline-block;
    }
    .btn-edit {
      background: #3b82f6;
      color: white;
    }
    .btn-edit:hover {
      background: #2563eb;
    }
    .btn-delete {
      background: #ef4444;
      color: white;
    }
    .btn-delete:hover {
      background: #dc2626;
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
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 9999;
      align-items: center;
      justify-content: center;
    }
    .modal.active {
      display: flex;
    }
    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 16px;
      max-width: 500px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
    }
    .modal-content h3 {
      color: #667eea;
      margin-top: 0;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      color: #764ba2;
      margin-bottom: 5px;
    }
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 10px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
    }
    .btn-save {
      background: #10b981;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
    }
    .btn-cancel {
      background: #6c757d;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-left: 10px;
    }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <div class="modern-container">
    <a href="fee_status.php" class="btn">
      <i class="fas fa-arrow-left"></i> Back to Fee Status
    </a>
    
    <div class="page-header">
      <h1><i class="fas fa-file-invoice-dollar"></i> Payment Details</h1>
    </div>
    
    <?php if (isset($success_message)): ?>
    <div class="success-msg">
      <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
    <div class="error-msg">
      <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
    
    <div class="student-info">
      <h3>Student Information</h3>
      <div class="info-grid">
        <div class="info-item">
          <span class="info-label">Name</span>
          <span class="info-value"><?php echo htmlspecialchars($student['username']); ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Class</span>
          <span class="info-value"><?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Email</span>
          <span class="info-value"><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Phone</span>
          <span class="info-value"><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></span>
        </div>
      </div>
    </div>
    
    <div class="summary-card">
      <h2>UGX <?php echo number_format($total_paid); ?></h2>
      <p>Total Amount Paid</p>
    </div>
    
    <h3 style="color: #764ba2; margin-top: 30px;">Payment History</h3>
    
    <?php if ($payments->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Amount</th>
          <th>Payment Method</th>
          <th>Term</th>
          <th>Academic Year</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $i = 1;
        while ($payment = $payments->fetch_assoc()): 
          $badge_class = 'badge-cash';
          if ($payment['payment_method'] == 'Mobile Money') $badge_class = 'badge-mobile';
          elseif ($payment['payment_method'] == 'Bank Transfer') $badge_class = 'badge-bank';
          elseif ($payment['payment_method'] == 'Cheque') $badge_class = 'badge-cheque';
        ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
          <td class="amount">UGX <?php echo number_format($payment['amount_paid']); ?></td>
          <td>
            <span class="badge <?php echo $badge_class; ?>">
              <?php echo htmlspecialchars($payment['payment_method']); ?>
            </span>
          </td>
          <td><?php echo htmlspecialchars($payment['term']); ?></td>
          <td><?php echo htmlspecialchars($payment['academic_year']); ?></td>
          <td>
            <button class="action-btn btn-edit" onclick="editPayment(<?php echo htmlspecialchars(json_encode($payment)); ?>)">
              <i class="fas fa-edit"></i> Edit
            </button>
            <button type="button" class="action-btn btn-delete"
               onclick="showDeleteModal('Payment #<?php echo $payment['id']; ?> — UGX <?php echo number_format($payment['amount_paid']); ?>', '?student_id=<?php echo $student_id; ?>&delete_payment=<?php echo $payment['id']; ?>')">
              <i class="fas fa-trash"></i> Remove
            </button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div style="text-align: center; padding: 60px; color: #999;">
      <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 20px;"></i>
      <p style="font-size: 1.2rem;">No payment records found for this student</p>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Edit Payment Modal -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <h3><i class="fas fa-edit"></i> Edit Payment</h3>
    <form method="post">
      <input type="hidden" name="payment_id" id="edit_payment_id">
      <input type="hidden" name="update_payment" value="1">
      
      <div class="form-group">
        <label>Amount (UGX)</label>
        <input type="number" name="amount" id="edit_amount" step="0.01" required>
      </div>
      
      <div class="form-group">
        <label>Payment Method</label>
        <select name="payment_method" id="edit_payment_method" required>
          <option value="Cash">Cash</option>
          <option value="Mobile Money">Mobile Money</option>
          <option value="Bank Transfer">Bank Transfer</option>
          <option value="Cheque">Cheque</option>
        </select>
      </div>
      
      <div class="form-group">
        <label>Term</label>
        <select name="term" id="edit_term" required>
          <option value="Term 1">Term 1</option>
          <option value="Term 2">Term 2</option>
          <option value="Term 3">Term 3</option>
        </select>
      </div>
      
      <div class="form-group">
        <label>Academic Year</label>
        <input type="text" name="academic_year" id="edit_academic_year" required>
      </div>
      
      <div class="form-group">
        <label>Payment Date</label>
        <input type="date" name="payment_date" id="edit_payment_date" required>
      </div>
      
      <button type="submit" class="btn-save">
        <i class="fas fa-save"></i> Save Changes
      </button>
      <button type="button" class="btn-cancel" onclick="closeModal()">
        Cancel
      </button>
    </form>
  </div>
</div>

<script>
function editPayment(payment) {
  document.getElementById('edit_payment_id').value = payment.id;
  document.getElementById('edit_amount').value = payment.amount_paid;
  document.getElementById('edit_payment_method').value = payment.payment_method;
  document.getElementById('edit_term').value = payment.term;
  document.getElementById('edit_academic_year').value = payment.academic_year;
  document.getElementById('edit_payment_date').value = payment.payment_date;
  
  document.getElementById('editModal').classList.add('active');
}

function closeModal() {
  document.getElementById('editModal').classList.remove('active');
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeModal();
  }
});
</script>
<?php include 'delete_modal.php'; ?>
</body>
</html>
