# RSS Feed for Zen Cart

RSS Feed generates RSS 2.0 feeds for Zen Cart products, categories, new products, best sellers, specials, featured products, upcoming products, and supported news installations.

Version 3.0.3 modernizes the 2012 release for Zen Cart 2.0.x through 2.2.x and PHP 8.0 through 8.5. It is an encapsulated Plugin Manager package with no core or template overwrites.

## Bot and cache protection

- Every feed type, including random feeds, uses the server cache.
- Query parameters are normalized before the cache key is created. Unknown parameters cannot create unlimited cache variants.
- The public `limit` parameter is capped by the configurable maximum request limit.
- A configurable per-IP request limit returns HTTP 429 with `Retry-After` when exceeded.
- A generation lock prevents many simultaneous requests from rebuilding the same expired feed.
- When another request is rebuilding a feed, an existing stale copy is served instead of repeating the database work.
- Atomic cache writes prevent partial XML files.
- `ETag`, `Last-Modified`, `Cache-Control`, and HTTP 304 responses reduce repeated transfers.

The defaults are 15 minutes of server caching, 250 maximum requested items, and 30 requests per IP address per minute. These can be changed under **Configuration > RSS Feed**.

## Installation

See [installation and upgrade](docs/INSTALLATION.md). Configuration details and feed examples are in [configuration](docs/CONFIGURATION.md).

## Compatibility

- Zen Cart 2.0.x, 2.1.x, and 2.2.x
- PHP 8.0 through 8.5, within the limits supported by the installed Zen Cart version
- MySQL 5.7+ or MariaDB equivalents supported by Zen Cart

## Support

Report reproducible bugs through the [PRO-Webs helpdesk](https://prowebsinc.zohodesk.com/portal/en/newticket). Installation, configuration, and customization are not included with free distribution. Custom work is available separately at standard hourly rates.

## Credits and license

Originally created by Andrew Berezin, with portions from the Zen Cart and osCommerce teams. Modernized and maintained by Melanie Prough, [PRO-Webs, Inc.](https://pro-webs.net/).

Distributed under GPL-2.0-only without warranty. See [LICENSE](LICENSE).
