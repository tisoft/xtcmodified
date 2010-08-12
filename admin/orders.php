<?php

/* --------------------------------------------------------------
   $Id: orders.php 1189 2005-08-28 15:27:00Z hhgag $
   $Id: orders.php 467 2007-07-25 15:17:27Z mzanier $
   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(orders.php,v 1.109 2003/05/28); www.oscommerce.com 
   (c) 2003	 nextcommerce (orders.php,v 1.19 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------
   Third Party contribution:
   OSC German Banktransfer v0.85a       	Autor:	Dominik Guder <osc@guder.org>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   credit card encryption functions for the catalog module
   BMC 2003 for the CC CVV Module

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require ('includes/application_top.php');
require_once (DIR_FS_CATALOG.DIR_WS_CLASSES.'class.phpmailer.php');
require_once (DIR_FS_INC.'xtc_php_mail.inc.php');
require_once (DIR_FS_INC.'xtc_add_tax.inc.php');
require_once (DIR_FS_INC.'changedataout.inc.php');
require_once (DIR_FS_INC.'xtc_validate_vatid_status.inc.php');
require_once (DIR_FS_INC.'xtc_get_attributes_model.inc.php');

// initiate template engine for mail
$smarty = new Smarty;
require (DIR_WS_CLASSES.'currencies.php');
$currencies = new currencies();


if ((($_GET['action'] == 'edit') || ($_GET['action'] == 'update_order')) && ($_GET['oID'])) {
	$oID = xtc_db_prepare_input($_GET['oID']);

	$orders_query = xtc_db_query("select orders_id from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($oID)."'");
	$order_exists = true;
	if (!xtc_db_num_rows($orders_query)) {
		$order_exists = false;
		$messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
	}
}
//BOF - web28 - 2010-04-10 added for ADMIN SEARCH BAR
if ($_GET['action'] == 'search' && $_GET['oID']) {
	$oID = xtc_db_prepare_input($_GET['oID']);	
	$orders_query_raw = "select o.orders_id, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and o.orders_id LIKE '%".xtc_db_input($oID)."%' and ot.class = 'ot_total' order by o.orders_id DESC";
	$orders_query = xtc_db_query($orders_query_raw);
	$order_exists = false;
	if (xtc_db_num_rows($orders_query) == 1) {
	   $order_exists = true;
	   $oID_array = xtc_db_fetch_array($orders_query);
	   $oID = $oID_array['orders_id'];
	   $_GET['action'] = 'edit';
	   $_GET['oID'] = $oID;
	   //$messageStack->add('1 Treffer: ' . $oID, 'notice');
    }	
}
//EOF  - web28 - 2010-04-10 added for ADMIN SEARCH BAR

require (DIR_WS_CLASSES.'order.php');
if ((($_GET['action'] == 'edit') || ($_GET['action'] == 'update_order')) && ($order_exists)) {
	$order = new order($oID);
}

  $lang_query = xtc_db_query("select languages_id from " . TABLE_LANGUAGES . " where directory = '" . $order->info['language'] . "'");
  $lang = xtc_db_fetch_array($lang_query);
  $lang=$lang['languages_id'];

if (!isset($lang)) $lang=$_SESSION['languages_id'];
$orders_statuses = array ();
$orders_status_array = array ();
$orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$lang."'");
while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
	$orders_statuses[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}
switch ($_GET['action']) {

  //BOF - DokuMan - 2010-08-12 resend email to admin and/or customer
	case 'send' :
		$send_to_customer = 0;
		$send_to_admin = 0;
		
		if (isset($_GET['stc']))
			$send_to_customer = $_GET['stc'];
		if (isset($_GET['sta']))
			$send_to_admin = $_GET['sta'];

		$oID = xtc_db_prepare_input($_GET['oID']);
		$order = new order($oID);
		require (DIR_FS_CATALOG.DIR_WS_CLASSES.'xtcPrice.php');
		$xtPrice = new xtcPrice($order->info['currency'], $order->info['status']);
	// set dirs manual
		$smarty->template_dir = DIR_FS_CATALOG.'templates';
		$smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
		$smarty->config_dir = DIR_FS_CATALOG.'lang';
		
		$smarty->assign('address_label_customer', xtc_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
		$smarty->assign('address_label_shipping', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
		if ($_SESSION['credit_covers'] != '1') {
			$smarty->assign('address_label_payment', xtc_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
		}
		$smarty->assign('csID', $order->customer['csID']);
	
		// get products data
		$order_query = xtc_db_query("SELECT
								products_id,
								orders_products_id,
								products_model,
								products_name,
								final_price,
								products_quantity
								FROM ".TABLE_ORDERS_PRODUCTS."
								WHERE orders_id='".$oID."'");
	
		$order_data = array ();
		while ($order_data_values = xtc_db_fetch_array($order_query)) {
			$attributes_query = xtc_db_query("SELECT
									products_options,
									products_options_values,
									price_prefix,
									options_values_price
									FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES."
									WHERE orders_products_id='".$order_data_values['orders_products_id']."'");
			$attributes_data = '';
			$attributes_model = '';
			while ($attributes_data_values = xtc_db_fetch_array($attributes_query)) {	
				$attributes_data .= $attributes_data_values['products_options'].':'.$attributes_data_values['products_options_values'].'<br />';
				$attributes_model .= xtc_get_attributes_model($order_data_values['products_id'], $attributes_data_values['products_options_values']).'<br />';
			}
			$order_data[] = array ('PRODUCTS_MODEL' => $order_data_values['products_model'], 'PRODUCTS_NAME' => $order_data_values['products_name'], 'PRODUCTS_ATTRIBUTES' => $attributes_data, 'PRODUCTS_ATTRIBUTES_MODEL' => $attributes_model, 'PRODUCTS_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price'], true),'PRODUCTS_SINGLE_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price']/$order_data_values['products_quantity'], true), 'PRODUCTS_QTY' => $order_data_values['products_quantity']);		
		}
		// get order_total data
		$oder_total_query = xtc_db_query("SELECT
							title,
							text,
							sort_order
							FROM ".TABLE_ORDERS_TOTAL."
							WHERE orders_id='".$oID."'
							ORDER BY sort_order ASC");
	
		$order_total = array ();
		while ($oder_total_values = xtc_db_fetch_array($oder_total_query)) {
	
			$order_total[] = array ('TITLE' => $oder_total_values['title'], 'TEXT' => $oder_total_values['text']);
		}
	
		// assign language to template for caching
		$smarty->assign('language', $_SESSION['language']);
		$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
		$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
		$smarty->assign('oID', $oID);
		if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
			include ('..'.DIR_WS_LANGUAGES.$_SESSION['language'].'/modules/payment/'.$order->info['payment_method'].'.php');
			$payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
		}
		$smarty->assign('PAYMENT_METHOD', $payment_method);
		$smarty->assign('DATE', xtc_date_long($order->info['date_purchased']));
		$smarty->assign('order_data', $order_data);
		$smarty->assign('order_total', $order_total);
		$smarty->assign('NAME', $order->customer['name']);
		$smarty->assign('COMMENTS', $order->info['comments']);
		$smarty->assign('EMAIL', $order->customer['email_address']);
		$smarty->assign('PHONE',$order->customer['telephone']);
	
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
		$order_subject = str_replace('{$nr}', $oID, EMAIL_BILLING_SUBJECT_ORDER);
		$order_subject = str_replace('{$date}', strftime(DATE_FORMAT_LONG), $order_subject);
		$order_subject = str_replace('{$lastname}', $order->customer['lastname'], $order_subject);
		$order_subject = str_replace('{$firstname}', $order->customer['firstname'], $order_subject);
	
		// send mail to admin
		if ($send_to_admin==1)
			xtc_php_mail($order->customer['email_address'], $order->customer['firstname'], EMAIL_BILLING_ADDRESS, STORE_NAME, EMAIL_BILLING_FORWARDING_STRING, $order->customer['email_address'], $order->customer['firstname'], '', '', $order_subject, $html_mail, $txt_mail);
	
		// send mail to customer
		if ($send_to_customer==1)
			xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $order->customer['email_address'], $order->customer['firstname'].' '.$order->customer['lastname'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $order_subject, $html_mail, $txt_mail);
	
		if (AFTERBUY_ACTIVATED == 'true') {
			require_once (DIR_WS_CLASSES.'afterbuy.php');
			$aBUY = new xtc_afterbuy_functions($oID);
			if ($aBUY->order_send())
				$aBUY->process_order();
		}
		$messageStack->add_session(TEXT_SUCCESS_ORDER_SEND, 'success');
		
		xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID']));
    //EOF - DokuMan - 2010-08-12 resend email to admin and/or customer

	case 'update_order' :
		$oID = xtc_db_prepare_input($_GET['oID']);
		$status = xtc_db_prepare_input($_POST['status']);
		$comments = xtc_db_prepare_input($_POST['comments']);
	//	$order = new order($oID);
		$order_updated = false;
		$check_status_query = xtc_db_query("select customers_name, customers_email_address, orders_status, date_purchased from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($oID)."'");
		$check_status = xtc_db_fetch_array($check_status_query);
		if ($check_status['orders_status'] != $status || $comments != '') {
			xtc_db_query("update ".TABLE_ORDERS." set orders_status = '".xtc_db_input($status)."', last_modified = now() where orders_id = '".xtc_db_input($oID)."'");

			$customer_notified = '0';
			if ($_POST['notify'] == 'on') {
				$notify_comments = '';
				if ($_POST['notify_comments'] == 'on') {
					//$notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments)."\n\n";
					$notify_comments = $comments;
				} else {
					$notify_comments = '';
				}

				// assign language to template for caching
				$smarty->assign('language', $_SESSION['language']);
				$smarty->caching = false;

				// set dirs manual
				$smarty->template_dir = DIR_FS_CATALOG.'templates';
				$smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
				$smarty->config_dir = DIR_FS_CATALOG.'lang';

				$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
				$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

				$smarty->assign('NAME', $check_status['customers_name']);
				$smarty->assign('ORDER_NR', $oID);
				$smarty->assign('ORDER_LINK', xtc_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id='.$oID, 'SSL'));
				$smarty->assign('ORDER_DATE', xtc_date_long($check_status['date_purchased']));
				$smarty->assign('NOTIFY_COMMENTS', nl2br($notify_comments)); // Tomcraft - 2009-10-10 - Fixed wordwrap in notify_comments
				$smarty->assign('ORDER_STATUS', $orders_status_array[$status]);

				$html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/admin/mail/'.$order->info['language'].'/change_order_mail.html');
				$txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/admin/mail/'.$order->info['language'].'/change_order_mail.txt');

				xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'], $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_BILLING_SUBJECT, $html_mail, $txt_mail);
				$customer_notified = '1';
			}

			xtc_db_query("insert into ".TABLE_ORDERS_STATUS_HISTORY." (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".xtc_db_input($oID)."', '".xtc_db_input($status)."', now(), '".$customer_notified."', '".xtc_db_input($comments)."')");

			$order_updated = true;
		}

		if ($order_updated) {
			$messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
		} else {
			$messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
		}

		xtc_redirect(xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('action')).'action=edit'));
		break;
	case 'deleteconfirm' :
		$oID = xtc_db_prepare_input($_GET['oID']);

		xtc_remove_order($oID, $_POST['restock']);

		xtc_redirect(xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action'))));
		break;
		// BMC Delete CC info Start
		// Remove CVV Number
	case 'deleteccinfo' :
		$oID = xtc_db_prepare_input($_GET['oID']);

		xtc_db_query("update ".TABLE_ORDERS." set cc_cvv = null where orders_id = '".xtc_db_input($oID)."'");
		xtc_db_query("update ".TABLE_ORDERS." set cc_number = '0000000000000000' where orders_id = '".xtc_db_input($oID)."'");
		xtc_db_query("update ".TABLE_ORDERS." set cc_expires = null where orders_id = '".xtc_db_input($oID)."'");
		xtc_db_query("update ".TABLE_ORDERS." set cc_start = null where orders_id = '".xtc_db_input($oID)."'");
		xtc_db_query("update ".TABLE_ORDERS." set cc_issue = null where orders_id = '".xtc_db_input($oID)."'");

		xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=edit'));
		break;

	case 'afterbuy_send' :
		$oID = xtc_db_prepare_input($_GET['oID']);
		require_once (DIR_FS_CATALOG.'includes/classes/afterbuy.php');
		$aBUY = new xtc_afterbuy_functions($oID);
		if ($aBUY->order_send())
			$aBUY->process_order();

		break;

		// BMC Delete CC Info End
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php

require (DIR_WS_INCLUDES.'header.php');
?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php

if (($_GET['action'] == 'edit') && ($order_exists)) {
	//    $order = new order($oID);
?>
      <tr>
      <td width="100%">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_customers.gif'); ?></td>
    <td class="pageHeading"><?php echo HEADING_TITLE . ' Nr : ' . $oID . ' - ' . $order->info['date_purchased'] ; ?></td>
  </tr>
  <tr>
    <td class="main" valign="top">Customers</td>
  </tr>
</table>
 <?php echo '<a class="button" href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('action'))) . '">' . BUTTON_BACK . '</a>'; ?>
 <!-- Bestellbearbeitung Anfang -->
   <a class="button" href="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'oID='.$_GET['oID'].'&cID=' . $order->customer['ID']);?>"><?php echo BUTTON_EDIT ?></a>
<!-- Bestellbearbeitung Ende -->
 </td>

      </tr>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td colspan="3"><?php echo xtc_draw_separator(); ?></td>
          </tr>
          <tr>
            <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <?php if ($order->customer['csID']!='') { ?>
                <tr>
                <td class="main" valign="top" bgcolor="#FFCC33"><b><?php echo ENTRY_CID; ?></b></td>
                <td class="main" bgcolor="#FFCC33"><?php echo $order->customer['csID']; ?></td>
              </tr>
            <?php } ?>
              <tr>
                <td class="main" valign="top"><b><?php echo ENTRY_CUSTOMER; ?></b></td>
                <td class="main"><?php echo xtc_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
              </tr>

              <tr>
                <td class="main" valign="top"><b><?php echo CUSTOMERS_MEMO; ?></b></td>
<?php

	// memoquery
	$memo_query = xtc_db_query("SELECT count(*) as count FROM ".TABLE_CUSTOMERS_MEMO." where customers_id='".$order->customer['ID']."'");
	$memo_count = xtc_db_fetch_array($memo_query);
?>
                <td class="main"><b><?php echo $memo_count['count'].'</b>'; ?>  <a style="cursor:pointer" onclick="javascript:window.open('<?php echo xtc_href_link(FILENAME_POPUP_MEMO,'ID='.$order->customer['ID']); ?>', 'popup', 'scrollbars=yes, width=500, height=500')">(<?php echo DISPLAY_MEMOS; ?>)</a></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_TELEPHONE; ?></b></td>
                <td class="main"><?php echo $order->customer['telephone']; ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
                <td class="main"><?php echo '<a href="mailto:' . $order->customer['email_address'] . '"><u>' . $order->customer['email_address'] . '</u></a>'; ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_CUSTOMERS_VAT_ID; ?></b></td>
                <td class="main"><?php echo $order->customer['vat_id']; ?></td>
              </tr>
              <tr>
                <td class="main" valign="top" bgcolor="#FFCC33"><b><?php echo IP; ?></b></td>
                <td class="main" bgcolor="#FFCC33"><b><?php echo $order->customer['cIP']; ?></b></td>
              </tr>
            </table></td>
            <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" valign="top"><b><?php echo ENTRY_SHIPPING_ADDRESS; ?></b></td>
                <td class="main"><?php echo xtc_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'); ?></td>
              </tr>
            </table></td>
            <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" valign="top"><b><?php echo ENTRY_BILLING_ADDRESS; ?></b></td>
                <td class="main"><?php echo xtc_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td class="main"><b><?php echo ENTRY_LANGUAGE; ?></b></td>
            <td class="main"><?php echo $order->info['language']; ?></td>
          </tr>
          <tr>
            <td class="main"><b><?php echo ENTRY_PAYMENT_METHOD; ?></b></td>
            <td class="main"><?php echo $order->info['payment_method']; ?></td>
          </tr>
<?php

	if ((($order->info['cc_type']) || ($order->info['cc_owner']) || ($order->info['cc_number']))) {
?>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
            <td class="main"><?php echo $order->info['cc_type']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
            <td class="main"><?php echo $order->info['cc_owner']; ?></td>
          </tr>
<?php

		// BMC CC Mod Start
		if ($order->info['cc_number'] != '0000000000000000') {
			if (strtolower(CC_ENC) == 'true') {
				$cipher_data = $order->info['cc_number'];
				$order->info['cc_number'] = changedataout($cipher_data, CC_KEYCHAIN);
			}
		}
		// BMC CC Mod End
?>
          <tr>
            <td class="main"><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
            <td class="main"><?php echo $order->info['cc_number']; ?></td>
          </tr>
          <tr>
          <td class="main"><?php echo ENTRY_CREDIT_CARD_CVV; ?></td>
          <td class="main"><?php echo $order->info['cc_cvv']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
            <td class="main"><?php echo $order->info['cc_expires']; ?></td>
          </tr>
<?php

	}


// BOF - Tomcraft - 2009-11-03 - commented out the old sofortueberweisung.de payment module
/*
// Start sofortüberweisung.de
    if (MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_STATUS == 'True' && $order->info['payment_method']=='pn_sofortueberweisung') {
      $sql = "SELECT * FROM payment_sofortueberweisung WHERE orders_id='$oID'";
      $sofort_query = xtc_db_query($sql);
      if ($sofort = xtc_db_fetch_array($sofort_query)) {
?>
      <tr>
        <td class="main" valign="top"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" valign="top">Transcaction-ID:</td>
                <td class="main" valign="top"><?php echo $sofort['transaction']; ?></td>
              </tr>
              <tr>
                <td class="main" valign="top">Betrag:</td>
                <td class="main" valign="top"><?php echo number_format($sofort['amount'], 2) . ' ' . $sofort['currency_id']; ?></td>
              </tr>
              <tr>
                <td class="main" valign="top">Verwendungszweck 1:</td>
                <td class="main" valign="top"><?php echo $sofort['reason_1'] ; ?></td>
              </tr>
              <tr>
                <td class="main" valign="top">Verwendungszweck 2:</td>
                <td class="main" valign="top"><?php echo $sofort['reason_2'] ; ?></td>
              </tr>
              <tr>
                <td class="main" valign="top">Sicherheits-Kriterien erf&uuml;llt:</td>
                <td class="main" valign="top"><?php echo  ($sofort['security_criteria'] == 1 ? 'Ja' : 'Nein') ; ?></td>
              </tr>
            </table></td>
          </tr>
              <tr>
                <td><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td colspan="2" class="main" valign="top"><b>Absender der &Uuml;berweisung:</b></td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">Inhaber:</td>
                    <td class="main"><?php echo $sofort['sender_holder'] ; ?></td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">Konto:</td>
                    <td class="main"><?php echo $sofort['sender_account_number'] ; ?></td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">BLZ:</td>
                    <td class="main"><?php echo $sofort['sender_bank_code'] ; ?></td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">Bank:</td>
                    <td class="main"><?php echo $sofort['sender_bank_name'] ; ?></td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">BIC:</td>
                    <td class="main"><?php echo $sofort['sender_bank_bic'] ; ?></td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">IBAN:</td>
                    <td class="main"><?php echo $sofort['sender_iban'] ; ?></td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">Land:</td>
                    <td class="main"><?php echo $sofort['sender_country_id'] ; ?></td>
                  </tr>
                </table></td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td><table border="0" cellspacing="0" cellpadding="2">
                    <tr>
                      <td colspan="2" class="main" valign="top"><b>Empf&auml;nger der &Uuml;berweisung:</b></td>
                    </tr>
                    <tr>
                      <td class="main" valign="top">Inhaber:</td>
                      <td class="main"><?php echo $sofort['recipient_holder'] ; ?></td>
                    </tr>
                    <tr>
                      <td class="main" valign="top">Konto:</td>
                      <td class="main"><?php echo $sofort['recipient_account_number'] ; ?></td>
                    </tr>
                    <tr>
                      <td class="main" valign="top">BLZ:</td>
                      <td class="main"><?php echo $sofort['recipient_bank_code'] ; ?></td>
                    </tr>
                    <tr>
                      <td class="main" valign="top">Bank:</td>
                      <td class="main"><?php echo $sofort['recipient_bank_name'] ; ?></td>
                    </tr>
                    <tr>
                      <td class="main" valign="top">BIC:</td>
                      <td class="main"><?php echo $sofort['recipient_bank_bic'] ; ?></td>
                    </tr>
                    <tr>
                      <td class="main" valign="top">IBAN:</td>
                      <td class="main"><?php echo $sofort['recipient_iban'] ; ?></td>
                    </tr>
                    <tr>
                      <td class="main" valign="top">Land:</td>
                      <td class="main"><?php echo $sofort['recipient_country_id'] ; ?></td>
                    </tr>
                </table></td>
              </tr>

            </table></td>
          </tr>

<?php
    }
  }
// End sofortüberweisung.de
*/
// EOF - Tomcraft - 2009-11-03 - commented out the old sofortueberweisung.de payment module

	// begin modification for banktransfer
	$banktransfer_query = xtc_db_query("select banktransfer_prz, banktransfer_status, banktransfer_owner, banktransfer_number, banktransfer_bankname, banktransfer_blz, banktransfer_fax from banktransfer where orders_id = '".xtc_db_input($_GET['oID'])."'");
	$banktransfer = xtc_db_fetch_array($banktransfer_query);
	if (($banktransfer['banktransfer_bankname']) || ($banktransfer['banktransfer_blz']) || ($banktransfer['banktransfer_number'])) {
?>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANK_NAME; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_bankname']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANK_BLZ; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_blz']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANK_NUMBER; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_number']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANK_OWNER; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_owner']; ?></td>
          </tr>
