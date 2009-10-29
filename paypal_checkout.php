<?php
/**
 * Project: xt:Commerce - eCommerce Engine
 * @version $Id
 *
 * xt:Commerce - Shopsoftware
 * (c) 2003-2007 xt:Commerce (Winger/Zanier), http://www.xt-commerce.com
 *
 * xt:Commerce ist eine gesch�tzte Handelsmarke und wird vertreten durch die xt:Commerce GmbH (Austria)
 * xt:Commerce is a protected trademark and represented by the xt:Commerce GmbH (Austria)
 *
 * @copyright Copyright 2003-2007 xt:Commerce (Winger/Zanier), www.xt-commerce.com
 * @copyright based on Copyright 2002-2003 osCommerce; www.oscommerce.com
 * @copyright based on Copyright 2003 nextcommerce; www.nextcommerce.org
 * @license http://www.xt-commerce.com.com/license/2_0.txt GNU Public License V2.0
 *
 * For questions, help, comments, discussion, etc., please join the
 * xt:Commerce Support Forums at www.xt-commerce.com
 *
 * ab 15.08.2008 Teile vom Hamburger-Internetdienst ge�ndert
 * Hamburger-Internetdienst Support Forums at www.forum.hamburger-internetdienst.de
 * Stand 29.04.2009
*/
include('includes/application_top.php');
// create smarty elements
$smarty = new Smarty;
require(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
// include needed functions
require_once(DIR_FS_INC.'xtc_address_label.inc.php');
require_once(DIR_FS_INC.'xtc_get_address_format_id.inc.php');
require_once(DIR_FS_INC.'xtc_count_shipping_modules.inc.php');
require_once(DIR_FS_INC . 'xtc_check_stock.inc.php');
require_once(DIR_FS_INC . 'xtc_calculate_tax.inc.php');
require_once(DIR_FS_INC . 'xtc_check_stock.inc.php');
require_once(DIR_FS_INC . 'xtc_display_tax_value.inc.php');

require(DIR_WS_CLASSES.'http_client.php');
unset($_SESSION['tmp_oID']);

switch($_GET['error_message']) {
	case "1":
		$message = str_replace('\n', '', ERROR_CONDITIONS_NOT_ACCEPTED);
		$messageStack->add('checkout_payment', $message);
		break;
	case "2":
		$message = str_replace('\n', '', ERROR_ADDRESS_NOT_ACCEPTED);
		$messageStack->add('checkout_payment', $message);
		break;
	case "12":
		$message = str_replace('\n', '', ERROR_CONDITIONS_NOT_ACCEPTED);
		$messageStack->add('checkout_payment', $message);
		$message = str_replace('\n', '', ERROR_ADDRESS_NOT_ACCEPTED);
		$messageStack->add('checkout_payment', $message);
		break;
}

// Kein Token mehr da durch Back im Browser auf die Seite
if(!$_SESSION['reshash']['TOKEN']):
	unset($_SESSION['payment']);
	unset($_SESSION['nvpReqArray']);
	unset($_SESSION['reshash']);
	unset($_SESSION['sendto']);
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
endif;

// Get Customer Data and Check for existing Account.
$o_paypal->paypal_get_customer_data();

if(!isset($_SESSION['customer_id'])) {
	if(ACCOUNT_OPTIONS == 'guest') {
		xtc_redirect(xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'));
	} else {
		xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
	}
}
//// zahlungsweise in session schreiben
$_SESSION['payment'] = 'paypalexpress';

if(isset($_POST['act_shipping']))
	$_SESSION['act_shipping'] = 'true';

if(isset($_POST['act_payment']))
	$_SESSION['act_payment'] = 'true';

if(isset($_POST['payment']))
	$_SESSION['payment'] = xtc_db_prepare_input($_POST['payment']);

if($_POST['comments_added'] != '')
	$_SESSION['comments'] = xtc_db_prepare_input($_POST['comments']);

//-- TheMedia Begin check if display conditions on checkout page is true
if(isset($_POST['cot_gv']))
	$_SESSION['cot_gv'] = true;

// if there is nothing in the customers cart, redirect them to the shopping cart page
if($_SESSION['cart']->count_contents() < 1)
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));

