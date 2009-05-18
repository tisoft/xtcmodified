<?php
/* -----------------------------------------------------------------------------------------
   $Id: moneybookers.php 57 2007-01-04 00:57:53Z mzanier $   

   xt:Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2007 xt:Commerce

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
 
 
 class moneybookers_callback {
 	
 	
 		function moneybookers_callback() {
 			
 		$this->repost = false;
		$this->Error = '';
		$this->oID = 0;
		$this->debug = false;
	
 		}
 	
 		function callback_process($data) {

		$this->data = $data;

		// order ID already inserted ?
		$err = $this->_CheckStatus();
		if (!$err)
			return false;

		// check if merchant ID matches
		$err = $this->_check_Merchant();

		if (!$err)
			return false;

		// validate md5signature (not implemented yet)
		$err = $this->_check_md5sig();
		if (!$err)
			return false;

		// validate transaction ID + Amount
		$err = $this->_check_TRID();
		if (!$err)
			return false;


		// transaction ID is correct, 
		$this->_setStatus();
		

	}
	
		
	function _getType($orders_id) {
		
		// get payment type from database
		$order_query = "SELECT payment_class FROM ".TABLE_ORDERS." WHERE orders_id = '" . $orders_id . "'";
		$order_query = xtc_db_query($order_query);
		$order_data = xtc_db_fetch_array($order_query);
		if ($order_data['payment_class']=='') $order_data['payment_class']='moneybookers';
		$this->statusPending=constant(MODULE_PAYMENT_.strtoupper($order_data['payment_class'])._PENDING_STATUS_ID);
		$this->statusCanceled=constant(MODULE_PAYMENT_.strtoupper($order_data['payment_class'])._CANCELED_STATUS_ID);
		$this->statusProcessed=constant(MODULE_PAYMENT_.strtoupper($order_data['payment_class'])._PROCESSED_STATUS_ID);
		$this->PWD=constant(MODULE_PAYMENT_.strtoupper($order_data['payment_class'])._PWD);
		$this->merchantID=constant(MODULE_PAYMENT_.strtoupper($order_data['payment_class'])._MERCHANTID);
		$this->emailID=constant(MODULE_PAYMENT_.strtoupper($order_data['payment_class'])._EMAILID);

//		echo $this->statusPending.'<br />';
//		echo $this->statusCanceled.'<br />';
//		echo $this->statusProcessed.'<br />';
//		echo $this->PWP.'<br />';
//		echo $this->merchantID.'<br />';
//		echo $this->emailID.'<br />';

	}
	
	function _CheckStatus() {

		$order_query = "SELECT mb_ORDERID as oid FROM payment_moneybookers WHERE mb_TRID = '" . $this->data['transaction_id'] . "'";
		$order_query = xtc_db_query($order_query);
		$order_data = xtc_db_fetch_array($order_query);

		if ($order_data['oid'] > 0) {
			$this->_getType($order_data['oid']);
			return true;
		}

		$this->Error = '1005';
		$this->repost = true;
		return false;

	}

	function _check_TRID() {
		// valid trid ?
		$query = "SELECT mb_TRID,mb_ORDERID FROM payment_moneybookers WHERE mb_TRID = '" . $this->data['transaction_id'] . "'";
		$query = xtc_db_query($query);
		if (!xtc_db_num_rows($query)) {
			$this->Error = '1002';
			return false;
		}
		// ok Insert mb transaction ID
		$query = "UPDATE payment_moneybookers SET mb_MBTID ='" . $this->data['mb_transaction_id'] . "'  WHERE mb_TRID = '" . $this->data['transaction_id'] . "'";
		$query = xtc_db_query($query);
		return true;

	}

	function _setStatus() {

		switch ($this->data['status']) {

			// processed
			case 2 :
				$result = xtc_db_query("UPDATE payment_moneybookers SET mb_ERRNO = '200', mb_ERRTXT = 'OK', mb_MBTID = '" . $this->data['mb_transaction_id'] . "', mb_STATUS = '" . $this->data['status'] . "' WHERE mb_TRID = '" . $this->data['transaction_id'] . "'");
				$status = $this->statusProcessed;
				$text = 'OK, Payment received';
				break;

				// canceled
			case -2 :
			case -1 :
				$result = xtc_db_query("UPDATE payment_moneybookers SET mb_ERRNO = '999', mb_ERRTXT = 'Transaction failed.', mb_MBTID = '" . $this->data['mb_transaction_id'] . "', mb_STATUS = '" . $this->data['status'] . "' WHERE mb_TRID = '" . $this->data['transaction_id'] . "'");
				$status = $this->statusCanceled;
				$text = 'ERROR, Transaction Failed';
				break;

			case 1 :
				$result = xtc_db_query("UPDATE payment_moneybookers SET mb_ERRNO = '200', mb_ERRTXT = 'PENDING', mb_MBTID = '" . $this->data['mb_transaction_id'] . "', mb_STATUS = '" . $this->data['status'] . "' WHERE mb_TRID = '" . $this->data['transaction_id'] . "'");
				$status = $this->statusPending;
				$text = 'WAIT, Transaction Pending';
				break;

		}

		$order_query = "SELECT mb_ORDERID as oid FROM payment_moneybookers WHERE mb_TRID = '" . $this->data['transaction_id'] . "'";
		$order_query = xtc_db_query($order_query);
		$order_data = xtc_db_fetch_array($order_query);

		xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status='" . $status . "' WHERE orders_id='" . $order_data['oid'] . "'");
		xtc_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified) values ('" . $order_data['oid'] . "', '" . $status . "', now(), '0')");

		$this->_notifyTransaction($order_data['oid'],$text);

	}

	function _check_md5sig() {

		if ($this->PWD == '')
			return true;

		$secret = $this->PWD;
		$md5sec = strtoupper(md5($secret));
		$hash = $this->data['merchant_id'] . $this->data['transaction_id'] . $md5sec . $this->data['mb_amount'] . $this->data['mb_currency'] . $this->data['status'];
		$hash = strtoupper(md5($hash));
		if ($hash != $this->data['md5sig']) {
			$this->Error = '1004';
			return false;
		}

		return true;

	}

	function _check_Merchant() {

		// does merchant ID exists ?
		if (!isset ($this->data['merchant_id']) || $this->data['merchant_id'] != $this->merchantID) {
			$this->Error = '1001';
			$this->EInfo = 'Merchant ID SEND:' . $this->data['merchant_id'] . ' Merchant ID STORED:' . $this->merchantID;
			return false;
		}
		// merchant mail ?
		if (!isset ($this->data['pay_to_email']) || $this->data['pay_to_email'] != $this->emailID) {
			$this->Error = '1003';
			$this->EInfo = 'Merchant EMAIL SEND:' . $this->data['pay_to_email'] . ' Merchant EMAIL STORED:' . $this->emailID;
			return false;
		}

		return true;
	}

	function _getError($error) {

		if ($error == '')
			return false;

		switch ($error) {

			case '1001' :
				$txt = 'merchant id does not match ' . $this->EInfo;
				break;
			case '1002' :
				$txt = 'transaction id doest not match';
				break;
			case '1003' :
				$txt = 'merchant email does not match ' . $this->EInfo;
				break;
			case '1004' :
				$txt = 'md5 signature does not match';
				break;
			case '1005' :
				$txt = 'order id not inserted yet';
				break;

		}

		// update order text
		if ($this->data['mb_transaction_id'] != '') {
			xtc_db_query("UPDATE payment_moneybookers SET mb_ERRNO = '999', mb_ERRTXT = '" . $txt . "' WHERE mb_MBTID = '" . $this->data['mb_transaction_id'] . "'");
		}

		return $txt;
	}

	function _logTransactions() {

		$this->logFileMoneybookers = DIR_FS_CATALOG . 'includes/mb.log';

		$error = $this->_getError($this->Error, $this->data);
		if ($error == '')
			$error = 'OK';

		$line = 'MB TRANS|' . date("d.m.Y H:i", time()) . '|' . xtc_get_ip_address() . '|' . $error . '|';

		foreach ($_POST as $key => $val)
			$line .= $key . ':' . $val . '|';

		error_log($line . "\n", 3, $this->logFileMoneybookers);

	}
	
	function _notifyTransaction($oID,$text) {

		
	$email_body = "Order ID: ".$oID."\n" . 'Message: '.$text . "\n\n";
	
	require_once (DIR_WS_CLASSES . 'class.phpmailer.php');
			if (EMAIL_TRANSPORT == 'smtp')
				require_once (DIR_WS_CLASSES . 'class.smtp.php');
			require_once (DIR_FS_INC . 'xtc_Security.inc.php');
	
	
	xtc_php_mail(EMAIL_BILLING_ADDRESS, 
					EMAIL_BILLING_NAME, 
					EMAIL_BILLING_ADDRESS, 
					STORE_NAME, 
					EMAIL_BILLING_FORWARDING_STRING, 
					EMAIL_BILLING_ADDRESS, 
					STORE_NAME, '', '', 'Moneybookers Payment Notification', $email_body, $email_body);
	

	}
	
 }
?>