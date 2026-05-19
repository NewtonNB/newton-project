<?php
// get_subjects.php - API endpoint to fetch subjects
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../shared/config.php';

$level = $_GET['level'] ?? 'olevel';

if (!in_array($level, ['olevel', 'alevel'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid level parameter. Use "olevel" or "alevel"']);
    exit();
}

$table = $level . "_subjects";
$subjects = $conn->query("SELECT * FROM $table ORDER BY subject_name ASC");

$result = [];
if ($subjects->num_rows > 0) {
    while ($row = $subjects->fetch_assoc()) {
        $result[] = [
            'id' => $row['id'],
            'subject_name' => $row['subject_name']
        ];
    }
}

echo json_encode([
    'success' => true,
    'level' => $level,
    'subjects' => $result,
    'count' => count($result)
]);
?> 