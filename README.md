# Digital Exhibition — WordPress backend

Version control for the `wp-content` of the WordPress install behind
<https://digitalexhibition.arch.tue.nl/> (TU/e, Built Environment).

Part of the headless migration: this site is being reduced to a content API
consumed by the Nuxt front-end. See the migration plan (`project/` in the
workspace) for the full picture.

## What's tracked

- `wp-content/plugins/figuro-folder` (Figuro Media) and `wp-content/plugins/custom_dashboard` — **our own code only.** Every other plugin is gitignored; see below.
- `wp-content/themes/tue-digital-exhibition/` — the active theme (only). Renamed from `digital` so Plesk/WP Toolkit auto-updates can't overwrite it; don't rename it back
- `wp-content/mu-plugins/`
- `wp-content/maintenance*`
- `wp-config.reference.php` — placeholder config for deploys
- `bin/*.php` — one-off, idempotent WP-CLI scripts that bring a database in line with the code (see below)

## Third-party plugins are not tracked

This repo version-controls the site's own code, not vendored plugin code that
also has to sit on the server independent of this repo. `wp-content/plugins/`
is gitignored except our in-house plugins (above). The rest stay on disk
locally and on the server exactly as before — just not on GitHub. Each
environment manages its own third-party plugins by hand (live and staging
intentionally differ, e.g. FluentSMTP on live, WP Mail SMTP on staging).

## Database scripts (`bin/`)

Run from the WordPress root with `wp eval-file`. Each is safe to re-run; the
`migrate-*` ones are a dry run unless given `apply`. Order matters:

1. `bin/add-pdf-file-field.php` — adds the `pdf_file` sub-field to `pdf_post`
2. `bin/migrate-pdf-file-field.php [apply]` — backfills `pdf_file` from the old dFlip shortcodes
3. `bin/add-video-file-field.php` — adds the `video_url` sub-field to `list_video`
4. `bin/migrate-video-file-field.php [apply]` — backfills `video_url` from "Embed Video" uploads

Run them **before** deploying the code: the PDF list renders only `pdf_file`, so
code that arrives first shows empty PDF lists until the backfill has run. The
old code ignores the new fields, so scripts-first has no gap.

## What's **not** tracked (kept on localhost only)

| Path | Why |
| --- | --- |
| `wp-admin/`, `wp-includes/`, root `wp-*.php`, `.htaccess`, `index.php` | WordPress core — needed to run the site locally, but vendor code. Restore with `wp core download` or from the host. |
| `wp-config.php` | Live DB credentials + auth salts. Use `wp-config.reference.php`. |
| `wp-content/plugins/*` (except figuro-folder, custom_dashboard) | Third-party plugins — see above. Managed per environment. |
| `wp-content/ngg/` | NextGEN Gallery module cache |
| `wp-content/themes/{chique,chique-pro,twentytwentyfive}` | Inactive themes — removed |
| `wp-content/uploads/` | Media. **Live keeps every asset local in this folder — no R2.** Staging serves media from Cloudflare R2 via the `advanced-media-offloader` plugin (installed on staging only); its credentials are `ADVMO_CLOUDFLARE_R2_*` constants in staging's `wp-config.php` (template in `wp-config.reference.php`), never in the DB option. |
| `*.log`, `wp-content/logs/`, `wp-content/compressx/log/` | Runtime logs |
| `wp-content/upgrade*/`, `*-upgrade-temp-backup/` | WordPress update scratch dirs |


## Live vs staging: R2

The code is the same on both; only the environment differs. R2 is switched on
by the `ADVMO_CLOUDFLARE_R2_DOMAIN` constant in `wp-config.php` plus the
`advanced-media-offloader` plugin, both **staging only**. Without the constant
the two R2 mu-plugins (`dflip-r2-pdf-urls`, `advmo-regenerate-thumbnails-compat`)
do nothing. Never define it on live. The R2 database state (`advmo_*`
attachment meta, `advmo_settings`, rewritten dFlip URLs) lives in staging's DB
only and is never copied to live.

## Deploy

Not wired yet — planned as GitHub Actions → SSH/rsync, on
merge to `main`, after the change is verified on staging (plan workstream 6).
Until then this repo is history + backup only.
