#!/usr/bin/env bash
set -euo pipefail

root="$(cd "$(dirname "$0")/.." && pwd)"
version_root="$root/files/zc_plugins/RssFeed/v3.0.0"

test -f "$version_root/manifest.php"
test -f "$version_root/Installer/ScriptedInstaller.php"
test -f "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php"
test -f "$version_root/catalog/includes/classes/rss_feed.php"
test -f "$version_root/catalog/includes/functions/extra_functions/rss_feed_guard.php"
test -f "$version_root/admin/includes/functions/extra_functions/rss_feed_menu.php"

grep -q "'pluginVersion' => 'v3.0.0'" "$version_root/manifest.php"
grep -q "public string \$version = '3.0.0'" "$version_root/Installer/ScriptedInstaller.php"
grep -q "RSS_FEED_VERSION', '3.0.0'" "$version_root/Installer/ScriptedInstaller.php"

echo "Package checks passed."
