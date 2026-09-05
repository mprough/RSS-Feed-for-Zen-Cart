<?php

/**
 * Emit the generated RSS document instead of an HTML storefront page.
 *
 * @copyright Copyright 2026 PRO-Webs, Inc.
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL-2.0-only
 */

$rss->rss_feed_out();
require DIR_WS_INCLUDES . 'application_bottom.php';
zen_exit();
