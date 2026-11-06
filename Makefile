# This file is licensed under the Affero General Public License version 3 or
# later. See the COPYING file.
#
# Thin wrapper around the shared Makefile of the build-system submodule,
# only the app-specific settings live here.

SRCDIR = .
ABSSRCDIR = $(CURDIR)
DEV_LIB_DIR = $(ABSSRCDIR)/dev-scripts/lib

APP_NOTIFICATION_API = 1
APPSTORE_FILES_EXTRA = config

include $(DEV_LIB_DIR)/makefile/app.mk
