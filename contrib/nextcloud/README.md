## What is this?

This folders contains some config examples for Nextcloud, e.g. to
better support archive MIME-types and the like. Please make sure to
also read the
[Nextcloud admin-documentation](https://docs.nextcloud.com/server/latest/admin_manual)
before (then non-)blindly using these examples.

The directory layout of this folder mimcs the layout of the Nextcloud
server distribution. From this it should be clear where each file belongs to.

## Content

- `config/mimetypemapping.json`
  See
  [Nextcloud MIME-type configuration](https://docs.nextcloud.com/server/latest/admin_manual/configuration_mimetypes/index.html).
  Note that is neccessary to run
  ```
  occ maintenance:mimetype:update-db
  ```
  after adding the content of this file to the nextcloud configuration.
  - map the `.iso` extension to the MIME-type
    `application/x-iso9660-image` in order to be able to mount
    CD-images. The default mapping of Nextcloud as of v25 is
    `application/octett-stream` (AKA "I do not konw").
  - map the `.tar.xz` extension to the MIME-type `application/x-gtar`.

