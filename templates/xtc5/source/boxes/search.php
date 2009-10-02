<?php


/* -----------------------------------------------------------------------------------------
   $Id: search.php 1262 2005-09-30 10:00:32Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(search.php,v 1.22 2003/02/10); www.oscommerce.com 
   (c) 2003	 nextcommerce (search.php,v 1.9 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
//BOF - Dokuman - 2009-10-20 - Ajax Search suggest
$jav .="<script type=\"text/javascript\">";
$jav .="function foc(){";
$jav .="if(document.getElementById('quick_find').keywords.value!='') document.getElementById('quick_find').keywords.value = '';}";
$jav .="function foc_1(){";
$jav .="if(document.getElementById('quick_find').keywords.value=='') document.getElementById('quick_find').keywords.value ='".IMAGE_BUTTON_SEARCH."';}</script>";
//EOF - Dokuman - 2009-10-20 - Ajax Search suggest

$box_smarty = new smarty;
$box_smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
$box_content = '';

require_once (DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once (DIR_FS_INC . 'xtc_hide_session_id.inc.php');

$box_smarty->assign('FORM_ACTION', $jav.xtc_draw_form('quick_find', xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get').xtc_hide_session_id());
//BOF - Dokuman - 2009-10-20 - Ajax Search suggest
//$box_smarty->assign('INPUT_SEARCH', xtc_draw_input_field('keywords', '', 'size="20" maxlength="30"'));
$box_smarty->assign('INPUT_SEARCH', xtc_draw_input_field('keywords', IMAGE_BUTTON_SEARCH, 'id="txtSearch" onfocus="foc();" onblur="foc_1();" onkeyup="searchSuggest();" size="20" autocomplete="off" maxlength="30" style="width: ' . (BOX_WIDTH-30) . 'px"').'<div id="search_suggest"></div>');
//EOF - Dokuman - 2009-10-20 - Ajax Search suggest
$box_smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_quick_find.gif', IMAGE_BUTTON_SEARCH));
$box_smarty->assign('FORM_END', '</form>');
$box_smarty->assign('LINK_ADVANCED', xtc_href_link(FILENAME_ADVANCED_SEARCH));
$box_smarty->assign('BOX_CONTENT', $box_content);

$box_smarty->assign('language', $_SESSION['language']);
// set cache ID
 if (!CacheCheck()) {
	$box_smarty->caching = 0;
	$box_search = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_search.html');
} else {
	$box_smarty->caching = 1;
	$box_smarty->cache_lifetime = CACHE_LIFETIME;
	$box_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'];
	$box_search = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_search.html', $cache_id);
}

$smarty->assign('box_SEARCH', $box_search);
?>