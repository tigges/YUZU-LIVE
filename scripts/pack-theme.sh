#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
dest="$root/wordpress/yuzu-round/static"
rm -rf "$dest"
mkdir -p "$dest"
cp -a "$root/site/." "$dest/"
rm -f "$dest/.htaccess"
echo "Copied site/ into wordpress/yuzu-round/static"
(
  cd "$root/wordpress"
  rm -f yuzu-round.zip
  zip -r yuzu-round.zip yuzu-round -x '*.DS_Store'
)
echo "Wrote wordpress/yuzu-round.zip"
