#!/bin/bash
# SPDX-License-Identifier: AGPL-3.0-or-later
#
# Update from a previous release to the app archive under test, the way the
# server does it: in the same PHP process that has registered the previous
# version (see in-process-update.php). An archive mounted with the previous
# release has to survive the update.
#
# If the two Nextcloud majors differ, the server is upgraded in between,
# like a server upgrade replacing an app that is incompatible with the new
# server version.
#
# Usage: tests/ci/test-update.sh <from nextcloud major> <to nextcloud major> \
#          <sqlite|pgsql|mysql> <previous app tarball> <app tarball>

source "$(dirname "$0")/lib.sh"

[ $# -eq 5 ] || fail "usage: $0 <from nextcloud major> <to nextcloud major> <sqlite|pgsql|mysql> <previous app tarball> <app tarball>"
FROM_NC=$1
TO_NC=$2
DATABASE=$3
OLD_TARBALL=$(realpath "$4")
TARBALL=$(realpath "$5")
OLD_VERSION=$(tarball_version "$OLD_TARBALL")
VERSION=$(tarball_version "$TARBALL")

step "Nextcloud $FROM_NC with $DATABASE, $APP_ID $OLD_VERSION"
start_db "$DATABASE"
start_nc "$FROM_NC"
install_nc
install_tarball "$OLD_TARBALL"
occ app:enable "$APP_ID" || fail "app:enable of the previous release failed"

step "mount an archive with $OLD_VERSION"
upload_zip old.zip
expect_api 200 POST /archive/mount/old.zip/old-mount
expect_dav 200 GET old-mount/dir/a.txt

if [ "$FROM_NC" != "$TO_NC" ]; then
  step "upgrade the server to Nextcloud $TO_NC"
  start_nc "$TO_NC"
  occ status --output=json | grep -q '"needsDbUpgrade":false' || fail "the server upgrade did not complete"
  # The updater has disabled the incompatible app. On a real upgrade it
  # replaces the app from the app store right away, in the process which
  # registered the previous version while it was still enabled; re-enabling
  # it here makes the next process start from the same state.
  occ config:app:set "$APP_ID" enabled --value=yes >/dev/null
fi

step "update $OLD_VERSION -> $VERSION in-process"
reset_log
docker cp "$TARBALL" "$NC:/tmp/update.tar.gz"
docker cp "$(dirname "$0")/in-process-update.php" "$NC:/tmp/in-process-update.php"
docker exec -u www-data -w /var/www/html "$NC" php /tmp/in-process-update.php /tmp/update.tar.gz || fail "the in-process update failed"
check_installed "$VERSION"
check_schema

step "the archive mounted with $OLD_VERSION is still there"
expect_dav 200 GET old-mount/dir/a.txt
expect_api 200 GET /archive/mount/old.zip '"mounted":true'

smoke_test
check_log
