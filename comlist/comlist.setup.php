<?php
/* ====================
[BEGIN_COT_EXT]
Code=comlist
Name=[SEDBY] Comlist
Category=navigation-structure
Description=A widget displaying comments from any template of your site
Version=2.00b
Date=2023-06-20
Author=Dmitri Beliavski
Copyright=&copy; 2012-2023 Seditio.By
Notes=Check comlist.functions.php for parameters & info
Auth_guests=R
Lock_guests=12345A
Auth_members=R
Lock_members=12345A
Recommends_modules=page,polls
Requires_plugins=comments
[END_COT_EXT]
[BEGIN_COT_EXT_CONFIG]

useajax=00:separator:::
ajax=01:radio::0:Use AJAX
encrypt_ajax_urls=02:radio::0:Encrypt ajax URLs
encrypt_key=03:string::1234567890123456:Secret Key
encrypt_iv=04:string::1234567890123456:Initialization Vector

gentags=20:separator:::
page=21:radio::0:Generate Page tags
users=22:radio::0:Generate User tags

[END_COT_EXT_CONFIG]
==================== */

/**
* Comlist Plugin / Setup
*
* @package comlist
* @author Dmitri Beliavski
* @copyright (c) 2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');
