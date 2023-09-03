<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comlist.query
[END_COT_EXT]
==================== */

/**
* Comlist Plugin / Black & White Lists Support (requires Pagelist plugin)
*
* @package comlist
* @author Dmitri Beliavski
* @copyright (c) 2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('pagelist', 'plug');

if ($bw_mode) {
  $bw_cats = explode(';', $bw_cats);
  $bw_cond = cot_compilecats($bw_mode, $bw_cats, $bw_subs);
  $comlist_join_tables = " JOIN cot_pages ON (com_area='page' AND com_code=page_id) WHERE " . $bw_cond;
}
