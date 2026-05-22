<?php
header('Content-Type: application/json');
require_once '../shared/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event = null;

$stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $row = $res->fetch_assoc()) {
        $gallery = [];
        if (!empty($row['gallery'])) {
            $g = json_decode($row['gallery'], true);
            if (is_array($g)) $gallery = $g;
        }
        $speakers = [];
        if (!empty($row['speakers'])) {
            $s = json_decode($row['speakers'], true);
            if (is_array($s)) {
                foreach ($s as $name) {
                    $speakers[] = ['name' => is_string($name) ? $name : ($name['name'] ?? ''), 'title' => ''];
                }
            }
        }
        $event = [
            'title' => $row['title'],
            'date' => $row['date'] ? date('d M Y', strtotime($row['date'])) : '',
            'time' => $row['time'] ?? '',
            'image' => $gallery[0] ?? 'nyabzgallery/current.jpg',
            'gallery' => $gallery ?: ['nyabzgallery/current.jpg'],
            'location' => $row['location'] ?? '',
            'download' => '',
            'speakers' => $speakers,
            'description' => $row['content'] ?? '',
        ];
    }
}

if (!$event) {
    require __DIR__ . '/event_details_demo.inc.php';
    $event = $events[$id] ?? null;
}

echo json_encode(['found' => (bool)$event, 'event' => $event]);
