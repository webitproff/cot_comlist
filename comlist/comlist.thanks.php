<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comlist.loop
[END_COT_EXT]
==================== */

/**
* Comlist Plugin / Loop (Polls support example)
*
* @package comlist
* @author Dmitri Beliavski
* @copyright (c) 2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['comlist']['thanks'] && cot_plugin_active('thanks')) {
	require_once cot_incfile('thanks', 'plug', 'api');
	$t->assign(array(
		'PAGE_ROW_THANKS_COUNT' => thanks_count('comments', $row['com_id']),
	));
}
