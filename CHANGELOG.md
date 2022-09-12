# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.0.5] - 2022-09-05

### Fixed

- fix settings logic

### Added

- screenshots

## [1.0.4] - 2022-09-05

### Fixed

- fix duplicate bookmarks for folders which contain both, plain files
  and sub-directories.

### Added

- optional on-the-fly extraction of archive files by means of
  [wapmorgan/unified-archive](https://github.com/wapmorgan/UnifiedArchive)

- admin-customizable custom conversion scripts (default and fallback), including
  the possibility to disable the builtin converters.

- display the found converter executables on the admin settings page.

## [1.0.3] - 2022-09-02

### Fixed

- fix infinite loop with unoconv conversion
- fix generation of error page when the conversion fails.

## [1.0.2] - 2022-08-22

### Fixed

- Remove blacklisted files (i.e. .htaccess) from app-store distribution

## [1.0.1] - 2022-07-20

### Fixed

- Personal setting "page-label generation" was not remembered across invocations of the settings-app

## [1.0.0] - 2022-07-20

### Added

- First release
