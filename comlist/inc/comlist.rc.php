<?
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
* Comlist Plugin / Resources (misc)
*
* @package comlist
* @author Dmitri Beliavski
* @copyright (c) 2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

// Redefine Avatars
$R['comlist_avatar'] = '<img src="{$src}" alt="{$user}" class="img-fluid" />';
$R['comlist_default_avatar'] = '<img src="datas/defaultav/default.png" alt="'.$L['Avatar'].'" class="avatar img-fluid" />';
