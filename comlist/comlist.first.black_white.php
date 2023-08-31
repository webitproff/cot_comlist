<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comlist.first
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

if (strpos($extra, ';')) {
  $bw_mode = (substr($extra, 0, 1) == "+") ? 'array_white' : 'array_black';
  $bw_cats = substr($extra, 2);
  $bw_subs = substr($extra, 1, 1);
  $extra = '';
}
