# fix_html_migration.ps1
# Repairs HTML pages after PHP->HTML conversion:
# - Injects navbar/sidebar/footer/delete-modal placeholders from PHP source
# - Rewrites backend API paths to ../backend/
# - Fixes internal .php links to .html

$frontendDir = "c:\xampp\htdocs\school-project\frontend"
$backendScripts = @(
    'add_admin_ajax','add_announcement_ajax','add_student_ajax','add_teacher_ajax',
    'approve_student','bulk_assign_class','check_users','contactus_process_ajax',
    'data_check','delete_admin_ajax','delete_announcement','delete_announcement_ajax',
    'delete_attendance','delete_class_ajax','delete_gallery_image','delete_message_ajax',
    'delete_registration','delete_student','delete_teacher','edit_admin_ajax',
    'edit_announcement_ajax','edit_class_ajax','edit_exam_ajax','edit_student_ajax',
    'edit_teacher_ajax','enter_marks','export_students','export_teachers',
    'fee_collection','fee_status','generate_report','get_announcement',
    'get_dashboard_data','get_gallery_images','get_registration_details',
    'get_registrations_ajax','get_statistics','get_subjects','get_teacher',
    'process_admission','register_event','reply_contact','send_newsletter',
    'subscribe_newsletter','track_gallery_view','update_attendance',
    'update_gallery_metadata','upload_gallery_image','login_check','do_logout',
    'sync_staff_photos','import_database','drop_students_table'
)

$frontendPages = @(
    "index","about","Academics","olevel","alevel","admission","anthem",
    "staff","nonstaff","clubs","events","event_details","gallery",
    "viewgallery","dynamic_gallery","dynamic_gallery_admin","contactus",
    "dashboard","login","logout","view_student","view_teacher",
    "announcements","attendance","manage_classes","manage_admins",
    "manage_subjects","manage_gallery","exam_schedule","settings",
    "user","studenthome","view_event_registrations","view_payment_details",
    "report_cards","trash","trash_messages","test_navbar"
)

$adminCssLinks = @(
    '<link rel="stylesheet" href="admin.css">',
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">'
) -join "`n    "

function Test-PhpInclude($phpContent, $pattern) {
    return $phpContent -match $pattern
}

