<?php

declare(strict_types=1);

require __DIR__ . '/../files/zc_plugins/RssFeed/v3.0.0/catalog/includes/functions/extra_functions/rss_feed_guard.php';

$request = prowebs_rss_normalize_request([
    'feed' => 'products',
    'limit' => '99999',
    'cPath' => '12_34',
    'utm_source' => str_repeat('bot', 100),
    'junk' => str_repeat('x', 1000),
], 250);

assert($request === ['feed' => 'products', 'limit' => 250, 'cPath' => '12_34']);
assert(prowebs_rss_normalize_request(['feed' => 'invalid'], 250)['feed'] === 'new_products');
assert(prowebs_rss_normalize_request(['products_id' => '-9'], 250)['products_id'] === 0);

$first = prowebs_rss_cache_key(['feed' => 'products', 'limit' => 10], 1, 'USD');
$second = prowebs_rss_cache_key(['limit' => 10, 'feed' => 'products'], 1, 'USD');
assert($first === $second);

$directory = sys_get_temp_dir() . '/rss-feed-test-' . getmypid();
assert(prowebs_rss_rate_limit($directory, 2));
assert(prowebs_rss_rate_limit($directory, 2));
assert(!prowebs_rss_rate_limit($directory, 2));

foreach ((array)glob($directory . '/rss-rate/*') as $file) {
    unlink($file);
}
@rmdir($directory . '/rss-rate');
@rmdir($directory);

echo "RSS Feed runtime checks passed.\n";
