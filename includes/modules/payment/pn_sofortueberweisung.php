<?php
/**
 * @version sofortüberweisung.de 3.1.2 - 26.10.2009
 * @author Payment Network AG (integration@payment-network.com)
 * @link http://www.payment-network.com/
 *
 * @copyright 2006 - 2007 Henri Schmidhuber
 * @link http://www.in-solution.de
 *
 * @link http://www.xt-commerce.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 *
 ***********************************************************************************
 * this file contains code based on:
 * (c) 2000 - 2001 The Exchange Project
 * (c) 2001 - 2003 osCommerce, Open Source E-Commerce Solutions
 * (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org
 * (c) 2003 - 2006 XT-Commerce
 * Released under the GNU General Public License
 ***********************************************************************************
 *
 */
 
class pn_sofortueberweisung {

	var $code, $title, $description, $enabled;
	function pn_sofortueberweisung () {
		global $order;
		$this->code = 'pn_sofortueberweisung';
		$this->version = '3.1.2';
		$this->title = MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_INFO;
		$this->tmpOrders = true;
		$this->tmpStatus = MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TMP_STATUS_ID;
		if ((int) MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ORDER_STATUS_ID;
		}
		if (is_object($order))
			$this->update_status();

		$this->form_action_url = 'https://www.sofortueberweisung.de/payment/start';

		$this->defaultCurrency = DEFAULT_CURRENCY;

	}
	function update_status ()
	{
		global $order;
		if (($this->enabled == true) && ((int) MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ZONE > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("SELECT zone_id FROM " . TABLE_ZONES_TO_GEO_ZONES . " WHERE geo_zone_id = '" . MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' ORDER BY zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				} elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
			}
			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}
	function javascript_validation () {
		return false;
	}
	function selection () {
		/* If temporary order is still in session, check if order ID exists and delete order and all relating (session) data
		 * User might have returned to the shop for changing the order or payment method
		 */
		if (! empty($_SESSION['cart_pn_sofortueberweisung_ID'])) {
			$order_id = substr($_SESSION['cart_pn_sofortueberweisung_ID'], strpos($_SESSION['cart_pn_sofortueberweisung_ID'], '-') + 1);
			$check_query = xtc_db_query('SELECT orders_status FROM ' . TABLE_ORDERS . ' WHERE orders_id = "' . (int) $order_id . '" LIMIT 1');
			if ($result = xtc_db_fetch_array($check_query)) {
				if ($result['orders_status'] == MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TMP_STATUS_ID) {
					$this->_remove_order( (int) $order_id, 'on');
					unset($_SESSION['cart_pn_sofortueberweisung_ID']);
					unset($_SESSION['tmp_oID']);
				}
			}
		}
		$title = '';
		switch (MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_IMAGE) {
			case 'Logo & Text':
				$image = xtc_image(sprintf('lang/%s/modules/payment/images/sofortueberweisung_logo.gif', $_SESSION['language']), MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT);
				$title = str_replace('{{image}}', $image, sprintf(MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGE, MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_TEXT));
				break;
			case 'Logo':
				$image = xtc_image(sprintf('lang/%s/modules/payment/images/sofortueberweisung_logo.gif', $_SESSION['language']), MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT);
				$title = str_replace('{{image}}', $image, sprintf(MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGE, ''));
				break;
			case 'Infographic':
				$image = xtc_image(sprintf('lang/%s/modules/payment/images/sofortueberweisung_info.gif', $_SESSION['language']), MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT);
				$title = str_replace('{{image}}', $image, sprintf(MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGE, ''));
				break;
		}
		return array('id' => $this->code , 'module' => $this->title , 'description' => $title);
	}
	function pre_confirmation_check () {
		// Fix for XTC Bug
		// We need a cartID
		if (empty($_SESSION['cart']->cartID)) {
			$_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();
		}
		return false;
	}
	function confirmation () {
		global $order;
		
		/* If temporary order is still in session, check if order ID exists and delete order and all relating (session) data
		 * User might have returned to the shop for changing the order or payment method
		 */
		if (! empty($_SESSION['cart_pn_sofortueberweisung_ID'])) {
			$order_id = substr($_SESSION['cart_pn_sofortueberweisung_ID'], strpos($_SESSION['cart_pn_sofortueberweisung_ID'], '-') + 1);
			$cartID = substr($_SESSION['cart_pn_sofortueberweisung_ID'], 0, strlen($_SESSION['cart']->cartID));
			$check_query = xtc_db_query("SELECT currency, orders_status FROM " . TABLE_ORDERS . " WHERE orders_id = '" . (int) $order_id . "'");
			$result = xtc_db_fetch_array($check_query);
			if (($result['orders_status'] == MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TMP_STATUS_ID) || ($result['currency'] != $order->info['currency']) || ($_SESSION['cart']->cartID != $cartID)) {
				$this->_remove_order( (int) $order_id, 'on');
				unset($_SESSION['cart_pn_sofortueberweisung_ID']);
				unset($_SESSION['tmp_oID']);
			}
		}
		return false;
	}
	function process_button () {
		return false;
	}
	function before_process () {
		return false;
	}
	function payment_action () {
		global $order, $xtPrice, $insert_id;

		$customer_id = $_SESSION['customer_id'];
		$order_id = $insert_id;
		$_SESSION['cart_pn_sofortueberweisung_ID'] = $_SESSION['cart']->cartID . '-' . $insert_id;
		$myCurrency = $this->defaultCurrency;

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}
		if ($_SESSION['currency'] == $myCurrency) {
			$amount = round($total, $xtPrice->get_decimal_places($myCurrency));
		} else {
			$amount = round($xtPrice->xtcCalculateCurrEx($total, $myCurrency), $xtPrice->get_decimal_places($myCurrency));
		}
		
		// Fix for XTC Bug
		// $order->info['total'] is in 'before_process' String without Tax, after email it is TEXT with currency
		// so it has to be set here

		$amount = number_format($amount, 2, '.', '');
		$_SESSION['sofortueberweisung_total'] = $amount;
		$parameter = array();
		$parameter['user_id'] = MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_USER_ID;
		$parameter['project_id'] = MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_PROJECT_ID;
		$parameter['amount'] = $amount;
		$parameter['currency_id'] = ($_SESSION['currency'] != $myCurrency) ? $_SESSION['currency'] : $myCurrency;

		$reason_1 = str_replace('{{order_id}}', $order_id, MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_REASON_1);
		$reason_2 = str_replace('{{order_id}}', $order_id, MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_REASON_2);
		$reason_1 = str_replace('{{customer_id}}', $customer_id, $reason_1);
		$reason_2 = str_replace('{{customer_id}}', $customer_id, $reason_2);

		$reason_2 = str_replace('{{order_date}}', strftime(DATE_FORMAT_SHORT), $reason_2);
		$reason_2 = str_replace('{{customer_name}}', $order->customer['firstname'] . ' ' . $order->customer['lastname'], $reason_2);
		$reason_2 = str_replace('{{customer_company}}', $order->customer['company'], $reason_2);
		$reason_2 = str_replace('{{customer_email}}', $order->customer['email_address'], $reason_2);

		$reason_1 = substr($reason_1, 0, 27);
		$reason_2 = substr($reason_2, 0, 27);
		
		
		$parameter['reason_1'] = $reason_1;
		$parameter['reason_2'] = $reason_2;
		$parameter['user_variable_0'] = $order_id;
		$parameter['user_variable_1'] = $customer_id;
		
		$session = '&' . session_name() . '=' . session_id();

	        if (ENABLE_SSL == true)
		      $server = str_replace('https://', '', HTTPS_SERVER);
	        else
		      $server = str_replace('http://', '', HTTP_SERVER);
		
		// success return url:
		$parameter['user_variable_2'] = $server . DIR_WS_CATALOG . FILENAME_CHECKOUT_PROCESS . '?transaction=-TRANSACTION-' . $session;

		// cancel return url:
		$parameter['user_variable_3'] = $server . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?payment_error=pn_sofortueberweisung' . $session;
	
		// notification url:
		$parameter['user_variable_4'] = $server . DIR_WS_CATALOG . 'callback/pn_sofortueberweisung/callback.php';

		$parameter['user_variable_5'] = $_SESSION['cart']->cartID;

		if (strlen(MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_PROJECT_PASSWORD) > 0) {
			$tmparray = array(
				$parameter['user_id'],
				$parameter['project_id'],
				'', // sender_holder
				'', // sender_account_number
				'', // sender_bank_code
				'', // sender_country_id|
				$parameter['amount'],
				$parameter['currency_id'],
				$parameter['reason_1'],
				$parameter['reason_2'],
				$parameter['user_variable_0'],
				$parameter['user_variable_1'],
				$parameter['user_variable_2'],
				$parameter['user_variable_3'],
				$parameter['user_variable_4'],
				$parameter['user_variable_5'],
				MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_PROJECT_PASSWORD);
			$parameter['hash'] = sha1(implode("|", $tmparray));
			
			$parameter['encoding'] = 'iso-8859-1';
			$parameter['payment_module'] = sprintf('XTC %s (v%s)', $this->code, $this->version);
		}
		$dataString = '';
		foreach ($parameter as $key => $value) {
		      $dataString .= $key . '=' . urlencode($value) . '&';
		}
		xtc_redirect($this->form_action_url . '?' . $dataString);
	}
	function after_process () {
		/* Clear our session data
		 * All other session data will be handled in checkout_process.php
		 */
		if (isset($_SESSION)) {
			if (isset($_SESSION['cart_pn_sofortueberweisung_ID'])) unset($_SESSION['cart_pn_sofortueberweisung_ID']);
			if (isset($_SESSION['sofortueberweisung_total'])) unset($_SESSION['sofortueberweisung_total']);
		}
	}
	function output_error (){
		return false;
	}
	function check () {
		if (! isset($this->_check)) {
			$check_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}
	function get_error () {
		$error = false;
		if (! empty($_GET['payment_error'])) {
			$error = array('title' => MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_ERROR_HEADING , 'error' => MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_ERROR_MESSAGE);
		}
		return $error;
	}

    function autoinstall() {

	if (!isset($_SESSION['pn_sofortueberweisung_pw'])) {
	$_SESSION['pn_sofortueberweisung_pw'] = $this->_create_random_value(12);
	}
	
	$backlink = xtc_href_link(FILENAME_MODULES, 'set=payment&module=pn_sofortueberweisung&action=install', 'SSL');
	
	$header_redir_url = 'http://-USER_VARIABLE_2-';
	if (ENABLE_SSL_CATALOG == 'true' && strpos(HTTPS_CATALOG_SERVER, 'tps://') === 2) {
		$header_redir_url = 'https://-USER_VARIABLE_2-'; //
	}
	$html_abortlink = 'http://-USER_VARIABLE_3-';
	if (ENABLE_SSL_CATALOG == 'true' && strpos(HTTPS_CATALOG_SERVER, 'tps://') === 2) {
		$html_abortlink = 'https://-USER_VARIABLE_3-'; //
	}
	$alert_http_url = 'http://-USER_VARIABLE_4-';
	if (ENABLE_SSL_CATALOG == 'true' && strpos(HTTPS_CATALOG_SERVER, 'tps://') === 2) {
		$alert_http_url = 'https://-USER_VARIABLE_4-'; //
	}

	$html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<title>Schnellregistrierung | sofortueberweisung.de</title>
</head>
<body onload="document.getElementById('form').submit()">
<form method="post" action="https://www.sofortueberweisung.de/payment/createNew/" id="form">
<input type="hidden" name="project_name" value="%s">
<input type="hidden" name="project_homepage" value="%s">
<input type="hidden" name="projectsnotification_email_email" value="%s">
<input type="hidden" name="projectsnotification_email_activated" value="1">
<input type="hidden" name="projectsnotification_email_language_id" value="%s">
<input type="hidden" name="projectssetting_interface_cancel_link" value="%s">
<input type="hidden" name="projectssetting_interface_success_link_redirect" value="1">
<input type="hidden" name="projectssetting_interface_success_link" value="%s">
<input type="hidden" name="projectssetting_currency_id" value="%s">
<input type="hidden" name="projectssetting_locked_amount" value="1">
<input type="hidden" name="projectssetting_locked_reason_1" value="1">
<input type="hidden" name="projectssetting_locked_reason_2" value="1">
<input type="hidden" name="projectssetting_interface_input_hash_check_enabled" value="1">
<input type="hidden" name="projectssetting_project_password" value="%s">
<input type="hidden" name="project_shop_system_id" value="208">
<input type="hidden" name="project_hash_algorithm" value="sha1">
<input type="hidden" name="user_shop_system_id" value="208">
<input type="hidden" name="projectsnotification_http_activated" value="1">
<input type="hidden" name="projectsnotification_http_url" value="%s">
<input type="hidden" name="projectsnotification_http_method" value="1">
<input type="hidden" name="backlink" value="%s">
<input type="hidden" name="debug" value="0">
<noscript><input type="submit"></noscript>
</form>
</body>
</html>
HTML;

$html = sprintf($html, STORE_NAME, xtc_catalog_href_link(), STORE_OWNER_EMAIL_ADDRESS, DEFAULT_LANGUAGE, $html_abortlink, $header_redir_url,
	DEFAULT_CURRENCY, $_SESSION['pn_sofortueberweisung_pw'], $alert_http_url, $backlink);

	return $html;
    }
	function install () {
		
	      if (isset($_GET['autoinstall']) && ($_GET['autoinstall'] == '1')) {
		// Module already installed
		if (defined('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_STATUS') && (MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_STATUS == 'True')) {
			xtc_redirect(xtc_href_link_admin('admin/modules.php', 'set=payment&module=pn_sofortueberweisung', 'SSL'));
		}
		      print $this->autoinstall();
		      exit();
	      } else {

		$user_id = (! empty($_GET['user_id'])) ? xtc_db_prepare_input($_GET['user_id']) : '10000';
		$project_id = (! empty($_GET['project_id'])) ? xtc_db_prepare_input($_GET['project_id']) : '500000';
		
	        if (isset($_SESSION['pn_sofortueberweisung_pw']) && !empty($_SESSION['pn_sofortueberweisung_pw'])) {
		      $project_password = $_SESSION['pn_sofortueberweisung_pw'];
		      unset($_SESSION['pn_sofortueberweisung_pw']);
	      } else $project_password = '';

		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_STATUS', 'True', '6', '3', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ALLOWED', '', '6', '0', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_USER_ID', '" . (int) $user_id . "',  '6', '4', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_PROJECT_ID', '" . (int) $project_id . "',  '6', '4', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_PROJECT_PASSWORD', '". $project_password ."',  '6', '4', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_SORT_ORDER', '1', '6', '0', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TMP_STATUS_ID', '0',  '6', '8', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_UNC_STATUS_ID', '0',  '6', '8', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_REASON_1', 'Nr. {{order_id}} Kd-Nr. {{customer_id}}',  '6', '4', 'xtc_cfg_select_option(array(\'Nr. {{order_id}} Kd-Nr. {{customer_id}}\',\'-TRANSACTION-\'), ', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_REASON_2', '" . STORE_NAME . "', '6', '4', now())");
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_IMAGE', 'Logo & Text',  '6', '6', 'xtc_cfg_select_option(array(\'Infographic\',\'Logo & Text\',\'Logo\'), ', now())");
	      }
	}
	function remove () {
		xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
	}
	function keys () {
		return array('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_STATUS' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ALLOWED' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_USER_ID' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_PROJECT_ID' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_PROJECT_PASSWORD' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ZONE' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_REASON_1', 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TEXT_REASON_2' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_IMAGE' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TMP_STATUS_ID' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_UNC_STATUS_ID' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ORDER_STATUS_ID' , 'MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_SORT_ORDER');
	}
	// xtc_create_random_value() in inc/xtc_create_random_value.php
	function _create_random_value($length, $type = 'mixed') {
	    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;
	
	    $rand_value = '';
	    while (strlen($rand_value) < $length) {
	      if ($type == 'digits') {
		$char = xtc_rand(0,9);
	      } else {
		$char = chr(xtc_rand(0,255));
	      }
	      if ($type == 'mixed') {
// BOF - DokuMan - 2009-10-11 - replaced depricated function eregi with preg_match to be ready for PHP >= 5.3
/*
		if (eregi('^[a-z0-9]$', $char)) $rand_value .= $char;
	      } elseif ($type == 'chars') {
		if (eregi('^[a-z]$', $char)) $rand_value .= $char;
	      } elseif ($type == 'digits') {
		if (ereg('^[0-9]$', $char)) $rand_value .= $char;
*/
		if (preg_match('/^[a-z0-9]$/i', $char)) $rand_value .= $char;
	      } elseif ($type == 'chars') {
		if (preg_match('/^[a-z]$/i', $char)) $rand_value .= $char;
	      } elseif ($type == 'digits') {
		if (preg_match('/^[0-9]$/i', $char)) $rand_value .= $char;
// EOF - DokuMan - 2009-10-11 - replaced depricated function eregi with preg_match to be ready for PHP >= 5.3
	      }
	    }
	
	    return $rand_value;
	 }

	// xtc_remove_order() in admin/includes/functions/general.php
	function _remove_order($order_id, $restock = false) {
		if ($restock == 'on') {
			$order_query = xtc_db_query("select products_id, products_quantity from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".xtc_db_input($order_id)."'");
			while ($order = xtc_db_fetch_array($order_query)) {
				xtc_db_query("update ".TABLE_PRODUCTS." set products_quantity = products_quantity + ".$order['products_quantity'].", products_ordered = products_ordered - ".$order['products_quantity']." where products_id = '".$order['products_id']."'");
			}
		}
	
		xtc_db_query("delete from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($order_id)."'");
		xtc_db_query("delete from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".xtc_db_input($order_id)."'");
		xtc_db_query("delete from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".xtc_db_input($order_id)."'");
		xtc_db_query("delete from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".xtc_db_input($order_id)."'");
		xtc_db_query("delete from ".TABLE_ORDERS_TOTAL." where orders_id = '".xtc_db_input($order_id)."'");
	}
}
?>