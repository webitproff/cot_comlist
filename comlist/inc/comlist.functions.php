<?php
/**
* Comlist Plugin / Functions
*
* @package ComList
* @author Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');
require_once cot_incfile('page', 'module');

/**
 * Generates page list widget
 * @param  string  $tpl        Template code
 * @param  integer $items      Number of items to show. 0 - all items
 * @param  integer $period     Limit of days in past, 0 - no limits
 * @param  string  $order      Sorting order (SQL)
 * @param  string  $condition  Custom selection filter (SQL)
 * @param  string  $area       Custom areas list semicolon separated
 * @param  integer $cachtime   Caching time in ms, 0 for none
 * @param  string  $pagination Pagination parameter name for the URL, e.g. 'pcm'. Make sure it does not conflict with other paginations. Leave it empty to turn off pagination
 * @param  string  $ajax_block DOM block ID for ajax pagination
 * @return string              Parsed HTML
 */

function cot_comlist($tpl = 'comlist', $items = 0, $order = '', $area = '', $extra = '', $group = 0, $pagination = '', $ajax_block = 'comlist', $cache_name = '', $cache_ttl = 86400) {

	if (empty($pagination) && (cot::$cfg['plugin']['comlist']['ajax'] == 0) && !empty($cache_name) && (cot::$cache && cot::$cache->db->exists($cache_name, '[SEDBY] cot_comlist'))) {
		$output = cot::$cache->db->get($cache_name, '[SEDBY] cot_comlist');
	}
	else {
		$db_com = cot::$db->com;
		$db_users = cot::$db->users;
		$db_pages = cot::$db->pages;

		// Get pagination number if necessary
		if (!empty($pagination)) {
			list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);
		}
		else {
			$d = 0;
		}

		// Compile number
		$sql_items = ($items > 0) ? "LIMIT $d, $items" : '';

		// Compile order
		$sql_order = (empty($order)) ? "com_date DESC" : $order;
		$sql_order = "ORDER BY " . $sql_order;

		// Compile area
		$sql_area = (!empty($area)) ? "c.com_area IN ('".str_replace(";", "','", $area)."')" : "TRUE";
		$sql_area = "WHERE " . $sql_area;

		// Compile extra SQL condition
		$sql_extra = (!empty($extra)) ? "AND $extra" : '';

		// Compile group
		$sql_group = ($group == 1) ? "AND c.com_id = (SELECT MAX(com_id) FROM $db_com AS c2 WHERE c2.com_code = c.com_code)" : '';

		// Display the items
		$t = new XTemplate(cot_tplfile($tpl, 'plug'));

		$join_columns = "";
		$join_tables = "";

		/* === Hook === */
		foreach (cot_getextplugins('comlist.query') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$query = "SELECT c.*, u.*, p.* $join_columns
			FROM $db_com AS c
				LEFT JOIN $db_users AS u ON u.user_id = c.com_authorid
				LEFT JOIN $db_pages AS p ON c.com_code = p.page_id AND c.com_area = 'page'
			$join_tables
			$sql_area
			$sql_group
			$sql_extra
			$sql_order
			$sql_items";

		$res = cot::$db->query($query);
		$jj = 1;

		/* === Hook - Part 1 === */
		$loop_extplugins = cot_getextplugins('comlist.loop');
		/* ===== */

		while ($row = $res->fetch()) {

			if ($row['com_area'] == 'page') {
				if (empty($row['page_id']) && isset(cot::$structure[$row['com_area']][$row['com_code']])) {
					// Category comments
					$cat = cot::$structure[$row['com_area']][$row['com_code']];
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

			$t->assign(cot_generate_usertags($row, 'PAGE_ROW_AUTHOR_'));

			$com_text = cot_parse($row['com_text'], cot::$cfg['plugin']['comments']['markup']);

			$t->assign(array(
				'PAGE_ROW_COMLIST_NUM' => $jj,
				'PAGE_ROW_COMLIST_ODDEVEN' => cot_build_oddeven($jj),

				'PAGE_ROW_COMLIST_IS_NUMERIC' => is_numeric($row['com_code']) ? TRUE : FALSE,

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

			// $t->assign(cot_generate_usertags($row, "PAGE_ROW_OWNER_", htmlspecialchars($row['com_author']), false,false));

			if (((cot::$usr['id'] > 0 && $row['com_authorid'] != cot::$usr['id']) || cot::$usr['id'] == 0) && cot::$usr['lastvisit'] < $row['com_date']) {
				$t->assign('PAGE_ROW_NEW', cot::$L['New']);
				$jn++;
			}
			else {
				$t->assign('PAGE_ROW_NEW', '');
			}

			/* === Hook - Part 2 === */
			foreach ($loop_extplugins as $pl)
			{
				include $pl;
			}
			/* ===== */

			$t->parse("MAIN.PAGE_ROW");
			$jj++;
		}

		$t->assign('COMMENT_TOP_NEWCOUNT', $jn);

		if (!empty($pagination)) {

			$total = cot::$db->query("SELECT c.*, u.*, p.* $join_columns
				FROM $db_com AS c
					LEFT JOIN $db_users AS u ON u.user_id = c.com_authorid
					LEFT JOIN $db_pages AS p ON c.com_code = p.page_id AND c.com_area = 'page'
				$join_tables
				$sql_area
				$sql_group
				$sql_extra");
			$totalitems = $total->rowCount();

			// Render pagination
			$url_area = defined('COT_PLUG') ? 'plug' : cot::$env['ext'];

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
			else {
				$url_params = array();
			}

			$url_params[$pagination] = $durl;

			if (cot::$cfg['plugin']['comlist']['ajax'] == 1) {
				$ajax = true;
				$ajax_plug = 'plug';
				$ajax_plug_params = "r=comlist&tpl=$tpl&items=$items&order=$order&area=$area&extra=$extra&group=$group&pagination=$pagination&ajax_block=$ajax_block&cache_name=$cache_name&cache_ttl=$cache_ttl";
			}
			else {
				$ajax = false;
				$ajax_plug = '';
				$ajax_plug_params = "";
			}
			$pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $items, $pagination, '', $ajax, $ajax_block, $ajax_plug, $ajax_plug_params);

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
		// else {
		// 	$totalitems = "";
		// }

		/* === Hook === */
		foreach (cot_getextplugins('comlist.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($jj==1) {
			$t->parse("MAIN.NONE");
		}

		$t->parse();
		$output = $t->text();

		if (empty($pagination) && (cot::$cfg['plugin']['comlist']['ajax'] == 0) && !empty($cache_name) && cot::$cache && $cache_ttl > 0)
		cot::$cache->db->store($cache_name, $output, '[SEDBY] cot_comlist', $cache_ttl);
	}
	return $output;
}
