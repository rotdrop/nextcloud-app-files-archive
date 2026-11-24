#!/bin/bash
# SPDX-License-Identifier: AGPL-3.0-or-later

set -euo pipefail

tmp=$(mktemp -d)
trap 'rm -rf "$tmp"' EXIT
app="$tmp/files_archive"
toolkit="$app/vendor-scoped/rotdrop/nextcloud-app-toolkit/src"
mkdir -p "$toolkit/Traits" "$toolkit/Doctrine/ORM" "$app/lib" "$tmp/bin"
for file in \
  "$toolkit/Traits/TranslatableEnumTrait.php" \
  "$toolkit/Traits/BackedEnumTrait.php" \
  "$toolkit/Doctrine/ORM/EnumOrderByOptions.php"; do
  echo 'valid' > "$file"
done
echo 'valid' > "$app/lib/App.php"
cat > "$tmp/bin/php" <<'EOF'
#!/bin/bash
if [ "$1" = -r ]; then
  printf '%s' "$FAKE_PHP_VERSION"
else
  printf '%s\n' "$2" >> "$LINTED_FILES"
  if grep -q invalid "$2"; then
    echo "syntax error"
    exit 1
  fi
fi
EOF
chmod +x "$tmp/bin/php"
export PATH="$tmp/bin:$PATH" LINTED_FILES="$tmp/linted"
script="$(dirname "$0")/lint-archive.sh"
pack() { tar -czf "$tmp/app.tar.gz" -C "$tmp" files_archive; }

pack
export FAKE_PHP_VERSION=8.2
bash "$script" "$tmp/app.tar.gz"
[ "$(wc -l < "$LINTED_FILES")" -eq 1 ]
grep -q '/lib/App.php$' "$LINTED_FILES"

: > "$LINTED_FILES"
export FAKE_PHP_VERSION=8.3
bash "$script" "$tmp/app.tar.gz"
[ "$(wc -l < "$LINTED_FILES")" -eq 4 ]

echo 'invalid' > "$toolkit/Traits/BackedEnumTrait.php"
pack
if bash "$script" "$tmp/app.tar.gz" >/dev/null 2>&1; then
  echo "PHP 8.3 accepted an invalid toolkit file" >&2
  exit 1
fi

echo 'invalid' > "$app/lib/App.php"
pack
export FAKE_PHP_VERSION=8.2
if bash "$script" "$tmp/app.tar.gz" >/dev/null 2>&1; then
  echo "PHP 8.2 accepted an invalid app file" >&2
  exit 1
fi
echo "Archive lint regression checks passed."