<?php

		if ($banktransfer['banktransfer_status'] == 0) {
?>
          <tr>
            <td class="main"><?php echo TEXT_BANK_STATUS; ?></td>
            <td class="main"><?php echo "OK"; ?></td>
          </tr>
<?php

		} else {
?>
          <tr>
            <td class="main"><?php echo TEXT_BANK_STATUS; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_status']; ?></td>
          </tr>
<?php

			switch ($banktransfer['banktransfer_status']) {
				case 1 :
					$error_val = TEXT_BANK_ERROR_1;
					break;
				case 2 :
					$error_val = TEXT_BANK_ERROR_2;
					break;
				case 3 :
					$error_val = TEXT_BANK_ERROR_3;
					break;
				case 4 :
					$error_val = TEXT_BANK_ERROR_4;
					break;
				case 5 :
					$error_val = TEXT_BANK_ERROR_5;
					break;
				case 8 :
					$error_val = TEXT_BANK_ERROR_8;
					break;
				case 9 :
					$error_val = TEXT_BANK_ERROR_9;
					break;
			}
?>
          <tr>
            <td class="main"><?php echo TEXT_BANK_ERRORCODE; ?></td>
            <td class="main"><?php echo $error_val; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANK_PRZ; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_prz']; ?></td>
          </tr>
<?php

		}
	}
	if ($banktransfer['banktransfer_fax']) {
?>
          <tr>
            <td class="main"><?php echo TEXT_BANK_FAX; ?></td>
            <td class="main"><?php echo $banktransfer['banktransfer_fax']; ?></td>
          </tr>
<?php

	}
	// end modification for banktransfer

