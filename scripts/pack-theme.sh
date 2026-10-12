#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
dest="$root/wordpress/yuzu-round/static"
rm -rf "$dest"
mkdir -p "$dest"
cp -a "$root/site/." "$dest/"
rm -f "$dest/.htaccess"
echo "Copied site/ into wordpress/yuzu-round/static"
