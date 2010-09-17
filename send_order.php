<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce; www.oscommerce.com
   (c) 2003      nextcommerce; www.nextcommerce.org
   (c) 2006      xt:Commerce; www.xt-commerce.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once (DIR_FS_INC.'xtc_get_order_data.inc.php');
require_once (DIR_FS_INC.'xtc_get_attributes_model.inc.php');
// check if customer is allowed to send this order!
$order_query_check = xtc_db_query("SELECT
  					customers_id
  					FROM ".TABLE_ORDERS."
  					WHERE orders_id='".$insert_id."'");

$order_check = xtc_db_fetch_array($order_query_check);
//BOF - web28 - 2010-03-20 - Send Order by Admin
//if ($_SESSION['customer_id'] == $order_check['customers_id'] ) {
if ($_SESSION['customer_id'] == $order_check['customers_id'] || $send_by_admin) {
//EOF - web28 - 2010-03-20 - Send Order by Admin

	$order = new order($insert_id);

  //BOF - web28 - 2010-03-20 - Send Order by Admin
	if (isset($send_by_admin)) {//DokuMan - 2010-09-18 - Undefined variable: send_by_admin
		$xtPrice = new xtcPrice($order->info['currency'], $order->info['status']);
	}
  //EOF - web28 - 2010-03-20 - Send Order by Admin

	$smarty->assign('address_label_customer', xtc_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
	$smarty->assign('address_label_shipping', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
    if (!isset($_SESSION['credit_covers']) || $_SESSION['credit_covers'] != '1') {
		$smarty->assign('address_label_payment', xtc_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
	}
	$smarty->assign('csID', $order->customer['csID']);

	$order_total = $order->getTotalData($insert_id); //ACHTUNG für Bestellbestätigung  aus Admin Funktion in admin/includes/classes/order.php
	$smarty->assign('order_data', $order->getOrderData($insert_id)); //ACHTUNG für Bestellbestätigung  aus Admin Funktion in admin/includes/classes/order.php
	$smarty->assign('order_total', $order_total['data']);

	// assign language to template for caching
	$smarty->assign('language', $_SESSION['language']);
	//BOF - GTB - 2010-08-03 - Security Fix - Base
	$smarty->assign('tpl_path',DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
	//$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
	//EOF - GTB - 2010-08-03 - Security Fix - Base
	$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
	$smarty->assign('oID', $insert_id);
	if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
	//BOF - web28 - 2010-03-20 - Send Order by Admin
        if (isset($send_by_admin)) include (DIR_FS_LANGUAGES.$_SESSION['language'].'/modules/payment/'.$order->info['payment_method'].'.php'); //DokuMan - 2010-09-18 - Undefined variable: send_by_admin
		else
	//BOF - web28 - 2010-03-20 - Send Order by Admin
		include (DIR_WS_LANGUAGES.$_SESSION['language'].'/modules/payment/'.$order->info['payment_method'].'.php');
		$payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
	}
	$smarty->assign('PAYMENT_METHOD', $payment_method);
	$smarty->assign('DATE', xtc_date_long($order->info['date_purchased']));

	$smarty->assign('NAME', $order->customer['name']);

	//BOF - web28 - 2010-08-20 - Anrede {$GENDER} in Bestätigungsmail verfügbar machen
	$gender_query = xtc_db_query("SELECT customers_gender FROM " . TABLE_CUSTOMERS . " WHERE customers_id = '" . $order->customer['id'] . "'");
	$gender = xtc_db_fetch_array($gender_query);
	if ($gender['customers_gender']=='f') {
		$smarty->assign('GENDER', FEMALE);
	} elseif ($gender['customers_gender']=='m') {
		$smarty->assign('GENDER', MALE);
	} else {
		$smarty->assign('GENDER', '');
	}
	//EOF - web28 - 2010-08-20 - Anrede {$GENDER} in Bestätigungsmail verfügbar machen

	//BOF - web28 - 2010-08-20 - Erweiterung Variablen für Bestätigungsmail
	$smarty->assign('CITY', $order->customer['city']);
	$smarty->assign('POSTCODE', $order->customer['postcode']);
	$smarty->assign('STATE', $order->customer['state']);
	$smarty->assign('COUNTRY', $order->customer['country']);
	$smarty->assign('COMPANY', $order->customer['company']);
	$smarty->assign('STREET', $order->customer['street_address']);
	$smarty->assign('FIRSTNAME', $order->customer['firstname']);
    $smarty->assign('LASTNAME', $order->customer['lastname']);
	//EOF - web28 - 2010-08-20 - Erweiterung Variablen für Bestätigungsmail

	$smarty->assign('COMMENTS', $order->info['comments']);
	$smarty->assign('EMAIL', $order->customer['email_address']);
	$smarty->assign('PHONE',$order->customer['telephone']);

	//BOF  - web28 - 2010-03-27 PayPal Bezahl-Link
	unset ($_SESSION['paypal_link']);
    if ($order->info['payment_method'] == 'paypal_ipn') {

		//BOF - web28 - 2010-06-11 - Send Order  by Admin Paypal IPN
	    if(isset($send_by_admin)) { //DokuMan - 2010-09-18 - Undefined variable: send_by_admin
			require (DIR_FS_CATALOG_MODULES.'payment/paypal_ipn.php');
			include(DIR_FS_LANGUAGES.$_SESSION['language'].'/modules/payment/paypal_ipn.php');
			$payment_modules = new paypal_ipn;
		}
		//EOF - web28 - 2010-06-11 - Send Order  by Admin Paypal IPN

	    $order_id= $insert_id;
		$paypal_link = array();
        $payment_modules->create_paypal_link();

		$smarty->assign('PAYMENT_INFO_HTML', $paypal_link['html']);
		$smarty->assign('PAYMENT_INFO_TXT',  MODULE_PAYMENT_PAYPAL_IPN_TXT_EMAIL . $paypal_link['text']);
        $_SESSION['paypal_link']= $paypal_link['checkout'];

    }
	//EOF  - web28 - 2010-03-27 PayPal Bezahl-Link

	// PAYMENT MODUL TEXTS
	// EU Bank Transfer
	if ($order->info['payment_method'] == 'eustandardtransfer') {
		$smarty->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION);
		$smarty->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION));
	}

	// MONEYORDER
	if ($order->info['payment_method'] == 'moneyorder') {
		$smarty->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION);
		$smarty->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION));
	}

	// dont allow cache
	$smarty->caching = false;

	$html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/order_mail.html');
	$txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/order_mail.txt');

	// create subject
	$order_subject = str_replace('{$nr}', $insert_id, EMAIL_BILLING_SUBJECT_ORDER);
	$order_subject = str_replace('{$date}', strftime(DATE_FORMAT_LONG), $order_subject);
	$order_subject = str_replace('{$lastname}', $order->customer['lastname'], $order_subject);
	$order_subject = str_replace('{$firstname}', $order->customer['firstname'], $order_subject);

	// send mail to admin
  //BOF Dokuman - 2009-08-19 - BUGFIX: #0000227 customers surname in reply address in orders mail to admin
  //xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, EMAIL_BILLING_ADDRESS, STORE_NAME, EMAIL_BILLING_FORWARDING_STRING, $order->customer['email_address'], $order->customer['firstname'], '', '', $order_subject, $html_mail, $txt_mail);
	xtc_php_mail(EMAIL_BILLING_ADDRESS,
				 EMAIL_BILLING_NAME,
				 EMAIL_BILLING_ADDRESS,
				 STORE_NAME,
				 EMAIL_BILLING_FORWARDING_STRING,
				 $order->customer['email_address'],
				 $order->customer['firstname'].' '.$order->customer['lastname'], '', '',
				 $order_subject,
				 $html_mail,
				 $txt_mail);
  //EOF Dokuman - 2009-08-19 - BUGFIX: #0000227 customers surname in reply address in orders mail to admin

	// send mail to customer
  //BOF - Dokuman - 2009-10-17 - Send emails to customer only, when set to "true" in admin panel
  //BOF - web28 - 2010-03-20 - Send Order by Admin
  //if (SEND_EMAILS == 'true') {
	if (SEND_EMAILS == 'true' || $send_by_admin) {
  //BOF - web28 - 2010-03-20 - Send Order by Admin
  //EOF - Dokuman - 2009-10-17 - Send emails to customer only, when set to "true" in admin panel
	xtc_php_mail(EMAIL_BILLING_ADDRESS,
				 EMAIL_BILLING_NAME,
				 $order->customer['email_address'],
				 $order->customer['firstname'].' '.$order->customer['lastname'], '',
				 EMAIL_BILLING_REPLY_ADDRESS,
				 EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '',
				 $order_subject,
				 $html_mail,
				 $txt_mail);
  //BOF - Dokuman - 2009-10-17 - Send emails to customer only, when set to "true" in admin panel
  }
  //EOF - Dokuman - 2009-10-17 - Send emails to customer only, when set to "true" in admin panel

	if (AFTERBUY_ACTIVATED == 'true') {
		require_once (DIR_WS_CLASSES.'afterbuy.php');
		$aBUY = new xtc_afterbuy_functions($insert_id);
		if ($aBUY->order_send())
			$aBUY->process_order();
	}
  //BOF - web28 - 2010-03-20 - Send Order by Admin
	if(isset($send_by_admin)) { //DokuMan - 2010-09-18 - Undefined variable: send_by_admin
	    $customer_notified = '1';
		$orders_status_id = '1';

		xtc_db_query("insert into ".TABLE_ORDERS_STATUS_HISTORY." (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".xtc_db_input($insert_id)."', '".xtc_db_input($orders_status_id)."', now(), '".$customer_notified."', '".COMMENT_SEND_ORDER_BY_ADMIN."')");

		$messageStack->add_session(SUCCESS_ORDER_SEND, 'success');

		if (isset($_GET['site']) && $_GET['site'] == 1) { //DokuMan - 2010-09-18 - Undefined variable
			xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=edit'));
		} else xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID']));
	}
  //EOF - web28 - 2010-03-20 - Send Order by Admin

} else {
	$smarty->assign('ERROR', 'You are not allowed to view this order!');
	$smarty->display(CURRENT_TEMPLATE.'/module/error_message.html');
}
?>