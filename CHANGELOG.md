# Change history

## 3.0.5, 2026-09-05

- Changed the feed response type from generic `text/xml` to the standard `application/rss+xml` type.
- Cleared any earlier PHP cache headers before sending the configured RSS cache policy. This avoids conflicting application cache directives and reduces the chance of broad XML cache rules being applied to RSS responses.
- Added the official Zen Cart plugin directory link and plugin ID 511.

## 3.0.4, 2026-09-05

- Emit RSS directly from the page controller and exit before Zen Cart loads the storefront HTML template. This avoids template resolver differences that could still send the feed through the Bootstrap HTML page and fail on a core template path.
- Replaced the deprecated `zen_random_select()` call with `$db->ExecuteRandomMulti()` for random feeds on current Zen Cart releases.

## 3.0.3, 2026-09-05

- Added clickable public feed links to the RSS Feed configuration page for shop owners.
- Fixed repeated undefined `$rssAuthor` warnings by resolving the optional author inside product and recursive category generation functions.
- Added the standard `templates/tpl_rss_feed_default.php` page template required by current Zen Cart storefront templates. This prevents the generated feed from falling through to the HTML storefront and failing on a missing template.

## 3.0.2, 2026-09-05

- Fixed the feed endpoint HTTP 500 by loading the feed class and guard from the active encapsulated plugin directory.
- Normalized legacy HTML entities in text settings so ampersands do not expand after editing.
- Clarified that the channel image accepts a full HTTP or HTTPS URL, or a path relative to the catalog images directory. Invalid and missing images are omitted.
- Made the three public email fields optional, validated their RSS address format, documented address-harvesting risk, and stopped publishing the store owner email by default.
- Reduced the default per-IP request limit from 120 to 30 requests per minute. Existing untouched 120-request defaults migrate to 30.
- Replaced exact inventory quantities with `in stock` or `out of stock` availability.

## 3.0.1, 2026-09-05

- Converted the catalog language file to the required returned-array format. This prevents `ArraysLanguageLoader::loadArrayDefineFile()` from receiving integer `1` and causing an HTTP 500 on Zen Cart 2.1.x storefront pages immediately after installation.

## 3.0.0, 2026-09-05

- Converted the loose-file release to an encapsulated Zen Cart Plugin Manager package.
- Added support targets for Zen Cart 2.0.x through 2.2.x and PHP 8.0 through 8.5.
- Corrected the reversed cache-expiration comparison that deleted fresh files while retaining expired files.
- Added canonical cache keys so unknown query parameters cannot create unlimited cache variants.
- Added request throttling, request item caps, cache generation locking, stale responses during rebuilds, and atomic cache writes.
- Enabled caching for random feeds to prevent repeated expensive database selection.
- Added `ETag`, `Last-Modified`, `Cache-Control`, `Expires`, and HTTP 304 handling.
- Fixed the product-model SQL quoting defect.
- Fixed image metadata, skip-day output, text-input XML, empty-feed handling, and PHP 8 null or undefined-value warnings.
- Added Plugin Manager installation, legacy migration, configuration preservation, clean uninstall, and admin menu self-repair.
- Replaced obsolete active installation files with current documentation and archived the historical instructions.

## 2.1.6, 2012-03-02

- Repaired SQL and added Zen Cart 1.5.0 upgrade information.

Earlier history is preserved in [the archived readme](docs/archive/readme-v2.1.6.txt).
