<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(also_purchased_products.php,v 1.21 2003/02/12); www.oscommerce.com 
   (c) 2003	 nextcommerce (also_purchased_products.php,v 1.9 2003/08/17); www.nextcommerce.org
   (c) 2006 XT-Commerce

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

$module_smarty = new Smarty;
//BOF - GTB - 2010-08-03 - Security Fix - Base
$module_smarty->assign('tpl_path',DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/');
//$module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
//EOF - GTB - 2010-08-03 - Security Fix - Base
// include needed files

$data = $product->getAlsoPurchased();
if (count($data) >= MIN_DISPLAY_ALSO_PURCHASED) {

	$module_smarty->assign('language', $_SESSION['language']);
	$module_smarty->assign('module_content', $data);
	// set cache ID

	$module_smarty->caching = 0;
	$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/also_purchased.html');

	$info_smarty->assign('MODULE_also_purchased', $module);

}
?>