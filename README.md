# Archive Inspector and Extractor

## Intro
This is an app for the Nextcloud cloud software.

- archive inspection is implemented as an external mount where
  archive members are streamed on the fly as they are accessed
- archive extraction extracts the archive contents into the file-space
  of the cloud. The app tries to be quota-aware while doing this.

The app adds an item to the file-actions menu which lets you mount the
archive file as "external" mount into the current directory. This
mount is "movable" and can be renamed or moved into another folder.

The app further adds an entry to the details-view in the right
side-bar wheren archive information is displayed, and controls for
mounting and extracing the archive to any location in the cloud
file-system are available.

### State

Works for me.

### Security

- in order to somehow reduce the danger of
  [zip-bombs](https://en.wikipedia.org/wiki/Zip_bomb) there is a
  hard-coded upper limit of the decompressed archive size
- administrators can lower this limit in order to reduce resource
  usage on the server or if they feel that the builtin limit of 2^30
  bytes is too high.
- users may decrease this limit further on a per-user basis

### Implementation
This package relies on
[`wapmorgan/unified-archive`](https://github.com/wapmorgan/UnifiedArchive)
as archive handling backend. Please see there for a list of supported
archive formats and how to support further archive formats.
