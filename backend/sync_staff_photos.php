<?php
// sync_staff_photos.php
// Run this script ONCE to sync staff photos from staff.html to the teachers table

require '../shared/config.php';

$staffHtml = file_get_contents('staff.html');
if (!$staffHtml) {
    die('Could not read staff.html');
}

// Match all staff items
preg_match_all('/<div class="staff-item[^"]*">\s*<img src="([^"]+)"[^>]*>\s*<h3>([^<]+)<\/h3>\s*<p>([^<]*)<\/p>/i', $staffHtml, $matches, PREG_SET_ORDER);

if (!$matches) {
    die('No staff found in staff.html');
}

$updated = 0;
foreach ($matches as $m) {
    $img = trim($m[1]); // e.g. nyabzgallery/NAME.jpg
    $name = trim($m[2]); // e.g. MR. MANDU PEACE ISAAC
    // Remove MR., MS., MRS., DR., etc. and extra spaces
    $name_clean = preg_replace('/^(MR\.|MS\.|MRS\.|DR\.|MISS)\s+/i', '', $name);
    // Try to match teacher by full_name (case-insensitive)
    $stmt = $conn->prepare('UPDATE teachers SET Photo=? WHERE LOWER(full_name)=LOWER(?)');
    $photoFile = basename($img); // just the filename
    $stmt->bind_param('ss', $photoFile, $name_clean);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Updated $name_clean with photo $photoFile<br>\n";
        $updated++;
    }
    $stmt->close();
}

if ($updated === 0) {
    echo "No teachers were updated. Check that names in staff.html match those in the database.";
} else {
    echo "<br>Done! Updated $updated teacher photos.";
}
$conn->close(); 