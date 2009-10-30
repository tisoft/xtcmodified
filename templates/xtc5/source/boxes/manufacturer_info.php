<?php
/* -----------------------------------------------------------------------------------------
   $Id: manufacturer_info.php 1262 2005-09-30 10:00:32Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(manufacturer_info.php,v 1.10 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (manufacturer_info.php,v 1.6 2003/08/13); www.nextcommerce.org 

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

$box_smarty = new smarty;
$box_content='';
$rebuild = false;

$box_smarty->assign('language', $_SESSION['language']);
// set cache ID
if (!CacheCheck()) {
	$cache=false;
	$box_smarty->caching = 0;
} else {
	$cache=true;
	$box_smarty->caching = 1;
	$box_smarty->cache_lifetime = CACHE_LIFETIME;
	$box_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$product->data['products_id'];
}

if (!$box_smarty->is_cached(CURRENT_TEMPLATE.'/boxes/box_manufacturers_info.html', $cache_id) || !$cache) {
	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
	$rebuild = true;

    $manufacturer_query = xtDBquery("select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url from " . TABLE_MANUFACTURERS . " m left join " . TABLE_MANUFACTURERS_INFO . " mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$_SESSION['languages_id'] . "'), " . TABLE_PRODUCTS . " p  where p.products_id = '" . $product->data['products_id'] . "' and p.manufacturers_id = m.manufacturers_id");
    if (xtc_db_num_rows($manufacturer_query,true)) {
      $manufacturer = xtc_db_fetch_array($manufacturer_query,true);

      $image='';
      if (xtc_not_null($manufacturer['manufacturers_image'])) {
        $image=DIR_WS_IMAGES . $manufacturer['manufacturers_image'];
// BOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
        if (!file_exists($image)) $image=DIR_WS_IMAGES . 'manufacturers/noimage.gif';
// EOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
      }
      $box_smarty->assign('IMAGE',$image);
      $box_smarty->assign('NAME',$manufacturer['manufacturers_name']);
      
        if ($manufacturer['manufacturers_url']!='')$box_smarty->assign('URL','<a href="' . xtc_href_link(FILENAME_REDIRECT, 'action=manufacturer&'.xtc_manufacturer_link($manufacturer['manufacturers_id'],$manufacturer['manufacturers_name'])) . '" onclick="window.open(this.href); return false;">' . sprintf(BOX_MANUFACTURER_INFO_HOMEPAGE, $manufacturer['manufacturers_name']) . '</a>');
        $box_smarty->assign('LINK_MORE','<a href="' . xtc_href_link(FILENAME_DEFAULT, xtc_manufacturer_link($manufacturer['manufacturers_id'],$manufacturer['manufacturers_name'])) . '">' . BOX_MANUFACTURER_INFO_OTHER_PRODUCTS . '</a>');

    }
  
}

if (!$cache || $rebuild) {
	if ($manufacturer['manufacturers_name']!='') {
		$box_manufacturers_info = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_manufacturers_info.html');	
		$smarty->assign('box_MANUFACTURERS_INFO',$box_manufacturers_info);
	}
} else {
	$box_manufacturers_info = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_manufacturers_info.html', $cache_id);
	$smarty->assign('box_MANUFACTURERS_INFO',$box_manufacturers_info);
}
?>
