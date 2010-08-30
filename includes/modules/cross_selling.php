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
   (c) 2006 xt:Commerce (cross_selling.php 1243 2005-09-25); www.xt-commerce.de

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');

$data = $product->getCrossSells();

if (count($data) > 0) {

	$module_smarty->assign('language', $_SESSION['language']);
	$module_smarty->assign('module_content', $data);
	// set cache ID

	$module_smarty->caching = 0;
	$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/cross_selling.html');
	$info_smarty->assign('MODULE_cross_selling', $module);
}

// reverse cross selling
if (ACTIVATE_REVERSE_CROSS_SELLING=='true') {
$module_smarty = new Smarty;
//BOF - Dokuman - 2010-01-20: set template path also on activated cross selling
$module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
//EOF - Dokuman - 2010-01-20: set template path also on activated cross selling

$data = $product->getReverseCrossSells();

  if (count($data) > 0) {
    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('module_content', $data);
    // set cache ID

    $module_smarty->caching = 0;
    $module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/reverse_cross_selling.html');

    $info_smarty->assign('MODULE_reverse_cross_selling', $module);
  }

}
?>