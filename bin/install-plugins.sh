#!/usr/bin/env bash
#
# Reinstalls the third-party plugins that are gitignored from this repo
# (wp-content/plugins/ tracks only our own in-house plugins — figuro-folder,
# custom_dashboard, cloudflare-deploy-trigger). Run this after a fresh clone /
# on a new server, from the WordPress root:
#
#   bin/install-plugins.sh
#
# Requires wp-cli, run against a WordPress install that already has its DB
# configured (wp-config.php in place). Always installs the latest version —
# re-running is safe, wp-cli just reports "already installed" for plugins
# already there.

# --- Free, wordpress.org-hosted plugins -------------------------------------
wp plugin install advanced-custom-fields-pro --activate
wp plugin install advanced-media-offloader --activate
wp plugin install aryo-activity-log --activate
wp plugin install dflip --activate
wp plugin install duplicate-page --activate
wp plugin install elementor --activate
wp plugin install header-footer-elementor --activate
wp plugin install post-grid-elementor-addon --activate
wp plugin install post-type-switcher --activate
wp plugin install regenerate-thumbnails --activate
wp plugin install wordpress-importer --activate
wp plugin install wordpress-seo --activate
wp plugin install wp-health --activate
wp plugin install wp-mail-smtp --activate

echo
echo "== Manual steps — premium plugins (not on wordpress.org, need a license) =="
echo " * Advanced Custom Fields PRO — download from your ACF account, then:"
echo "     wp plugin install /path/to/advanced-custom-fields-pro.zip --activate"
echo " * Elementor Pro — download from your Elementor account, then:"
echo "     wp plugin install /path/to/elementor-pro.zip --activate"
echo " * DearFlip (dflip) — download from your DearFlip/CodeCanyon account, then:"
echo "     wp plugin install /path/to/dflip.zip --activate"
echo
echo "Not reinstalled on purpose: filebird-pro (inactive, superseded by our"
echo "own Figuro Media — wp-content/plugins/figuro-folder, already tracked)."
