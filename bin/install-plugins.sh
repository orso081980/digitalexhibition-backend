#!/usr/bin/env bash
#
# Reinstalls the third-party plugins that are gitignored from this repo
# (wp-content/plugins/ tracks only our own in-house plugins — figuro-folder,
# custom_dashboard). Run this after a fresh clone / on a new server, from the
# WordPress root:
#
#   bin/install-plugins.sh
#
# Requires wp-cli, run against a WordPress install that already has its DB
# configured (wp-config.php in place). Versions are pinned to what's actually
# in use so a plugin update elsewhere doesn't silently change what a fresh
# install gets.
set -euo pipefail

cd "$(dirname "$0")/.."

install() {
	local slug="$1" version="$2" activate="$3"
	if wp plugin is-installed "$slug" --quiet 2>/dev/null; then
		echo "-- ${slug} already installed, skipping"
		return
	fi
	echo "-- installing ${slug} ${version}"
	wp plugin install "$slug" --version="$version"
	if [ "$activate" = "activate" ]; then
		wp plugin activate "$slug"
	fi
}

# --- Free, wordpress.org-hosted plugins ------------------------------------
install advanced-media-offloader 4.5.2 activate
install duplicate-page            4.5.9 activate
install elementor                 4.2.4 activate
install post-grid-elementor-addon 2.0.24 activate
install post-type-switcher        4.0.1 activate
install regenerate-thumbnails     3.1.6 activate
install header-footer-elementor   2.9.4 activate
install wp-mail-smtp              4.9.0 activate
install wordpress-seo             28.4 activate

install better-search-replace     1.4.11 skip
install disable-wp-rest-api       2.6.9 skip
install show-current-template     0.5.4 skip
install wordpress-importer        0.9.6 skip

echo
echo "== Manual steps — premium plugins (not on wordpress.org, need a license) =="
echo " * Advanced Custom Fields PRO 6.8.10  — download from your ACF account, then:"
echo "     wp plugin install /path/to/advanced-custom-fields-pro.zip --activate"
echo " * Elementor Pro 4.2.3                — download from your Elementor account, then:"
echo "     wp plugin install /path/to/elementor-pro.zip --activate"
echo " * DearFlip (dflip) 2.4.37            — download from your DearFlip/CodeCanyon account, then:"
echo "     wp plugin install /path/to/dflip.zip --activate"
echo
echo "Not reinstalled on purpose: filebird-pro (inactive, superseded by our"
echo "own Figuro Media — wp-content/plugins/figuro-folder, already tracked)."
