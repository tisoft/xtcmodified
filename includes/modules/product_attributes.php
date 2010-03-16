<?php

/* -----------------------------------------------------------------------------------------
   $Id: product_attributes.php 1255 2005-09-28 15:10:36Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com 
   (c) 2003      nextcommerce (product_info.php,v 1.46 2003/08/25); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist
   New Attribute Manager v4b                            Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Cross-Sell (X-Sell) Admin 1                          Autor: Joshua Dechant (dreamscape)
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');


if ($product->getAttributesCount() > 0) {
// BOF - Tomcraft - 2009-11-07 - Added sortorder to products_options
	//$products_options_name_query = xtDBquery("select distinct popt.products_options_id, popt.products_options_name from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".$product->data['products_id']."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."' order by popt.products_options_name");
	$products_options_name_query = xtDBquery("select distinct popt.products_options_id, popt.products_options_name from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".$product->data['products_id']."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."' order by popt.products_options_sortorder, popt.products_options_id");
// EOF - Tomcraft - 2009-11-07 - Added sortorder to products_options
	$row = 0;
	$col = 0;
	$products_options_data = array ();
	while ($products_options_name = xtc_db_fetch_array($products_options_name_query,true)) {
		$selected = 0;
		$products_options_array = array ();

		$products_options_data[$row] = array ('NAME' => $products_options_name['products_options_name'], 'ID' => $products_options_name['products_options_id'], 'DATA' => '');
		$products_options_query = xtDBquery("select pov.products_options_values_id,
		                                                 pov.products_options_values_name,
		                                                 pa.attributes_model,
		                                                 pa.options_values_price,
		                                                 pa.price_prefix,
		                                                 pa.attributes_stock
		                                                 from ".TABLE_PRODUCTS_ATTRIBUTES." pa,
		                                                 ".TABLE_PRODUCTS_OPTIONS_VALUES." pov
		                                                 where pa.products_id = '".$product->data['products_id']."'
		                                                 and pa.options_id = '".$products_options_name['products_options_id']."'
		                                                 and pa.options_values_id = pov.products_options_values_id
		                                                 and pov.language_id = '".(int) $_SESSION['languages_id']."'
		                                                 order by pa.sortorder");
		$col = 0;
		while ($products_options = xtc_db_fetch_array($products_options_query,true)) {
			$price = '';
			if ($_SESSION['customers_status']['customers_status_show_price'] == '0') {
				$products_options_data[$row]['DATA'][$col] = array ('ID' => $products_options['products_options_values_id'], 'TEXT' => $products_options['products_options_values_name'], 'MODEL' => $products_options['attributes_model'], 'PRICE' => '', 'FULL_PRICE' => '', 'PREFIX' => $products_options['price_prefix']);
			} else {
				if ($products_options['options_values_price'] != '0.00') {
					$price = $xtPrice->xtcFormat($products_options['options_values_price'], false, $product->data['products_tax_class_id']);
				}

				$products_price = $xtPrice->xtcGetPrice($product->data['products_id'], $format = false, 1, $product->data['products_tax_class_id'], $product->data['products_price']);
				if ($_SESSION['customers_status']['customers_status_discount_attributes'] == 1 && $products_options['price_prefix'] == '+')
					$price -= $price / 100 * $discount;
					
					$attr_price=$price;
					if ($products_options['price_prefix']=="-") $attr_price=$price*(-1);
					$full = $products_price + $attr_price;
				$products_options_data[$row]['DATA'][$col] = array ('ID' => $products_options['products_options_values_id'], 'TEXT' => $products_options['products_options_values_name'], 'MODEL' => $products_options['attributes_model'], 'PRICE' => $xtPrice->xtcFormat($price, true), 'FULL_PRICE' => $xtPrice->xtcFormat($full, true), 'PREFIX' => $products_options['price_prefix']);

				//if PRICE for option is 0 we don't need to display it
				if ($price == 0) {
					unset ($products_options_data[$row]['DATA'][$col]['PRICE']);
					unset ($products_options_data[$row]['DATA'][$col]['PREFIX']);
				}

			}
			$col ++;
		}
		$row ++;
	}

}

if ($product->data['options_template'] == '' or $product->data['options_template'] == 'default') {
	$files = array ();
	if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/')) {
		while (($file = readdir($dir)) !== false) {
// BOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
			//if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
			if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/'.$file) and (substr($file, -5) == ".html") and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
// EOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
				$files[] = array ('id' => $file, 'text' => $file);
			} //if
		} // while
		closedir($dir);
	}
	$product->data['options_template'] = $files[0]['id'];
}

$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('options', $products_options_data);
// set cache ID

	$module_smarty->caching = 0;
	$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/product_options/'.$product->data['options_template']);

$info_smarty->assign('MODULE_product_options', $module);
?>
