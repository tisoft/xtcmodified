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
 */

chdir('../../');
require ('includes/application_top.php');

define('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_NO_ORDER_DETAILS', 'Error (SU101): No order-ID or customer-ID' . "\n");
define('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_ORDER_NOT_FOUND', 'Error (SU102): Order %s not found' . "\n");
define('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_WRONG_PASSWORD', 'Error (SU201): Invalid project password' . "\n");
define('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_WRONG_HASH', 'Error (SU202): Hash validation failed' . "\n");
define('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_WRONG_TOTALS', "Error (SU203): Totals do not match.\n(%s != %s)\n");
define('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_UNEXPECTED_STATUS', 'Error (SU204): Order status is not temporary' . "\n");
define('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_TRANSACTION', "Error during HTTP notification\nPlease check transaction and notification\nTransaction-ID: %s\n");
define('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_SUCCESS_TRANSACTION', "Payment successful\nTransaction-ID: %s\n");
define('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_SUCCESS_CALLBACK', 'Success (SU000): Order status successfully updated' . "\n");
define('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_WARNING_CALLBACK', 'Warning (SU001): Error discovered, but order status updated' . "\n");


$fields = array('transaction',
		'user_id',
		'project_id',
		'sender_holder',
		'sender_account_number',
		'sender_bank_code',
		'sender_bank_name',
		'sender_bank_bic',
		'sender_iban',
		'sender_country_id',
		'recipient_holder',
		'recipient_account_number',
		'recipient_bank_code',
		'recipient_bank_name',
		'recipient_bank_bic',
		'recipient_iban',
		'recipient_country_id',
		'international_transaction',
		'amount',
		'currency_id',
		'reason_1',
		'reason_2',
		'security_criteria',
		'user_variable_0',
		'user_variable_1',
		'user_variable_2',
		'user_variable_3',
		'user_variable_4',
		'user_variable_5',
		'created'
		);

$data = array();
foreach($fields as $key) {
	$data[$key] = $_POST[$key];
}
$data['project_password'] = MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_PROJECT_PASSWORD;

$validationhash = sha1(implode('|', $data));
$order_id = $customer_id = $amount = '';
$error = false;

if (! empty($_POST['user_variable_0'])) {
	$order_id = $_POST['user_variable_0'];
}
if (! empty($_POST['user_variable_1'])) {
	$customer_id = $_POST['user_variable_1'];
}
if (! empty($_POST['amount'])) {
	$amount = number_format($_POST['amount'], 2, '.', '');
}

if (empty($order_id) || empty($customer_id)) {
	print (MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_NO_ORDER_DETAILS);
	$error = true;
}
if ($validationhash != $_POST['hash']) {
	print (MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_WRONG_HASH);
	$error = true;
}

$order_query = xtc_db_query("select orders_status, currency_value from " . TABLE_ORDERS . " where orders_id = '" . (int) $order_id . "' and customers_id = '" . (int) $customer_id . "'");
if (xtc_db_num_rows($order_query) < 1) {
	printf(MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_ORDER_NOT_FOUND, $order_id);
} else {
	$order = xtc_db_fetch_array($order_query);
	
	if ($order['orders_status'] == MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_TMP_STATUS_ID) {
		$total_query = xtc_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int) $order_id . "' and class = 'ot_total' limit 1");
		$total = xtc_db_fetch_array($total_query);
		$order_total = number_format($total['value'] / $order['currency_value'], 2, '.', '');

		if ($amount == $order_total) {
			$order_status = (MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ORDER_STATUS_ID > 0 ? (int) MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ORDER_STATUS_ID : (int) DEFAULT_ORDERS_STATUS_ID);
			$comment = sprintf(MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_SUCCESS_TRANSACTION, $_POST['transaction']);
			if (!$error) print (MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_SUCCESS_CALLBACK);
		} else {
			printf(MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_WRONG_TOTALS, $amount, $order_total);
			$error = true;
		}
		
		if ($error) {
			$order_status = (MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_UNC_STATUS_ID > 0 ? (int) MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_UNC_STATUS_ID : (int) DEFAULT_ORDERS_STATUS_ID);
			$comment = sprintf(MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_TRANSACTION, $_POST['transaction']);
			print (MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_PROBLEM_CALLBACK);
		}
		
		// Update status
		$sql_data_array = array('orders_id' => (int) $order_id , 'orders_status_id' => $order_status , 'date_added' => 'now()' , 'customer_notified' => '0' , 'comments' => $comment);
		xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
		xtc_db_query("update " . TABLE_ORDERS . " set orders_status = '" . $order_status . "', last_modified = now() where orders_id = '" . (int) $order_id . "'");

	} else {
		print (MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_ERROR_UNEXPECTED_STATUS);
	}
}

?>