Get-ChildItem -Path $frontendDir -Filter "*.html" | ForEach-Object {
    $htmlFile = $_.FullName
    $name = $_.BaseName
    if ($name -in @('navbar','admin_sidebar','admin_css','delete_modal','modern-footer')) { return }

    $phpFile = Join-Path $frontendDir "$name.php"
    $phpContent = ''
    if (Test-Path $phpFile) {
        $phpContent = [System.IO.File]::ReadAllText($phpFile)
    }

    $content = [System.IO.File]::ReadAllText($htmlFile)
    $changed = $false

    # --- Placeholders from PHP includes ---
    if ($phpContent) {
        if ((Test-PhpInclude $phpContent "include\s+['\`"]navbar\.php['\`"]") -and $content -notmatch 'navbar-placeholder') {
            if ($content -match '(<body[^>]*>)') {
                $content = $content -replace '(<body[^>]*>)', "`$1`n<div id=`"navbar-placeholder`"></div>"
                $changed = $true
                Write-Host "[$name] + navbar placeholder"
            }
        }
        if ((Test-PhpInclude $phpContent "include\s+['\`"]admin_sidebar\.php['\`"]") -and $content -notmatch 'sidebar-placeholder') {
            if ($content -match '(<body[^>]*>)') {
                $bodyTag = [regex]::Match($content, '<body[^>]*>').Value
                $inject = "$bodyTag`n<div id=`"sidebar-placeholder`"></div>"
                $content = $content.Replace($bodyTag, $inject)
                $changed = $true
                Write-Host "[$name] + sidebar placeholder"
            }
        }
        if ((Test-PhpInclude $phpContent "include\s+['\`"]admin_css\.php['\`"]") -and $content -match 'admin_css\.php') {
            $content = $content -replace '<link[^>]*admin_css\.php[^>]*>', $adminCssLinks
            $changed = $true
            Write-Host "[$name] fixed admin_css link"
        }
        if ((Test-PhpInclude $phpContent "include\s+['\`"]admin_css\.php['\`"]") -and $content -notmatch 'admin\.css') {
            if ($content -match '</head>') {
                $content = $content -replace '</head>', "    $adminCssLinks`n</head>"
                $changed = $true
                Write-Host "[$name] + admin.css in head"
            }
        }
        if ((Test-PhpInclude $phpContent "include\s+['\`"]delete_modal\.php['\`"]") -and $content -notmatch 'delete-modal-placeholder') {
            if ($content -match '</body>') {
                $content = $content -replace '</body>', '<div id="delete-modal-placeholder"></div>' + "`n</body>"
                $changed = $true
                Write-Host "[$name] + delete modal placeholder"
            }
        }
        if ((Test-PhpInclude $phpContent "include\s+['\`"]modern-footer\.html['\`"]") -and $content -notmatch 'footer-placeholder') {
            if ($content -match '</body>') {
                $content = $content -replace '</body>', '<div id="footer-placeholder"></div>' + "`n</body>"
                $changed = $true
                Write-Host "[$name] + footer placeholder"
            }
        }
    }

    # studenthome special case
    if ($name -eq 'studenthome' -and $content -match 'admin_css\.php') {
        $content = $content -replace '<link[^>]*admin_css\.php[^>]*>', $adminCssLinks
        $changed = $true
    }

    # --- Backend path rewrites (skip if already ../backend/) ---
    foreach ($script in $backendScripts) {
        $replacements = @(
            @("fetch('$script.php", "fetch('../backend/$script.php"),
            @('fetch("' + $script + '.php', 'fetch("../backend/' + $script + '.php'),
            @("action=`"$script.php`"", "action=`"../backend/$script.php`""),
            @("action='$script.php'", "action='../backend/$script.php'"),
            @("href=`"$script.php`"", "href=`"../backend/$script.php`""),
            @("href='$script.php'", "href='../backend/$script.php'"),
            @("'$script.php'", "'../backend/$script.php'"),
            @("`"$script.php`"", "`"../backend/$script.php`"")
        )
        foreach ($pair in $replacements) {
            if ($content.Contains($pair[0]) -and -not $content.Contains("../backend/$script.php")) {
                $content = $content.Replace($pair[0], $pair[1])
                $changed = $true
            }
        }
        # Fix partial replacements that missed query strings
        $content = $content.Replace("../backend/../backend/$script.php", "../backend/$script.php")
    }

    # --- Frontend page links .php -> .html ---
    foreach ($page in $frontendPages) {
        $content = $content -replace "href=`"$page\.php`"", "href=`"$page.html`""
        $content = $content -replace "href='$page\.php'", "href='$page.html'"
        $content = $content -replace "location\.href\s*=\s*['""]$page\.php['""]", "location.href = '$page.html'"
    }

    # modern-footer partial
    if ($name -eq 'modern-footer') {
        foreach ($page in $frontendPages) {
            $content = $content -replace "href=`"$page\.php`"", "href=`"$page.html`""
        }
        $changed = $true
    }

    # admin_sidebar backend links
    if ($name -eq 'admin_sidebar') {
        foreach ($script in @('enter_marks','fee_collection','fee_status','send_newsletter')) {
            $content = $content -replace "href=`"$script\.php`"", "href=`"../backend/$script.php`""
        }
        if ($content -notmatch 'send_sms') { }
        $content = $content -replace 'href="send_sms\.php"', 'href="../backend/send_sms.php"'
        $changed = $true
    }

    # showDeleteModal still pointing at frontend php pages
    $content = $content -replace "showDeleteModal\('',\s*'$name\.php\?delete='", "showDeleteModal('', '../backend/delete_${name}.php?delete='"
    $content = $content -replace 'manage_subjects\.php\?delete=', '../backend/manage_subjects.php?delete='
    $content = $content -replace 'manage_classes\.php\?delete=', '../backend/delete_class_ajax.php?delete='
    $content = $content -replace 'exam_schedule\.php\?delete=', '../backend/edit_exam_ajax.php?delete='

    # view_student class link
    $content = $content -replace 'view_student\.php\?class_id=', 'view_student.html?class_id='

    # setup_database links (dev only)
    $content = $content -replace 'setup_database\.php', '../backend/import_database.php'

    # Ensure includes.js
    if ($content -notmatch 'includes\.js' -and $content -match '</body>') {
        $content = $content -replace '</body>', '<script src="js/includes.js"></script>' + "`n</body>"
        $changed = $true
    }

    if ($changed) {
        [System.IO.File]::WriteAllText($htmlFile, $content)
    }
}

Write-Host "Migration fix complete."
