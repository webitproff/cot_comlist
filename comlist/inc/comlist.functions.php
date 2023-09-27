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
require_once cot_incfile('pagelist', 'plug', 'functions.extra');

/**
 * Generates comment list widget
 *
 * @param  string  $tpl					01. Template code
 * @param  int     $items				02. Number of items to show. 0 - show all items
 * @param  string  $order				03. Sorting order (SQL)
 * @param  string  $extra				04. Custom selection filter (SQL)
 * @param  int     $group				05. Group comments by code
 * @param  int     $offset			06. Exclude specified number of records starting from the beginning
 * @param  string  $pagination	07. Pagination parameter name for the URL, e.g. 'pcm'. Make sure it does not conflict with other paginations. Leave it empty to turn off pagination
 * @param  string  $ajax_block	08. DOM block ID for ajax pagination
 * @param  string  $cache_name	09. Cache name
 * @param  int     $cache_ttl		10. Cache TTL
 * @return string								Parsed HTML
 */
function sedby_comlist($tpl = 'comlist', $items = 0, $order = '', $extra = '', $group = 0, $offset = 0, $pagination = '', $ajax_block = '', $cache_name = '', $cache_ttl = '') {

	$enableAjax = $enableCache = $enablePagination = false;

  // Condition shortcut
  if (Cot::$cache && !empty($cache_name) && ((int)$cache_ttl > 0) && (Cot::$usr['id'] == 0)) {
    $enableCache = true;
    $cache_name = str_replace(' ', '_', $cache_name);
  }

	if ($enableCache && Cot::$cache->db->exists($cache_name, SEDBY_COMLIST_REALM)) {
		$output = Cot::$cache->db->get($cache_name, SEDBY_COMLIST_REALM);
	} else {

		/* === Hook === */
		foreach (cot_getextplugins('comlist.first') as $pl) {
			include $pl;
		}
		/* ===== */

    // Condition shortcuts
    if ((Cot::$cfg['turnajax']) && (Cot::$cfg['plugin']['comlist']['ajax']) && !empty($ajax_block)) {
			$enableAjax = true;
		}

    if (!empty($pagination) && ((int)$items > 0)) {
			$enablePagination = true;
		}

		// DB tables shortcuts
		$db_com = Cot::$db->com;

		// Display the items
    (!isset($tpl) || empty($tpl)) && $tpl = 'comlist';
		$t = new XTemplate(cot_tplfile($tpl, 'plug'));

		// Get pagination if necessary
		if ($enablePagination) {
      list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);
    } else {
      $d = 0;
    }

		// Compile items number
    ((int)$offset <= 0) && $offset = 0;
    $d = $d + (int)$offset;
		$sql_limit = ($items > 0) ? "LIMIT $d, $items" : "";

		// Compile order
		$sql_order = empty($order) ? "ORDER BY com_id DESC" : " ORDER BY $order";

		// Compile group
		$sql_group = ($group == 1) ? "c.com_id = (SELECT MAX(com_id) FROM " . $db_com . " AS c2 WHERE c2.com_code = c.com_code)" : "";

		// Compile extra SQL condition
		$sql_extra = (empty($extra)) ? "" : $extra;

		$sql_cond = sedby_build_where(array($sql_group, $sql_extra));

		$comlist_join_columns = "";
		$comlist_join_tables = "";

		// Page Module Support
		if (Cot::$cfg['plugin']['comlist']['pagetags']) {
			$db_pages = Cot::$db->pages;
			$comlist_join_columns .= " , p.* ";
			$comlist_join_tables .= "LEFT JOIN $db_pages AS p ON (c.com_code = p.page_id AND c.com_area = 'page')";
		}

		// Users Module Support
		if (Cot::$cfg['plugin']['comlist']['usertags']) {
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
			if (Cot::$cfg['plugin']['comlist']['pagetags'] == 1 && $row['com_area'] == 'page') {
				if (empty($row['page_id']) && isset(Cot::$structure[$row['com_area']][$row['com_code']])) {
					// Category comments
					$cat = Cot::$structure[$row['com_area']][$row['com_code']];
					$link_params = array('c' => $row['com_code']);
					$t->assign(array(
						'PAGE_ROW_CAT_TITLE' => htmlspecialchars($cat['title']),
						'PAGE_ROW_CAT_URL' => cot_url('page', $link_params),
					));
				} else {
					// Page comments
					$link_params = array('c' => $row['page_cat']);
					empty($row['page_alias']) ? $link_params['id'] = $row['page_id'] : $link_params['al'] = $row['page_alias'];
          $t->assign(cot_generate_pagetags($row, 'PAGE_ROW_PAGE_'));
				}
			}

			if (Cot::$cfg['plugin']['comlist']['usertags']) {
				$t->assign(cot_generate_usertags($row, 'PAGE_ROW_USER_'));
			}

      $com_author = htmlspecialchars($row['com_author']);
			$com_text = cot_parse($row['com_text'], Cot::$cfg['plugin']['comments']['markup']);

			$t->assign(array(
				'PAGE_ROW_NUM' => $jj,
				'PAGE_ROW_ODDEVEN' => cot_build_oddeven($jj),

				'PAGE_ROW_CODE_IS_NUMERIC' => is_numeric($row['com_code']) ? TRUE : FALSE,

				'PAGE_ROW_ID' => $row['com_id'],
				'PAGE_ROW_CODE' => $row['com_code'],
				'PAGE_ROW_AREA' => $row['com_area'],

				'PAGE_ROW_AUTHORNAME' => $com_author,
				'PAGE_ROW_AUTHORID' => $row['com_authorid'],
				'PAGE_ROW_AUTHORIP' => $row['com_authorip'],

				'PAGE_ROW_TEXT' => $com_text,
				'PAGE_ROW_TEXT_PLAIN' => strip_tags($com_text),

				'PAGE_ROW_DATE' => cot_date('datetime_medium', $row['com_date']),
				'PAGE_ROW_DATE_STAMP' => $row['com_date'],
			));

			if ((Cot::$usr['id'] > 0 && $row['com_authorid'] != Cot::$usr['id']) && (Cot::$usr['lastvisit'] < $row['com_date'])) {
				$t->assign('PAGE_ROW_NEW', Cot::$L['New']);
				$jn++;
			} else {
				$t->assign('PAGE_ROW_NEW', '');
				$jn = 0;
			}

			/* === Hook - Part 2 === */
			foreach ($extp as $pl) {
				include $pl;
			}
			/* ===== */

			$t->parse("MAIN.PAGE_ROW");
			$jj++;
		}

		$t->assign('PAGE_TOP_NEW_COMMENTS', $jn);

		// Render pagination if needed
		if ($enablePagination) {
			$totalitems = Cot::$db->query("SELECT c.* FROM $db_com AS c $sql_cond")->rowCount();

			$url_area = sedby_geturlarea();
			$url_params = sedby_geturlparams();
			$url_params[$pagination] = $durl;

			if ($enableAjax) {
				$ajax_mode = true;
				$ajax_plug = 'plug';
				if (Cot::$cfg['plugin']['comlist']['encrypt_ajax_urls']) {
					$h = $tpl . ',' . $items . ',' . $order . ',' . $extra . ',' . $group . ',' . $offset . ',' . $pagination . ',' . $ajax_block . ',' . $cache_name . ',' . $cache_ttl;
					$h = sedby_encrypt_decrypt('encrypt', $h, Cot::$cfg['plugin']['comlist']['encrypt_key'], Cot::$cfg['plugin']['comlist']['encrypt_iv']);
					$h = str_replace('=', '', $h);
					$ajax_plug_params = "r=comlist&h=$h";
				} else {
					$ajax_plug_params = "r=comlist&tpl=$tpl&items=$items&order=$order&extra=$extra&group=$group&offset=$offset&pagination=$pagination&ajax_block=$ajax_block&cache_name=$cache_name&cache_ttl=$cache_ttl";
				}
			} else {
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

    // Assign service tags
    if ((!$enableCache) && (Cot::$usr['maingrp'] == 5)) {
      $t->assign(array(
        'PAGE_TOP_QUERY' => $query,
        'PAGE_TOP_RES' => $res,
      ));
    }

		($jj==1) && $t->parse("MAIN.NONE");

		/* === Hook === */
		foreach (cot_getextplugins('comlist.tags') as $pl) {
			include $pl;
		}
		/* ===== */

		$t->parse();
		$output = $t->text();

		if ($enableCache && !$enablePagination && ($jj > 1)) {
			Cot::$cache->db->store($cache_name, $output, SEDBY_COMLIST_REALM, $cache_ttl);
		}
	}
	return $output;
}
