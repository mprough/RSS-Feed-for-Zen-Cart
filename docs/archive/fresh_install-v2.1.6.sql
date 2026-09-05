SET @configuration_group_id=0;
SELECT (@configuration_group_id:=configuration_group_id) FROM configuration_group WHERE configuration_group_title= 'RSS Feed' LIMIT 1;
DELETE FROM configuration WHERE configuration_group_id = @configuration_group_id AND configuration_group_id != 0;
DELETE FROM configuration_group WHERE configuration_group_id = @configuration_group_id AND configuration_group_id != 0;
DELETE FROM configuration WHERE configuration_key = 'RSS_FEED_VERSION';

INSERT INTO configuration_group (configuration_group_id, configuration_group_title, configuration_group_description, sort_order, visible) VALUES (NULL, 'RSS Feed', 'RSS Feed', '1', '1');
SET @configuration_group_id=last_insert_id();
UPDATE configuration_group SET sort_order = @configuration_group_id WHERE configuration_group_id = @configuration_group_id;

INSERT INTO configuration (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function) VALUES 
(NULL, 'RSS Title', 'RSS_TITLE', '', 'RSS Title (if empty use Store Name)', @configuration_group_id, 1, NOW(), NULL, NULL),
(NULL, 'RSS Description', 'RSS_DESCRIPTION', '', 'RSS description', @configuration_group_id, 2, NOW(), NULL, NULL),
(NULL, 'RSS Image', 'RSS_IMAGE', '', 'A GIF, JPEG or PNG image that represents the channel', @configuration_group_id, 3, NOW(), NULL, NULL),
(NULL, 'RSS Image Name', 'RSS_IMAGE_NAME', '', 'RSS Image Name (if empty use Store Name)', @configuration_group_id, 4, NOW(), NULL, NULL),
(NULL, 'RSS Copyright', 'RSS_COPYRIGHT', '', 'RSS Copyright (if empty use Store Owner)', @configuration_group_id, 5, NOW(), NULL, NULL),
(NULL, 'RSS Managing Editor', 'RSS_MANAGING_EDITOR', '', 'RSS Managing Editor (if empty use Store Owner Email Address and Store Owner)', @configuration_group_id, 6, NOW(), NULL, NULL),
(NULL, 'RSS Webmaster', 'RSS_WEBMASTER', '', 'RSS Webmaster (if empty use Store Owner Email Address and Store Owner)', @configuration_group_id, 7, NOW(), NULL, NULL),
(NULL, 'RSS Author', 'RSS_AUTHOR', '', 'RSS Author (if empty use Store Owner Email Address and Store Owner)', @configuration_group_id, 8, NOW(), NULL, NULL),

(NULL, 'RSS Home Page Feed', 'RSS_HOMEPAGE_FEED', 'new_products', 'RSS Home Page Feed', @configuration_group_id, 8, NOW(), NULL, 'zen_cfg_select_option(array(\'news\', \'new_products\', \'upcoming\', \'featured\', \'specials\', \'products\', \'categories\'),'),
(NULL, 'RSS Default Feed', 'RSS_DEFAULT_FEED', 'new_products', 'RSS Default Feed', @configuration_group_id, 8, NOW(), NULL, 'zen_cfg_select_option(array(\'news\', \'new_products\', \'upcoming\', \'featured\', \'specials\', \'products\', \'categories\'),'),

(NULL, 'Strip tags', 'RSS_STRIP_TAGS', 'false', 'Strip tags', @configuration_group_id, 20, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Generate Descriptions', 'RSS_ITEMS_DESCRIPTION', 'true', 'Generate Descriptions', @configuration_group_id, 21, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Descriptions Length', 'RSS_ITEMS_DESCRIPTION_MAX_LENGTH', '0', 'How many characters in description (0 for no limit)', @configuration_group_id, 22, NOW(), NULL, NULL),
(NULL, 'Time to live', 'RSS_TTL', '1440', 'Time to live - time after reader should refresh the info in minutes', @configuration_group_id, 23, NOW(), NULL, NULL),

(NULL, 'Default Products Limit', 'RSS_PRODUCTS_LIMIT', '100', 'Default Limit to Products Feed', @configuration_group_id, 31, NOW(), NULL, NULL),
(NULL, 'Add Product image', 'RSS_PRODUCTS_DESCRIPTION_IMAGE', 'true', 'Add product image to product description tag', @configuration_group_id, 32, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Add "buy now" button', 'RSS_PRODUCTS_DESCRIPTION_BUYNOW', 'true', 'Add "buy now" button to product description tag', @configuration_group_id, 33, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),

(NULL, 'Categories for Products', 'RSS_PRODUCTS_CATEGORIES', 'master', 'Use \'all\' or only \'master\' Categories for Products when specified cPath parameter', @configuration_group_id, 23, NOW(), NULL, 'zen_cfg_select_option(array(\'master\', \'all\'),'),

(NULL, 'Generate Products Price', 'RSS_PRODUCTS_PRICE', 'true', 'Generate Products Price (extended tag &lt;g:price&gt;)', @configuration_group_id, 90, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Generate Products ID', 'RSS_PRODUCTS_ID', 'true', 'Generate Products ID (extended tag &lt;g:id&gt;)', @configuration_group_id, 91, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Generate Products Weight', 'RSS_PRODUCTS_WEIGHT', 'true', 'Generate Products Weight (extended tag &lt;g:weight&gt;)', @configuration_group_id, 93, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Generate Products Brand', 'RSS_PRODUCTS_BRAND', 'true', 'Generate Products Manufacturers Name (extended tag &lt;g:brand&gt;)', @configuration_group_id, 94, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Generate Products Currency', 'RSS_PRODUCTS_CURRENCY', 'true', 'Generate Products Currency (extended tag &lt;g:currency&gt;)', @configuration_group_id, 95, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Generate Products Quantity', 'RSS_PRODUCTS_QUANTITY', 'true', 'Generate Products Quantity (extended tag &lt;g:quantity&gt;)', @configuration_group_id, 96, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Generate Products Model', 'RSS_PRODUCTS_MODEL', 'true', 'Generate Products Model (extended tag &lt;g:model_number&gt;)', @configuration_group_id, 97, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Generate Products Rating', 'RSS_PRODUCTS_RATING', 'true', 'Generate Products Rating (extended tag &lt;g:rating&gt;)', @configuration_group_id, 98, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Generate Products Images', 'RSS_PRODUCTS_IMAGES', 'true', 'Generate Products Images (extended tag &lt;g:image_link&gt;)', @configuration_group_id, 98, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
(NULL, 'Generate Products Images Size', 'RSS_DEFAULT_IMAGE_SIZE', 'large', 'What image size Generate (extended tag &lt;g:image_link&gt;)', @configuration_group_id, 99, NOW(), NULL, 'zen_cfg_select_option(array(\'small\', \'medium\', \'large\'),'),
(NULL, 'Feed Cache Time', 'RSS_CACHE_TIME', '10', 'Cache Time (in min). If you don\'t want caching, set it to 0', @configuration_group_id, 200, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),');

SELECT @t4:=configuration_group_id FROM configuration_group WHERE configuration_group_title = 'RSS Feed';
INSERT INTO admin_pages (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES ('configRSSFeed','BOX_CONFIGURATION_RSS_FEED','FILENAME_CONFIGURATION',CONCAT('gID=',@t4), 'configuration', 'Y', @t4);