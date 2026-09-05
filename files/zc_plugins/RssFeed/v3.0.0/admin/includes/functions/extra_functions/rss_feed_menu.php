<?php

if (!defined('BOX_CONFIGURATION_RSS_FEED')) {
    define('BOX_CONFIGURATION_RSS_FEED', 'RSS Feed');
}
if (!defined('FILENAME_CONFIGURATION')) {
    define('FILENAME_CONFIGURATION', 'configuration');
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
