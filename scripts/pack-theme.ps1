# Pack site/ into the Yuzu Round theme and write wordpress/yuzu-round.zip
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
$dest = Join-Path $root "wordpress\yuzu-round\static"
$theme = Join-Path $root "wordpress\yuzu-round"
$zip = Join-Path $root "wordpress\yuzu-round.zip"

$python = if (Get-Command python3 -ErrorAction SilentlyContinue) { "python3" } else { "python" }
& $python (Join-Path $root "scripts\build.py")
if ($LASTEXITCODE -ne 0) {
    throw "scripts/build.py failed"
}

if (Test-Path $dest) {
    Remove-Item -Recurse -Force $dest
}
New-Item -ItemType Directory -Path $dest | Out-Null
Copy-Item -Path (Join-Path $root "site\*") -Destination $dest -Recurse -Force
$copiedHtaccess = Join-Path $dest ".htaccess"
if (Test-Path $copiedHtaccess) {
    Remove-Item -Force $copiedHtaccess
}
Copy-Item -Path (Join-Path $theme "redirect.htaccess") -Destination $copiedHtaccess -Force
$partials = Join-Path $dest "partials"
if (Test-Path $partials) {
    Remove-Item -Recurse -Force $partials
}

if (Test-Path $zip) {
    Remove-Item -Force $zip
}
Compress-Archive -Path $theme -DestinationPath $zip
Write-Host "Wrote $zip"
