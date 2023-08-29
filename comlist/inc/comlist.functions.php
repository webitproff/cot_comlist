<?php
/**
* Comlist Plugin / Functions
*
* @package comlist
* @author Dmitri Beliavski
* @copyright (c) 2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

// define globals
define('SEDBY_COMLIST_REALM', '[SEDBY] Comlist');

require_once cot_incfile('comments', 'plug');
require_once cot_incfile('page', 'module');

/**
 * Encrypts or decrypts string
 *
 * @param		string	$action	01.	Action (encrypt || decrypt)
 * @param		string	$string	02.	String to encrypt / decrypt
 * @param		string	$key		03. Secret key
 * @param		string	$iv			04. Initialization vector
 * @param		string	$method	05. Encryption method (optional)
 * @return	string					Encrypted / decrypted string
 */
 if (!function_exists('cot_encrypt_decrypt')) {
	function cot_encrypt_decrypt($action, $string, $key, $iv, $method = '') {
		$method = empty($method) ? 'AES-256-CBC' : $method;
		$key = hash('sha256', $key);
		$iv = substr(hash('sha256', $iv), 0, 16);

		if ($action == 'encrypt') {
			$output = openssl_encrypt($string, $method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		elseif ($action == 'decrypt') {
			$output = base64_decode($string);
			$output = openssl_decrypt($output, $method, $key, 0, $iv);
		}
		return $output;
	}
 }

/**
 * Generates comment list widget
 *
 * @param  string  $tpl					01. Template code
 * @param  integer $items				02. Number of items to show. 0 - show all items
 * @param  string  $order				03. Sorting order (SQL)
 * @param  string  $extra				04. Custom selection filter (SQL)
 * @param  integer $group				05. Group comments by code
 * @param  string  $pagination	06. Pagination parameter name for the URL, e.g. 'pcm'. Make sure it does not conflict with other paginations. Leave it empty to turn off pagination
 * @param  string  $ajax_block	07. DOM block ID for ajax pagination
 * @param  integer $cache_name	08. Cache name
 * @param  integer $cache_ttl		09. Cache TTL
 * @return string								Parsed HTML
 */
function cot_comlist($tpl = 'comlist', $items = 0, $order = '', $extra = '', $group = 0, $pagination = '', $ajax_block = '', $cache_name = '', $cache_ttl = '') {

	$cache_name = (!empty($cache_name)) ? str_replace(' ', '_', $cache_name) : '';

	if (Cot::$cache && !empty($cache_name) && Cot::$cache->db->exists($cache_name, SEDBY_COMLIST_REALM))
		$output = Cot::$cache->db->get($cache_name, SEDBY_COMLIST_REALM);
	else {

		/* === Hook === */
		foreach (array_merge(cot_getextplugins('comlist.first')) as $pl) {
			include $pl;
		}
		/* ===== */

		if (Cot::$cfg['plugin']['comlist']['encrypt_ajax_urls']) {
			$h = $tpl.','.$items.','.$order.','.$extra.','.$group.','.$pagination.','.$ajax_block.','.$cache_name.','.$cache_ttl;
			$h = cot_encrypt_decrypt('encrypt', $h, Cot::$cfg['plugin']['comlist']['encrypt_key'], Cot::$cfg['plugin']['comlist']['encrypt_iv']);
			$h = str_replace('=', '', $h);
		}

		$db_com = Cot::$db->com;

		// Display the items
		$t = new XTemplate(cot_tplfile($tpl, 'plug'));

		// Get pagination number if necessary
		if (!empty($pagination))
			list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);
		else
			$d = 0;

		// Compile items number
		$sql_limit = ($items > 0) ? "LIMIT $d, $items" : "";

		// Compile order
		$sql_order = empty($order) ? "" : "ORDER BY $order";

		// Compile group
		$sql_group = ($group == 1) ? "c.com_id = (SELECT MAX(com_id) FROM " .$db_com . " AS c2 WHERE c2.com_code = c.com_code)" : '';

		// Compile extra SQL condition
		$sql_extra = (empty($extra)) ? "" : $extra;

		if (!empty($sql_group) && !empty($sql_extra))
			$sql_cond = "WHERE " . $sql_group . " AND " . $sql_extra;
		elseif (!empty($sql_group) && empty($sql_extra))
			$sql_cond = "WHERE " . $sql_group;
		elseif (empty($sql_group) && !empty($sql_extra))
			$sql_cond = "WHERE " . $sql_extra;
		else
			$sql_cond = "";

		$comlist_join_columns = "";
		$comlist_join_tables = "";

		// Page Module Support
		if (Cot::$cfg['plugin']['comlist']['page'] == 1) {
			$db_pages = Cot::$db->pages;
			$comlist_join_columns .= " , p.* ";
			$comlist_join_tables .= "LEFT JOIN $db_pages AS p ON (c.com_code = p.page_id AND c.com_area = 'page')";
		}

		// Users Module Support
		if (Cot::$cfg['plugin']['comlist']['users'] == 1) {
			$db_users = Cot::$db->users;
			$comlist_join_columns .= " , u.* ";
			$comlist_join_tables .= "LEFT JOIN $db_users AS u ON (u.user_id = c.com_authorid)";
		}

		/* === Hook === */
		foreach (cot_getextplugins('comlist.query') as $pl) {
			include $pl;
		}
		/* ===== */

		$query = "SELECT c.* $comlist_join_columns FROM $db_com AS c $comlist_join_tables $sql_cond $sql_order $sql_limit";
		$res = Cot::$db->query($query);
		$jj = 1;

		/* === Hook - Part 1 === */
		$extp = cot_getextplugins('comlist.loop');
		/* ===== */

		while ($row = $res->fetch()) {
			if (Cot::$cfg['plugin']['comlist']['page'] == 1 && $row['com_area'] == 'page') {
				if (empty($row['page_id']) && isset(Cot::$structure[$row['com_area']][$row['com_code']])) {
					// Category comments
					$cat = Cot::$structure[$row['com_area']][$row['com_code']];
					$link_params = array('c' => $row['com_code']);
					$t->assign(array(
						'PAGE_ROW_CAT_TITLE' => htmlspecialchars($cat['title']),
						'PAGE_ROW_CAT_URL' => cot_url('page', $link_params),
					));
				}
				else {
					// Page comments
					$t->assign(cot_generate_pagetags($row, 'PAGE_ROW_'));
					$link_params = array('c' => $row['page_cat']);
					empty($row['page_alias']) ? $link_params['id'] = $row['page_id'] : $link_params['al'] = $row['page_alias'];
				}
			}

			if (Cot::$cfg['plugin']['comlist']['users'] == 1)
				$t->assign(cot_generate_usertags($row, 'PAGE_ROW_AUTHOR_'));

			$com_text = cot_parse($row['com_text'], Cot::$cfg['plugin']['comments']['markup']);

			$t->assign(array(
				'PAGE_ROW_COMLIST_NUM' => $jj,
				'PAGE_ROW_COMLIST_ODDEVEN' => cot_build_oddeven($jj),

				'PAGE_ROW_COMLIST_CODE_IS_NUMERIC' => is_numeric($row['com_code']) ? TRUE : FALSE,

				'PAGE_ROW_COMLIST_ID' => $row['com_id'],
				'PAGE_ROW_COMLIST_CODE' => $row['com_code'],
				'PAGE_ROW_COMLIST_AREA' => $row['com_area'],

				'PAGE_ROW_COMLIST_AUTHORNAME' => htmlspecialchars($row['com_author']),
				'PAGE_ROW_COMLIST_AUTHORID' => $row['com_authorid'],
				'PAGE_ROW_COMLIST_AUTHORIP' => $row['com_authorip'],
				'PAGE_ROW_COMLIST_AUTHOR' => cot_build_user($row['com_authorid'], htmlspecialchars($row['com_author'])),

				'PAGE_ROW_COMLIST_TEXT' => $com_text,
				'PAGE_ROW_COMLIST_TEXT_PLAIN' => strip_tags($com_text),

				'PAGE_ROW_COMLIST_DATE' => cot_date('datetime_medium', $row['com_date']),
				'PAGE_ROW_COMLIST_DATE_STAMP' => $row['com_date']
			));

			if ((Cot::$usr['id'] > 0 && $row['com_authorid'] != Cot::$usr['id']) && (Cot::$usr['lastvisit'] < $row['com_date'])) {
				$t->assign('PAGE_ROW_NEW', Cot::$L['New']);
				$jn++;
			}
			else
				$t->assign('PAGE_ROW_NEW', '');

			/* === Hook - Part 2 === */
			foreach ($extp as $pl) {
				include $pl;
			}
			/* ===== */

			$t->parse("MAIN.PAGE_ROW");
			$jj++;
		}

		$t->assign('COMLIST_NEWCOMMENTS', $jn);

		// Render pagination if needed
		if (!empty($pagination)) {

			$totalitems = Cot::$db->query("SELECT COUNT(*) FROM $db_com $sql_cond")->fetchColumn();

			$url_area = defined('COT_PLUG') ? 'plug' : Cot::$env['ext'];

			if (defined('COT_LIST')) {
				global $list_url_path;
				$url_params = $list_url_path;
			}
			elseif (defined('COT_PAGES')) {
				global $al, $id, $pag;
				$url_params = empty($al) ? array('c' => $pag['page_cat'], 'id' => $id) :  array('c' => $pag['page_cat'], 'al' => $al);
			}
			elseif(defined('COT_USERS')) {
				global $m;
				$url_params = empty($m) ? array() :  array('m' => $m);
			}
			elseif (defined('COT_ADMIN')) {
				$url_area = 'admin';
				global $m, $p, $a;
				$url_params = array('m' => $m, 'p' => $p, 'a' => $a);
			}
			else {
				$url_params = array();
			}

			$url_params[$pagination] = $durl;

			if ((Cot::$cfg['turnajax'] == 1) && (Cot::$cfg['plugin']['comlist']['ajax'] == 1) && !empty($ajax_block)) {
				$ajax_mode = true;
				$ajax_plug = 'plug';
				if (Cot::$cfg['plugin']['comlist']['encrypt_ajax_urls'] == 1)
					$ajax_plug_params = "r=comlist&h=$h";
				else
					$ajax_plug_params = "r=comlist&tpl=$tpl&items=$items&order=$order&extra=$extra&group=$group&pagination=$pagination&ajax_block=$ajax_block&cache_name=$cache_name&cache_ttl=$cache_ttl";
			}
			else {
				$ajax_mode = false;
				$ajax_plug = $ajax_plug_params = '';
			}

			$pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $items, $pagination, '', $ajax_mode, $ajax_block, $ajax_plug, $ajax_plug_params);

			// Assign pagination tags
			$t->assign(array(
				'PAGE_TOP_PAGINATION'  => $pagenav['main'],
				'PAGE_TOP_PAGEPREV'    => $pagenav['prev'],
				'PAGE_TOP_PAGENEXT'    => $pagenav['next'],
				'PAGE_TOP_FIRST'       => $pagenav['first'],
				'PAGE_TOP_LAST'        => $pagenav['last'],
				'PAGE_TOP_CURRENTPAGE' => $pagenav['current'],
				'PAGE_TOP_TOTALLINES'  => $totalitems,
				'PAGE_TOP_MAXPERPAGE'  => $items,
				'PAGE_TOP_TOTALPAGES'  => $pagenav['total']
			));
		}

		if ($jj==1)
			$t->parse("MAIN.NONE");

		/* === Hook === */
		foreach (cot_getextplugins('comlist.tags') as $pl) {
			include $pl;
		}
		/* ===== */

		$t->parse();
		$output = $t->text();

		if (Cot::$cache && ($jj > 1) && empty($pagination) && !empty($cache_name) && !empty($cache_ttl) && ($cache_ttl > 0))
		Cot::$cache->db->store($cache_name, $output, SEDBY_COMLIST_REALM, $cache_ttl);
	}
	return $output;
}
