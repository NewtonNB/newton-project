<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require_once '../shared/config.php';
require_once 'email_helper.php';

$conn->query("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Handle delete subscriber
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM newsletter_subscribers WHERE id=" . intval($_GET['delete']));
    header('Location: send_newsletter.php'); exit;
}

// Handle send
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_newsletter'])) {
    $subject = trim($_POST['subject']);
    $body    = trim($_POST['body']);
    if ($subject && $body) {
        $subs = $conn->query("SELECT name, email FROM newsletter_subscribers");
        $total = $subs->num_rows;
        $sent = $failed = 0;
        while ($s = $subs->fetch_assoc()) {
            $html = "<p>Dear <strong>{$s['name']}</strong>,</p>"
                  . nl2br(htmlspecialchars($body))
                  . "<hr style='border:none;border-top:1px solid #eee;margin:24px 0;'>"
                  . "<p style='font-size:12px;color:#999;'>You received this because you subscribed to Nyabikoni Secondary School newsletter. Reply 'Unsubscribe' to opt out.</p>";
            sendEmail($s['email'], $s['name'], $subject, $html) ? $sent++ : $failed++;
        }
        $result = compact('sent','failed','total');
    }
}

$subscribers = $conn->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC");
$total_subs  = $subscribers->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Send Newsletter</title>
  <?php include '../frontend/admin_css.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { background: linear-gradient(135deg,#667eea,#764ba2); min-height:100vh; font-family:'Poppins',sans-serif; margin:0; }
    .content { margin-left:280px; padding:30px 20px; max-width:calc(100vw - 300px); }
    .card { background:#fff; border-radius:20px; padding:36px; margin-bottom:28px; box-shadow:0 8px 32px rgba(0,0,0,0.08); position:relative; overflow:hidden; }
    .card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#667eea,#764ba2,#f093fb); }
    h2 { font-size:1.8rem; font-weight:800; background:linear-gradient(135deg,#667eea,#764ba2); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; display:flex; align-items:center; gap:12px; }
    .stat-box { display:inline-flex; align-items:center; gap:10px; background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; padding:14px 24px; border-radius:12px; font-size:1.1rem; font-weight:700; margin-bottom:24px; }
    label { font-weight:600; color:#764ba2; display:block; margin-bottom:6px; }
    input[type=text], textarea { width:100%; padding:13px 16px; border:2px solid #e0e0e0; border-radius:10px; font-size:1rem; font-family:'Poppins',sans-serif; transition:all 0.3s; margin-bottom:18px; box-sizing:border-box; }
    input[type=text]:focus, textarea:focus { border-color:#667eea; outline:none; box-shadow:0 0 0 3px rgba(102,126,234,0.1); }
    textarea { min-height:200px; resize:vertical; }
    .btn { background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; border:none; border-radius:10px; padding:13px 32px; font-size:1.05rem; font-weight:700; cursor:pointer; transition:all 0.3s; display:inline-flex; align-items:center; gap:8px; }
    .btn:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(102,126,234,0.3); }
    .btn-sm { background:linear-gradient(135deg,#ff6b6b,#ee5a52); padding:6px 14px; font-size:0.82rem; border-radius:8px; color:#fff; border:none; cursor:pointer; }
    .success-box { background:#eafaf1; border-left:4px solid #27ae60; padding:16px 20px; border-radius:8px; margin-bottom:20px; color:#1e8449; font-weight:600; }
    .info-box { background:#f8f8ff; border-left:4px solid #667eea; padding:14px 18px; border-radius:8px; margin-bottom:20px; color:#555; font-size:0.92rem; }
    .warn-box { background:#fff8e1; border-left:4px solid #f39c12; padding:14px 18px; border-radius:8px; margin-bottom:20px; color:#7d6608; font-weight:600; }
    table { width:100%; border-collapse:collapse; }
    th { background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; padding:12px 14px; text-align:left; font-weight:600; }
    td { padding:12px 14px; border-bottom:1px solid #f0f0f0; color:#333; }
    tr:hover td { background:#f8f8ff; }
  </style>
</head>
<body>
<?php include '../frontend/admin_sidebar.php'; ?>
<div class="content">

  <div class="stat-box">
    <i class="fa-solid fa-users"></i>
    <?php echo $total_subs; ?> Subscriber<?php echo $total_subs != 1 ? 's' : ''; ?>
  </div>

  <!-- Send Form -->
  <div class="card">
    <h2><i class="fa-solid fa-paper-plane"></i> Send Newsletter</h2>

    <?php if ($result !== null): ?>
      <?php if ($result['total'] == 0): ?>
        <div class="warn-box"><i class="fa-solid fa-triangle-exclamation"></i> No subscribers found yet.</div>
      <?php else: ?>
        <div class="success-box">
          <i class="fa-solid fa-check-circle"></i>
          Newsletter sent! &nbsp; ✅ <?php echo $result['sent']; ?> delivered
          <?php if ($result['failed'] > 0): ?> &nbsp; ❌ <?php echo $result['failed']; ?> failed<?php endif; ?>
          &nbsp; out of <?php echo $result['total']; ?> subscribers.
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($total_subs == 0): ?>
      <div class="warn-box"><i class="fa-solid fa-info-circle"></i> No subscribers yet. Share the newsletter signup on your website to grow your list.</div>
    <?php else: ?>
    <form method="post" novalidate>
      <input type="hidden" name="send_newsletter" value="1">
      <div>
        <label>Subject *</label>
        <input type="text" name="subject" placeholder="e.g. School News - Term 1 2026" required>
      </div>
      <div>
        <label>Message *</label>
        <textarea name="body" placeholder="Write your newsletter content here..." required></textarea>
      </div>
      <div class="info-box">
        <i class="fa-solid fa-info-circle" style="color:#667eea;"></i>
        This will be sent to all <strong><?php echo $total_subs; ?></strong> subscriber(s). Each email will be personalised with the subscriber's name.
      </div>
      <button type="button" class="btn"
        onclick="showConfirmModal('Send this newsletter to all <?php echo $total_subs; ?> subscriber(s)? Each will receive a personalised email.', function(){ document.querySelector('form[method=post]').submit(); }, {title:'Send Newsletter?', confirmText:'Yes, Send Now', icon:'fa-paper-plane', isWarning:false})">
        <i class="fa-solid fa-paper-plane"></i> Send to All Subscribers
      </button>
    </form>
    <?php endif; ?>
  </div>

  <!-- Subscribers List -->
  <div class="card">
    <h2><i class="fa-solid fa-list"></i> Subscribers</h2>
    <?php if ($total_subs == 0): ?>
      <p style="color:#999;text-align:center;padding:30px;">No subscribers yet.</p>
    <?php else: ?>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subscribed</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $i=1; while ($s = $subscribers->fetch_assoc()): ?>
          <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($s['name']); ?></td>
            <td><?php echo htmlspecialchars($s['email']); ?></td>
            <td><?php echo date('M d, Y', strtotime($s['subscribed_at'])); ?></td>
            <td>
              <button type="button" class="btn-sm" onclick="showDeleteModal('<?php echo htmlspecialchars(addslashes($s['name'] . ' <' . $s['email'] . '>')); ?>', 'send_newsletter.php?delete=<?php echo $s['id']; ?>')">
                <i class="fa-solid fa-trash"></i> Remove
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
<?php include 'delete_modal.php'; ?>
</body>
</html>
