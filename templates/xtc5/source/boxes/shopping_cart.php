<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(shopping_cart.php,v 1.18 2003/02/10); www.oscommerce.com
   (c) 2003	 nextcommerce (shopping_cart.php,v 1.15 2003/08/17); www.nextcommerce.org
   (c) 2006 XT-Commerce (shopping_cart.php 1281 2005-10-03)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
$box_smarty = new smarty;
//BOF - GTB - 2010-08-03 - Security Fix - Base
$box_smarty->assign('tpl_path',DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
//$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
//EOF - GTB - 2010-08-03 - Security Fix - Base
$box_content = '';
$box_price_string = '';
$products_in_cart = array (); //DokuMan - 2010-02-28 - Undefined variable: products_in_cart
$total = 0.0; //DokuMan - 2010-08-24 - Undefined variable: total
$qty = 0; //DokuMan - 2010-02-28 - Undefined variable: qty
// include needed files
require_once (DIR_FS_INC.'xtc_recalculate_price.inc.php');

if (strstr($PHP_SELF, FILENAME_CHECKOUT_PAYMENT) || strstr($PHP_SELF, FILENAME_CHECKOUT_CONFIRMATION) || strstr($PHP_SELF, FILENAME_CHECKOUT_SHIPPING)){
	$box_smarty->assign('deny_cart', 'true');
} else {
	$box_smarty->assign('deny_cart', 'false');
}

if ($_SESSION['cart']->count_contents() > 0) {
	$products = $_SESSION['cart']->get_products();
	//$products_in_cart = array (); //DokuMan - 2010-02-28 - Undefined variable: products_in_cart
	//$qty = 0; //DokuMan - 2010-02-28 - Undefined variable: qty
	for ($i = 0, $n = sizeof($products); $i < $n; $i ++) {
		$qty += $products[$i]['quantity'];
		$products_in_cart[] = array (
		'QTY' => $products[$i]['quantity'],
		'LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($products[$i]['id'],$products[$i]['name'])),
		'NAME' => $products[$i]['name']);
	}
	$box_smarty->assign('PRODUCTS', $qty);
	$box_smarty->assign('empty', 'false');
} else {
	// cart empty
	$box_smarty->assign('empty', 'true');
}

if ($_SESSION['cart']->count_contents() > 0) {

	$total = $_SESSION['cart']->show_total();
	$discount = 0; //DokuMan - 2010-03-01 - set undefined variable
  if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1' && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
      $price = $total-$_SESSION['cart']->show_tax(false);
    } else {
      $price = $total;
    }
    $discount = $xtPrice->xtcGetDC($price, $_SESSION['customers_status']['customers_status_ot_discount']);
    $box_smarty->assign('DISCOUNT', $xtPrice->xtcFormat(($discount * (-1)), $price_special = 1, $calculate_currencies = false));
  }

  if ($_SESSION['customers_status']['customers_status_show_price'] == '1') {
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) $total-=$discount;
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) $total-=$discount;
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) $total-=$discount;
    $box_smarty->assign('TOTAL', $xtPrice->xtcFormat($total, true));
  }

	$box_smarty->assign('UST', $_SESSION['cart']->show_tax());

	if (SHOW_SHIPPING=='true') {
		//BOF - DokuMan - 2009-08-09 - fixed wrong quotationmark position and fixed wrong question mark on KeepThis=true
		//$box_smarty->assign('SHIPPING_INFO',' '.SHIPPING_EXCL.'<a target="_blank" href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS.'?KeepThis=true&TB_iframe=true&height=400&width=600"').' title="Information" class="thickbox"">'.SHIPPING_COSTS.'</a>');
		$box_smarty->assign('SHIPPING_INFO',' '.SHIPPING_EXCL.' <a target="_blank" href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS.'&KeepThis=true&TB_iframe=true&height=400&width=600', $request_type).'" title="Information" class="thickbox">'.SHIPPING_COSTS.'</a>'); // web28 - 2010-11-05 - change SSL -> $request_type
 	  	//EOF - DokuMan - 2009-08-09 - fixed wrong quotationmark position and fixed wrong question mark on KeepThis=true
	}
}

if (ACTIVATE_GIFT_SYSTEM == 'true') {
	$box_smarty->assign('ACTIVATE_GIFT', 'true');
}

// GV Code Start
if (isset ($_SESSION['customer_id'])) {
	$gv_query = xtc_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$_SESSION['customer_id']."'");
	$gv_result = xtc_db_fetch_array($gv_query);
	if ($gv_result['amount'] > 0) {
		$box_smarty->assign('GV_AMOUNT', $xtPrice->xtcFormat($gv_result['amount'], true, 0, true));
		$box_smarty->assign('GV_SEND_TO_FRIEND_LINK', '<a href="'.xtc_href_link(FILENAME_GV_SEND).'">');
	}
}
if (isset ($_SESSION['gv_id'])) {
	$gv_query = xtc_db_query("select coupon_amount from ".TABLE_COUPONS." where coupon_id = '".$_SESSION['gv_id']."'");
	$coupon = xtc_db_fetch_array($gv_query);
	$box_smarty->assign('COUPON_AMOUNT2', $xtPrice->xtcFormat($coupon['coupon_amount'], true, 0, true));
}
if (isset ($_SESSION['cc_id'])) {
	//BOF - DokuMan - 2010-11-26 - FIX LOST cID - 2010-11-05 - add SSL FIX -> $request_type and correct href and added title tag
	//$box_smarty->assign('COUPON_HELP_LINK', '<a href="javascript:popupWindow(\''.xtc_href_link(FILENAME_POPUP_COUPON_HELP, 'cID='.$_SESSION['cc_id']).'\')">');
	$box_smarty->assign('COUPON_HELP_LINK', '<a target="_blank" class="thickbox" title="Information" href="'.xtc_href_link(FILENAME_POPUP_COUPON_HELP, 'cID='.$_SESSION['cc_id'].'&KeepThis=true&TB_iframe=true&height=400&width=600', $request_type).'">Information</a>');
	//EOF - DokuMan - 2010-11-26 - FIX LOST cID - 2010-11-05 - add SSL FIX -> $request_type and correct href and added title tag
}

// GV Code End
$box_smarty->assign('LINK_CART', xtc_href_link(FILENAME_SHOPPING_CART, '', $request_type)); // web28 - 2010-11-05 - change SSL -> $request_type
$box_smarty->assign('products', $products_in_cart);

$box_smarty->caching = 0;
$box_smarty->assign('language', $_SESSION['language']);
$box_shopping_cart = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_cart.html');
$smarty->assign('box_CART', $box_shopping_cart);
?>