# Installation Instructions

<!-- markdown-toc start - Don't edit this section. Run M-x markdown-toc-refresh-toc -->
**Table of Contents**

- ["Official" Releases](#official-releases)
- [Release-Branches](#release-branches)
- [Building from Source](#building-from-source)

<!-- markdown-toc end -->


## "Official" Releases

This app is available throught the
[Nextcloud app-store](https://apps.nextcloud.com/apps/files_archive)
and show up automatically in the app-installation option of you
Nextcloud instance if you are logged in as administrator. The goal is
to have working versions for the most recent three (3) releases of
nextcloud available through the Nextcloud app-store.

This is the preferred installation method for all "consumer"-users
which have no or little experience in software development.

## Release-Branches

> Installing from GitHub needs more-or-less advanced programming skill
> or at least the will to learn new things

On GitHub there are release branches for stable releases of
Nextcloud. These include all needed assets to run the app and probably
a lot of unneeded data. Also, updating those release branches is as of
now not automated, so they may lack behind. They may even lack behind
the releases available through the app-store. But perhaps they are
more up to date. Or not. Or they may be. Or not.

Anyhow. Example: release branch for Nextcloud vXY:

- get yourself terminal access to your Nextcloud instance, probably by ssh
  - don't know what this means? Then this section is not for you
- [`git clone https://github.com/rotdrop/nextcloud-app-files-archive.git`](https://github.com/rotdrop/nextcloud-app-files-archive.git) into your apps directory
- `git checkout stableXY`
- fix permissions, maybe something like

  `chmod -R www-data:www-data YOUR_INSTALLATION_DIRECTORY_OF_CHOICE`

  might be needed. This depends on your installation.
- afterwards the app should show app in the admin's app-management
  page and can be enabled there.

## Building from Source

> This assumes that you really have firm programming knowledge, best
> with a deep background in web-applications, you know what `npm`,
> `composer`, `webpack`, `vue` means and you can help yourself: if
> something does not work then your primary reaction is to use one of
> the various available search engines, read the docs, Read Those Fine
> Docs [RTFM], and finally, if really nothing helps after trying hard
> for 24 hours, you may then contact the author of this package and
> suggest improvements, submit pull-request and so forth.

As of the time of this writing building the app from source requires
that it is installed into a running Nextcloud instance. This is
unfortunate, please submit a pull request if you do not like the
current state.

So:

- get yourself terminal access to your Nextcloud instance, probably by ssh
  - don't know what this means? Then this section is not for you
- `git clone https://github.com/rotdrop/nextcloud-app-files-archvie.git` into your apps directory
- optionally change to a release-branch, but you **very probably**
  just want to build the app from the default branch (main or master)
- install all needed dependencies
  - a recent version of `npm`
  - GNU make
  - a recent version of `composer`
  - perhaps a lot of further utilities, watch out for error messages during the build process
    - **pull request to improve this part of the documentation are very welcome**
- build the app with
  - `make build` to generate a production version of the app
  - `make dev` to generate a development version debuging info
    compiled in. This mostly affects the generated JavaScript assets.
  - in case you run `make` repeatedly (perhaps after adjusting things
    to your liking) and it fails: try to run `make realclean` in order
    to just remove every build artifact. Then try again.
  - if you just edit individual files then `make` should just pick up
    your changes and rebuild stuff as needed
