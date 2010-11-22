<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_shipping.php,v 1.15 2003/04/08); www.oscommerce.com
   (c) 2003 nextcommerce (checkout_shipping.php,v 1.20 2003/08/20); www.nextcommerce.org
   (c) 2006 xtCommerce (checkout_shipping.php 1037 2005-07-17)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
include ('includes/application_top.php');
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
// include needed functions

require_once (DIR_FS_INC.'xtc_address_label.inc.php');
require_once (DIR_FS_INC.'xtc_get_address_format_id.inc.php');
require_once (DIR_FS_INC.'xtc_count_shipping_modules.inc.php');

require (DIR_WS_CLASSES.'http_client.php');

// check if checkout is allowed
if (isset($_SESSION['allow_checkout']) && $_SESSION['allow_checkout'] == 'false')
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));

// if the customer is not logged on, redirect them to the login page
if (!isset ($_SESSION['customer_id'])) {
	if (ACCOUNT_OPTIONS == 'guest') {
		xtc_redirect(xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'));
	} else {
		xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
	}
}

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1) {
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}
//BOF - Dokuman - 2009-06-06 - checkout only if minimum order value is reached
if ($_SESSION['cart']->show_total() > 0 ) {
  if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order'] ) {
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
  }
}
//EOF - Dokuman - 2009-06-06 - checkout only if minimum order value is reached

// if no shipping destination address was selected, use the customers own address as default
if (!isset ($_SESSION['sendto'])) {
	$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
} else {
	// verify the selected shipping address
	$check_address_query = xtc_db_query("select count(*) as total from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $_SESSION['customer_id']."' and address_book_id = '".(int) $_SESSION['sendto']."'");
	$check_address = xtc_db_fetch_array($check_address_query);

	if ($check_address['total'] != '1') {
		$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
		if (isset ($_SESSION['shipping']))
			unset ($_SESSION['shipping']);
	}
}

require (DIR_WS_CLASSES.'order.php');
$order = new order();

//BOF - DokuMan - 2010-08-30 - check for cartID also in checkout_shipping
// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset ($_SESSION['cart']->cartID) && isset ($_SESSION['cartID'])) {
    if ($_SESSION['cart']->cartID !== $_SESSION['cartID']) {
        unset($_SESSION['shipping']);
        unset($_SESSION['payment']);
    }
}
//EOF - DokuMan - 2010-08-30 - check for cartID also in checkout_shipping

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
$_SESSION['cartID'] = $_SESSION['cart']->cartID;

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
if ($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_virtual() == 0)) { // GV Code added
	$_SESSION['shipping'] = false;
	$_SESSION['sendto'] = false;
	xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

$total_weight = $_SESSION['cart']->show_weight();
$total_count = $_SESSION['cart']->count_contents();

if ($order->delivery['country']['iso_code_2'] != '') {
	$_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
}
// load all enabled shipping modules
require (DIR_WS_CLASSES.'shipping.php');
$shipping_modules = new shipping;

if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
	switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
		case 'national' :
			if ($order->delivery['country_id'] == STORE_COUNTRY)
				$pass = true;
			break;
		case 'international' :
			if ($order->delivery['country_id'] != STORE_COUNTRY)
				$pass = true;
			break;
		case 'both' :
			$pass = true;
			break;
		default :
			$pass = false;
			break;
	}

	$free_shipping = false;
	if (($pass == true) && ($order->info['total'] - $order->info['shipping_cost'] >= $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, false, 0, true))) {
		$free_shipping = true;

		include (DIR_WS_LANGUAGES.$_SESSION['language'].'/modules/order_total/ot_shipping.php');
	}
} else {
	$free_shipping = false;
}

// process the selected shipping method
if (isset ($_POST['action']) && ($_POST['action'] == 'process')) {

	if ((xtc_count_shipping_modules() > 0) || ($free_shipping == true)) {
		if ((isset ($_POST['shipping'])) && (strpos($_POST['shipping'], '_'))) {
			$_SESSION['shipping'] = $_POST['shipping'];

			list ($module, $method) = explode('_', $_SESSION['shipping']);
			if ((isset($$module) && is_object($$module) ) || ($_SESSION['shipping'] == 'free_free')) {
				if ($_SESSION['shipping'] == 'free_free') {
					$quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
					$quote[0]['methods'][0]['cost'] = '0';
				} else {
					$quote = $shipping_modules->quote($method, $module);
				}
				if (isset ($quote['error'])) {
					unset ($_SESSION['shipping']);
				} else {
					if ((isset ($quote[0]['methods'][0]['title'])) && (isset ($quote[0]['methods'][0]['cost']))) {
						$_SESSION['shipping'] = array ('id' => $_SESSION['shipping'],
                                           'title' => (($free_shipping == true) ? $quote[0]['methods'][0]['title'] : $quote[0]['module'].' ('.$quote[0]['methods'][0]['title'].')'),
                                           'cost' => $quote[0]['methods'][0]['cost']);

						xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
					}
				}
			} else {
				unset ($_SESSION['shipping']);
			}
		}
	} else {
		$_SESSION['shipping'] = false;
		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
	}
}

