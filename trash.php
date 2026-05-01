<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require_once 'config.php';

// Ensure columns exist
$conn->query("ALTER TABLE students ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
$conn->query("ALTER TABLE teachers ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
$conn->query("ALTER TABLE announcements ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
$conn->query("ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");

$tab = $_GET['tab'] ?? 'students';

$students     = $conn->query("SELECT * FROM students WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
$teachers     = $conn->query("SELECT * FROM teachers WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
$announcements= $conn->query("SELECT * FROM announcements WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
$messages     = $conn->query("SELECT * FROM contact_messages WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");

$counts = [
    'students'      => $students->num_rows,
    'teachers'      => $teachers->num_rows,
    'announcements' => $announcements->num_rows,
    'messages'      => $messages->num_rows,
];
$total = array_sum($counts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trash</title>
  <?php include 'admin_css.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { background: linear-gradient(135deg,#667eea,#764ba2); min-height:100vh; font-family:'Poppins',sans-serif; margin:0; }
    .content { margin-left:280px; padding:30px 20px; max-width:calc(100vw - 300px); }
    .card { background:#fff; border-radius:20px; padding:36px; margin-bottom:24px; box-shadow:0 8px 32px rgba(0,0,0,0.08); position:relative; overflow:hidden; }
    .card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#e74c3c,#ff6b6b); }
    h2 { font-size:1.8rem; font-weight:800; color:#e74c3c; margin-bottom:4px; display:flex; align-items:center; gap:12px; }
    .tabs { display:flex; gap:8px; margin-bottom:24px; flex-wrap:wrap; }
    .tab { padding:10px 20px; border-radius:10px; border:2px solid #e0e0e0; background:#fff; color:#666; font-weight:600; cursor:pointer; text-decoration:none; font-size:0.9rem; transition:all 0.2s; }
    .tab.active { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border-color:transparent; }
    .tab .badge { background:rgba(255,255,255,0.3); border-radius:20px; padding:1px 8px; margin-left:6px; font-size:0.8rem; }
    .tab:not(.active) .badge { background:#f0f0f0; color:#e74c3c; }
    table { width:100%; border-collapse:collapse; }
    th { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; padding:12px 14px; text-align:left; font-weight:600; font-size:0.9rem; }
    td { padding:11px 14px; border-bottom:1px solid #f0f0f0; color:#333; font-size:0.9rem; }
    tr:hover td { background:#fff5f5; }
    .btn-restore { background:linear-gradient(135deg,#27ae60,#2ecc71); color:#fff; border:none; border-radius:8px; padding:6px 14px; font-size:0.82rem; font-weight:600; cursor:pointer; }
    .btn-perm { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border:none; border-radius:8px; padding:6px 14px; font-size:0.82rem; font-weight:600; cursor:pointer; margin-left:6px; }
    .empty { text-align:center; padding:50px; color:#bbb; }
    .empty i { font-size:2.5rem; display:block; margin-bottom:12px; }
    .header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .btn-empty-all { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border:none; border-radius:10px; padding:10px 22px; font-size:0.9rem; font-weight:700; cursor:pointer; }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="content">
  <div class="card">
    <div class="header-row">
      <h2><i class="fa-solid fa-trash"></i> Trash <?php if($total > 0): ?><span style="font-size:1rem;color:#e74c3c;background:#fff5f5;padding:4px 14px;border-radius:20px;"><?php echo $total; ?> items</span><?php endif; ?></h2>
      <?php if($total > 0): ?>
      <button class="btn-empty-all" onclick="emptyAll()"><i class="fa-solid fa-trash"></i> Empty All Trash</button>
      <?php endif; ?>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <a href="?tab=students" class="tab <?php echo $tab=='students'?'active':''; ?>">
        <i class="fa-solid fa-user-graduate"></i> Students <span class="badge"><?php echo $counts['students']; ?></span>
      </a>
      <a href="?tab=teachers" class="tab <?php echo $tab=='teachers'?'active':''; ?>">
        <i class="fa-solid fa-chalkboard-teacher"></i> Teachers <span class="badge"><?php echo $counts['teachers']; ?></span>
      </a>
      <a href="?tab=announcements" class="tab <?php echo $tab=='announcements'?'active':''; ?>">
        <i class="fa-solid fa-bullhorn"></i> Announcements <span class="badge"><?php echo $counts['announcements']; ?></span>
      </a>
      <a href="?tab=messages" class="tab <?php echo $tab=='messages'?'active':''; ?>">
        <i class="fa-solid fa-envelope"></i> Messages <span class="badge"><?php echo $counts['messages']; ?></span>
      </a>
    </div>

    <!-- Students Tab -->
    <?php if ($tab === 'students'): ?>
    <?php if ($counts['students'] == 0): ?>
      <div class="empty"><i class="fa-solid fa-user-graduate"></i> No deleted students</div>
    <?php else: ?>
    <table><thead><tr><th>Name</th><th>Email</th><th>Class</th><th>Deleted</th><th>Actions</th></tr></thead><tbody id="tbody">
      <?php while($r = $students->fetch_assoc()): ?>
      <tr id="row-<?php echo $r['id']; ?>">
        <td><?php echo htmlspecialchars($r['username']); ?></td>
        <td><?php echo htmlspecialchars($r['email']); ?></td>
        <td><?php echo $r['class_id'] ?? '-'; ?></td>
        <td><?php echo date('M d, Y', strtotime($r['deleted_at'])); ?></td>
        <td>
          <button class="btn-restore" onclick="act('delete_student.php','restore',<?php echo $r['id']; ?>)"><i class="fa-solid fa-rotate-left"></i> Restore</button>
          <button class="btn-perm" onclick="act('delete_student.php','permanent',<?php echo $r['id']; ?>)"><i class="fa-solid fa-trash"></i> Delete Forever</button>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody></table>
    <?php endif; ?>

    <!-- Teachers Tab -->
    <?php elseif ($tab === 'teachers'): ?>
    <?php if ($counts['teachers'] == 0): ?>
      <div class="empty"><i class="fa-solid fa-chalkboard-teacher"></i> No deleted teachers</div>
    <?php else: ?>
    <table><thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Deleted</th><th>Actions</th></tr></thead><tbody id="tbody">
      <?php while($r = $teachers->fetch_assoc()): ?>
      <tr id="row-<?php echo $r['id']; ?>">
        <td><?php echo htmlspecialchars($r['full_name']); ?></td>
        <td><?php echo htmlspecialchars($r['email']); ?></td>
        <td><?php echo htmlspecialchars($r['subject']); ?></td>
        <td><?php echo date('M d, Y', strtotime($r['deleted_at'])); ?></td>
        <td>
          <button class="btn-restore" onclick="act('delete_teacher.php','restore',<?php echo $r['id']; ?>)"><i class="fa-solid fa-rotate-left"></i> Restore</button>
          <button class="btn-perm" onclick="act('delete_teacher.php','permanent',<?php echo $r['id']; ?>)"><i class="fa-solid fa-trash"></i> Delete Forever</button>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody></table>
    <?php endif; ?>

    <!-- Announcements Tab -->
    <?php elseif ($tab === 'announcements'): ?>
    <?php if ($counts['announcements'] == 0): ?>
      <div class="empty"><i class="fa-solid fa-bullhorn"></i> No deleted announcements</div>
    <?php else: ?>
    <table><thead><tr><th>Title</th><th>Category</th><th>Date</th><th>Deleted</th><th>Actions</th></tr></thead><tbody id="tbody">
      <?php while($r = $announcements->fetch_assoc()): ?>
      <tr id="row-<?php echo $r['id']; ?>">
        <td><?php echo htmlspecialchars($r['title']); ?></td>
        <td><?php echo htmlspecialchars($r['category']); ?></td>
        <td><?php echo $r['date']; ?></td>
        <td><?php echo date('M d, Y', strtotime($r['deleted_at'])); ?></td>
        <td>
          <button class="btn-restore" onclick="act('delete_announcement_ajax.php','restore',<?php echo $r['id']; ?>)"><i class="fa-solid fa-rotate-left"></i> Restore</button>
          <button class="btn-perm" onclick="act('delete_announcement_ajax.php','permanent',<?php echo $r['id']; ?>)"><i class="fa-solid fa-trash"></i> Delete Forever</button>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody></table>
    <?php endif; ?>

    <!-- Messages Tab -->
    <?php elseif ($tab === 'messages'): ?>
    <?php if ($counts['messages'] == 0): ?>
      <div class="empty"><i class="fa-solid fa-envelope"></i> No deleted messages</div>
    <?php else: ?>
    <table><thead><tr><th>Name</th><th>Email</th><th>Message</th><th>Deleted</th><th>Actions</th></tr></thead><tbody id="tbody">
      <?php while($r = $messages->fetch_assoc()): ?>
      <tr id="row-<?php echo $r['id']; ?>">
        <td><?php echo htmlspecialchars($r['first_name'].' '.$r['last_name']); ?></td>
        <td><?php echo htmlspecialchars($r['email']); ?></td>
        <td><?php echo htmlspecialchars(mb_strimwidth($r['message'],0,50,'...')); ?></td>
        <td><?php echo date('M d, Y', strtotime($r['deleted_at'])); ?></td>
        <td>
          <button class="btn-restore" onclick="act('delete_message_ajax.php','restore',<?php echo $r['id']; ?>)"><i class="fa-solid fa-rotate-left"></i> Restore</button>
          <button class="btn-perm" onclick="act('delete_message_ajax.php','permanent',<?php echo $r['id']; ?>)"><i class="fa-solid fa-trash"></i> Delete Forever</button>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody></table>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<script>
function act(url, action, id) {
    if (action === 'permanent') {
        showConfirmModal(
            'Permanently delete this item? This action cannot be undone.',
            function() { doAct(url, action, id); },
            { title: 'Delete Forever?', confirmText: 'Yes, Delete', icon: 'fa-trash' }
        );
    } else {
        doAct(url, action, id);
    }
}
function doAct(url, action, id) {
    fetch(url, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'action=' + action + '&id=' + id
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const row = document.getElementById('row-' + id);
            if (row) {
                row.style.transition = 'opacity 0.4s';
                row.style.opacity = '0';
                setTimeout(() => { row.remove(); }, 400);
            }
        }
    });
}

function emptyAll() {
    showConfirmModal(
        'Permanently delete ALL items in trash? This cannot be undone.',
        function() {
            document.querySelectorAll('#tbody tr').forEach(row => {
                const id = row.id.replace('row-','');
                const tab = new URLSearchParams(window.location.search).get('tab') || 'students';
                const urls = { students:'delete_student.php', teachers:'delete_teacher.php', announcements:'delete_announcement_ajax.php', messages:'delete_message_ajax.php' };
                fetch(urls[tab], { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=permanent&id='+id });
            });
            setTimeout(() => location.reload(), 800);
        },
        { title: 'Empty All Trash?', confirmText: 'Yes, Delete All', icon: 'fa-trash' }
    );
}
</script>
<?php include 'delete_modal.php'; ?>
</body>
</html>
