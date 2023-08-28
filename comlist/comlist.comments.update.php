<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.send.new,comments.delete
[END_COT_EXT]
==================== */

/**
* Comlist Plugin / Comment add/delete action
*
* @package comlist
* @author Dmitri Beliavski
* @copyright (c) 2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

Cot::$cache && Cot::$cache->clear_realm(SEDBY_COMLIST_REALM, COT_CACHE_TYPE_ALL);
