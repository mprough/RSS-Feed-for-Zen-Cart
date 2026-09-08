#!/usr/bin/env bash
set -euo pipefail

root="$(cd "$(dirname "$0")/.." && pwd)"
version_root="$root/files/zc_plugins/RssFeed/v3.0.6"

test -f "$version_root/manifest.php"
test -f "$version_root/Installer/ScriptedInstaller.php"
test -f "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php"
test -f "$version_root/catalog/includes/templates/template_default/templates/tpl_rss_feed_default.php"
test -f "$version_root/catalog/includes/classes/rss_feed.php"
test -f "$version_root/catalog/includes/functions/extra_functions/rss_feed_guard.php"
test -f "$version_root/admin/includes/functions/extra_functions/rss_feed_menu.php"

grep -q "'pluginVersion' => 'v3.0.6'" "$version_root/manifest.php"
grep -q "'pluginId' => 511" "$version_root/manifest.php"
grep -q "public string \$version = '3.0.6'" "$version_root/Installer/ScriptedInstaller.php"
grep -q "RSS_FEED_VERSION', '3.0.6'" "$version_root/Installer/ScriptedInstaller.php"
grep -q "RSS_FEED_LINKS" "$version_root/Installer/ScriptedInstaller.php"
grep -q "function zen_cfg_rss_feed_links" "$version_root/admin/includes/functions/extra_functions/rss_feed_menu.php"
grep -q "require_once __DIR__ . '/../../../classes/rss_feed.php'" "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php"
grep -q "g:availability" "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php"
! grep -q "g:quantity" "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php"
test "$(grep -c '\$rssAuthor = prowebs_rss_contact_value' "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php")" -eq 3
grep -q "\$rss->rss_feed_out()" "$version_root/catalog/includes/templates/template_default/templates/tpl_rss_feed_default.php"
grep -q "\$db->ExecuteRandomMulti(\$sql_products, 1)" "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php"
! grep -q "zen_random_select" "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php"
test "$(grep -c '\$rss->rss_feed_out();' "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php")" -eq 1
grep -q "zen_exit();" "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php"
grep -q "rss_feed_content_type('application/rss+xml')" "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php"
grep -q "header_remove('Cache-Control')" "$version_root/catalog/includes/classes/rss_feed.php"

for guarded_file in \
    "$version_root/catalog/includes/extra_datafiles/rss_feed.php" \
    "$version_root/catalog/includes/classes/rss_feed.php" \
    "$version_root/catalog/includes/functions/extra_functions/rss_feed_guard.php" \
    "$version_root/catalog/includes/functions/extra_functions/rss_feed.php" \
    "$version_root/catalog/includes/modules/pages/rss_feed/header_php.php"
do
    grep -Fq "defined('IS_ADMIN_FLAG')" "$guarded_file"
done

echo "Package checks passed."