// Kein Token mehr da durch Back im Browser auf die Seite
if( !($_SESSION['nvpReqArray']['TOKEN']) OR !($_SESSION['reshash']['PAYERID']) ):
	unset($_SESSION['payment']);
	unset($_SESSION['nvpReqArray']);
	unset($_SESSION['reshash']);
	unset($_SESSION['sendto']);
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
endif;

if(isset($_SESSION['credit_covers']))
	unset($_SESSION['credit_covers']); //ICW ADDED FOR CREDIT CLASS SYSTEM

// Stock Check
if((STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true')) {
	$products = $_SESSION['cart']->get_products();
	$any_out_of_stock = 0;
	for($i = 0, $n = sizeof($products); $i < $n; $i++) {
		if(xtc_check_stock($products[$i]['id'], $products[$i]['quantity']))
			$any_out_of_stock = 1;
	}
	if($any_out_of_stock == 1)
		xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

// if no shipping destination address was selected, use the customers own address as default
if(!isset($_SESSION['sendto'])) {
	$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
} else {
	// verify the selected shipping address
	$check_address_query = xtc_db_query("select count(*) as total from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $_SESSION['customer_id']."' and address_book_id = '".(int) $_SESSION['sendto']."'");
	$check_address = xtc_db_fetch_array($check_address_query);
	if($check_address['total'] != '1') {
		$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
		if(isset($_SESSION['shipping']))
			unset($_SESSION['shipping']);
	}
}

// if no billing destination address was selected, use the customers own address as default
if(!isset($_SESSION['billto'])) {
	$_SESSION['billto'] = $_SESSION['customer_default_address_id'];
} else {
	// verify the selected billing address
	$check_address_query = xtc_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int) $_SESSION['customer_id'] . "' and address_book_id = '" . (int) $_SESSION['billto'] . "'");
	$check_address = xtc_db_fetch_array($check_address_query);
	if($check_address['total'] != '1') {
		$_SESSION['billto'] = $_SESSION['customer_default_address_id'];
		if(isset($_SESSION['payment']))
			unset($_SESSION['payment']);
	}
}

require(DIR_WS_CLASSES.'order.php');
$order = new order();
if($order->delivery['country']['iso_code_2'] != '') {
	$_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
}

$total_weight = $_SESSION['cart']->show_weight();
$total_count = $_SESSION['cart']->count_contents();

// load all enabled shipping modules
require(DIR_WS_CLASSES.'shipping.php');
$shipping_modules = new shipping;
if(defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
	switch(MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
		case 'national' :
			if($order->delivery['country_id'] == STORE_COUNTRY)
				$pass = true;
			break;
		case 'international' :
			if($order->delivery['country_id'] != STORE_COUNTRY)
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
	if(($pass == true) && ($order->info['total'] - $order->info['shipping_cost'] >= $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, false, 0, true))) {
		$free_shipping = true;
		include(DIR_WS_LANGUAGES.$_SESSION['language'].'/modules/order_total/ot_shipping.php');
	}
} else {
	$free_shipping = false;
}

// process the selected shipping method
if(isset($_POST['action']) && ($_POST['action'] == 'process')) {
	if((xtc_count_shipping_modules() > 0) || ($free_shipping == true)) {
		if((isset($_POST['shipping'])) && (strpos($_POST['shipping'], '_'))) {
			$_SESSION['shipping'] = $_POST['shipping'];
			list($module, $method) = explode('_', $_SESSION['shipping']);
			if(is_object($$module) || ($_SESSION['shipping'] == 'free_free')) {
				if($_SESSION['shipping'] == 'free_free') {
					$quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
					$quote[0]['methods'][0]['cost'] = '0';
				} else {
					$quote = $shipping_modules->quote($method, $module);
				}
				if(isset($quote['error'])) {
					unset($_SESSION['shipping']);
				} else {
					if((isset($quote[0]['methods'][0]['title'])) && (isset($quote[0]['methods'][0]['cost']))) {
						$_SESSION['shipping'] = array('id' => $_SESSION['shipping'], 'title' => (($free_shipping == true) ? $quote[0]['methods'][0]['title'] : $quote[0]['module'].' ('.$quote[0]['methods'][0]['title'].')'), 'cost' => $quote[0]['methods'][0]['cost']);
						xtc_redirect(xtc_href_link(FILENAME_PAYPAL_CHECKOUT, '', 'SSL'));
					}
				}
			} else {
				unset($_SESSION['shipping']);
			}
		}
	} else {
		$_SESSION['shipping'] = false;
		xtc_redirect(xtc_href_link(FILENAME_PAYPAL_CHECKOUT, '', 'SSL'));
	}
}

// get all available shipping quotes
$quotes = $shipping_modules->quote();
// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
if(!isset($_SESSION['shipping']) || (isset($_SESSION['shipping']) && ($_SESSION['shipping'] == false) && (xtc_count_shipping_modules() > 1)))
	$_SESSION['shipping'] = $shipping_modules->cheapest();
$order = new order();
// load all enabled payment modules
require(DIR_WS_CLASSES . 'payment.php');

$payment_modules = new payment($_SESSION['payment']);
$payment_modules->update_status();

require(DIR_WS_CLASSES . 'order_total.php'); // GV Code ICW ADDED FOR CREDIT CLASS SYSTEM
$order_total_modules = new order_total();
$order_total_modules->process();

// GV Code Start
$order_total_modules->collect_posts();
$order_total_modules->pre_confirmation_check();
// GV Code End

if(is_array($payment_modules->modules))
	$payment_modules->pre_confirmation_check();

$breadcrumb->add(NAVBAR_TITLE_PAYPAL_CHECKOUT, xtc_href_link(FILENAME_PAYPAL_CHECKOUT, '', 'SSL'));
require(DIR_WS_INCLUDES.'header.php');
if(SHOW_IP_LOG == 'true') {
	$smarty->assign('IP_LOG', 'true');
	if($_SERVER["HTTP_X_FORWARDED_FOR"]) {
		$customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$customers_ip = $_SERVER["REMOTE_ADDR"];
	}
	$smarty->assign('CUSTOMERS_IP',$customers_ip);
}

$smarty->assign('FORM_SHIPPING_ACTION', xtc_draw_form('checkout_shipping', xtc_href_link(FILENAME_PAYPAL_CHECKOUT, '', 'SSL')).xtc_draw_hidden_field('action', 'process'));
$smarty->assign('ADDRESS_SHIPPING_LABEL', xtc_address_label($_SESSION['customer_id'], $_SESSION['sendto'], true, ' ', '<br />'));
$smarty->assign('BUTTON_CONTINUE', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
$smarty->assign('FORM_END', '</form>');
$smarty->assign('ADDRESS_PAYMENT_LABEL', xtc_address_label($_SESSION['customer_id'], $_SESSION['billto'], true, ' ', '<br />'));
if(PAYPAL_EXPRESS_ADDRESS_CHANGE == 'true') {
	$smarty->assign('BUTTON_SHIPPING_ADDRESS', '<a href="'.xtc_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL').'">'.xtc_image_button('button_change_address.gif', IMAGE_BUTTON_CHANGE_ADDRESS).'</a>');
	$smarty->assign('BUTTON_PAYMENT_ADDRESS', '<a href="' . xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL') . '">' . xtc_image_button('button_change_address.gif', IMAGE_BUTTON_CHANGE_ADDRESS) . '</a>');
}
$module_smarty = new Smarty;
if(xtc_count_shipping_modules() > 0) {
	$showtax = $_SESSION['customers_status']['customers_status_show_price_tax'];
	$module_smarty->assign('FREE_SHIPPING', $free_shipping);
	# free shipping or not...
	if($free_shipping == true) {
		$module_smarty->assign('FREE_SHIPPING_TITLE', FREE_SHIPPING_TITLE);
		$module_smarty->assign('FREE_SHIPPING_DESCRIPTION', sprintf(FREE_SHIPPING_DESCRIPTION, $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, true, 0, true)).xtc_draw_hidden_field('shipping', 'free_free'));
		$module_smarty->assign('FREE_SHIPPING_ICON', $quotes[$i]['icon']);
	} else {
		$radio_buttons = 0;
		#loop through installed shipping methods...
		for($i = 0, $n = sizeof($quotes); $i < $n; $i ++) {
			if(!isset($quotes[$i]['error'])) {
				for($j = 0, $n2 = sizeof($quotes[$i]['methods']); $j < $n2; $j ++) {
					# set the radio button to be checked if it is the method chosen
					$quotes[$i]['methods'][$j]['radio_buttons'] = $radio_buttons;
					$checked = (($quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']['id']) ? true : false);
					if(($checked == true) || ($n == 1 && $n2 == 1)) {
						$quotes[$i]['methods'][$j]['checked'] = 1;
					}
					if(($n > 1) || ($n2 > 1)) {
						if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = '';
						if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = 0;
						$quotes[$i]['methods'][$j]['price'] = $xtPrice->xtcFormat(xtc_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true);
						$quotes[$i]['methods'][$j]['radio_field'] = xtc_draw_hidden_field('act_shipping', 'true').xtc_draw_radio_field('shipping', $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'], $checked, 'onclick="this.form.submit();"');
					} else {
						if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
							$quotes[$i]['tax'] = 0;
						$quotes[$i]['methods'][$j]['price'] = $xtPrice->xtcFormat(xtc_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true).xtc_draw_hidden_field('shipping', $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id']);
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

if($order->info['total'] > 0) {
	if(isset($_GET['payment_error']) && is_object(${ $_GET['payment_error'] }) && ($error = ${$_GET['payment_error']}->get_error())) {
		$smarty->assign('error', htmlspecialchars($error['error']));
	}
	$selection = $payment_modules->selection();
	$radio_buttons = 0;
	for($i = 0, $n = sizeof($selection); $i < $n; $i++) {
		$selection[$i]['radio_buttons'] = $radio_buttons;
		if(($selection[$i]['id'] == $payment) || ($n == 1)) {
			$selection[$i]['checked'] = 1;
		}
		if(sizeof($selection) > 1) {
			$selection[$i]['selection'] = xtc_draw_radio_field('payment', $selection[$i]['id'], ($selection[$i]['id'] == $_SESSION['payment']), 'onclick="this.form.submit();"').xtc_draw_hidden_field('act_payment', 'true');
		} else {
			$selection[$i]['selection'] = xtc_draw_hidden_field('payment', $selection[$i]['id']).xtc_draw_hidden_field('act_payment', 'true');
		}
		if(isset($selection[$i]['error'])) {

		} else {
			$radio_buttons++;
		}
	}
	$module_smarty->assign('module_content', $selection);
} else {
	$smarty->assign('GV_COVER', 'true');
}

if(ACTIVATE_GIFT_SYSTEM == 'true') {
	$smarty->assign('module_gift', $order_total_modules->credit_selection());
}

$module_smarty->caching = 0;
$payment_block = $module_smarty->fetch(CURRENT_TEMPLATE . '/module/checkout_payment_block.html');

if($messageStack->size('checkout_payment') > 0) {
	$smarty->assign('error', $messageStack->output('checkout_payment'));
}

if($order->info['payment_method'] != 'no_payment' && $order->info['payment_method'] != '') {
	include(DIR_WS_LANGUAGES . '/' . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php');
	$smarty->assign('PAYMENT_METHOD', constant(MODULE_PAYMENT_ . strtoupper($order->info['payment_method']) . _TEXT_TITLE));
}

$smarty->assign('products_data', $order->products);

if(MODULE_ORDER_TOTAL_INSTALLED) {
	$smarty->assign('total_block', $order_total_modules->pp_output());
}

if(is_array($checkout_payment_modules->modules)) {
	if($confirmation = $checkout_payment_modules->confirmation()) {
		for($i = 0, $n = sizeof($confirmation['fields']); $i < $n; $i++) {
			$payment_info[] = array('TITLE'=>$confirmation['fields'][$i]['title'],
															'FIELD'=>stripslashes($confirmation['fields'][$i]['field']));
		}
		$smarty->assign('PAYMENT_INFORMATION', $payment_info);
	}
}

if(isset($$_SESSION['payment']->form_action_url) && !$$_SESSION['payment']->tmpOrders) {
	$form_action_url = $$_SESSION['payment']->form_action_url;
} else {
	$form_action_url = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
}
$smarty->assign('CHECKOUT_FORM', xtc_draw_form('checkout_confirmation', $form_action_url, 'post'));
$checkout_payment_button = '';
if(is_array($checkout_payment_modules->modules)) {
	$checkout_payment_button .= $checkout_payment_modules->process_button();
}
$smarty->assign('MODULE_BUTTONS', $checkout_payment_button);
$smarty->assign('CHECKOUT_BUTTON', xtc_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER) . "\n");

if($order->info['shipping_method']) {
	$smarty->assign('SHIPPING_METHOD', $order->info['shipping_method']);
	$smarty->assign('SHIPPING_EDIT', xtc_href_link(FILENAME_PAYPAL_CHECKOUT_SHIPPING, '', 'SSL'));
}
$smarty->assign('COMMENTS', xtc_draw_textarea_field('comments', 'soft', '60', '5', $_SESSION['comments']) . xtc_draw_hidden_field('comments_added', 'YES'));
$smarty->assign('ADR_checkbox', '<input type="checkbox" value="address" name="address" />');
//check if display conditions on checkout page is true
if(DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
	if(GROUP_CHECK == 'true') {
		$group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
	}
	$shop_content_query = xtc_db_query("SELECT
																			content_title,
																			content_heading,
																			content_text,
																			content_file
																			FROM " . TABLE_CONTENT_MANAGER . "
																			WHERE content_group='3' " . $group_check . "
																			AND languages_id='" . $_SESSION['languages_id'] . "'");
	$shop_content_data = xtc_db_fetch_array($shop_content_query);
	if($shop_content_data['content_file'] != '') {
		$conditions = '<iframe SRC="' . DIR_WS_CATALOG . 'media/content/' . $shop_content_data['content_file'] . '" width="100%" height="300">';
		$conditions .= '</iframe>';
	} else {
		$conditions = '<textarea name="blabla" cols="60" rows="10" readonly="readonly">' . strip_tags(str_replace('<br />', "\n", $shop_content_data['content_text'])) . '</textarea>';
	}
	$smarty->assign('AGB', $conditions);
	$smarty->assign('AGB_LINK', $main->getContentLink(3, MORE_INFO));
	if(isset($_GET['step']) && $_GET['step'] == 'step2') {
		$smarty->assign('AGB_checkbox', '<input type="checkbox" value="conditions" name="conditions" checked />');
	} else {
		$smarty->assign('AGB_checkbox', '<input type="checkbox" value="conditions" name="conditions" />');
	}
}

//check if display conditions on checkout page is true
if(DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {
	if(GROUP_CHECK == 'true') {
		$group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
	}
	$shop_content_query = "SELECT
												content_title,
												content_heading,
												content_text,
												content_file
												FROM " . TABLE_CONTENT_MANAGER . "
												WHERE content_group='" . REVOCATION_ID . "' " . $group_check . "
												AND languages_id='" . $_SESSION['languages_id'] . "'";
	$shop_content_query = xtc_db_query($shop_content_query);
	if($shop_content_query):
		$shop_content_data = xtc_db_fetch_array($shop_content_query);
		if($shop_content_data['content_file'] != '') {
			ob_start();
			if(strpos($shop_content_data['content_file'], '.txt'))
				echo '<pre>';
			include(DIR_FS_CATALOG . 'media/content/' . $shop_content_data['content_file']);
			if(strpos($shop_content_data['content_file'], '.txt'))
				echo '</pre>';
			$revocation = ob_get_contents();
			ob_end_clean();
		} else {
			$revocation = $shop_content_data['content_text'];
		}
		$smarty->assign('REVOCATION', $revocation);
		$smarty->assign('REVOCATION_TITLE', $shop_content_data['content_heading']);
		$smarty->assign('REVOCATION_LINK', $main->getContentLink(REVOCATION_ID, MORE_INFO));
	endif;
}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('SHIPPING_BLOCK', $shipping_block);
$payment_hidden = xtc_draw_hidden_field('payment','paypalexpress') . xtc_draw_hidden_field('act_payment','true');
$smarty->assign('PAYMENT_HIDDEN', $payment_hidden);
$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/checkout_paypal.html');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if(!defined(RM))
	$smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include('includes/application_bottom.php');
?>
