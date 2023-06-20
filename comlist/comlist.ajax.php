<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('comments', 'plug');

$tpl = cot_import('tpl','G','TXT');
$items = cot_import('items','G','INT');
$order = cot_import('order','G','TXT');
$area = cot_import('area','G','TXT');
$extra = cot_import('extra','G','TXT');
$group = cot_import('group','G','INT');
$pagination = cot_import('pagination','G','TXT');
$ajax_block = cot_import('ajax_block','G','TXT');
$cache_name = cot_import('cache_name','G','TXT');
$cache_ttl = cot_import('cache_ttl','G','INT');

ob_clean();
echo cot_comlist($tpl, $items, $order, $area, $extra, $group, $pagination, $ajax_block, $cache_name, $cache_ttl);
ob_flush();
exit;

// $period = cot_import('period','G','INT');
// $cachetime = cot_import('cachetime','G','INT');
