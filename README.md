# Digital Exhibition — WordPress backend

Version control for the `wp-content` of the WordPress install behind
<https://digitalexhibition.arch.tue.nl/> (TU/e, Built Environment).

Part of the headless migration: this site is being reduced to a content API
consumed by the Nuxt front-end. See the migration plan (`project/` in the
workspace) for the full picture.

## What's tracked

- `wp-content/plugins/figuro-folder` (Figuro Media), `wp-content/plugins/custom_dashboard`, and `wp-content/plugins/pdf-flipper` — **our own code only.** Every other plugin is gitignored; see below.
- `wp-content/themes/digital/` — the active theme (only)
- `wp-content/mu-plugins/`
- `wp-content/maintenance*`
- `wp-config.reference.php` — placeholder config for deploys
- `bin/install-plugins.sh` — reinstalls the gitignored third-party plugins

## Third-party plugins are not tracked

This repo version-controls the site's own code, not vendored plugin code that
also has to sit on the server independent of this repo. `wp-content/plugins/`
is gitignored except our two in-house plugins (above). The rest stay on disk
locally and on the server exactly as before — just not on GitHub.

To reproduce them on a fresh clone or a new server:

```
bin/install-plugins.sh
```

It pins the exact version currently in use for every free, wordpress.org-hosted
plugin, and prints manual steps for the premium ones it can't fetch itself
(ACF PRO, Elementor Pro, DearFlip) — those need a zip from the relevant
account. `filebird-pro` is deliberately not reinstalled: inactive, superseded
by our own Figuro Media.

## What's **not** tracked (kept on localhost only)

| Path | Why |
| --- | --- |
| `wp-admin/`, `wp-includes/`, root `wp-*.php`, `.htaccess`, `index.php` | WordPress core — needed to run the site locally, but vendor code. Restore with `wp core download` or from the host. |
| `wp-config.php` | Live DB credentials + auth salts. Use `wp-config.reference.php`. |
| `wp-content/plugins/*` (except figuro-folder, custom_dashboard, pdf-flipper) | Third-party plugins — see above. Reinstall with `bin/install-plugins.sh`. |
| `wp-content/ngg/` | NextGEN Gallery module cache |
| `wp-content/themes/{chique,chique-pro,twentytwentyfive}` | Inactive themes — removed |
| `wp-content/uploads/` | Media — moves to Cloudflare R2 (plan task 4.1) via the `advanced-media-offloader` plugin. Credentials live as `ADVMO_CLOUDFLARE_R2_*` constants in `wp-config.php` (template in `wp-config.reference.php`), never in the DB option. Folder kept locally (empty) so WP can still write to it before offload. |
| `*.log`, `wp-content/logs/`, `wp-content/compressx/log/` | Runtime logs |
| `wp-content/upgrade*/`, `*-upgrade-temp-backup/` | WordPress update scratch dirs |

## Restoring something that was removed

Everything deleted here is still in the initial import (commit `35c6d25`):

```
git checkout 35c6d25 -- wp-content/themes/chique
```

## Deploy

Not wired yet — planned as GitHub Actions → SSH/rsync to the TU/e server, on
merge to `main`, after the change is verified on staging (plan workstream 6).
Until then this repo is history + backup only.