// get all available shipping quotes
$quotes = $shipping_modules->quote();

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
if (!isset ($_SESSION['shipping']) || (isset ($_SESSION['shipping']) && ($_SESSION['shipping'] == false) && (xtc_count_shipping_modules() > 1)))
	$_SESSION['shipping'] = $shipping_modules->cheapest();

$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_SHIPPING, xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_SHIPPING, xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

require (DIR_WS_INCLUDES.'header.php');

$smarty->assign('FORM_ACTION', xtc_draw_form('checkout_address', xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')).xtc_draw_hidden_field('action', 'process'));
$smarty->assign('ADDRESS_LABEL', xtc_address_label($_SESSION['customer_id'], $_SESSION['sendto'], true, ' ', '<br />'));
$smarty->assign('BUTTON_ADDRESS', '<a href="'.xtc_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL').'">'.xtc_image_button('button_change_address.gif', IMAGE_BUTTON_CHANGE_ADDRESS).'</a>');
// DokuMan - 2009-06-01 - do not correct the name 'BUTON_CONTINUE' to remain compatible to standard templates
$smarty->assign('BUTON_CONTINUE', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
$smarty->assign('FORM_END', '</form>');

$module_smarty = new Smarty;
$shipping_block = ''; //DokuMan - 2010-08-30 - set undefined variable
if (xtc_count_shipping_modules() > 0) {
	$showtax = $_SESSION['customers_status']['customers_status_show_price_tax'];
	$module_smarty->assign('FREE_SHIPPING', $free_shipping);
	# free shipping or not...
	if ($free_shipping == true) {
		$module_smarty->assign('FREE_SHIPPING_TITLE', FREE_SHIPPING_TITLE);
		$module_smarty->assign('FREE_SHIPPING_DESCRIPTION', sprintf(FREE_SHIPPING_DESCRIPTION, $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, true, 0, true)).xtc_draw_hidden_field('shipping', 'free_free'));
		$module_smarty->assign('FREE_SHIPPING_ICON', $quotes[$i]['icon']);
	} else {
		$radio_buttons = 0;
		#loop through installed shipping methods...
		for ($i = 0, $n = sizeof($quotes); $i < $n; $i ++) {
			if (!isset ($quotes[$i]['error'])) {
				for ($j = 0, $n2 = sizeof($quotes[$i]['methods']); $j < $n2; $j ++) {
					# set the radio button to be checked if it is the method chosen
					$quotes[$i]['methods'][$j]['radio_buttons'] = $radio_buttons;
					$checked = (($quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']['id']) ? true : false);
					if (($checked == true) || ($n == 1 && $n2 == 1)) {
						$quotes[$i]['methods'][$j]['checked'] = 1;
					}
					if (($n > 1) || ($n2 > 1)) {
						if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = '';
						if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = 0;
						//BOF - DokuMan - 2010-09-17 - Undefined index: tax
						if (!isset($quotes[$i]['tax']))
							$quotes[$i]['tax'] = 0;
						//EOF - DokuMan - 2010-09-17 - Undefined index: tax
						$quotes[$i]['methods'][$j]['price'] = $xtPrice->xtcFormat(xtc_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true);
						$quotes[$i]['methods'][$j]['radio_field'] = xtc_draw_radio_field('shipping', $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'], $checked);
					} else {
						if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = 0;
						$quotes[$i]['methods'][$j]['price'] = $xtPrice->xtcFormat(xtc_add_tax($quotes[$i]['methods'][$j]['cost'], isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0), true, 0, true).xtc_draw_hidden_field('shipping', $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id']);
					}
					$radio_buttons ++;
				}
			}
		}
		$module_smarty->assign('module_content', $quotes);
	}
	$module_smarty->caching = 0;
	$shipping_block = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/checkout_shipping_block.html');
}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('SHIPPING_BLOCK', $shipping_block);
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/checkout_shipping.html');
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined('RM'))
	$smarty->loadfilter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
?>