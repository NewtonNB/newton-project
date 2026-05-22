# fix_includes.ps1
# Injects includes.js and creates the js/ directory in frontend

$frontendDir = "c:\xampp\htdocs\school-project\frontend"

# Create js directory if it doesn't exist
$jsDir = Join-Path $frontendDir "js"
if (-not (Test-Path $jsDir)) {
    New-Item -ItemType Directory -Path $jsDir | Out-Null
    Write-Host "Created js/ directory"
}

$htmlFiles = Get-ChildItem -Path $frontendDir -Filter "*.html"
foreach ($file in $htmlFiles) {
    $content = [System.IO.File]::ReadAllText($file.FullName)
    if ($content -notmatch 'includes\.js') {
        $injection = '<script src="js/includes.js"></script>' + "`n" + '</body>'
        $content = $content.Replace('</body>', $injection)
        [System.IO.File]::WriteAllText($file.FullName, $content)
        Write-Host "Fixed: $($file.Name)"
    }
}
Write-Host "Done."
