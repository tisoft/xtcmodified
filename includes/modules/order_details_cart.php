<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(order_details.php,v 1.8 2003/05/03); www.oscommerce.com
   (c) 2003	 nextcommerce (order_details.php,v 1.16 2003/08/17); www.nextcommerce.org
   (c) 2006 xt:Commerce (order_details_cart.php 1281 2005-10-03); www.xt-commerce.de

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

$module_smarty = new Smarty;
//BOF - GTB - 2010-08-03 - Security Fix - Base
$module_smarty->assign('tpl_path',DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
//$module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
//EOF - GTB - 2010-08-03 - Security Fix - Base
// include needed functions
require_once (DIR_FS_INC.'xtc_check_stock.inc.php');
require_once (DIR_FS_INC.'xtc_get_products_stock.inc.php');
require_once (DIR_FS_INC.'xtc_remove_non_numeric.inc.php');
require_once (DIR_FS_INC.'xtc_get_short_description.inc.php');
require_once (DIR_FS_INC.'xtc_format_price.inc.php');
require_once (DIR_FS_INC.'xtc_get_attributes_model.inc.php');

$module_content = array ();
$any_out_of_stock = '';
$mark_stock = '';

for ($i = 0, $n = sizeof($products); $i < $n; $i ++) {

	if (STOCK_CHECK == 'true') {
		$mark_stock = xtc_check_stock($products[$i]['id'], $products[$i]['quantity']);
		if ($mark_stock)
			$_SESSION['any_out_of_stock'] = 1;
	}

	$image = '';
	if ($products[$i]['image'] != '') {
		$image = DIR_WS_THUMBNAIL_IMAGES.$products[$i]['image'];
	}

  //BOF - Dokuman - 15.08.2009 - show 'delete button' in shopping cart
	$del_button = '<a href="'
				  . xtc_href_link(FILENAME_SHOPPING_CART, 'action=remove_product&prd_id=' . $products[$i]['id'], 'SSL')
				  . '">' . xtc_image_button('cart_del.gif', IMAGE_BUTTON_DELETE) . '</a>';

	$del_link = '<a href="'
				  . xtc_href_link(FILENAME_SHOPPING_CART, 'action=remove_product&prd_id=' . $products[$i]['id'], 'SSL')
				  . '">' . IMAGE_BUTTON_DELETE . '</a>';
  //EOF - Dokuman - 15.08.2009 - show 'delete button' in shopping cart

	$module_content[$i] = array ('PRODUCTS_NAME' => $products[$i]['name'].$mark_stock,
                               'PRODUCTS_QTY' => xtc_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="2"')
                                                .xtc_draw_hidden_field('products_id[]', $products[$i]['id'])
                                                .xtc_draw_hidden_field('old_qty[]', $products[$i]['quantity']),
                                'PRODUCTS_MODEL' => $products[$i]['model'],
                                'PRODUCTS_SHIPPING_TIME'=>$products[$i]['shipping_time'],
                                'PRODUCTS_TAX' => number_format($products[$i]['tax'], TAX_DECIMAL_PLACES),
                                'PRODUCTS_IMAGE' => $image, 'IMAGE_ALT' => $products[$i]['name'],
                                'BOX_DELETE' => xtc_draw_checkbox_field('cart_delete[]', $products[$i]['id']),
                                'PRODUCTS_LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($products[$i]['id'], $products[$i]['name'])),
  //BOF - Dokuman - 15.08.2009 - show 'delete button' in shopping cart
                                'BUTTON_DELETE' => $del_button,
                                'LINK_DELETE' => $del_link,
  //EOF - Dokuman - 15.08.2009 - show 'delete button' in shopping cart
                                'PRODUCTS_PRICE' => $xtPrice->xtcFormat($products[$i]['price'] * $products[$i]['quantity'], true),
                                'PRODUCTS_SINGLE_PRICE' =>$xtPrice->xtcFormat($products[$i]['price'], true),
                                'PRODUCTS_SHORT_DESCRIPTION' => xtc_get_short_description($products[$i]['id']),
                                'ATTRIBUTES' => '');
	// Product options names
    $attributes_exist = ((isset ($products[$i]['attributes']) && is_array($products[$i]['attributes'])) ? 1 : 0);

	if ($attributes_exist == 1) {
		reset($products[$i]['attributes']);

		while (list ($option, $value) = each($products[$i]['attributes'])) {

			if (ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_CHECK == 'true') {
				$attribute_stock_check = xtc_check_stock_attributes($products[$i][$option]['products_attributes_id'], $products[$i]['quantity']);
				if ($attribute_stock_check)
					$_SESSION['any_out_of_stock'] = 1;
			}

			$module_content[$i]['ATTRIBUTES'][] = array (
			'ID' => $products[$i][$option]['products_attributes_id'],
			'MODEL' => xtc_get_attributes_model(xtc_get_prid($products[$i]['id']), $products[$i][$option]['products_options_values_name'],$products[$i][$option]['products_options_name']),
			'NAME' => $products[$i][$option]['products_options_name'],
			'VALUE_NAME' => $products[$i][$option]['products_options_values_name'].$attribute_stock_check
			);
		}
	}
}

$discount = 0;
$total_content = '';
$total =$_SESSION['cart']->show_total();
if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1' && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
		$price = $total-$_SESSION['cart']->show_tax(false);
	} else {
		$price = $total;
	}
	$discount = $xtPrice->xtcGetDC($price, $_SESSION['customers_status']['customers_status_ot_discount']);
	$total_content = $_SESSION['customers_status']['customers_status_ot_discount'].' % '.SUB_TITLE_OT_DISCOUNT.' -'.xtc_format_price($discount, $price_special = 1, $calculate_currencies = false).'<br />';
}