if ($order->info['payment_method'] == 'amoneybookers') {
if (file_exists(DIR_FS_CATALOG.DIR_WS_MODULES.'payment/'.$order->info['payment_method'].'.php')) {
include(DIR_FS_CATALOG.DIR_WS_MODULES.'payment/'.$order->info['payment_method'].'.php');
include(DIR_FS_CATALOG.'lang/'.$order->info['language'].'/modules/payment/'.$order->info['payment_method'].'.php');
$class = $order->info['payment_method'];
$payment = new $class();
$payment->admin_order($_GET['oID']);
}
}
?>
        </table></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
<?php

	if ($order->products[0]['allow_tax'] == 1) {
?>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
<?php

	}
?>
            <td class="dataTableHeadingContent" align="right"><?php

	echo TABLE_HEADING_TOTAL_INCLUDING_TAX;
	if ($order->products[$i]['allow_tax'] == 1) {
		echo ' (excl.)';
	}
?></td>
          </tr>
<?php

	for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++) {

		echo '          <tr class="dataTableRow">'."\n".'            <td class="dataTableContent" valign="top" align="right">'.$order->products[$i]['qty'].'&nbsp;x&nbsp;</td>'."\n".'            <td class="dataTableContent" valign="top">'.$order->products[$i]['name'];

		if (sizeof($order->products[$i]['attributes']) > 0) {
			for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j ++) {

				echo '<br /><nobr><small>&nbsp;<i> - '.$order->products[$i]['attributes'][$j]['option'].': '.$order->products[$i]['attributes'][$j]['value'].': ';

			}

			echo '</i></small></nobr>';
		}

		echo '            </td>'."\n".'            <td class="dataTableContent" valign="top">';

		if ($order->products[$i]['model'] != '') {
			echo $order->products[$i]['model'];
		} else {
			echo '<br />';
		}

		// attribute models
		if (sizeof($order->products[$i]['attributes']) > 0) {
			for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j ++) {

				$model = xtc_get_attributes_model($order->products[$i]['id'], $order->products[$i]['attributes'][$j]['value'],$order->products[$i]['attributes'][$j]['option']);
				if ($model != '') {
					echo $model.'<br />';
				} else {
					echo '<br />';
				}
			}
		}
		
    //BOF - DokuMan - 2010-07-13 - Error while editing orders with quantity with zero
		//echo '&nbsp;</td>'."\n".'            <td class="dataTableContent" align="right" valign="top">'.format_price($order->products[$i]['final_price'] / $order->products[$i]['qty'], 1, $order->info['currency'], $order->products[$i]['allow_tax'], $order->products[$i]['tax']).'</td>'."\n";
		echo '&nbsp;</td>'."\n".'            <td class="dataTableContent" align="right" valign="top">'.format_price($order->products[$i]['price'], 1, $order->info['currency'], $order->products[$i]['allow_tax'], $order->products[$i]['tax']).'</td>'."\n";
    //EOF - DokuMan - 2010-07-13 - Error while editing orders with quantity with zero

		if ($order->products[$i]['allow_tax'] == 1) {
			echo '<td class="dataTableContent" align="right" valign="top">';
			echo xtc_display_tax_value($order->products[$i]['tax']).'%';
			echo '</td>'."\n";
			echo '<td class="dataTableContent" align="right" valign="top"><b>';

      //BOF - DokuMan - 2010-07-13 - Error while editing orders with quantity with zero
			//echo format_price($order->products[$i]['final_price'] / $order->products[$i]['qty'], 1, $order->info['currency'], 0, 0);
			echo format_price($order->products[$i]['price'], 1, $order->info['currency'], 0, 0);
      //EOF - DokuMan - 2010-07-13 - Error while editing orders with quantity with zero

			echo '</b></td>'."\n";
		}
		echo '            <td class="dataTableContent" align="right" valign="top"><b>'.format_price(($order->products[$i]['final_price']), 1, $order->info['currency'], 0, 0).'</b></td>'."\n";
		echo '          </tr>'."\n";
	}
