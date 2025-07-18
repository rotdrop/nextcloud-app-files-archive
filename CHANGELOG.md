# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.2.7] -- 2025-06-22

### Added

- optionally make mounting the archive the default action when
  clicking on the file, this means:
  - the action will still be visible in the action/context
    menu, but just like clicking on a folder will navigate to that
    folder, clicking on the archive file will now first mount it and
    then navigate to the mount point, i.e. open the mounted directory tree
  - if an archive is already mounted, then clicking on the archive (if
    configured) will still display a notice that this is the case and
    the also will navigate to the mounted archive folder.
  - if asynchronous mounting is the default then a simple click will
    still initiate the mount background job, but that's it in this case.

### Fixed

- WIP: composer dependencies with NC29

- some of the personal settings tried to access admin settings which
  luckily did not work. Fix this and store all values in the personal
  settings of the respective user.

## [1.2.6] -- 2025-04-09

### Fixed

- node busy indicator handling for frontend operations

## [1.2.5] -- 2025-04-08

### Added

- support Nextcloud 29 / 30 / 31

### Removed

- drop support fro Nextcloud 28 and older

### Changed

- convert to Vue Composition API and Typescript

### Remarks

Starting with NC31 the (unfortunately unofficial) interface for
defining storages has changed. Starting from NC v31 the underlying
abstract base class is strongly type-hinted. Before v31 it was
"sloppy". This introduces a code bifurcation in lib/Storage which
hopefully can be abandoned when NC V29 and v30 are obsoleted,
i.e. when Nextcloud v33 is released.

## [1.2.4-rc7] - 2024-05-29

### Fixed

- stripping of common directory prefixes was deeply broken

- fix issue #38 "Unable to copy folders to another folder"

- fix setting the archive-bomb limits in the settinhs pages

- fix non-utf-8 external apps by setting environment variables

- fix mime-type and aliases registration

- work around AlchemyZippy bug in UnifiedArchive

- fix construction of zip-bomp protection exception

- pome quirks, unicode normalization

### Added

- support NC 29

- include symfony console to make vendor/bin/cam (UnifiedArchive command line utility) functional

## [1.2.3] - 2024-03-30

### Fixed

- A spurious error message in the logs. This did not affect the
  functi
  onality of the app but puzzled its users.

## [1.2.2] - 2024-03-23

### Fixed

- restore PHP 8.1 compatibility

- personal settings dialog courtesy of @kemitix

## [1.2.1] - 2024-03-21

### Fixed

- Database migration

- Some trait classes use constants, so we need PHP >= 8.2

## [1.2.0] - 2024-03-17

### Added

- Support Nextcloud v28, in particular use the new event-bus as the
  old legacy file-list is no longer available.

### Changed

- Reduce the size of the JS assets needed to hook in to the files-app sidebar

- Use latest @nextcloud/vue toolkit.

- Drop support for Nextcloud <= v27. The differences in the files-API
  are just too big.

- Translations

### Fixed

- Fix the use of the file-cache in order to avoid excessive re-scans
  of the potentionally large and complicated archive file.

## [1.1.4-rc1] - unreleased

#### Fixed

- update some packages

## [1.1.3] - 2023-08-02

### Fixed

- Update Vue components
- Update archive backend
- translations and spelling
- fix handling of password protected archives

## [1.1.2] - 2023-01-19

### Fixes

- Bump the minimal required PHP version to 8.0 as the app breaks installations still using PHP 7.4.

## [1.1.1] - 2023-01-17

### Fixed

- Theming and styles for Nextcloud v25

## [1.1.0] - 2022-12-11

### Added

- background jobs

### Fixed

- a bunch of orthographical errors with respect to the untranslated (en_US) texts

## [1.0.6] - 2022-11-17

### Added

- internal restructuring of Vue and PHP source code, there is now a
  common base of code shared between the
  [PDF Downloader](https://github.com/rotdrop/nextcloud-app-pdf-downloader)
  and this app.

## Fixed

- fix mime-type injection code

## [1.0.5] - 2022-11-12

### Fixed

- app category is now "files", no longer "office"

## [1.0.4] - 2022-11-12

### Fixed

- Nextcloud is missing some prominent MIME-type mappings. Add them.
- Performance for large archives -- partly. This is still an issue,
  but has been improved
- Correctness of file-listing if underlying archive file has changed.

### Added

- expose the back-end driver name in the side-bar menu to ease debugging

## [1.0.3] - 2022-11-10

### Fixed

- File-cache inconsistency causing mount-points not showing up in UI
- Avoid using NelexaZip as archive-backend for ZIP-archives as it
  causes problems at least with archives generated by the `zipper`
  app.

### Added

- Optionally strip a common path prefix when mounting or extracting
  archives. This is meant for the not unsual case when the archive
  content is wrapped into a top-level directory.
- templates for the mount/extraction folder names via user preferences
- conflict resolution if the target mount point / extraction folder
  already exists

## [1.0.2] - 2022-11-09

### Fixed

- screenshot links in appinfo.xml

## [1.0.1] - 2022-11-09

### Added

- Encryption support

## [1.0.0] - 2022-11-07

### Added

- First un-release
