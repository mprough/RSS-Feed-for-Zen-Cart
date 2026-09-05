# Change history

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