?>
          <tr>
            <td align="right" colspan="10"><table border="0" cellspacing="0" cellpadding="2">
<?php

	for ($i = 0, $n = sizeof($order->totals); $i < $n; $i ++) {
		echo '              <tr>'."\n".'                <td align="right" class="smallText">'.$order->totals[$i]['title'].'</td>'."\n".'                <td align="right" class="smallText">'.$order->totals[$i]['text'].'</td>'."\n".'              </tr>'."\n";
	}
?>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><table border="1" cellspacing="0" cellpadding="5">
          <tr>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_DATE_ADDED; ?></b></td>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></b></td>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_STATUS; ?></b></td>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
          </tr>
<?php

	$orders_history_query = xtc_db_query("select orders_status_id, date_added, customer_notified, comments from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".xtc_db_input($oID)."' order by date_added");
	if (xtc_db_num_rows($orders_history_query)) {
		while ($orders_history = xtc_db_fetch_array($orders_history_query)) {
			echo '          <tr>'."\n".'            <td class="smallText" align="center">'.xtc_datetime_short($orders_history['date_added']).'</td>'."\n".'            <td class="smallText" align="center">';
			if ($orders_history['customer_notified'] == '1') {
				echo xtc_image(DIR_WS_ICONS.'tick.gif', ICON_TICK)."</td>\n";
			} else {
				echo xtc_image(DIR_WS_ICONS.'cross.gif', ICON_CROSS)."</td>\n";
			}
			echo '            <td class="smallText">';
			if($orders_history['orders_status_id']!='0') {
				echo $orders_status_array[$orders_history['orders_status_id']];
			}else{
				echo '<font color="#FF0000">'.TEXT_VALIDATING.'</font>';
			}
			echo '</td>'."\n".'            <td class="smallText">'.nl2br(xtc_db_output($orders_history['comments'])).'&nbsp;</td>'."\n".'          </tr>'."\n";
		}
	} else {
		echo '          <tr>'."\n".'            <td class="smallText" colspan="5">'.TEXT_NO_ORDER_HISTORY.'</td>'."\n".'          </tr>'."\n";
	}
