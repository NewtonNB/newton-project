# bulk_convert.ps1
# Converts all frontend .php files to .html
# Removes PHP logic blocks and rewrites internal links

$frontendDir = "c:\xampp\htdocs\school-project\frontend"

# Frontend pages that should be linked as .html (not backend handlers)
$frontendPages = @(
    "index","about","Academics","olevel","alevel","admission","anthem",
    "staff","nonstaff","clubs","events","event_details","gallery",
    "viewgallery","dynamic_gallery","dynamic_gallery_admin","contactus",
    "dashboard","login","logout","view_student","view_teacher",
    "announcements","attendance","manage_classes","manage_admins",
    "manage_subjects","manage_gallery","exam_schedule","settings",
    "user","studenthome","view_event_registrations","view_payment_details",
    "report_cards","trash","trash_messages","test_navbar","admin_sidebar",
    "delete_modal"
)

Get-ChildItem -Path $frontendDir -Filter "*.php" | ForEach-Object {
    $phpFile = $_.FullName
    $htmlFile = $phpFile -replace "\.php$", ".html"
    $basename = $_.BaseName

    Write-Host "Converting: $($_.Name) -> $($_.BaseName).html"

    $content = Get-Content -Path $phpFile -Raw -Encoding UTF8

    # 1. Remove PHP-only files (logout - pure redirect, login_check is backend)
    if ($basename -eq "logout") {
        # Replace with a meta-redirect page
        $content = @"
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logging out...</title>
  <meta http-equiv="refresh" content="0;url=../backend/do_logout.php">
</head>
<body>
  <script>
    // Clear any local session indicators and redirect to backend logout
    localStorage.removeItem('nyabz_loggedIn');
    localStorage.removeItem('nyabz_user');
    window.location.href = '../backend/do_logout.php';
  </script>
</body>
</html>
"@
        Set-Content -Path $htmlFile -Value $content -Encoding UTF8
        return
    }

    # 2. Strip top-level PHP blocks (<?php ... ?> that appear before <!DOCTYPE)
    # Remove entire PHP opening blocks
    $content = [regex]::Replace($content, '(?s)<\?php.*?\?>\s*', '', [System.Text.RegularExpressions.RegexOptions]::Singleline)

    # 3. Replace PHP includes with HTML placeholders
    $content = $content -replace '<\?php\s+include\s+[''"]navbar\.php[''"]\s*;\s*\?>', '<div id="navbar-placeholder"></div>'
    $content = $content -replace '<\?php\s+include\s+[''"]admin_sidebar\.php[''"]\s*;\s*\?>', '<div id="sidebar-placeholder"></div>'
    $content = $content -replace '<\?php\s+include\s+[''"]admin_css\.php[''"]\s*;\s*\?>', '<link rel="stylesheet" href="admin.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">'
    $content = $content -replace '<\?php\s+include\s+[''"]delete_modal\.php[''"]\s*;\s*\?>', '<div id="delete-modal-placeholder"></div>'

    # 4. Remove any remaining PHP blocks (echo, if/endif, while loops, etc.)
    $content = [regex]::Replace($content, '(?s)<\?php.*?\?>', '<!-- php-removed -->', [System.Text.RegularExpressions.RegexOptions]::Singleline)

    # 5. Rewrite internal .php hrefs to .html for frontend pages only
    foreach ($page in $frontendPages) {
        # href links
        $content = $content -replace "href=`"$page\.php`"", "href=`"$page.html`""
        $content = $content -replace "href='$page\.php'", "href='$page.html'"
        # JS location redirects (window.location = "page.php")
        $content = $content -replace "location\.href\s*=\s*['""]$page\.php['""]", "location.href = '$page.html'"
        $content = $content -replace "location\s*=\s*['""]$page\.php['""]", "location = '$page.html'"
    }

    # 6. Fix action attributes - backend handlers stay .php, frontend forms go to backend/
    # login_check stays .php
    $content = $content -replace 'action="login_check\.php"', 'action="../backend/login_check.php"'
    $content = $content -replace 'action="contactus_process_ajax\.php"', 'action="../backend/contactus_process_ajax.php"'
    $content = $content -replace 'action="register_event\.php"', 'action="../backend/register_event.php"'
    $content = $content -replace 'action="reply_contact\.php"', 'action="../backend/reply_contact.php"'
    $content = $content -replace 'action="process_admission\.php"', 'action="../backend/process_admission.php"'

    # 7. Fix relative asset paths that reference backend
    $content = $content -replace '"login_check\.php"', '"../backend/login_check.php"'

    # 8. Fix header includes for CSS/JS that may use relative paths
    # navbar.css, modern-footer.css, etc. stay the same (same directory)

    # 9. Add JS includes.js before closing </body> if not already present
    if ($content -notmatch 'includes\.js') {
        $content = $content -replace '</body>', '<script src="js/includes.js"></script>' + "`n</body>"
    }

    Set-Content -Path $htmlFile -Value $content -Encoding UTF8
    Write-Host "  -> Done: $($_.BaseName).html"
}

Write-Host ""
Write-Host "=== Conversion complete! ==="
