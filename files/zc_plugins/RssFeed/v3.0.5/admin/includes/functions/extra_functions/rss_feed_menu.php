<?php

if (!defined('BOX_CONFIGURATION_RSS_FEED')) {
    define('BOX_CONFIGURATION_RSS_FEED', 'RSS Feed');
}
if (!defined('FILENAME_CONFIGURATION')) {
    define('FILENAME_CONFIGURATION', 'configuration');
}

if (!function_exists('zen_cfg_rss_feed_links')) {
    function zen_cfg_rss_feed_links(mixed $currentValue = '', string $fieldName = ''): string
    {
        $catalogServer = defined('HTTP_CATALOG_SERVER') ? HTTP_CATALOG_SERVER : HTTP_SERVER;
        $baseUrl = rtrim($catalogServer . DIR_WS_CATALOG, '/') . '/index.php?main_page=rss_feed&feed=';
        $feeds = [
            'New products' => 'new_products',
            'Random new product' => 'new_products_random',
            'Best sellers' => 'best_sellers',
            'Random best seller' => 'best_sellers_random',
            'Specials' => 'specials',
            'Random special' => 'specials_random',
            'Featured products' => 'featured',
            'Random featured product' => 'featured_random',
            'Upcoming products' => 'upcoming',
            'All products' => 'products',
            'Categories' => 'categories',
        ];

        $links = [];
        foreach ($feeds as $label => $feed) {
            $url = htmlspecialchars($baseUrl . rawurlencode($feed), ENT_QUOTES, CHARSET);
            $links[] = '<a href="' . $url . '" target="_blank" rel="noopener">' . htmlspecialchars($label, ENT_QUOTES, CHARSET) . '</a>';
        }
        return '<div class="rss-feed-links">' . implode('<br>', $links) . '</div>';
    }
}

$rssFeedInstalled = defined('RSS_FEED_VERSION');
$rssFeedGroupId = 0;
if (defined('IS_ADMIN_FLAG') && IS_ADMIN_FLAG === true && isset($db)) {
    $rssFeedVersionRow = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'RSS_FEED_VERSION' LIMIT 1");
    if (!$rssFeedVersionRow->EOF) {
        $rssFeedInstalled = true;
        $rssFeedGroupId = (int)$rssFeedVersionRow->fields['configuration_group_id'];
    }
}

if (
    function_exists('zen_register_admin_page')
    && function_exists('zen_page_key_exists')
    && $rssFeedInstalled
    && $rssFeedGroupId > 0
    && !zen_page_key_exists('configRSSFeed')
) {
    zen_register_admin_page('configRSSFeed', 'BOX_CONFIGURATION_RSS_FEED', 'FILENAME_CONFIGURATION', 'gID=' . $rssFeedGroupId, 'configuration', 'Y', $rssFeedGroupId);
}

unset($rssFeedInstalled, $rssFeedGroupId, $rssFeedVersionRow);
