# Installation and upgrade

## Before installation

Back up the database and store files.

## Plugin Manager installation

1. Copy the contents of the package `files` directory to the store root.
2. In Zen Cart admin, open **Modules > Plugin Manager**.
3. Find **RSS Feed** and select **Install**.
4. Open **Configuration > RSS Feed** and review the cache and request-control settings.
5. Test `index.php?main_page=rss_feed&feed=new_products`.

The package uses `template_default` fallbacks and does not overwrite a storefront template or Zen Cart core file.

## Upgrade from 2.1.6 or another loose-file release

Plugin Manager detects the existing `RSS Feed` configuration group and preserves its values. It adds the new cache and throttling settings, updates the installed version, and repairs the Configuration menu entry.

After confirming the current version works, compare and remove the old loose RSS files previously copied under `includes`. The historical file list is available in `docs/archive/readme-v2.1.6.txt`. Do not remove the Plugin Manager files under `zc_plugins/RssFeed`.

## Uninstall

Use **Modules > Plugin Manager > RSS Feed > Uninstall**. Uninstall removes plugin-owned `RSS_*` settings, the RSS Feed configuration group when empty, and its admin page registration. Store product and category data is never removed.
