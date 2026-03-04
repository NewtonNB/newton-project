<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// manage_subjects.php
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
        .badge-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .tab-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
                            <th width="5%">#</th>
                            <th width="85%">Subject Name</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $list = $active_tab === 'alevel' ? $alevel : $olevel;
                    if ($list && $list->num_rows > 0): $i = 1;
                        while ($row = $list->fetch_assoc()): ?>
                            <tr>
                                <td><span class="badge <?php echo $active_tab === 'alevel' ? 'badge-info' : 'badge-primary'; ?>"><?php echo $i++; ?></span></td>
                                <td><strong><?php echo htmlspecialchars($row['subject_name']); ?></strong></td>
                                <td>
                                    <a href="manage_subjects.php?delete=<?php echo $row['id']; ?>&level=<?php echo $active_tab; ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Are you sure you want to delete subject <?php echo htmlspecialchars($row['subject_name']); ?>? This action cannot be undone.')">
                                       <i class="fas fa-trash"></i> Delete
                                    </a>
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

</body>
</html>