if ($_SESSION['customers_status']['customers_status_show_price'] == '1') {
	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) $total-=$discount;
	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) $total-=$discount;
	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) $total-=$discount;
	$total_content .= SUB_TITLE_SUB_TOTAL.$xtPrice->xtcFormat($total, true).'<br />';
} else {
	$total_content .= NOT_ALLOWED_TO_SEE_PRICES.'<br />';
}
// display only if there is an ot_discount
//BOF - DokuMan - 2010-08-31 - use customers_status instead
//if ($customer_status_value['customers_status_ot_discount'] != 0) {
//	$total_content .= TEXT_CART_OT_DISCOUNT.$customer_status_value['customers_status_ot_discount'].'%';
if ($_SESSION['customers_status']['customers_status_ot_discount'] != 0) {
    $total_content .= TEXT_CART_OT_DISCOUNT.$_SESSION['customers_status']['customers_status_ot_discount'].'%';
//EOF - DokuMan - 2010-08-31 - use customers_status instead
}
if (SHOW_SHIPPING == 'true') {
  //BOF - DokuMan - 2009-08-09 - fixed wrong quotationmark position and fixed wrong question mark on KeepThis=true
	//$module_smarty->assign('SHIPPING_INFO', ' '.SHIPPING_EXCL.'<a target="_blank" href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS.'?KeepThis=true&TB_iframe=true&height=400&width=600"').' title="Information" class="thickbox""> '.SHIPPING_COSTS.'</a>');
	$module_smarty->assign('SHIPPING_INFO', ' '.SHIPPING_EXCL.' <a target="_blank" href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS.'&KeepThis=true&TB_iframe=true&height=400&width=600').'" title="Information" class="thickbox">'.SHIPPING_COSTS.'</a>');
 	//EOF - DokuMan - 2009-08-09 - fixed wrong quotationmark position and fixed wrong question mark on KeepThis=true
}
if ($_SESSION['customers_status']['customers_status_show_price'] == '1') {
$module_smarty->assign('UST_CONTENT', $_SESSION['cart']->show_tax());
}
$module_smarty->assign('TOTAL_CONTENT', $total_content);
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('module_content', $module_content);

$module_smarty->caching = 0;
$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/order_details.html');

$smarty->assign('MODULE_order_details', $module);
?>