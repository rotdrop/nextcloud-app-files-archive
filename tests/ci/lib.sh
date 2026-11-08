# shellcheck shell=bash
# SPDX-License-Identifier: AGPL-3.0-or-later
#
# Helpers shared by the install and update tests in this directory. They
# only need docker and run the same way in CI and locally: Nextcloud comes
# from the official nextcloud:<version>-apache images, the database (unless
# SQLite) from a throw-away postgres or mariadb container, all on a private
# docker network. Set KEEP=1 to keep the containers for inspection.

set -euo pipefail

APP_ID=files_archive
CI_ID=${CI_ID:-fa-ci-$$}
NET=$CI_ID
NC=$CI_ID-nc
DBC=$CI_ID-db
VOL=$CI_ID-html
DB_ARGS=()

step() { echo "### $*"; }

fail() {
  echo "::error::$*"
  exit 1
}

cleanup() {
  local rc=$?
  if [ $rc -ne 0 ] && docker inspect "$NC" >/dev/null 2>&1; then
    echo "### container log (tail)"
    docker logs --tail 40 "$NC" 2>&1 || true
    echo "### nextcloud.log (warnings and errors)"
    print_log 2 || true
  fi
  if [ -n "${KEEP:-}" ]; then
    echo "### keeping $NC (KEEP is set)"
  else
    docker rm -f -v "$NC" "$DBC" >/dev/null 2>&1 || true
    docker volume rm "$VOL" >/dev/null 2>&1 || true
    docker network rm "$NET" >/dev/null 2>&1 || true
  fi
  exit $rc
}
trap cleanup EXIT

occ() { docker exec -u www-data "$NC" php occ "$@"; }

# Create a user unless it exists already: several test functions share the
# instance and must not trip over each other when they run in sequence.
create_user() {
  if docker exec -u www-data "$NC" php occ user:info "$1" >/dev/null 2>&1; then
    return 0
  fi
  docker exec -e "OC_PASS=$2" -u www-data "$NC" \
    php occ user:add --password-from-env --display-name="$1" "$1" >/dev/null
}

tarball_version() {
  tar -xzOf "$1" "$APP_ID/appinfo/info.xml" | sed -n 's:.*<version>\(.*\)</version>.*:\1:p' | head -1
}

# docker run would print the progress of the image download line by line.
pull() { docker pull -q "$1" >/dev/null; }

start_db() {
  docker network inspect "$NET" >/dev/null 2>&1 || docker network create "$NET" >/dev/null
  case $1 in
    sqlite)
      DB_ARGS=(--database sqlite)
      ;;
    pgsql)
      pull postgres:17
      docker run -d --name "$DBC" --network "$NET" \
        -e POSTGRES_USER=nextcloud -e POSTGRES_PASSWORD=nextcloud -e POSTGRES_DB=nextcloud \
        postgres:17 >/dev/null
      DB_ARGS=(--database pgsql --database-host "$DBC" --database-name nextcloud --database-user nextcloud --database-pass nextcloud)
      ;;
    mysql)
      pull mariadb:11
      docker run -d --name "$DBC" --network "$NET" \
        -e MARIADB_ROOT_PASSWORD=root -e MARIADB_USER=nextcloud -e MARIADB_PASSWORD=nextcloud -e MARIADB_DATABASE=nextcloud \
        mariadb:11 >/dev/null
      DB_ARGS=(--database mysql --database-host "$DBC" --database-name nextcloud --database-user nextcloud --database-pass nextcloud)
      ;;
    *)
      fail "unknown database '$1'"
      ;;
  esac
}

# Start (or replace) the Nextcloud container on the persistent volume. The
# entrypoint first copies the server sources and, if the volume holds an
# older installed version, runs `occ upgrade`; apache only starts afterwards.
start_nc() {
  docker rm -f "$NC" >/dev/null 2>&1 || true
  pull "nextcloud:$1-apache"
  docker run -d --name "$NC" --network "$NET" -v "$VOL:/var/www/html" "nextcloud:$1-apache" >/dev/null
  local _
  for _ in $(seq 150); do
    if docker logs "$NC" 2>&1 | grep -q 'resuming normal operations'; then
      return 0
    fi
    if [ "$(docker inspect -f '{{.State.Running}}' "$NC")" != true ]; then
      fail "the nextcloud:$1 container exited during start-up"
    fi
    sleep 2
  done
  fail "the nextcloud:$1 container did not start"
}