?>
        </table></td>
      </tr>
      <tr>
        <td class="main"><br /><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
      </tr>
      <tr><?php echo xtc_draw_form('status', FILENAME_ORDERS, xtc_get_all_get_params(array('action')) . 'action=update_order'); ?>
        <td class="main"><?php echo xtc_draw_textarea_field('comments', 'soft', '60', '5', $order->info['comments']); ?></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo ENTRY_STATUS; ?></b> <?php echo xtc_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status']); ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_NOTIFY_CUSTOMER; ?></b> <?php echo xtc_draw_checkbox_field('notify', '', true); ?></td>
                <td class="main"><b><?php echo ENTRY_NOTIFY_COMMENTS; ?></b> <?php echo xtc_draw_checkbox_field('notify_comments', '', true); ?></td>
              </tr>
            </table></td>
            <td valign="top"><input type="submit" class="button" value="<?php echo BUTTON_UPDATE; ?>"></td>
          </tr>
        </table></td>
      </form></tr>
      <tr>
        <td colspan="2" align="right">
<?php
	if (ACTIVATE_GIFT_SYSTEM == 'true') {
		echo '<a class="button" href="'.xtc_href_link(FILENAME_GV_MAIL, xtc_get_all_get_params(array ('cID', 'action')).'cID='.$order->customer['ID']).'">'.BUTTON_SEND_COUPON.'</a>';
	}
