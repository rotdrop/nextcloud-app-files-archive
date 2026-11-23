#!/bin/bash
# SPDX-License-Identifier: AGPL-3.0-or-later

set -euo pipefail

[ $# -eq 1 ] || { echo "usage: $0 <app tarball>" >&2; exit 1; }
archive=$(realpath "$1")
tmp=$(mktemp -d)
trap 'rm -rf "$tmp"' EXIT
tar -xzf "$archive" -C "$tmp"
app="$tmp/files_archive"
[ -d "$app" ] || { echo "No files_archive directory in $archive" >&2; exit 1; }

php_version=$(php -r 'echo PHP_MAJOR_VERSION, ".", PHP_MINOR_VERSION;')
find "$app" -type f -name '*.php' -print0 > "$tmp/php-files"
[ -s "$tmp/php-files" ] || { echo "No PHP files in $archive" >&2; exit 1; }
while IFS= read -r -d '' file; do
  relative=${file#"$app"/}
  if [ "$php_version" = 8.2 ]; then
    case "$relative" in
      vendor-scoped/rotdrop/nextcloud-app-toolkit/src/Traits/TranslatableEnumTrait.php|\
      vendor-scoped/rotdrop/nextcloud-app-toolkit/src/Traits/BackedEnumTrait.php|\
      vendor-scoped/rotdrop/nextcloud-app-toolkit/src/Doctrine/ORM/EnumOrderByOptions.php)
        echo "Skipping unused toolkit file on PHP 8.2: $relative"
        continue
        ;;
    esac
  fi
  if ! output=$(php -l "$file" 2>&1); then
    printf 'PHP lint failed: %s\n%s\n' "$relative" "$output" >&2
    exit 1
  fi
done < "$tmp/php-files"
