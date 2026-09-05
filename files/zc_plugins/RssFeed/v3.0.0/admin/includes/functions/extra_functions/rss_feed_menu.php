<?php

if (!defined('BOX_CONFIGURATION_RSS_FEED')) {
    define('BOX_CONFIGURATION_RSS_FEED', 'RSS Feed');
}
if (!defined('FILENAME_CONFIGURATION')) {
    define('FILENAME_CONFIGURATION', 'configuration');
}

if (defined('IS_ADMIN_FLAG') && IS_ADMIN_FLAG === true && isset($db)) {
    $group = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'RSS_FEED_VERSION' LIMIT 1");
    $groupId = (int)($group->fields['configuration_group_id'] ?? 0);
    if ($groupId > 0) {
        $page = $db->Execute("SELECT page_key FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = 'configRSSFeed' LIMIT 1");
        if ($page->EOF) {
            zen_register_admin_page('configRSSFeed', 'BOX_CONFIGURATION_RSS_FEED', 'FILENAME_CONFIGURATION', 'gID=' . $groupId, 'configuration', 'Y', $groupId);
        }
    }
}
