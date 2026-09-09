# Digital Exhibition — WordPress backend

Version control for the `wp-content` of the WordPress install behind
<https://digitalexhibition.arch.tue.nl/> (TU/e, Built Environment).

Part of the headless migration: this site is being reduced to a content API
consumed by the Nuxt front-end. See the migration plan (`project/` in the
workspace) for the full picture.

## What's tracked

- `wp-content/plugins/`
- `wp-content/themes/digital/` — the active theme (only)
- `wp-content/mu-plugins/`
- `wp-content/maintenance*`
- `wp-config.reference.php` — placeholder config for deploys

## What's **not** tracked (kept on localhost only)

| Path | Why |
| --- | --- |
| `wp-admin/`, `wp-includes/`, root `wp-*.php`, `.htaccess`, `index.php` | WordPress core — needed to run the site locally, but vendor code. Restore with `wp core download` or from the host. |
| `wp-config.php` | Live DB credentials + auth salts. Use `wp-config.reference.php`. |
| `wp-content/ngg/` | NextGEN Gallery module cache |
| `wp-content/themes/{chique,chique-pro,twentytwentyfive}` | Inactive themes — removed |
| `wp-content/uploads/` | Media — moves to Cloudflare R2 (plan task 4.1). Folder kept locally (empty) so WP can write to it. |
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
