<?php
require_once 'admin_require.php';
admin_require_json();
require_once '../shared/config.php';

$conn->query("ALTER TABLE exam_schedule ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");

$class_filter = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$term_filter = isset($_GET['term']) ? (int)$_GET['term'] : 0;
$year_filter = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

$where = ['e.deleted_at IS NULL'];
if ($class_filter) $where[] = "e.class_id = $class_filter";
if ($term_filter) $where[] = "e.term = $term_filter";
if ($year_filter) $where[] = "e.year = $year_filter";
$where_sql = 'WHERE ' . implode(' AND ', $where);

$classes = [];
$cr = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name ASC");
if ($cr) while ($row = $cr->fetch_assoc()) {
    $classes[] = ['id' => (int)$row['id'], 'class_name' => $row['class_name']];
}

$subjects = [];
foreach (['olevel_subjects', 'alevel_subjects'] as $tbl) {
    $sr = $conn->query("SELECT subject_name FROM $tbl ORDER BY subject_name ASC");
    if ($sr) while ($row = $sr->fetch_assoc()) {
        if (!in_array($row['subject_name'], $subjects, true)) $subjects[] = $row['subject_name'];
    }
}
sort($subjects);

$exams = [];
$er = $conn->query("SELECT e.*, c.class_name FROM exam_schedule e JOIN classes c ON e.class_id = c.id $where_sql ORDER BY e.exam_date ASC, e.start_time ASC");
if ($er) while ($row = $er->fetch_assoc()) {
    $exams[] = $row;
}

echo json_encode([
    'exams' => $exams,
    'classes' => $classes,
    'subjects' => $subjects,
    'filters' => ['class_id' => $class_filter, 'term' => $term_filter, 'year' => $year_filter],
]);
