<?php
// manage_classes.php
session_start();
require 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION['username'])) {
    header("location:login.php");
    exit();
} elseif($_SESSION['usertype']=='student'){
    header("location:login.php");
    exit();
}

$success = $error = "";

// Handle add class form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = strtoupper(trim($_POST['class_name']));
    $level = $_POST['level'];

    if ($class_name && $level) {
        $stmt = $conn->prepare("INSERT INTO classes (class_name, level) VALUES (?, ?)");
        $stmt->bind_param("ss", $class_name, $level);
        if ($stmt->execute()) {
            $success = "Class '$class_name' added successfully.";
        } else {
            $error = "Error: Database error occurred";
        }
    } else {
        $error = "All fields are required.";
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM classes WHERE id = $delete_id");
    header("Location: manage_classes.php");
    exit();
}

// Fetch all classes
$classes = $conn->query("SELECT * FROM classes ORDER BY class_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes - Admin Dashboard</title>
    <?php include 'admin_css.php'; ?>
    <style>
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            font-size: 1.3rem;
            font-weight: 600;
        }
        .card-body {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(255, 107, 107, 0.4);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
        }
        .table tbody td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .badge-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-layer-group"></i> Manage School Classes
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="class_name"><i class="fas fa-graduation-cap"></i> Class Name</label>
                        <input type="text" id="class_name" name="class_name" class="form-control" 
                               placeholder="e.g. S1, S2, S3..." required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="level"><i class="fas fa-level-up-alt"></i> Level</label>
                        <select id="level" name="level" class="form-control" required>
                            <option value="">Select Level</option>
                            <option value="O-Level">O-Level</option>
                            <option value="A-Level">A-Level</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i> Add Class
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive mt-4">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="40%">Class Name</th>
                            <th width="30%">Level</th>
                            <th width="25%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($classes->num_rows > 0): $i = 1; ?>
                        <?php while ($row = $classes->fetch_assoc()): ?>
                            <tr>
                                <td><span class="badge badge-primary"><?php echo $i++; ?></span></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['class_name']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge badge-success">
                                        <?php echo htmlspecialchars($row['level']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex; gap:10px; align-items:center;">
                                        <a href="manage_classes.php?delete=<?php echo $row['id']; ?>"
                                           class="btn btn-danger"
                                           title="Delete this class"
                                           onclick="return confirm('Are you sure you want to delete class <?php echo htmlspecialchars($row['class_name']); ?>? This action cannot be undone.');">
                                           <i class="fas fa-trash"></i> Delete
                                        </a>
                                        <button class="btn btn-primary btn-edit-class" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-class_name="<?php echo htmlspecialchars($row['class_name']); ?>" 
                                                data-level="<?php echo htmlspecialchars($row['level']); ?>"
                                                title="Edit this class">
                                          <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="view_student.php?class_id=<?php echo $row['id']; ?>" class="btn btn-primary" title="View all students in this class">
                                            <i class="fa fa-user-graduate"></i> View Students
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No classes found. Add your first class using the form above.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div class="modal" id="editClassModal">
  <div class="modal-card" style="max-width:400px;">
    <button class="close-x" id="closeEditClassX" title="Close">&times;</button>
    <h2>Edit Class</h2>
    <form id="editClassForm">
      <input type="hidden" name="id" id="editClassId">
      <div class="form-group">
        <label>Class Name</label>
        <input type="text" name="class_name" id="editClassName" required>
      </div>
      <div class="form-group">
        <label>Level</label>
        <select name="level" id="editClassLevel" required>
          <option value="O-Level">O-Level</option>
          <option value="A-Level">A-Level</option>
        </select>
      </div>
      <div id="editClassMsg"></div>
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
      <button type="button" id="closeEditClass" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</button>
    </form>
  </div>
</div>

<!-- Delete Class Confirmation Modal -->
<div class="modal" id="deleteClassModal">
  <div class="modal-card" style="max-width:350px;">
    <h3 style="margin-bottom:18px;">Are you sure you want to delete this class?</h3>
    <div id="deleteClassMsg" style="margin-bottom:12px;"></div>
    <div style="display:flex; gap:12px; justify-content:flex-end;">
      <button id="confirmDeleteClass" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
      <button id="cancelDeleteClass" class="btn btn-primary"><i class="fas fa-times"></i> Cancel</button>
    </div>
  </div>
</div>

<script>
// Edit Class Modal logic
const editClassBtns = document.querySelectorAll('.btn-edit-class');
const editClassModal = document.getElementById('editClassModal');
const editClassForm = document.getElementById('editClassForm');
const editClassMsg = document.getElementById('editClassMsg');
const closeEditClassX = document.getElementById('closeEditClassX');
const closeEditClass = document.getElementById('closeEditClass');

editClassBtns.forEach(btn => {
  btn.addEventListener('click', function() {
    document.getElementById('editClassId').value = this.dataset.id;
    document.getElementById('editClassName').value = this.dataset.class_name;
    document.getElementById('editClassLevel').value = this.dataset.level;
    editClassMsg.textContent = '';
    editClassModal.style.display = 'flex';
  });
});
closeEditClass.onclick = closeEditClassX.onclick = () => { editClassModal.style.display = 'none'; };

// Edit Class AJAX
editClassForm.onsubmit = function(e) {
  e.preventDefault();
  editClassMsg.textContent = '';
  const formData = new FormData(editClassForm);
  fetch('edit_class_ajax.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      editClassMsg.textContent = 'Class updated! Reloading...';
      editClassMsg.className = 'alert alert-success';
      setTimeout(() => window.location.reload(), 1200);
    } else {
      editClassMsg.textContent = data.error || 'Update failed.';
      editClassMsg.className = 'alert alert-danger';
    }
  })
  .catch(() => {
    editClassMsg.textContent = 'Network error.';
    editClassMsg.className = 'alert alert-danger';
  });
};
// Delete Class Modal logic
let deleteClassId = null;
const deleteClassModal = document.getElementById('deleteClassModal');
const deleteClassMsg = document.getElementById('deleteClassMsg');
const confirmDeleteClass = document.getElementById('confirmDeleteClass');
const cancelDeleteClass = document.getElementById('cancelDeleteClass');
// Replace delete button click
const deleteBtns = document.querySelectorAll('a.btn-danger[onclick]');
deleteBtns.forEach(btn => {
  btn.onclick = function(e) {
    e.preventDefault();
    deleteClassId = this.href.split('delete=')[1];
    deleteClassMsg.textContent = '';
    deleteClassModal.style.display = 'flex';
  };
});
cancelDeleteClass.onclick = () => { deleteClassModal.style.display = 'none'; };
confirmDeleteClass.onclick = function() {
  if (!deleteClassId) return;
  confirmDeleteClass.disabled = true;
  deleteClassMsg.textContent = '';
  fetch('delete_class_ajax.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + encodeURIComponent(deleteClassId)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      deleteClassMsg.textContent = 'Class deleted! Reloading...';
      deleteClassMsg.className = 'alert alert-success';
      setTimeout(() => window.location.reload(), 1200);
    } else {
      deleteClassMsg.textContent = data.error || 'Delete failed.';
      deleteClassMsg.className = 'alert alert-danger';
      confirmDeleteClass.disabled = false;
    }
  })
  .catch(() => {
    deleteClassMsg.textContent = 'Network error.';
    deleteClassMsg.className = 'alert alert-danger';
    confirmDeleteClass.disabled = false;
  });
};
</script>

</body>
</html>