?>
<!-- BOF - Tomcraft - 2010-04-03 - unified popups with scrollbars and make them resizable //->
<!--
   <a class="button" href="Javascript:void()" onclick="window.open('<?php echo xtc_href_link(FILENAME_PRINT_ORDER,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=0, width=640, height=600')"><?php echo BUTTON_INVOICE; ?></a>
   <a class="button" href="Javascript:void()" onclick="window.open('<?php echo xtc_href_link(FILENAME_PRINT_PACKINGSLIP,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=0, width=640, height=600')"><?php echo BUTTON_PACKINGSLIP; ?></a>
//-->
   <a class="button" href="Javascript:void()" onclick="window.open('<?php echo xtc_href_link(FILENAME_PRINT_ORDER,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width=640, height=600')"><?php echo BUTTON_INVOICE; ?></a>
   <a class="button" href="Javascript:void()" onclick="window.open('<?php echo xtc_href_link(FILENAME_PRINT_PACKINGSLIP,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width=640, height=600')"><?php echo BUTTON_PACKINGSLIP; ?></a>
<!-- EOF - Tomcraft - 2010-04-03 - unified popups with scrollbars and make them resizable //->
	<!-- BMC Delete CC Info -->
	<a class="button" href="<?php echo xtc_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=deleteccinfo').'">'.BUTTON_REMOVE_CC_INFO;?></a>&nbsp;
   <a class="button" href="<?php echo xtc_href_link(FILENAME_ORDERS, 'page='.$_GET['page'].'&oID='.$_GET['oID']).'">'.BUTTON_BACK;?></a>
       </td>
      </tr>
