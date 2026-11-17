#!/bin/bash
# SPDX-License-Identifier: AGPL-3.0-or-later

source "$(dirname "$0")/lib.sh"
trap - EXIT

occ() {
  printf '| /normalized | ok |\n'
  sleep 0.05
  printf 'remaining table output\n'
}

expect_occ_output '\| /normalized +\|' "$APP_ID:list-mounts" --all
upload_zip() { :; }
api() { echo '200 {}'; }
expect_api() { :; }
expect_dav() { :; }
smoke_test_mount_point_paths

if (expect_occ_output 'missing row' "$APP_ID:list-mounts" --all) >/dev/null; then
  fail "a missing match was accepted"
fi
occ() {
  printf '| /normalized | ok |\n'
  return 1
}
if (expect_occ_output '\| /normalized +\|' "$APP_ID:list-mounts" --all) >/dev/null; then
  fail "a failed occ command was accepted"
fi
if (smoke_test_mount_point_paths) >/dev/null; then
  fail "a failed list-mounts command was accepted"
fi
occ() { printf '| /normalized | absent |\n'; }
if (smoke_test_mount_point_paths) >/dev/null; then
  fail "an absent mount was accepted"
fi
if (
  occ() { return 1; }
  smoke_test
) >/dev/null; then
  fail "a failed deletion registration check was accepted"
fi
if (
  occ() {
    printf '| admin | /smoke-mount-2 |\n'
    sleep 0.05
    printf 'remaining table output\n'
  }
  smoke_test
) >/dev/null; then
  fail "the mount of a deleted archive was accepted"
fi
if (
  occ() { return 1; }
  check_schema
) >/dev/null; then
  fail "a failed command listing was accepted"
fi

create_user() { :; }
share_file() { :; }
expect_api_as() { :; }
shared_cleanup_deleted=false
expect_dav() { shared_cleanup_deleted=true; }
occ() {
  case $1 in
    "$APP_ID:list-mounts")
      if [ "$shared_cleanup_deleted" = false ]; then
        printf '| sharee2 | /shared-cleanup |\n'
      fi
      ;;
    "$APP_ID:prune-mounts")
      printf 'would remove: 0\n'
      ;;
    *) return 1 ;;
  esac
  sleep 0.05
  printf 'remaining table output\n'
}
notifications_as() {
  printf 'The archive folder cleaned-by-admin has been removed\n'
  sleep 0.05
  printf 'another notification\n'
}
smoke_test_shared_archive_cleanup

if (
  shared_cleanup_deleted=false
  occ() { printf 'no mounts\n'; }
  smoke_test_shared_archive_cleanup
) >/dev/null; then
  fail "a missing second-user mount was accepted"
fi
if (
  shared_cleanup_deleted=false
  occ() {
    printf '| sharee2 | /shared-cleanup |\n'
    return 1
  }
  smoke_test_shared_archive_cleanup
) >/dev/null; then
  fail "a failed shared-mount registration check was accepted"
fi
if (
  shared_cleanup_deleted=false
  occ() {
    printf '| sharee2 | /shared-cleanup |\n'
    sleep 0.05
    printf 'remaining table output\n'
  }
  smoke_test_shared_archive_cleanup
) >/dev/null; then
  fail "a surviving second-user mount was accepted"
fi
if (
  shared_cleanup_deleted=false
  occ() {
    if [ "$shared_cleanup_deleted" = false ]; then
      printf '| sharee2 | /shared-cleanup |\n'
    else
      return 1
    fi
  }
  smoke_test_shared_archive_cleanup
) >/dev/null; then
  fail "a failed shared-mount deletion check was accepted"
fi
echo "Shell helper regression checks passed."
