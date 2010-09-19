<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_listing.php,v 1.42 2003/05/27); www.oscommerce.com
   (c) 2003	 nextcommerce (product_listing.php,v 1.19 2003/08/1); www.nextcommerce.org
   (c) 2006 xt:Commerce (product_listing.php 1286 2005-10-07); www.xt-commerce.de

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

$module_smarty = new Smarty;
//BOF - GTB - 2010-08-03 - Security Fix - Base
$module_smarty->assign('tpl_path',DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
//$module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
//EOF - GTB - 2010-08-03 - Security Fix - Base
$result = true;
// include needed functions
require_once (DIR_FS_INC.'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');
$listing_split = new splitPageResults($listing_sql, (isset($_GET['page']) ? (int)$_GET['page'] : 1), MAX_DISPLAY_SEARCH_RESULTS, 'p.products_id');
$module_content = array ();
$category = array ();
if ($listing_split->number_of_rows > 0) {

	$navigation = '
		<table border="0" width="100%" cellspacing="0" cellpadding="2">
		  <tr>
		    <td class="smallText">'.$listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS).'</td>
		    <td class="smallText" align="right">'.TEXT_RESULT_PAGE.' '.$listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array ('page', 'info', 'x', 'y'))).'</td>
		  </tr>
		</table>';
	$group_check = '';
	if (GROUP_CHECK == 'true') {
		$group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
	}
	$category_query = xtDBquery("select
		                                    cd.categories_description,
		                                    cd.categories_name,
		                                    cd.categories_heading_title,
		                                    c.listing_template,
		                                    c.categories_image
		                                    from ".TABLE_CATEGORIES." c,
		                                    ".TABLE_CATEGORIES_DESCRIPTION." cd
		                                    where c.categories_id = '".$current_category_id."'
		                                    and cd.categories_id = '".$current_category_id."'
		                                    ".$group_check."
		                                    and cd.language_id = '".$_SESSION['languages_id']."'");

	$category = xtc_db_fetch_array($category_query,true);
	$image = '';
    if ($category['categories_image'] != '') {
      $image = DIR_WS_IMAGES.'categories/'.$category['categories_image'];
  // BOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
      if(!file_exists($image)) $image = DIR_WS_IMAGES.'categories/noimage.gif';
  // EOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
  	//BOF - GTB - 2010-08-03 - Security Fix - Base
  	$image = DIR_WS_BASE.$image;
  	//EOF - GTB - 2010-08-03 - Security Fix - Base
    }

	//BOF -web28- 2010-08-06 - BUGFIX no manufacturers image displayed
	if (isset ($_GET['manufacturers_id'])) {
		$manu_query = xtDBquery("select manufacturers_image from ".TABLE_MANUFACTURERS." where manufacturers_id = '".(int) $_GET['manufacturers_id']."'");
		$manu = xtc_db_fetch_array($manu_query,true);
		$image = DIR_WS_IMAGES.$manu['manufacturers_image'];
		if(!file_exists($image)) $image = '';
		//BOF - GTB - 2010-08-03 - Security Fix - Base
		if ($image != '') $image = DIR_WS_BASE.$image;
		//EOF - GTB - 2010-08-03 - Security Fix - Base
    }
	//EOF -web28- 2010-08-06 - BUGFIX no manufacturers image displayed

	$module_smarty->assign('CATEGORIES_NAME', $category['categories_name']);
	$module_smarty->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);
	$module_smarty->assign('CATEGORIES_IMAGE', $image);
	$module_smarty->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);

	$rows = 0;
	$listing_query = xtDBquery($listing_split->sql_query);
	while ($listing = xtc_db_fetch_array($listing_query, true)) {
		$rows ++;
		$module_content[] =  $product->buildDataArray($listing);
	}
} else {
	// no product found
	$result = false;
}
// get default template
if ($category['listing_template'] == '' or $category['listing_template'] == 'default') {
	$files = array ();
	if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_listing/')) {
		while (($file = readdir($dir)) !== false) {
// BOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
			//if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_listing/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
			if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_listing/'.$file) and (substr($file, -5) == ".html") and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
// EOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
// BOF - web28 - 2010-07-12 - sort templates array
				//$files[] = array ('id' => $file, 'text' => $file);
				$files[] = $file;
			} //if
		} // while
		closedir($dir);
	}
	sort($files);
	//$category['listing_template'] = $files[0]['id'];
	$category['listing_template'] = $files[0];
// EOF - web28 - 2010-07-12 - sort templates array
}

if ($result != false) {
    $module_smarty->assign('MANUFACTURER_DROPDOWN', (isset($manufacturer_dropdown) ? $manufacturer_dropdown : ''));
	$module_smarty->assign('language', $_SESSION['language']);
	$module_smarty->assign('module_content', $module_content);

	$module_smarty->assign('NAVIGATION', $navigation);
	// set cache ID
	 if (!CacheCheck()) {
		$module_smarty->caching = 0;
		$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/product_listing/'.$category['listing_template']);
	} else {
		$module_smarty->caching = 1;
		$module_smarty->cache_lifetime = CACHE_LIFETIME;
		$module_smarty->cache_modified_check = CACHE_CHECK;
		$cache_id = $current_category_id.'_'.$_SESSION['language'].'_'.$_SESSION['customers_status']['customers_status_name'].'_'.$_SESSION['currency'].'_'.$_GET['manufacturers_id'].'_'.$_GET['filter_id'].'_'.$_GET['page'].'_'.$_GET['keywords'].'_'.$_GET['categories_id'].'_'.$_GET['pfrom'].'_'.$_GET['pto'].'_'.$_GET['x'].'_'.$_GET['y'];
		$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/product_listing/'.$category['listing_template'], $cache_id);
	}
	$smarty->assign('main_content', $module);
} else {
	$error = TEXT_PRODUCT_NOT_FOUND;
	include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);
}
?>