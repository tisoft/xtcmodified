<?php

/* -----------------------------------------------------------------------------------------
   $Id: shopping_cart.php 1299 2005-10-09 18:54:29Z gwinger $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(shopping_cart.php,v 1.71 2003/02/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (shopping_cart.php,v 1.24 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------
   Third Party contributions:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
$cart_empty = false;
require ("includes/application_top.php");
// create smarty elements
$smarty = new Smarty;
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
// include needed functions
require_once (DIR_FS_INC.'xtc_array_to_string.inc.php');
require_once (DIR_FS_INC.'xtc_image_submit.inc.php');
require_once (DIR_FS_INC.'xtc_recalculate_price.inc.php');

$breadcrumb->add(NAVBAR_TITLE_SHOPPING_CART, xtc_href_link(FILENAME_SHOPPING_CART));

require (DIR_WS_INCLUDES.'header.php');

// BOF - Tomcraft - 2009-10-03 - Paypal Express Modul
if(!isset($_SESSION['paypal_warten']))
// EOF - Tomcraft - 2009-10-03 - Paypal Express Modul
include (DIR_WS_MODULES.'gift_cart.php');
// BOF - Tomcraft - 2009-10-03 - Paypal Express Modul (PayPal Express abgelehnt und erneut aufrufen!)
if(isset($_SESSION['reshash']['ACK']) AND strtoupper($_SESSION['reshash']['ACK'])!="SUCCESS" AND strtoupper($_SESSION['reshash']['ACK'])!="SUCCESSWITHWARNING"):
	if(isset($_SESSION['reshash']['REDIRECTREQUIRED'])  && strtoupper($_SESSION['reshash']['REDIRECTREQUIRED'])=="TRUE"):
		require (DIR_WS_CLASSES.'payment.php');
		$payment_modules = new payment($_SESSION['payment']);
		$_SESSION['paypal_fehler']=((PAYPAL_FEHLER)?PAYPAL_FEHLER:'PayPal Fehler...<br />');
		$_SESSION['paypal_warten']=((PAYPAL_WARTEN)?PAYPAL_WARTEN:'Sie m�ssen noch einmal zu PayPal. <br />');
		$payment_modules->giropay_process();
	endif;
endif;
unset($_SESSION['paypal_express_checkout']);
// Paypal Error Messages:
if(isset($_SESSION['paypal_fehler']) AND !isset($_SESSION['paypal_warten'])):
	if(!isset($_SESSION['reshash']['ACK']) AND strtoupper($_SESSION['reshash']['ACK'])!="SUCCESS" AND strtoupper($_SESSION['reshash']['ACK'])!="SUCCESSWITHWARNING"):
		$o_paypal->paypal_second_auth_call($_SESSION['tmp_oID']);
		xtc_redirect($o_paypal->payPalURL);
	endif;
	if(isset($_SESSION['reshash']['ACK']) AND (strtoupper($_SESSION['reshash']['ACK'])=="SUCCESS" OR strtoupper($_SESSION['reshash']['ACK'])=="SUCCESSWITHWARNING")):
		$o_paypal->paypal_get_customer_data();
		if($data['PayerID'] OR $_SESSION['reshash']['PAYERID']):
			require (DIR_WS_CLASSES.'order.php');
			$data = array_merge($_SESSION['nvpReqArray'],$_SESSION['reshash']);
			$data = array_merge($data,$GET);
			$o_paypal->complete_ceckout($_SESSION['tmp_oID'],$data);
			$o_paypal->write_status_history($_SESSION['tmp_oID']);
			$o_paypal->logging_status($_SESSION['tmp_oID']);
		endif;
	endif;
	$_SESSION['cart']->reset(true);
	// unregister session variables used during checkout
	$last_order =$_SESSION['tmp_oID'];
	unset ($_SESSION['sendto']);
	unset ($_SESSION['billto']);
	unset ($_SESSION['shipping']);
	unset ($_SESSION['comments']);
	unset ($_SESSION['last_order']);
	unset ($_SESSION['tmp_oID']);
	unset ($_SESSION['cc']);
	//GV Code Start
	if (isset ($_SESSION['credit_covers']))
		unset ($_SESSION['credit_covers']);
	require (DIR_WS_CLASSES.'order_total.php');
	$order_total_modules = new order_total();
	$order_total_modules->clear_posts(); //ICW ADDED FOR CREDIT CLASS SYSTEM
	// GV Code End
	if(isset($_SESSION['reshash']['ACK']) AND (strtoupper($_SESSION['reshash']['ACK'])=="SUCCESS" OR strtoupper($_SESSION['reshash']['ACK'])=="SUCCESSWITHWARNING")):
		$redirect=((isset($_SESSION['reshash']['REDIRECTREQUIRED'])  && strtoupper($_SESSION['reshash']['REDIRECTREQUIRED'])=="TRUE")?true:false);
		$o_paypal->paypal_get_customer_data();
		if($data['PayerID'] OR $_SESSION['reshash']['PAYERID']):
			if($redirect):
				unset($_SESSION['paypal_fehler']);
				require (DIR_WS_CLASSES.'payment.php');
				$payment_modules = new payment('paypalexpress');
				$payment_modules->giropay_process();
			endif;
			$weiter=true;
		endif;
		unset($_SESSION['payment']);
		unset($_SESSION['nvpReqArray']);
		unset($_SESSION['reshash']);
		if($weiter):
			unset($_SESSION['paypal_fehler']);
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
		endif;
	else:
		unset($_SESSION['payment']);
		unset($_SESSION['nvpReqArray']);
		unset($_SESSION['reshash']);
	endif;
	$smarty->assign('error', $_SESSION['paypal_fehler']);
	unset($_SESSION['paypal_fehler']);
endif;
// EOF - Tomcraft - 2009-10-03 - Paypal Express Modul (PayPal Express abgelehnt und erneut aufrufen!)

if ($_SESSION['cart']->count_contents() > 0) {

// BOF - Tomcraft - 2009-10-03 - Paypal Express Modul
	if(!isset($_SESSION['paypal_warten'])){
		// Normaler Warenkorb
// EOF - Tomcraft - 2009-10-03 - Paypal Express Modul

	$smarty->assign('FORM_ACTION', xtc_draw_form('cart_quantity', xtc_href_link(FILENAME_SHOPPING_CART, 'action=update_product', 'SSL')));
	$smarty->assign('FORM_END', '</form>');
	$hidden_options = '';
	$_SESSION['any_out_of_stock'] = 0;
	$products = $_SESSION['cart']->get_products();
	for ($i = 0, $n = sizeof($products); $i < $n; $i ++) {
		// Push all attributes information in an array
		if (isset ($products[$i]['attributes'])) {
			while (list ($option, $value) = each($products[$i]['attributes'])) {
				$hidden_options .= xtc_draw_hidden_field('id['.$products[$i]['id'].']['.$option.']', $value);
				$attributes = xtc_db_query("select popt.products_options_name, 
                                           poval.products_options_values_name, 
                                           pa.options_values_price,
                                           pa.price_prefix,
                                           pa.attributes_stock,
                                           pa.products_attributes_id,
                                           pa.attributes_model
                                    from ".TABLE_PRODUCTS_OPTIONS." popt, 
                                         ".TABLE_PRODUCTS_OPTIONS_VALUES." poval, 
                                         ".TABLE_PRODUCTS_ATTRIBUTES." pa
				                            where pa.products_id = '".$products[$i]['id']."'
				                            and pa.options_id = '".$option."'
				                            and pa.options_id = popt.products_options_id
				                            and pa.options_values_id = '".$value."'
				                            and pa.options_values_id = poval.products_options_values_id
				                            and popt.language_id = '".(int) $_SESSION['languages_id']."'
				                            and poval.language_id = '".(int) $_SESSION['languages_id']."'");
				$attributes_values = xtc_db_fetch_array($attributes);
				$products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
				$products[$i][$option]['options_values_id'] = $value;
				$products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
				$products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
				$products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
				$products[$i][$option]['weight_prefix'] = $attributes_values['weight_prefix'];
				$products[$i][$option]['options_values_weight'] = $attributes_values['options_values_weight'];
				$products[$i][$option]['attributes_stock'] = $attributes_values['attributes_stock'];
				$products[$i][$option]['products_attributes_id'] = $attributes_values['products_attributes_id'];
				$products[$i][$option]['products_attributes_model'] = $attributes_values['products_attributes_model'];
			}
		}
	}
	$smarty->assign('HIDDEN_OPTIONS', $hidden_options);
	require (DIR_WS_MODULES.'order_details_cart.php');
$_SESSION['allow_checkout'] = 'true';
	if (STOCK_CHECK == 'true') {
		if ($_SESSION['any_out_of_stock'] == 1) {
			if (STOCK_ALLOW_CHECKOUT == 'true') {
				// write permission in session
				$_SESSION['allow_checkout'] = 'true';
				$smarty->assign('info_message', OUT_OF_STOCK_CAN_CHECKOUT);
			} else {
				$_SESSION['allow_checkout'] = 'false';
				$smarty->assign('info_message', OUT_OF_STOCK_CANT_CHECKOUT);
			}
		} else {
			$_SESSION['allow_checkout'] = 'true';
		}
	}
// BOF - Tomcraft - 2009-10-03 - Paypal Express Modul
	}else{
		// 2. PayPal Aufruf - nur anzeigen
		require (DIR_WS_CLASSES.'order.php');
		$order = new order((int)$_SESSION['tmp_oID']);
		$smarty->assign('language', $_SESSION['language']);
		if ($order->delivery != false) {
			$smarty->assign('DELIVERY_LABEL', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'));
			if ($order->info['shipping_method']) { $smarty->assign('SHIPPING_METHOD', $order->info['shipping_method']); }
		}
		$order_total = $order->getTotalData((int)$_SESSION['tmp_oID']);
		$smarty->assign('order_data', $order->getOrderData((int)$_SESSION['tmp_oID']));
		$smarty->assign('order_total', $order_total['data']);
		$smarty->assign('BILLING_LABEL', xtc_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'));
		$smarty->assign('ORDER_NUMBER',$_SESSION['tmp_oID']);
		$smarty->assign('ORDER_DATE', xtc_date_long($order->info['date_purchased']));
		$smarty->assign('ORDER_STATUS', $order->info['orders_status']);
		$history_block = '<table summary="order history">';
		$order_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/account_history_info.html');
		$smarty->assign('info_message_1', $order_content);
		$smarty->assign('FORM_ACTION', '<br />'.$o_paypal->build_express_fehler_button().'<br />'.PAYPAL_NEUBUTTON);
	}
	if(isset($_SESSION['reshash']['FORMATED_ERRORS'])){
		$smarty->assign('error', $_SESSION['reshash']['FORMATED_ERRORS']);
	}
// EOF - Tomcraft - 2009-10-03 - Paypal Express Modul

// minimum/maximum order value
$checkout = true;
if ($_SESSION['cart']->show_total() > 0 ) {
 if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order'] ) {
  $_SESSION['allow_checkout'] = 'false';
  $more_to_buy = $_SESSION['customers_status']['customers_status_min_order'] - $_SESSION['cart']->show_total();
  $order_amount=$xtPrice->xtcFormat($more_to_buy, true);
  $min_order=$xtPrice->xtcFormat($_SESSION['customers_status']['customers_status_min_order'], true);
  $smarty->assign('info_message_1', MINIMUM_ORDER_VALUE_NOT_REACHED_1);
  $smarty->assign('info_message_2', MINIMUM_ORDER_VALUE_NOT_REACHED_2);
  $smarty->assign('order_amount', $order_amount);
  $smarty->assign('min_order', $min_order);
 }
 if  ($_SESSION['customers_status']['customers_status_max_order'] != 0) {
  if ($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order'] ) {
  $_SESSION['allow_checkout'] = 'false';
  $less_to_buy = $_SESSION['cart']->show_total() - $_SESSION['customers_status']['customers_status_max_order'];
  $max_order=$xtPrice->xtcFormat($_SESSION['customers_status']['customers_status_max_order'], true);
  $order_amount=$xtPrice->xtcFormat($less_to_buy, true);
  $smarty->assign('info_message_1', MAXIMUM_ORDER_VALUE_REACHED_1);
  $smarty->assign('info_message_2', MAXIMUM_ORDER_VALUE_REACHED_2);
  $smarty->assign('order_amount', $order_amount);
  $smarty->assign('min_order', $max_order);
  }
 }
}

// BOF - Tomcraft - 2009-10-03 - Paypal Express Modul
/*
	if ($_GET['info_message'])
		$smarty->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));
	$smarty->assign('BUTTON_RELOAD', xtc_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE_CART));
	$smarty->assign('BUTTON_CHECKOUT', '<a href="'.xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL').'">'.xtc_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT).'</a>');		
*/
	if(isset($_SESSION['paypal_warten'])):
		$smarty->assign('error', $_SESSION['paypal_warten']);
	else:
		if ($_GET['info_message'])
			$smarty->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));
		$smarty->assign('BUTTON_PAYPAL', $o_paypal->build_express_checkout_button());
		$smarty->assign('BUTTON_RELOAD', xtc_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE_CART));
		$smarty->assign('BUTTON_CHECKOUT', '<a href="'.xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL').'">'.xtc_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT).'</a>');
	endif;
// EOF - Tomcraft - 2009-10-03 - Paypal Express Modul
} else {
	// empty cart
	$cart_empty = true;
	if ($_GET['info_message'])
		$smarty->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));
	$smarty->assign('cart_empty', $cart_empty);
	$smarty->assign('BUTTON_CONTINUE', '<a href="'.xtc_href_link(FILENAME_DEFAULT).'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>');
}
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/shopping_cart.html');
$smarty->assign('main_content', $main_content);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
if (!defined(RM))
        $smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');

// BOF - Tomcraft - 2009-10-03 - Paypal Express Modul
if(!isset($_SESSION['paypal_warten'])):
	unset($_SESSION['nvpReqArray']);
	unset($_SESSION['reshash']['FORMATED_ERRORS']);
	unset($_SESSION['reshash']);
	unset($_SESSION['tmp_oID']);
else:
	unset($_SESSION['paypal_warten']);
endif;
// EOF - Tomcraft - 2009-10-03 - Paypal Express Modul

include ('includes/application_bottom.php');
?>