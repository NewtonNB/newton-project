<?php

session_start();

if(!isset($_SESSION['username']))
{
    header("location:login.php");
}
elseif($_SESSION['usertype']=='student'){
        header("location:login.php");
}

require_once 'config.php'; 

function get_initials($name) {
    $words = preg_split('/\s+/', trim($name));
    $initials = '';
    foreach ($words as $w) {
        if ($w !== '') {
            $initials .= strtoupper($w[0]);
        }
    }
    return $initials;
}

// Pagination setup
$applicationsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Get total applications
$totalSql = "SELECT COUNT(*) as total FROM admission";
$totalResult = $conn->query($totalSql);
$totalApplications = $totalResult ? (int)$totalResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalApplications / $applicationsPerPage);
$offset = ($page - 1) * $applicationsPerPage;

// Fetch paginated applications
$sql = "SELECT * FROM admission WHERE status != 'approved' LIMIT $applicationsPerPage OFFSET $offset";
$admission_result = $conn->query($sql);

$sql = "SELECT * FROM contact_messages ORDER BY submitted_at DESC";
$contact_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Management - NYABIKONI SECONDARY SCHOOL</title>
    <?php include 'admin_css.php'; ?>
    <link rel="stylesheet" href="modern-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    
    
    
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
    
    .modern-table-container h2 {
        font-size: 2.4rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 32px;
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
    
    .stats-info {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 16px 24px;
        margin-bottom: 32px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        display: inline-block;
    }
    
    .stats-info span {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .table-responsive {
        overflow-x: auto;
        width: 100%;
        min-height: 60px;
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
    
    .custom-table-th {
        padding: 20px 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
        text-align: left;
        border: none;
        letter-spacing: 0.5px;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    
    .modern-table th:first-child {
        border-top-left-radius: 16px;
    }
    
    .modern-table th:last-child {
        border-top-right-radius: 16px;
    }
    
    .custom-table-td {
        padding: 18px 24px;
        background: rgba(255, 255, 255, 0.8);
        font-size: 1rem;
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        color: #2d3748;
        font-weight: 500;
        vertical-align: middle;
        transition: all 0.3s ease;
    }
    
    .modern-table tr:last-child td:first-child {
        border-bottom-left-radius: 16px;
    }
    
    .modern-table tr:last-child td:last-child {
        border-bottom-right-radius: 16px;
    }
    
    tr:hover .custom-table-td {
        background: rgba(102, 126, 234, 0.05);
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }
    
    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        font-size: 1rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        letter-spacing: 1px;
        transition: transform 0.3s ease;
    }
    
    .avatar:hover {
        transform: scale(1.1) rotate(5deg);
    }
    
    .btn {
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
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn:hover::before {
        left: 100%;
    }
    
    .btn-success { 
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        box-shadow: 0 4px 15px rgba(67, 233, 123, 0.3);
    }
    
    .btn-success:hover { 
        background: linear-gradient(135deg, #38d16a 0%, #2dd4bf 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 233, 123, 0.4);
    }
    
    .btn-primary { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:hover { 
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    }
    
    .btn-danger:hover {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #718096;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        display: block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        opacity: 0.5;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #4a5568;
    }
    
    .empty-state p {
        font-size: 1rem;
        color: #718096;
        font-style: italic;
    }
    
    @media (max-width: 1200px) {
        .modern-table-container { 
            padding: 32px 20px; 
            max-width: 98vw; 
        }
    }
    
    @media (max-width: 900px) {
        .modern-table-container { 
            padding: 24px 16px; 
        }
        .content { 
            margin-top: 20px; 
            padding: 0 10px;
        }
        .modern-table th, .modern-table td, .custom-table-th, .custom-table-td { 
            font-size: 0.9rem; 
            padding: 12px 16px; 
        }
        .modern-table-container h2 { 
            font-size: 1.8rem; 
        }
        .avatar {
            width: 32px;
            height: 32px;
            font-size: 0.85rem;
        }
    }
    
    @media (max-width: 600px) {
        .modern-table th, .modern-table td, .custom-table-th, .custom-table-td { 
            font-size: 0.8rem; 
            padding: 8px 12px; 
        }
        .btn {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        .avatar {
            width: 28px;
            height: 28px;
            font-size: 0.8rem;
            margin-right: 8px;
        }
    }
    
    .modern-table-container + .modern-table-container {
        margin-top: 48px;
    }
    .compact-table th, .compact-table td {
  padding: 8px 10px;
  font-size: 0.95em;
  vertical-align: middle;
  white-space: nowrap;
  /* Remove max-width, overflow, and text-overflow for full text display */
}
.compact-table th.actions-col, .compact-table td.actions-col {
  width: 90px;
  min-width: 70px;
  max-width: 90px;
  text-align: center;
}
.compact-table .avatar, .compact-table .passport-thumb {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 1px 4px rgba(44,90,160,0.10);
  margin-right: 6px;
}
.compact-table .icon-btn {
  background: none;
  border: none;
  padding: 4px 6px;
  margin: 0 2px;
  color: #667eea;
  font-size: 1.1em;
  cursor: pointer;
  border-radius: 50%;
  transition: background 0.15s;
  position: relative;
}
.compact-table .icon-btn:hover {
  background: #f0f4f8;
}
.compact-table .icon-btn.approve { color: #10b981; }
.compact-table .icon-btn.reject { color: #e53e3e; }
.compact-table .icon-btn.details { color: #2c5aa0; }
.compact-table .icon-btn[title]:hover:after {
  content: attr(title);
  position: absolute;
  left: 50%;
  top: -28px;
  transform: translateX(-50%);
  background: #222;
  color: #fff;
  font-size: 0.85em;
  padding: 2px 8px;
  border-radius: 4px;
  white-space: nowrap;
  z-index: 10;
  opacity: 0.95;
}
.table-scroll-x {
  overflow-x: auto;
  width: 100%;
}
.compact-table {
  min-width: 1100px;
}
.action-icon-btn {
  width: 38px;
  height: 38px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  border: none;
  font-size: 1.25rem;
  margin: 0 4px;
  cursor: pointer;
  transition: background 0.18s, box-shadow 0.18s, transform 0.18s;
  box-shadow: 0 2px 8px rgba(44,90,160,0.08);
  position: relative;
}
.action-icon-btn.approve {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
  color: #fff;
}
.action-icon-btn.approve:hover {
  background: linear-gradient(135deg, #38d16a 0%, #2dd4bf 100%);
  transform: scale(1.08);
}
.action-icon-btn.reject {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  color: #fff;
}
.action-icon-btn.reject:hover {
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  transform: scale(1.08);
}
.action-icon-btn[title]:hover:after {
  content: attr(title);
  position: absolute;
  left: 50%;
  top: -32px;
  transform: translateX(-50%);
  background: #222;
  color: #fff;
  font-size: 0.85em;
  padding: 2px 10px;
  border-radius: 4px;
  white-space: nowrap;
  z-index: 10;
  opacity: 0.95;
  pointer-events: none;
}
</style>
</head>
<body>
  
<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="modern-table-container">
        <h2><span class="table-icon"><i class="fas fa-user-plus"></i></span> Admission Applications</h2>
        <div class="stats-info">
            <span>Total Applications: <?php echo ($admission_result ? $admission_result->num_rows : '0'); ?></span>
        </div>
        <div class="table-responsive">
            <table class="modern-table compact-table">
                <thead>
                    <tr>
                        <th class="custom-table-th">Applicant</th>
                        <th class="custom-table-th">DOB</th>
                        <th class="custom-table-th">Gender</th>
                        <th class="custom-table-th">Class</th>
                        <th class="custom-table-th">Parent Name</th>
                        <th class="custom-table-th">Parent Phone</th>
                        <th class="custom-table-th">Email</th>
                        <th class="custom-table-th">Phone</th>
                        <th class="custom-table-th">Photo</th>
                        <th class="custom-table-th actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($admission_result && $admission_result->num_rows > 0): ?>
                        <?php while($info = $admission_result->fetch_assoc()): ?>
                            <tr>
                                <td class="custom-table-td">
                                    <div style="display: flex; align-items: center;">
                                        <img class="avatar" src="<?php echo !empty($info['passport_photo']) ? htmlspecialchars($info['passport_photo']) : 'nyabzgallery/nyabzlogo.png'; ?>" alt="Avatar">
                                        <span style="font-weight: 600; color: #2d3748;">
                                            <?php echo htmlspecialchars($info['name'] ?? '-'); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="custom-table-td"> <?php echo htmlspecialchars($info['dob'] ?? '-'); ?> </td>
                                <td class="custom-table-td"> <?php echo htmlspecialchars($info['gender'] ?? '-'); ?> </td>
                                <td class="custom-table-td"> <?php echo htmlspecialchars($info['class_applying'] ?? '-'); ?> </td>
                                <td class="custom-table-td"> <?php echo htmlspecialchars($info['parent_name'] ?? '-'); ?> </td>
                                <td class="custom-table-td"> <?php echo htmlspecialchars($info['parent_phone'] ?? '-'); ?> </td>
                                <td class="custom-table-td">
                                    <?php echo htmlspecialchars($info['email'] ?? '-'); ?>
                                </td>
                                <td class="custom-table-td" style="max-width:100px;">
                                    <span style="display:inline-block; max-width:90px; overflow:hidden; text-overflow:ellipsis; vertical-align:middle;">
                                        <?php echo htmlspecialchars($info['phone'] ?? '-'); ?>
                                    </span>
                                </td>
                                <td class="custom-table-td">
                                    <?php if (!empty($info['passport_photo'])): ?>
                                        <img class="passport-thumb" src="<?php echo htmlspecialchars($info['passport_photo']); ?>" alt="Passport Photo">
                                    <?php else: ?>
                                        <span style="color:#aaa;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="custom-table-td actions-col">
                                    <form method="POST" action="process_admission.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="action-icon-btn approve" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="process_admission.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="action-icon-btn reject" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="icon-btn details btn-details" title="Details"
                                        data-name="<?php echo htmlspecialchars($info['name'] ?? ''); ?>"
                                        data-address="<?php echo htmlspecialchars($info['address'] ?? ''); ?>"
                                        data-nationality="<?php echo htmlspecialchars($info['nationality'] ?? ''); ?>"
                                        data-religion="<?php echo htmlspecialchars($info['religion'] ?? ''); ?>"
                                        data-prevschool="<?php echo htmlspecialchars($info['previous_school'] ?? ''); ?>"
                                        data-message="<?php echo htmlspecialchars($info['message'] ?? ''); ?>"
                                        >
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="custom-table-td">
                                <div class="empty-state">
                                    <i class="fas fa-user-plus"></i>
                                    <h3>No Applications Yet</h3>
                                    <p>No admission applications have been submitted yet.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination Controls -->
        <div style="margin-top: 32px; display: flex; justify-content: center; align-items: center; gap: 8px;">
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Admission pagination">
                    <ul style="list-style: none; display: flex; gap: 6px; padding: 0; margin: 0;">
                        <?php if ($page > 1): ?>
                            <li><a href="?page=<?php echo $page-1; ?>" class="btn btn-primary" style="padding: 6px 14px; font-size: 1rem;">&laquo; Prev</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li>
                                <a href="?page=<?php echo $i; ?>" class="btn <?php echo $i == $page ? 'btn-success' : 'btn-primary'; ?>" style="padding: 6px 14px; font-size: 1rem; <?php if($i == $page) echo 'pointer-events:none; opacity:0.85;'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <li><a href="?page=<?php echo $page+1; ?>" class="btn btn-primary" style="padding: 6px 14px; font-size: 1rem;">Next &raquo;</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailsModalLabel">Applicant Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group">
          <li class="list-group-item"><strong>Address:</strong> <span id="detailsAddress"></span></li>
          <li class="list-group-item"><strong>Nationality:</strong> <span id="detailsNationality"></span></li>
          <li class="list-group-item"><strong>Religion:</strong> <span id="detailsReligion"></span></li>
          <li class="list-group-item"><strong>Previous School:</strong> <span id="detailsPrevSchool"></span></li>
          <li class="list-group-item"><strong>Message:</strong> <span id="detailsMessage"></span></li>
        </ul>
      </div>
    </div>
  </div>
</div>
<script>
document.querySelectorAll('.btn-details').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.getElementById('detailsAddress').textContent = btn.getAttribute('data-address');
    document.getElementById('detailsNationality').textContent = btn.getAttribute('data-nationality');
    document.getElementById('detailsReligion').textContent = btn.getAttribute('data-religion');
    document.getElementById('detailsPrevSchool').textContent = btn.getAttribute('data-prevschool');
    document.getElementById('detailsMessage').textContent = btn.getAttribute('data-message');
    var modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    modal.show();
  });
});
</script>
<script>
// Add some nice animations on page load
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.6s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
</body>
</html>