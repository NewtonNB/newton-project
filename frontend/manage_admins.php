<?php
session_start();

if(!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') {
    header("location:login.php");
    exit();
}

require '../shared/config.php';

// Fetch all admins
$result = $conn->query("SELECT * FROM admins ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - Nyabikoni Secondary School</title>
    <?php include 'admin_css.php'; ?>
    <style>
        /* Use shared Poppins font from admin_css.php */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .main-content {
            margin-left: 280px;
            padding: 2rem;
            max-width: calc(100vw - 280px);
        }

        .admin-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .admin-header h1 {
            color: #667eea;
            margin: 0;
            font-size: 2rem;
        }

        .add-admin-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .add-admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .admins-table {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-active {
            background: #d4edda;
            color: #155724;
        }

        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .edit-btn {
            background: #ffc107;
            color: #000;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-content h2 {
            color: #667eea;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 2rem;
        }

        .submit-btn {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .cancel-btn {
            flex: 1;
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: none;
        }

        .success-msg {
            background: #d4edda;
            color: #155724;
            display: block;
        }

        .error-msg {
            background: #f8d7da;
            color: #721c24;
            display: block;
        }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="admin-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1><i class="fas fa-user-shield"></i> Manage Administrators</h1>
                <button class="add-admin-btn" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Admin
                </button>
            </div>
        </div>

        <div class="admins-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($admin = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $admin['id']; ?></td>
                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td><?php echo htmlspecialchars($admin['phone']); ?></td>
                        <td>
                            <span class="badge <?php echo $admin['status'] == 'Active' ? 'badge-active' : 'badge-inactive'; ?>">
                                <?php echo $admin['status']; ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                        <td>
                            <button class="action-btn edit-btn" onclick='editAdmin(<?php echo json_encode($admin); ?>)'>
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <?php if($admin['username'] != $_SESSION['username']): ?>
                            <button class="action-btn delete-btn" onclick="deleteAdmin(<?php echo $admin['id']; ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <h2>Add New Administrator</h2>
            <div id="addMessage" class="message"></div>
            <form id="addForm">
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone">
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="submit" class="submit-btn">Add Admin</button>
                    <button type="button" class="cancel-btn" onclick="closeAddModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <h2>Edit Administrator</h2>
            <div id="editMessage" class="message"></div>
            <form id="editForm">
                <input type="hidden" name="id" id="editId">
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" id="editUsername" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="editEmail" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" id="editPhone">
                </div>
                <div class="form-group">
                    <label>New Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="editPassword">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="editStatus">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="submit" class="submit-btn">Update Admin</button>
                    <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
            document.getElementById('addForm').reset();
            document.getElementById('addMessage').style.display = 'none';
        }

        function openEditModal() {
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.getElementById('editForm').reset();
            document.getElementById('editMessage').style.display = 'none';
        }

        function editAdmin(admin) {
            document.getElementById('editId').value = admin.id;
            document.getElementById('editUsername').value = admin.username;
            document.getElementById('editEmail').value = admin.email;
            document.getElementById('editPhone').value = admin.phone || '';
            document.getElementById('editStatus').value = admin.status;
            openEditModal();
        }

        function deleteAdmin(id) {
            showConfirmModal(
                'Are you sure you want to delete this administrator? This action cannot be undone.',
                function() {

                    fetch('delete_admin_ajax.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'id=' + id
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(err => alert('Network error'));
                },
                { title: 'Delete Administrator?', confirmText: 'Yes, Delete', icon: 'fa-user-slash' }
            );
        }

        // Add Form Submit
        document.getElementById('addForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const msg = document.getElementById('addMessage');

            fetch('add_admin_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    msg.className = 'message success-msg';
                    msg.textContent = 'Admin added successfully!';
                    setTimeout(() => location.reload(), 1500);
                } else {
                    msg.className = 'message error-msg';
                    msg.textContent = data.error;
                }
            })
            .catch(err => {
                msg.className = 'message error-msg';
                msg.textContent = 'Network error';
            });
        });

        // Edit Form Submit
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const msg = document.getElementById('editMessage');

            fetch('edit_admin_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    msg.className = 'message success-msg';
                    msg.textContent = 'Admin updated successfully!';
                    setTimeout(() => location.reload(), 1500);
                } else {
                    msg.className = 'message error-msg';
                    msg.textContent = data.error;
                }
            })
            .catch(err => {
                msg.className = 'message error-msg';
                msg.textContent = 'Network error';
            });
        });

        // Close modals on outside click
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
<?php include 'delete_modal.php'; ?>
</body>
</html>
