# Pack site/ into the Yuzu Round theme and write wordpress/yuzu-round.zip
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
$dest = Join-Path $root "wordpress\yuzu-round\static"
$theme = Join-Path $root "wordpress\yuzu-round"
$zip = Join-Path $root "wordpress\yuzu-round.zip"

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

if (Test-Path $zip) {
    Remove-Item -Force $zip
}
Compress-Archive -Path $theme -DestinationPath $zip
Write-Host "Wrote $zip"
