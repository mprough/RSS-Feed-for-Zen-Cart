<?php

declare(strict_types=1);

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

return [
    'pluginVersion' => 'v3.0.4',
    'pluginName' => 'RSS Feed',
    'pluginDescription' => 'Cached, throttled RSS 2.0 product and category feeds for Zen Cart.',
    'pluginAuthor' => 'PRO-Webs.net',
    'pluginId' => 0,
    'zcVersions' => ['v200', 'v210', 'v220'],
    'changelog' => 'https://github.com/mprough/RSS-Feed-for-Zen-Cart/blob/main/CHANGELOG.md',
    'github_repo' => 'https://github.com/mprough/RSS-Feed-for-Zen-Cart',
    'pluginGroups' => [],
];