<?php

}
elseif ($_GET['action'] == 'custom_action') {

	include ('orders_actions.php');

} else {
?>
      <tr>
        <td width="100%">


<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_customers.gif'); ?></td>
    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
    <td class="pageHeading" align="right">
              <?php echo xtc_draw_form('orders', FILENAME_ORDERS, '', 'get'); ?>
                <?php echo HEADING_TITLE_SEARCH . ' ' . xtc_draw_input_field('oID', '', 'size="12"') . xtc_draw_hidden_field('action', 'edit').xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()); ?>
              </form>
</td>
  </tr>
  <tr>
    <td class="main" valign="top">Customers</td>
    <td class="main" valign="top" align="right"><?php echo xtc_draw_form('status', FILENAME_ORDERS, '', 'get'); ?>
                <?php echo HEADING_TITLE_STATUS . ' ' . xtc_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)),array(array('id' => '0', 'text' => TEXT_VALIDATING)), $orders_statuses), '', 'onChange="this.form.submit();"').xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()); ?>
              </form></td>
  </tr>
</table>




        </td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo 'Nr'; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_TOTAL; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <?php if (AFTERBUY_ACTIVATED=='true') { ?>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_AFTERBUY; ?></td>
                <?php } ?>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php

	if ($_GET['cID']) {
		$cID = xtc_db_prepare_input($_GET['cID']);
// BOF - Tomcraft - 2009-10-11 - BUGFIX: #0000247 view orders query bug in admin
		//$orders_query_raw = "select o.orders_id, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.customers_id, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, o.orders_status, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.customers_id = '".xtc_db_input($cID)."' and (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '".$_SESSION['languages_id']."') order by orders_id DESC";
		$orders_query_raw = "select o.orders_id, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.customers_id, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, o.orders_status, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.customers_id = '".xtc_db_input($cID)."' and ((o.orders_status = s.orders_status_id) or (o.orders_status = '0' and  s.orders_status_id = '1')) and ot.class = 'ot_total' and s.language_id = '".$_SESSION['languages_id']."' order by orders_id DESC";
// EOF - Tomcraft - 2009-10-11 - BUGFIX: #0000247 view orders query bug in admin
	}
	elseif ($_GET['status']=='0') {
			$orders_query_raw = "select o.orders_id, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, o.orders_status, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id) where o.orders_status = '0' and ot.class = 'ot_total' order by o.orders_id DESC";
	}
	elseif ($_GET['status']) {
			$status = xtc_db_prepare_input($_GET['status']);
			$orders_query_raw = "select o.orders_id, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and s.orders_status_id = '".xtc_db_input($status)."' and ot.class = 'ot_total' order by o.orders_id DESC";
	}
	//BOF  - web28 - 2010-04-10 added for ADMIN SEARCH BAR
	elseif ($_GET['action'] == 'search' && $_GET['oID']) {	      
		   //$orders_query_raw siehe oben
	//EOF - web28 - 2010-04-10 added for ADMIN SEARCH BAR
	} else {
		$orders_query_raw = "select o.orders_id, o.orders_status, o.afterbuy_success, o.afterbuy_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from ".TABLE_ORDERS." o left join ".TABLE_ORDERS_TOTAL." ot on (o.orders_id = ot.orders_id), ".TABLE_ORDERS_STATUS." s where (o.orders_status = s.orders_status_id and s.language_id = '".$_SESSION['languages_id']."' and ot.class = 'ot_total') or (o.orders_status = '0' and ot.class = 'ot_total' and  s.orders_status_id = '1' and s.language_id = '".$_SESSION['languages_id']."') order by o.orders_id DESC";
	}
	$orders_split = new splitPageResults($_GET['page'], '20', $orders_query_raw, $orders_query_numrows);
	$orders_query = xtc_db_query($orders_query_raw);
	while ($orders = xtc_db_fetch_array($orders_query)) {
		if (((!$_GET['oID']) || ($_GET['oID'] == $orders['orders_id'])) && (!$oInfo)) {
			$oInfo = new objectInfo($orders);
		}

		if ((is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id)) {
			echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'pointer\'" onclick="document.location.href=\''.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'\'">'."\n";
		} else {
			echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'pointer\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\''.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID')).'oID='.$orders['orders_id']).'\'">'."\n";
		}
?>
                <td class="dataTableContent"><?php echo '<a href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit') . '">' . xtc_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $orders['customers_name']; ?></td>
                <td class="dataTableContent" align="right"><?php echo $orders['orders_id']; ?></td>
                <td class="dataTableContent" align="right"><?php echo strip_tags($orders['order_total']); ?></td>
                <td class="dataTableContent" align="center"><?php echo xtc_datetime_short($orders['date_purchased']); ?></td>
                <td class="dataTableContent" align="right"><?php if($orders['orders_status']!='0') { echo $orders['orders_status_name']; }else{ echo '<font color="#FF0000">'.TEXT_VALIDATING.'</font>';}?></td>
                <?php if (AFTERBUY_ACTIVATED=='true') { ?>
                <td class="dataTableContent" align="right"><?php

		if ($orders['afterbuy_success'] == 1) {
			echo $orders['afterbuy_id'];
		} else {
			echo 'TRANSMISSION_ERROR';
		}
?></td>
                <?php } ?>
