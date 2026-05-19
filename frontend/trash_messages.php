<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require_once '../shared/config.php';

// Ensure column exists
$conn->query("ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");

$messages = $conn->query("SELECT * FROM contact_messages WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
$count = $messages->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trash - Messages</title>
  <?php include 'admin_css.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { background: linear-gradient(135deg,#667eea,#764ba2); min-height:100vh; font-family:'Poppins',sans-serif; margin:0; }
    .content { margin-left:280px; padding:30px 20px; max-width:calc(100vw - 300px); }
    .card { background:#fff; border-radius:20px; padding:36px; box-shadow:0 8px 32px rgba(0,0,0,0.08); position:relative; overflow:hidden; }
    .card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#e74c3c,#ff6b6b); }
    h2 { font-size:1.8rem; font-weight:800; color:#e74c3c; margin-bottom:8px; display:flex; align-items:center; gap:12px; }
    .back-btn { display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; padding:10px 20px; border-radius:10px; text-decoration:none; font-weight:600; margin-bottom:24px; font-size:0.95rem; }
    .empty-trash-btn { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border:none; border-radius:10px; padding:10px 22px; font-size:0.95rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:8px; margin-bottom:24px; margin-left:10px; }
    table { width:100%; border-collapse:collapse; }
    th { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; padding:12px 14px; text-align:left; font-weight:600; }
    td { padding:12px 14px; border-bottom:1px solid #f0f0f0; color:#333; vertical-align:middle; }
    tr:hover td { background:#fff5f5; }
    .btn-restore { background:linear-gradient(135deg,#27ae60,#2ecc71); color:#fff; border:none; border-radius:8px; padding:7px 14px; font-size:0.85rem; font-weight:600; cursor:pointer; }
    .btn-delete { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border:none; border-radius:8px; padding:7px 14px; font-size:0.85rem; font-weight:600; cursor:pointer; margin-left:6px; }
    .empty-state { text-align:center; padding:60px; color:#999; }
    .empty-state i { font-size:3rem; margin-bottom:16px; display:block; }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <a href="dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
  <?php if ($count > 0): ?>
  <button class="empty-trash-btn" onclick="emptyTrash()">
    <i class="fa-solid fa-trash"></i> Empty Trash (<?php echo $count; ?>)
  </button>
  <?php endif; ?>

  <div class="card">
    <h2><i class="fa-solid fa-trash"></i> Trash</h2>
    <p style="color:#888;margin-bottom:24px;">Messages moved to trash. Restore them or permanently delete.</p>

    <?php if ($count == 0): ?>
      <div class="empty-state">
        <i class="fa-solid fa-trash-can"></i>
        <p>Trash is empty</p>
      </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Deleted</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="trashTable">
          <?php while ($row = $messages->fetch_assoc()): ?>
          <tr id="row-<?php echo $row['id']; ?>">
            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars(mb_strimwidth($row['message'], 0, 50, '...')); ?></td>
            <td><?php echo date('M d, Y g:i A', strtotime($row['deleted_at'])); ?></td>
            <td>
              <button class="btn-restore" onclick="restoreMsg(<?php echo $row['id']; ?>)">
                <i class="fa-solid fa-rotate-left"></i> Restore
              </button>
              <button class="btn-delete" onclick="permanentDelete(<?php echo $row['id']; ?>)">
                <i class="fa-solid fa-trash"></i> Delete Forever
              </button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
function restoreMsg(id) {
    fetch('../backend/delete_message_ajax.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=restore&id=' + id
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const row = document.getElementById('row-' + id);
            row.style.transition = 'opacity 0.4s';
            row.style.opacity = '0';
            setTimeout(() => { row.remove(); checkEmpty(); }, 400);
        }
    });
}

function permanentDelete(id) {
    showConfirmModal(
        'Permanently delete this message? This cannot be undone.',
        function() {
            fetch('../backend/delete_message_ajax.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=permanent&id=' + id
            }).then(r => r.json()).then(d => {
                if (d.success) {
                    const row = document.getElementById('row-' + id);
                    row.style.transition = 'opacity 0.4s';
                    row.style.opacity = '0';
                    setTimeout(() => { row.remove(); checkEmpty(); }, 400);
                }
            });
        },
        { title: 'Delete Forever?', confirmText: 'Yes, Delete', icon: 'fa-trash' }
    );
}

function emptyTrash() {
    showConfirmModal(
        'Permanently delete ALL messages in trash? This cannot be undone.',
        function() {
            document.querySelectorAll('#trashTable tr').forEach(row => {
                const id = row.id.replace('row-', '');
                fetch('../backend/delete_message_ajax.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=permanent&id=' + id
                });
            });
            setTimeout(() => location.reload(), 800);
        },
        { title: 'Empty Trash?', confirmText: 'Yes, Delete All', icon: 'fa-trash' }
    );
}

function checkEmpty() {
    if (document.querySelectorAll('#trashTable tr').length === 0) {
        location.reload();
    }
}
</script>
<?php include 'delete_modal.php'; ?>
</body>
</html>
