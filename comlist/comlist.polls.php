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

if ($row['com_area'] == 'polls') {
  $res2 = Cot::$db->query("SELECT poll_text FROM cot_polls WHERE poll_id = " . $row['com_code'] . " LIMIT 1")->fetch(PDO::FETCH_COLUMN);
  $t->assign(array(
    'PAGE_ROW_POLL_TITLE' => $res2,
    'PAGE_ROW_POLL_URL' => cot_url('polls', 'id=' . $row['com_code']),
  ));
};
