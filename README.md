# Digital Exhibition — WordPress backend

Version control for the WordPress install behind
<https://digitalexhibition.arch.tue.nl/> (TU/e, Built Environment).

Part of the headless migration: this site is being reduced to a content API
consumed by the Nuxt front-end. See the migration plan (`project/` in the
workspace) for the full picture.

## What's tracked

- WordPress core, `wp-admin/`, `wp-includes/`
- `wp-content/` — plugins, themes, mu-plugins, uploads

## What's **not** tracked

| Path | Why |
| --- | --- |
| `wp-config.php` | Live DB credentials + auth salts. Use `wp-config.reference.php`. |
| `*.log`, `wp-content/logs/` | Runtime logs |
| `wp-content/upgrade*/`, `*-upgrade-temp-backup/` | WordPress update scratch dirs |
| `.DS_Store` | macOS cruft |

`wp-content/uploads/` is tracked **for now** — it moves to Cloudflare R2 later
(plan task 4.1), at which point the commented line in `.gitignore` gets enabled.

## Deploy

Not wired yet — planned as GitHub Actions → SSH/rsync to the TU/e server, on
merge to `main`, after the change is verified on staging (plan workstream 6).
Until then this repo is history + backup only.
