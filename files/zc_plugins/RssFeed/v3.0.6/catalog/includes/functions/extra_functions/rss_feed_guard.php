<?php

/**
 * Request controls for RSS Feed.
 *
 * @copyright Copyright 2026 PRO-Webs, Inc.
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL-2.0-only
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

function prowebs_rss_normalize_request(array $request, int $maximumItems): array
{
    $feeds = [
        'categories', 'products', 'new_products', 'new_products_random',
        'best_sellers', 'best_sellers_random', 'specials', 'specials_random',
        'featured', 'featured_random', 'upcoming', 'upcoming_random', 'news',
    ];
    $feed = isset($request['feed']) ? (string)$request['feed'] : (defined('RSS_DEFAULT_FEED') ? RSS_DEFAULT_FEED : 'new_products');
    $clean = ['feed' => in_array($feed, $feeds, true) ? $feed : 'new_products'];
    $maximumItems = max(1, min(1000, $maximumItems));

    if (isset($request['limit'])) {
        $clean['limit'] = max(1, min($maximumItems, (int)$request['limit']));
    }
    if (isset($request['products_id'])) {
        $clean['products_id'] = max(0, (int)$request['products_id']);
    }
    if (isset($request['products_model'])) {
        $clean['products_model'] = substr(trim((string)$request['products_model']), 0, 64);
    }
    if (isset($request['cPath']) && preg_match('/^\d+(?:_\d+)*$/', (string)$request['cPath'])) {
        $clean['cPath'] = substr((string)$request['cPath'], 0, 255);
    }
    if (isset($request['imgsize']) && in_array($request['imgsize'], ['small', 'medium', 'large'], true)) {
        $clean['imgsize'] = $request['imgsize'];
    }
    return $clean;
}

function prowebs_rss_cache_key(array $request, int $languageId, string $currency): string
{
    ksort($request);
    return http_build_query($request, '', '&', PHP_QUERY_RFC3986) . '|language=' . $languageId . '|currency=' . $currency;
}

/**
 * Return a standards-compatible RSS contact or false when it should be omitted.
 */
function prowebs_rss_contact_value(string $value): string|false
{
    $value = trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($value === '') {
        return false;
    }

    if (preg_match('/^([^<>]+?)\s*<([^<>\s]+@[^<>\s]+)>$/', $value, $matches)) {
        $value = trim($matches[2]) . ' (' . trim($matches[1]) . ')';
    }

    if (!preg_match('/^([^\s()]+@[^\s()]+)(?:\s*\(([^()]*)\))?$/', $value, $matches)) {
        return false;
    }
    if (filter_var($matches[1], FILTER_VALIDATE_EMAIL) === false) {
        return false;
    }

    $name = trim($matches[2] ?? '');
    return $matches[1] . ($name === '' ? '' : ' (' . $name . ')');
}

/**
 * Resolve an optional channel image from a full URL or the catalog images directory.
 */
function prowebs_rss_channel_image_url(string $value): string|false
{
    $value = trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($value === '') {
        return false;
    }

    $urlParts = parse_url($value);
    $path = (string)($urlParts['path'] ?? '');
    if (!preg_match('/\.(?:gif|jpe?g|png)$/i', $path)) {
        return false;
    }
    if (isset($urlParts['scheme'])) {
        return in_array(strtolower((string)$urlParts['scheme']), ['http', 'https'], true) ? $value : false;
    }

    $relative = ltrim(str_replace('\\', '/', $value), '/');
    $imagesPath = trim(str_replace('\\', '/', DIR_WS_IMAGES), '/');
    if ($imagesPath !== '' && str_starts_with($relative, $imagesPath . '/')) {
        $relative = substr($relative, strlen($imagesPath) + 1);
    }
    if ($relative === '' || str_contains($relative, '../')) {
        return false;
    }
    if (!is_file(rtrim(DIR_FS_CATALOG, '/\\') . '/' . $imagesPath . '/' . $relative)) {
        return false;
    }

    $segments = array_map('rawurlencode', explode('/', $relative));
    return rtrim(HTTP_SERVER . DIR_WS_CATALOG, '/') . '/' . $imagesPath . '/' . implode('/', $segments);
}

function prowebs_rss_rate_limit(string $cacheDirectory, int $requestsPerMinute): bool
{
    if ($requestsPerMinute < 1) {
        return true;
    }
    $address = (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $bucket = hash('sha256', $address . '|' . gmdate('YmdHi'));
    $directory = rtrim($cacheDirectory, '/\\') . '/rss-rate';
    if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
        return true;
    }
    if (mt_rand(1, 20) === 1) {
        $checked = 0;
        foreach ((array)@scandir($directory) as $file) {
            if ($file === '.' || $file === '..' || ++$checked > 50) {
                continue;
            }
            $path = $directory . '/' . $file;
            if (is_file($path) && (time() - (int)filemtime($path)) > 180) {
                @unlink($path);
            }
        }
    }
    $handle = @fopen($directory . '/' . $bucket, 'c+');
    if (!is_resource($handle) || !flock($handle, LOCK_EX)) {
        return true;
    }
    $count = (int)trim((string)stream_get_contents($handle));
    $count++;
    rewind($handle);
    ftruncate($handle, 0);
    fwrite($handle, (string)$count);
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
    return $count <= $requestsPerMinute;
}
