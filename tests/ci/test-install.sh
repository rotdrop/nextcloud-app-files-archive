#!/bin/bash
# SPDX-License-Identifier: AGPL-3.0-or-later
#
# Fresh install of the app archive on a new Nextcloud instance, followed by
# a smoke test of the API and the occ commands.
#
# Usage: tests/ci/test-install.sh <nextcloud major> <sqlite|pgsql|mysql> <app tarball>

source "$(dirname "$0")/lib.sh"

[ $# -eq 3 ] || fail "usage: $0 <nextcloud major> <sqlite|pgsql|mysql> <app tarball>"
NC_VERSION=$1
DATABASE=$2
TARBALL=$(realpath "$3")
VERSION=$(tarball_version "$TARBALL")

step "Nextcloud $NC_VERSION with $DATABASE"
start_db "$DATABASE"
start_nc "$NC_VERSION"
install_nc

step "install $APP_ID $VERSION"
install_tarball "$TARBALL"
occ app:enable "$APP_ID" || fail "app:enable failed"
check_installed "$VERSION"
check_schema

smoke_test
check_log