<!-- BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
<!--
                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
-->
                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ICON_ARROW_RIGHT); } else { echo '<a href="' . xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
<!-- EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
              </tr>
<?php

	}
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $orders_split->display_count($orders_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
                    <td class="smallText" align="right"><?php echo $orders_split->display_links($orders_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page'], xtc_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php

	$heading = array ();
	$contents = array ();
	switch ($_GET['action']) {
		case 'delete' :
			$heading[] = array ('text' => '<b>'.TEXT_INFO_HEADING_DELETE_ORDER.'</b>');

			$contents = array ('form' => xtc_draw_form('orders', FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=deleteconfirm'));
			$contents[] = array ('text' => TEXT_INFO_DELETE_INTRO.'<br /><br /><b>'.$cInfo->customers_firstname.' '.$cInfo->customers_lastname.'</b>');
			$contents[] = array ('text' => '<br />'.xtc_draw_checkbox_field('restock').' '.TEXT_INFO_RESTOCK_PRODUCT_QUANTITY);
			$contents[] = array ('align' => 'center', 'text' => '<br /><input type="submit" class="button" value="'. BUTTON_DELETE .'"><a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id).'">' . BUTTON_CANCEL . '</a>');
			break;
		default :
			if (is_object($oInfo)) {
				$heading[] = array ('text' => '<b>['.$oInfo->orders_id.']&nbsp;&nbsp;'.xtc_datetime_short($oInfo->date_purchased).'</b>');

    //BOF - DokuMan - 2010-08-12 resend email to admin and/or customer
				//$contents[] = array ('align' => 'center', 'text' => '<a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'">'.BUTTON_EDIT.'</a> <a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=delete').'">'.BUTTON_DELETE.'</a>');
				$contents[] = array ('align' => 'center', 'text' => '<a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=edit').'">'.BUTTON_EDIT.'</a> <a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=delete').'">'.BUTTON_DELETE.'</a><br /><br /><a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=send&sta=1&stc=0').'">'.TEXT_ORDER_SEND_ADMIN.'</a><br /><br /><a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=send&sta=0&stc=1').'">'.TEXT_ORDER_SEND_CUSTOMER.'</a>');			
    //EOF - DokuMan - 2010-08-12 resend email to admin and/or customer
				
				if (AFTERBUY_ACTIVATED == 'true') {
					$contents[] = array ('align' => 'center', 'text' => '<a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=afterbuy_send').'">'.BUTTON_AFTERBUY_SEND.'</a>');

				}
				//$contents[] = array('align' => 'center', 'text' => '');

				$contents[] = array ('text' => '<br />'.TEXT_DATE_ORDER_CREATED.' '.xtc_date_short($oInfo->date_purchased));
				if (xtc_not_null($oInfo->last_modified))
					$contents[] = array ('text' => TEXT_DATE_ORDER_LAST_MODIFIED.' '.xtc_date_short($oInfo->last_modified));
				$contents[] = array ('text' => '<br />'.TEXT_INFO_PAYMENT_METHOD.' '.$oInfo->payment_method);
				// elari added to display product list for selected order
				$order = new order($oInfo->orders_id);
// BOF - Tomcraft - 2010-04-22 - Added a missing language definition
				//$contents[] = array ('text' => '<br /><br />'.sizeof($order->products).' Products ');
								$contents[] = array ('text' => '<br /><br />'.sizeof($order->products).'&nbsp;'.TEXT_PRODUCTS);
// EOF - Tomcraft - 2010-04-22 - Added a missing language definition
				for ($i = 0; $i < sizeof($order->products); $i ++) {
					$contents[] = array ('text' => $order->products[$i]['qty'].'&nbsp;x'.$order->products[$i]['name']);

					if (sizeof($order->products[$i]['attributes']) > 0) {
						for ($j = 0; $j < sizeof($order->products[$i]['attributes']); $j ++) {
							$contents[] = array ('text' => '<small>&nbsp;<i> - '.$order->products[$i]['attributes'][$j]['option'].': '.$order->products[$i]['attributes'][$j]['value'].'</i></small></nobr>');
						}
					}
				}
				// elari End add display products
			}
			break;
	}

// BOF - Tomcraft - 2009-10-22 - Added customer comments to default view on right column
	if ($order->info[comments]<>'') {
		$contents[] = array ('text' => '<br><strong>'.TABLE_HEADING_COMMENTS.':</strong><br>'.$order->info[comments]);
	}
// EOF - Tomcraft - 2009-10-22 - Added customer comments to default view on right column

	if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
		echo '            <td width="25%" valign="top">'."\n";

		$box = new box;
		echo $box->infoBox($heading, $contents);

		echo '            </td>'."\n";
	}
?>
          </tr>
        </table></td>
      </tr>
<?php

}
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php

require (DIR_WS_INCLUDES.'footer.php');
?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>