# Restart the web server so that it runs the code of the replaced app.
# The image ships opcache.revalidate_freq=60, so requests would otherwise be
# answered from the previous version for up to a minute - which would make a
# test that only runs over HTTP validate the old code.
reload_nc() {
  docker restart "$NC" >/dev/null
  local _ code
  for _ in $(seq 150); do
    code=$(docker exec "$NC" curl -s -o /dev/null -w '%{http_code}' http://localhost/status.php 2>/dev/null || true)
    if [ "$code" = 200 ]; then
      return 0
    fi
    sleep 2
  done
  fail "the container did not come back after the restart"
}

install_nc() {
  local _
  for _ in $(seq 30); do
    if occ maintenance:install "${DB_ARGS[@]}" --admin-user admin --admin-pass admin >/dev/null 2>&1; then
      # The tests must not depend on the app store: it rate-limits, and an
      # update from there would replace the build under test.
      occ config:system:set appstoreenabled --type=boolean --value=false >/dev/null
      occ log:manage --level=warning >/dev/null
      return 0
    fi
    sleep 3
  done
  occ maintenance:install "${DB_ARGS[@]}" --admin-user admin --admin-pass admin || fail "maintenance:install failed"
}

install_tarball() {
  docker cp "$1" "$NC:/tmp/app.tar.gz"
  docker exec "$NC" sh -c "rm -rf /var/www/html/custom_apps/$APP_ID && tar -xzf /tmp/app.tar.gz -C /var/www/html/custom_apps && chown -R www-data: /var/www/html/custom_apps/$APP_ID"
}

reset_log() { docker exec "$NC" sh -c ': > /var/www/html/data/nextcloud.log'; }

# Print the log entries at or above the given level, exit status 1 if any.
print_log() {
  docker exec "$NC" php -r '
    $n = 0;
    foreach (@file("/var/www/html/data/nextcloud.log") ?: [] as $line) {
      $entry = json_decode($line, true);
      if (($entry["level"] ?? 0) >= (int)$argv[1]) {
        $n++;
        echo "[", $entry["level"], "] ", $entry["app"] ?? "", ": ", $entry["message"] ?? $line, PHP_EOL;
      }
    }
    exit($n > 0 ? 1 : 0);' "$1"
}

check_log() {
  step "no errors in nextcloud.log"
  print_log 3 || fail "errors were logged, see above"
}

check_installed() {
  step "app enabled at version $1"
  local installed
  installed=$(occ config:app:get "$APP_ID" installed_version)
  [ "$installed" = "$1" ] || fail "installed_version is '$installed', expected '$1'"
  [ "$(occ config:app:get "$APP_ID" enabled)" = yes ] || fail "the app is not enabled"
  occ status --output=json | grep -q '"maintenance":false' || fail "the server is in maintenance mode"
  occ status --output=json | grep -q '"needsDbUpgrade":false' || fail "the server still needs an upgrade"
}

check_schema() {
  # db:schema:check (Nextcloud 35+) replays all migrations into an expected
  # schema and compares it with the live one, so it catches differences
  # between the fresh-install and the upgrade path of the migrations.
  if occ list db 2>/dev/null | grep -q 'db:schema:check'; then
    step "database schema matches the migrations"
    occ db:schema:check "oc_${APP_ID}_mounts" || fail "db:schema:check reported differences"
  fi
}

# Call an app route as admin, print "<http status> <start of the body>".
api() {
  docker exec -u www-data "$NC" bash -c '
    cd /tmp && rm -f cookies body
    token=$(curl -s -c cookies -u admin:admin http://localhost/index.php/apps/files/ | grep -o "data-requesttoken=\"[^\"]*" | head -1 | cut -d\" -f2)
    code=$(curl -s -b cookies -u admin:admin -H "requesttoken: $token" -H "Accept: application/json" -o body -w "%{http_code}" -X "$1" "http://localhost/index.php/apps/files_archive$2")
    echo "$code $(head -c 400 body)"' _ "$1" "$2"
}

expect_api() {
  local out
  out=$(api "$2" "$3")
  [ "${out%% *}" = "$1" ] || fail "$2 $3: expected HTTP $1, got: $out"
  if [ -n "${4:-}" ]; then
    [[ $out == *"$4"* ]] || fail "$2 $3: '$4' not in response: $out"
  fi
  echo "  $2 $3 -> $1"
}

# WebDAV request as admin on the given path below the user's files.
expect_dav() {
  local code
  code=$(docker exec -u www-data "$NC" curl -s -o /dev/null -w "%{http_code}" -u admin:admin -X "$2" ${4:+-T "$4"} "http://localhost/remote.php/dav/files/admin/$3")
  [ "$code" = "$1" ] || fail "WebDAV $2 $3: expected HTTP $1, got $code"
  echo "  WebDAV $2 $3 -> $1"
}

# Upload a small zip archive with dir/a.txt and b.txt to the root folder of
# admin. Archives stay in the root: Apache rejects encoded slashes (%2F) in
# the route parameters.
upload_zip() {
  docker exec -u www-data "$NC" php -r '
    $zip = new ZipArchive();
    $zip->open("/tmp/" . $argv[1], ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFromString("dir/a.txt", "hello\n");
    $zip->addFromString("b.txt", "world\n");
    $zip->close();' "$1"
  expect_dav 201 PUT "$1" "/tmp/$1"
}

# Call an app route, print "<http status> <start of the body>".
# Like api(), but with an arbitrary user instead of admin.
api_as() {
  docker exec -u www-data "$NC" bash -c '
    cd /tmp && rm -f cookies-as body-as
    user=$3 pass=$4
    token=$(curl -s -c cookies-as -u "$user:$pass" http://localhost/index.php/apps/files/ | grep -o "data-requesttoken=\"[^\"]*" | head -1 | cut -d\" -f2)
    code=$(curl -s -b cookies-as -u "$user:$pass" -H "requesttoken: $token" -H "Accept: application/json" -o body-as -w "%{http_code}" -X "$1" "http://localhost/index.php/apps/files_archive$2")
    echo "$code $(head -c 400 body-as)"' _ "$1" "$2" "${3%%:*}" "${3#*:}"
}

# WebDAV request on the given path below the given user's files.
# Like expect_dav(), but the user is passed in as "user:password".
expect_dav_as() {
  local code who=$4
  code=$(docker exec -u www-data "$NC" curl -s -o /dev/null -w "%{http_code}" -u "$who" -X "$2" "http://localhost/remote.php/dav/files/${who%%:*}/$3")
  [ "$code" = "$1" ] || fail "WebDAV $2 $3 as ${who%%:*}: expected HTTP $1, got $code"
  echo "  WebDAV $2 $3 as ${who%%:*} -> $1"
}

# A second user, plus a user-share of an archive file which stays in admin's
# storage. This is the setup in which the mount provider used to skip the
# mount: the archive file id is only reachable through the share, and at
# mount-assembly time the share mounts are not registered yet.
smoke_test_shared_archive() {
  local rec="sharee:sharee-pass-1234"
  step "smoke test: archive shared by another user"
  create_user sharee sharee-pass-1234

  upload_zip shared.zip
  expect_api 200 POST /archive/info/shared.zip '"format":"zip"' >/dev/null
  docker exec -u www-data "$NC" curl -s -o /dev/null -u admin:admin \
    -H 'OCS-APIRequest: true' -H 'Accept: application/json' \
    -X POST 'http://localhost/ocs/v2.php/apps/files_sharing/api/v1/shares' \
    -d 'path=/shared.zip' -d 'shareType=0' -d 'shareWith=sharee'

  # The sharee mounts the archive and must see it in the file list.
  if [ "$(api_as POST /archive/mount/shared.zip "$rec" | cut -d' ' -f1)" != 200 ]; then
    fail "the sharee could not mount the shared archive"
  fi
  expect_dav_as 200 GET shared/dir/a.txt "$rec"
  expect_dav_as 200 GET shared/b.txt "$rec"

  # The mount also has to survive a second, unrelated request: the provider is
  # asked to build the mounts again and must still find the shared archive.
  expect_dav_as 200 GET shared/dir/a.txt "$rec"

  expect_dav 204 DELETE shared.zip
}

# Exercise the main features of the installed version through its API.
smoke_test() {
  step "smoke test: info, mount, browse, extract, unmount, delete"
  upload_zip smoke.zip
  expect_api 200 POST /archive/info/smoke.zip '"format":"zip"'
  expect_api 200 POST /archive/mount/smoke.zip/smoke-mount
  expect_api 200 GET /archive/mount/smoke.zip '"mounted":true'
  expect_dav 200 GET smoke-mount/dir/a.txt
  expect_api 200 POST /archive/extract/smoke.zip/smoke-extract
  expect_dav 200 GET smoke-extract/b.txt
  expect_api 200 POST /archive/unmount/smoke.zip
  expect_dav 404 GET smoke-mount/dir/a.txt
  expect_api 200 POST /archive/mount/smoke.zip/smoke-mount-2
  expect_dav 204 DELETE smoke.zip
  if occ "$APP_ID:list-mounts" --all | grep -q smoke-mount-2; then
    fail "the mount of a deleted archive is still registered"
  fi

  smoke_test_shared_archive

  step "occ commands"
  occ "$APP_ID:list-mounts" --all
  occ "$APP_ID:rebuild-cache" --all
  occ maintenance:repair >/dev/null
}
