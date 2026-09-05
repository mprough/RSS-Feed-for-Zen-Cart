<?php

declare(strict_types=1);

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected string $configPageKey = 'configRSSFeed';
    protected int $cgi;
    public string $pluginKey = 'RssFeed';
    public string $version = '3.0.4';

    private function normalizeStoredTextValues(): void
    {
        $keys = [
            'RSS_TITLE', 'RSS_DESCRIPTION', 'RSS_IMAGE', 'RSS_IMAGE_NAME',
            'RSS_COPYRIGHT', 'RSS_MANAGING_EDITOR', 'RSS_WEBMASTER', 'RSS_AUTHOR',
        ];
        $quotedKeys = "'" . implode("','", $keys) . "'";
        $rows = $this->dbConn->Execute(
            "SELECT configuration_key, configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ($quotedKeys)"
        );
        foreach ($rows as $row) {
            $value = (string)$row['configuration_value'];
            $decoded = $value;
            for ($pass = 0; $pass < 5; $pass++) {
                $next = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, defined('CHARSET') ? CHARSET : 'UTF-8');
                if ($next === $decoded) {
                    break;
                }
                $decoded = $next;
            }
            if ($decoded !== $value) {
                $this->executeInstallerSql(
                    "UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . zen_db_input($decoded) . "' WHERE configuration_key = '" . zen_db_input($row['configuration_key']) . "'"
                );
            }
        }
    }

    protected function getOrCreateGroupId(): int
    {
        $result = $this->dbConn->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'RSS_FEED_VERSION' LIMIT 1");
        if (!$result->EOF) {
            return (int)$result->fields['configuration_group_id'];
        }
        $result = $this->dbConn->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = 'RSS Feed' ORDER BY configuration_group_id LIMIT 1");
        if (!$result->EOF) {
            return (int)$result->fields['configuration_group_id'];
        }
        $this->executeInstallerSql("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (configuration_group_title, configuration_group_description, sort_order, visible) VALUES ('RSS Feed', 'Configure RSS feed output, caching and request controls', 0, 1)");
        $cgi = (int)$this->dbConn->Insert_ID();
        $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = $cgi WHERE configuration_group_id = $cgi");
        return $cgi;
    }

    protected function executeInstall(): bool
    {
        $this->cgi = $this->getOrCreateGroupId();
        if ($this->cgi < 1) {
            return false;
        }
        $rows = [
            ['Installed version', 'RSS_FEED_VERSION', '3.0.4', 'Installed RSS Feed version.', 0, "zen_cfg_select_option(array('3.0.4'),"],
            ['Feed links', 'RSS_FEED_LINKS', '', 'Open a feed to test it, or copy its address for feed readers and shop integrations.', 5, 'zen_cfg_rss_feed_links('],
            ['RSS title', 'RSS_TITLE', '', 'RSS title. The store name is used when empty.', 10, null],
            ['RSS description', 'RSS_DESCRIPTION', '', 'RSS channel description.', 20, null],
            ['RSS image', 'RSS_IMAGE', '', 'Optional GIF, JPEG or PNG channel image. Enter a full https:// URL, or a path relative to the store images directory, such as rss/channel.png. Leave blank for no channel image.', 30, null],
            ['RSS image name', 'RSS_IMAGE_NAME', '', 'RSS image name. The store name is used when empty.', 40, null],
            ['RSS copyright', 'RSS_COPYRIGHT', '', 'Copyright owner. The store owner is used when empty.', 50, null],
            ['RSS managing editor', 'RSS_MANAGING_EDITOR', '', 'Optional public contact in email@example.com (Name) format. RSS readers and bots can see and harvest this address. Leave blank to omit it.', 60, null],
            ['RSS webmaster', 'RSS_WEBMASTER', '', 'Optional public contact in email@example.com (Name) format. RSS readers and bots can see and harvest this address. Leave blank to omit it.', 70, null],
            ['RSS author', 'RSS_AUTHOR', '', 'Optional public item author in email@example.com (Name) format. RSS readers and bots can see and harvest this address. Leave blank to omit it.', 80, null],
            ['Home page feed', 'RSS_HOMEPAGE_FEED', 'new_products', 'Feed linked from the home page.', 90, "zen_cfg_select_option(array('news','new_products','upcoming','featured','specials','products','categories'),"],
            ['Default feed', 'RSS_DEFAULT_FEED', 'new_products', 'Feed used when no valid feed is requested.', 100, "zen_cfg_select_option(array('news','new_products','upcoming','featured','specials','products','categories'),"],
            ['Strip tags', 'RSS_STRIP_TAGS', 'false', 'Remove HTML from item descriptions.', 110, "zen_cfg_select_option(array('true','false'),"],
            ['Generate descriptions', 'RSS_ITEMS_DESCRIPTION', 'true', 'Include item descriptions.', 120, "zen_cfg_select_option(array('true','false'),"],
            ['Description length', 'RSS_ITEMS_DESCRIPTION_MAX_LENGTH', '0', 'Maximum description characters. Use 0 for no limit.', 130, null],
            ['Time to live', 'RSS_TTL', '1440', 'Suggested reader refresh interval in minutes.', 140, null],
            ['Default product limit', 'RSS_PRODUCTS_LIMIT', '100', 'Default number of products returned.', 150, null],
            ['Maximum request limit', 'RSS_MAX_ITEMS', '250', 'Hard cap for the public limit parameter. Maximum 1000.', 160, null],
            ['Requests per minute', 'RSS_REQUESTS_PER_MINUTE', '30', 'Maximum feed requests per IP address each minute. The recommended default is 30. Use 0 to disable only when another trusted layer provides throttling.', 170, null],
            ['Feed cache time', 'RSS_CACHE_TIME', '15', 'Server cache lifetime in minutes. Minimum effective HTTP cache time is one minute.', 180, null],
            ['Add product image', 'RSS_PRODUCTS_DESCRIPTION_IMAGE', 'true', 'Add the product image to its description.', 200, "zen_cfg_select_option(array('true','false'),"],
            ['Add buy now button', 'RSS_PRODUCTS_DESCRIPTION_BUYNOW', 'true', 'Add a buy now button to the product description.', 210, "zen_cfg_select_option(array('true','false'),"],
            ['Categories for products', 'RSS_PRODUCTS_CATEGORIES', 'master', 'Use master or all category assignments.', 220, "zen_cfg_select_option(array('master','all'),"],
            ['Generate product price', 'RSS_PRODUCTS_PRICE', 'true', 'Include product price.', 300, "zen_cfg_select_option(array('true','false'),"],
            ['Generate product ID', 'RSS_PRODUCTS_ID', 'true', 'Include product ID.', 310, "zen_cfg_select_option(array('true','false'),"],
            ['Generate product weight', 'RSS_PRODUCTS_WEIGHT', 'true', 'Include product weight.', 320, "zen_cfg_select_option(array('true','false'),"],
            ['Generate product brand', 'RSS_PRODUCTS_BRAND', 'true', 'Include manufacturer name.', 330, "zen_cfg_select_option(array('true','false'),"],
            ['Generate product currency', 'RSS_PRODUCTS_CURRENCY', 'true', 'Include currency code.', 340, "zen_cfg_select_option(array('true','false'),"],
            ['Generate product availability', 'RSS_PRODUCTS_QUANTITY', 'true', 'Include in stock or out of stock availability without exposing the exact quantity.', 350, "zen_cfg_select_option(array('true','false'),"],
            ['Generate product model', 'RSS_PRODUCTS_MODEL', 'true', 'Include product model.', 360, "zen_cfg_select_option(array('true','false'),"],
            ['Generate product rating', 'RSS_PRODUCTS_RATING', 'true', 'Include product rating.', 370, "zen_cfg_select_option(array('true','false'),"],
            ['Generate product images', 'RSS_PRODUCTS_IMAGES', 'true', 'Include product image links.', 380, "zen_cfg_select_option(array('true','false'),"],
            ['Product image size', 'RSS_DEFAULT_IMAGE_SIZE', 'large', 'Image size used in product image links.', 390, "zen_cfg_select_option(array('small','medium','large'),"],
        ];
        foreach ($rows as [$title, $key, $value, $description, $sort, $setFunction]) {
            $setSql = $setFunction === null ? 'NULL' : "'" . addslashes($setFunction) . "'";
            $this->executeInstallerSql("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('" . addslashes($title) . "', '" . addslashes($key) . "', '" . addslashes($value) . "', '" . addslashes($description) . "', {$this->cgi}, " . (int)$sort . ", $setSql, NOW())");
            $this->executeInstallerSql(
                "UPDATE " . TABLE_CONFIGURATION
                . " SET configuration_title = '" . addslashes($title)
                . "', configuration_description = '" . addslashes($description)
                . "', configuration_group_id = {$this->cgi}, sort_order = " . (int)$sort
                . ", set_function = $setSql WHERE configuration_key = '" . addslashes($key) . "'"
            );
        }
        $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '30' WHERE configuration_key = 'RSS_REQUESTS_PER_MINUTE' AND configuration_value = '120'");
        $this->normalizeStoredTextValues();
        $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '3.0.4', set_function = 'zen_cfg_select_option(array(\\'3.0.4\\'),' WHERE configuration_key = 'RSS_FEED_VERSION'");
        zen_deregister_admin_pages([$this->configPageKey]);
        zen_register_admin_page($this->configPageKey, 'BOX_CONFIGURATION_RSS_FEED', 'FILENAME_CONFIGURATION', "gID={$this->cgi}", 'configuration', 'Y', $this->cgi);
        return true;
    }

    protected function executeUpgrade(...$args): bool
    {
        return $this->executeInstall();
    }

    protected function executeUninstall(): bool
    {
        $result = $this->dbConn->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'RSS_FEED_VERSION' LIMIT 1");
        $cgi = (int)($result->fields['configuration_group_id'] ?? 0);
        $this->executeInstallerSql("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = 'configRSSFeed'");
        $ownedKeys = array_column([
            ['x', 'RSS_FEED_VERSION'], ['x', 'RSS_FEED_LINKS'], ['x', 'RSS_TITLE'], ['x', 'RSS_DESCRIPTION'], ['x', 'RSS_IMAGE'],
            ['x', 'RSS_IMAGE_NAME'], ['x', 'RSS_COPYRIGHT'], ['x', 'RSS_MANAGING_EDITOR'], ['x', 'RSS_WEBMASTER'],
            ['x', 'RSS_AUTHOR'], ['x', 'RSS_HOMEPAGE_FEED'], ['x', 'RSS_DEFAULT_FEED'], ['x', 'RSS_STRIP_TAGS'],
            ['x', 'RSS_ITEMS_DESCRIPTION'], ['x', 'RSS_ITEMS_DESCRIPTION_MAX_LENGTH'], ['x', 'RSS_TTL'],
            ['x', 'RSS_PRODUCTS_LIMIT'], ['x', 'RSS_MAX_ITEMS'], ['x', 'RSS_REQUESTS_PER_MINUTE'], ['x', 'RSS_CACHE_TIME'],
            ['x', 'RSS_PRODUCTS_DESCRIPTION_IMAGE'], ['x', 'RSS_PRODUCTS_DESCRIPTION_BUYNOW'], ['x', 'RSS_PRODUCTS_CATEGORIES'],
            ['x', 'RSS_PRODUCTS_PRICE'], ['x', 'RSS_PRODUCTS_ID'], ['x', 'RSS_PRODUCTS_WEIGHT'], ['x', 'RSS_PRODUCTS_BRAND'],
            ['x', 'RSS_PRODUCTS_CURRENCY'], ['x', 'RSS_PRODUCTS_QUANTITY'], ['x', 'RSS_PRODUCTS_MODEL'],
            ['x', 'RSS_PRODUCTS_RATING'], ['x', 'RSS_PRODUCTS_IMAGES'], ['x', 'RSS_DEFAULT_IMAGE_SIZE'],
        ], 1);
        $quotedKeys = "'" . implode("','", $ownedKeys) . "'";
        $this->executeInstallerSql("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ($quotedKeys)");
        if ($cgi > 0) {
            $remaining = $this->dbConn->Execute("SELECT configuration_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = $cgi LIMIT 1");
            if ($remaining->EOF) {
                $this->executeInstallerSql("DELETE FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id = $cgi");
            }
        }
        return true;
    }
}
