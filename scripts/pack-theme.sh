#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
dest="$root/wordpress/yuzu-round/static"
python3 "$root/scripts/build.py"
rm -rf "$dest"
mkdir -p "$dest"
cp -a "$root/site/." "$dest/"
rm -f "$dest/.htaccess"
rm -rf "$dest/partials"
cp "$root/wordpress/yuzu-round/redirect.htaccess" "$dest/.htaccess"
echo "Copied site/ into wordpress/yuzu-round/static"
(
  cd "$root/wordpress"
  rm -f yuzu-round.zip
  zip -r yuzu-round.zip yuzu-round -x '*.DS_Store'
)
echo "Wrote wordpress/yuzu-round.zip"
