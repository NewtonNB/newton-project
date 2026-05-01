<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// manage_subjects.php
session_start();
require 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['admin'])) {
    header("location:login.php");
    exit();
}

$success = $error = "";
$active_tab = $_GET['tab'] ?? 'olevel';

// Handle adding subjects
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_name = trim($_POST['subject_name']);
    $level = $_POST['level'];

    if ($subject_name && in_array($level, ['olevel', 'alevel'])) {
        $table = $level . "_subjects"; // dynamic table name

        $stmt = $conn->prepare("INSERT INTO $table (subject_name) VALUES (?)");
        $stmt->bind_param("s", $subject_name);

        if ($stmt->execute()) {
            $success = ucfirst($level) . " subject added successfully.";
            $active_tab = $level;
        } else {
            $error = "Error: Database error occurred";
        }
    } else {
        $error = "All fields are required.";
    }
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['level'])) {
    $id = intval($_GET['delete']);
    $level = $_GET['level'];
    $table = $level . "_subjects";

    if (in_array($level, ['olevel', 'alevel'])) {
        $conn->query("DELETE FROM $table WHERE id = $id");
        header("Location: manage_subjects.php?tab=$level");
        exit();
    }
}

// Fetch both subject lists with error handling
$olevel = $conn->query("SELECT * FROM olevel_subjects ORDER BY id DESC");
if ($olevel === false) {
    $olevel = null;
    $olevel_error = "Database error occurred";
}

$alevel = $conn->query("SELECT * FROM alevel_subjects ORDER BY id DESC");
if ($alevel === false) {
    $alevel = null;
    $alevel_error = "Database error occurred";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - Admin Dashboard</title>
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
        .nav-tabs {
            border: none;
            margin-bottom: 30px;
        }
        .nav-tabs .nav-link {
            border: none;
            border-radius: 10px;
            margin-right: 10px;
            padding: 12px 25px;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .nav-tabs .nav-link:hover {
            background: #e9ecef;
            color: #495057;
        }
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
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
            width: 100%;
        }
        .table thead th {
            background: #f8f9fc;
            color: #5a5c69;
            border: none;
            border-bottom: 2px solid #e3e6f0;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table tbody td {
            padding: 15px 20px;
            border-bottom: 1px solid #e3e6f0;
            vertical-align: middle;
            color: #495057;
            font-size: 0.95rem;
        }
        .table tbody tr:hover {
            background-color: #f8f9fc;
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
        .badge-clean {
            background: #f8f9fc;
            color: #858796;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #e3e6f0;
        }
        .btn-icon-danger {
            background: #fff;
            color: #e74a3b;
            border: 1px solid #f8d7da;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-icon-danger:hover {
            background: #fee2e2;
            color: #c53030;
            border-color: #fecaca;
        }
        .tab-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        /* Custom Delete Modal */
        .delete-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeInOverlay 0.2s ease;
        }
        .delete-modal-overlay.active {
            display: flex;
        }
        @keyframes fadeInOverlay {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .delete-modal {
            background: #fff;
            border-radius: 20px;
            padding: 40px 36px 32px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            animation: popIn 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes popIn {
            from { transform: scale(0.75); opacity: 0; }
            to   { transform: scale(1);    opacity: 1; }
        }
        .delete-modal .icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(238,90,82,0.35);
        }
        .delete-modal .icon-wrap i {
            font-size: 30px;
            color: #fff;
        }
        .delete-modal h4 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 10px;
        }
        .delete-modal p {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 6px;
        }
        .delete-modal .subject-name-badge {
            display: inline-block;
            background: #fff3f3;
            color: #ee5a52;
            border: 1.5px solid #ffcdd2;
            border-radius: 8px;
            padding: 6px 16px;
            font-weight: 700;
            font-size: 1rem;
            margin: 8px 0 18px;
            letter-spacing: 0.5px;
        }
        .delete-modal .warning-note {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.82rem;
            color: #856404;
            margin-bottom: 24px;
            text-align: left;
        }
        .delete-modal .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .delete-modal .btn-cancel {
            flex: 1;
            padding: 12px 20px;
            border-radius: 12px;
            border: 2px solid #e9ecef;
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .delete-modal .btn-cancel:hover {
            background: #e9ecef;
            border-color: #ced4da;
        }
        .delete-modal .btn-confirm-delete {
            flex: 1;
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(238,90,82,0.35);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .delete-modal .btn-confirm-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238,90,82,0.5);
            color: #fff;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-book"></i> Manage School Subjects
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

            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $active_tab === 'olevel' ? 'active' : ''; ?>" href="?tab=olevel"><i class="fas fa-graduation-cap"></i> O-Level</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active_tab === 'alevel' ? 'active' : ''; ?>" href="?tab=alevel"><i class="fas fa-university"></i> A-Level</a>
                </li>
            </ul>

            <form method="POST" class="row mb-4">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="subject_name"><i class="fas fa-plus-circle"></i> Add Subject</label>
                        <input type="text" id="subject_name" name="subject_name" class="form-control" placeholder="Enter subject name" required>
                        <input type="hidden" name="level" value="<?php echo htmlspecialchars($active_tab); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i> Add Subject
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive mt-4">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th>Subject Name</th>
                            <th width="15%" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $list = $active_tab === 'alevel' ? $alevel : $olevel;
                    if ($list && $list->num_rows > 0): $i = 1;
                        while ($row = $list->fetch_assoc()): ?>
                            <tr>
                                <td><span class="badge-clean"><?php echo $i++; ?></span></td>
                                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn-icon-danger" title="Delete Subject"
                                        onclick="showDeleteModal('<?php echo htmlspecialchars(addslashes($row['subject_name'])); ?>', 'manage_subjects.php?delete=<?php echo $row['id']; ?>&level=<?php echo $active_tab; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <i class="fas <?php echo $active_tab === 'alevel' ? 'fa-university' : 'fa-book'; ?> fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No subjects found. Add your first subject using the form above.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'delete_modal.php'; ?>

</body>
</html>
