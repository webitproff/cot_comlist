<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comlist.query
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('pagelist', 'plug');

if ($bw_mode && Cot::$cfg['plugin']['comlist']['page']) {
  $bw_cats = explode(';', $bw_cats);
  $sql_cond2 = cot_compilecats($bw_mode, $bw_cats, $bw_subs);
  $sql_cond = ($sql_cond == '') ? " WHERE " . $sql_cond2 : $sql_cond . " AND " .$sql_cond2;
}
