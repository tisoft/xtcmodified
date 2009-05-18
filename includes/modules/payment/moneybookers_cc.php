<?php

/* -----------------------------------------------------------------------------------------
   $Id: moneybookers_cc.php 150 2007-01-24 09:54:08Z mzanier $

   xt:Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2006 xt:Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(moneybookers.php,v 1.00 2003/10/27); www.oscommerce.com

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Moneybookers v1.0                       Autor:    Gabor Mate  <gabor(at)jamaga.hu>

   Released under the GNU General Public License
   
   // Version History
    * 2.0 xt:Commerce Adaption
    * 2.1 new workflow, tmp orders
    * 2.2 new modules
   
   
   ---------------------------------------------------------------------------------------*/

class moneybookers_cc {
	var $code, $title, $description, $enabled, $auth_num, $transaction_id;
	var $mbLanguages, $mbCurrencies, $aCurrencies, $defCurr, $defLang;

	// class constructor
	function moneybookers_cc() {
		global $order, $language;

		$this->code = 'moneybookers_cc';
		$this->version = '2.1';
		$this->title = MODULE_PAYMENT_MONEYBOOKERS_CC_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_MONEYBOOKERS_CC_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_MONEYBOOKERS_CC_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_MONEYBOOKERS_CC_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_MONEYBOOKERS_CC_TEXT_INFO;
		$this->tmpOrders = true;
		$this->tmpStatus = MODULE_PAYMENT_MONEYBOOKERS_CC_TMP_STATUS_ID;
		$this->icons_available = xtc_image(DIR_WS_ICONS . 'cc_amex_small.jpg') . ' ' .
		xtc_image(DIR_WS_ICONS . 'cc_mastercard_small.jpg') . ' ' .
		xtc_image(DIR_WS_ICONS . 'cc_visa_small.jpg') . ' ' .
		xtc_image(DIR_WS_ICONS . 'cc_diners_small.jpg');

		$this->repost = false;
		$this->Error = '';
		$this->oID = 0;

		$this->debug = false;

		$this->transaction_id = '';



		if ((int) MODULE_PAYMENT_MONEYBOOKERS_CC_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_MONEYBOOKERS_CC_ORDER_STATUS_ID;
		}
		//
		if (is_object($order))
			$this->update_status();

		$this->form_action_url = 'https://www.moneybookers.com/app/payment.pl';
	}

	////
	// Status update
	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_MONEYBOOKERS_CC_ZONE > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_MONEYBOOKERS_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}

	// class methods
	function javascript_validation() {
		return false;
	}

	function selection() {

		$content = array();
		$accepted = '';
		$icons = explode(',', MODULE_PAYMENT_MONEYBOOKERS_CC_ICONS);
		foreach ($icons as $key => $val)
			$accepted .= xtc_image(DIR_WS_ICONS . $val) . ' ';



		$content = array_merge($content, array (
			array (
				'title' => ' ',
				'field' => $accepted.' '.xtc_image(DIR_WS_ICONS. 'powered_mb.png')
			)
		));
		

		return array (
			'id' => $this->code,
			'module' => $this->title,
			'fields' => $content,
			'description' => $this->info
		);
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return false;
	}

	function process_button() {
		return false;
	}


	function payment_action() {
		global $order, $xtPrice,$insert_id;

		$result = xtc_db_query("SELECT code FROM languages WHERE languages_id = '" . $_SESSION['languages_id'] . "'");

		$mbLanguage = strtoupper($_SESSION['language_code']);

		$mbCurrency = $_SESSION['currency'];

		$this->transaction_id = $this->generate_trid();
		$result = xtc_db_query("INSERT INTO payment_moneybookers (mb_TRID, mb_DATE) VALUES ('{$this->transaction_id}', NOW())");
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}
		if ($_SESSION['currency'] == $mbCurrency) {
			$amount = round($total, $xtPrice->get_decimal_places($mbCurrency));
		} else {
			$amount = round($xtPrice->xtcCalculateCurrEx($total, $mbCurrency), $xtPrice->get_decimal_places($mbCurrency));
		}

//		$process_button_string = 
		
		      $params = array('pay_to_email'=>  MODULE_PAYMENT_MONEYBOOKERS_CC_EMAILID,
		'transaction_id'=> $this->transaction_id,
		'return_url'=> xtc_href_link(FILENAME_CHECKOUT_PROCESS, 'trid=' . $this->transaction_id, 'NONSSL', true, false),
		'cancel_url'=>  xtc_href_link(FILENAME_CHECKOUT_PAYMENT, MODULE_PAYMENT_MONEYBOOKERS_CC_ERRORTEXT1 . $this->code . MODULE_PAYMENT_MONEYBOOKERS_CC_ERRORTEXT2, 'SSL', true, false),
		'status_url'=>  xtc_href_link('callback/moneybookers/callback_mb.php'),
		'language'=>  strtoupper($_SESSION['language_code']),
		'pay_from_email'=>  $order->customer['email_address'],
		'amount'=>  $amount,
		'currency'=>  $mbCurrency,
		'detail1_description'=>  'Shop:',
		'detail1_text'=>  STORE_NAME.' Order:'.$insert_id,

		'detail2_description'=>  'Datum:',
		'detail2_text'=>  strftime(DATE_FORMAT_LONG),

		'amount2_description'=>  'Summe:',
		'amount2'=>  round($amount,2),
		'payment_methods'=>'ACC',

		'merchant_fields'=>  'Field1',
		'Field1'=>  md5(MODULE_PAYMENT_MONEYBOOKERS_CC_MERCHANTID),

		'firstname'=>  $order->billing['firstname'],
		'lastname'=>  $order->billing['lastname'],
		'address'=>  $order->billing['street_address'],
		'postal_code'=>  $order->billing['postcode'],
		'city'=>  $order->billing['city'],
		'state'=>  $order->billing['state'],
		'country'=>  $order->billing['country']['iso_code_3'],
		'confirmation_note'=>  MODULE_PAYMENT_MONEYBOOKERS_CC_CONFIRMATION_TEXT);
		
		$data = '';
        foreach ($params as $key => $value) {
          $value = strtr($value, "áéíóöõúüûÁÉÍÓÖÕÚÜÛ", "aeiooouuuAEIOOOUUU");
          if ($key!='status_url') {
          	$value=urlencode($value);
          } 
          $data .= $key . '=' . $value . "&";
        }

		// moneyboocers.com payment gateway does not accept accented characters!
		// Please feel free to add any other accented characters to the list.
//		return strtr($process_button_string, "áéíóöõúüûÁÉÍÓÖÕÚÜÛ", "aeiooouuuAEIOOOUUU");

		// insert data
		xtc_db_query("UPDATE payment_moneybookers SET mb_ORDERID = '" . $insert_id . "' WHERE mb_TRID = '" . $this->transaction_id . "'");
		xtc_redirect($this->form_action_url.'?'.$data);
	}

	// manage returning data from moneybookers (errors, failures, success etc.)
	function before_process() {
		return false;
	}

	function after_process() {
		return false;

	}

	function admin_order($oID) {
		$oID = (int) $oID;

		$query = "SELECT * FROM payment_moneybookers WHERE mb_ORDERID = '" . $oID . "'";
		$query = xtc_db_query($query);

		$data = xtc_db_fetch_array($query);

		$html = '
						<tr>
				            <td class="main">' . MB_TEXT_MBDATE . '</td>
				            <td class="main">' . $data['mb_DATE'] . '</td>
				        </tr>
						<tr>
				            <td class="main">' . MB_TEXT_MBTID . '</td>
				            <td class="main">' . $data['mb_MBTID'] . '</td>
				        </tr>
						<tr>
				            <td class="main">' . MB_TEXT_MBERRTXT . '</td>
				            <td class="main">' . $data['mb_ERRTXT'] . '</td>
				        </tr>';

		echo $html;

	}

	function get_error() {
		global $_GET;

		$error = array (
			'title' => MODULE_PAYMENT_MONEYBOOKERS_CC_TEXT_ERROR,
			'error' => stripslashes(urldecode($_GET['error']
		)));

		return $error;
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_MONEYBOOKERS_CC_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {


		$this->remove();
		
		//
		$mb_installed = false;
		$tables = mysql_list_tables(DB_DATABASE);
		while ($row = mysql_fetch_row($tables)) {
    		if ($row[0] == 'payment_moneybookers') $mb_installed=true;
		}

		if ($mb_installed==false) {
		xtc_db_query("CREATE TABLE payment_moneybookers (mb_TRID varchar(255) NOT NULL default '',mb_ERRNO smallint(3) unsigned NOT NULL default '0',mb_ERRTXT varchar(255) NOT NULL default '',mb_DATE datetime NOT NULL default '0000-00-00 00:00:00',mb_MBTID bigint(18) unsigned NOT NULL default '0',mb_STATUS tinyint(1) NOT NULL default '0',mb_ORDERID int(11) unsigned NOT NULL default '0',PRIMARY KEY  (mb_TRID))");
		}

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_STATUS', 'True',  '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_EMAILID', '', '6', '1', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_PWD', '',  '6', '2', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_MERCHANTID', '', '6', '3', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_SORT_ORDER', '0',  '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_ZONE', '0',  '6', '7', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_ALLOWED', '', '6', '0', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_TMP_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_PROCESSED_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_PENDING_STATUS_ID', '0',  '6', '8', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_CANCELED_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_MONEYBOOKERS_CC_ICONS', 'cc_visa.jpg,cc_mastercard.jpg,cc_amex.jpg,cc_diners.jpg',  '6', '0', now())");

		// tables


	}

	function remove() {
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys() {
		return array (
			'MODULE_PAYMENT_MONEYBOOKERS_CC_STATUS',
			'MODULE_PAYMENT_MONEYBOOKERS_CC_EMAILID',
			'MODULE_PAYMENT_MONEYBOOKERS_CC_PWD',
			'MODULE_PAYMENT_MONEYBOOKERS_CC_MERCHANTID',
			'MODULE_PAYMENT_MONEYBOOKERS_CC_PROCESSED_STATUS_ID',
			'MODULE_PAYMENT_MONEYBOOKERS_CC_PENDING_STATUS_ID',
			'MODULE_PAYMENT_MONEYBOOKERS_CC_CANCELED_STATUS_ID',
			'MODULE_PAYMENT_MONEYBOOKERS_CC_TMP_STATUS_ID',
			'MODULE_PAYMENT_MONEYBOOKERS_CC_SORT_ORDER',
			'MODULE_PAYMENT_MONEYBOOKERS_CC_ALLOWED',
			'MODULE_PAYMENT_MONEYBOOKERS_CC_ZONE'
		);
	}



	// Parse the predefinied array to be 'module install' friendly
	// as it is used for select in the module's install() function
	function show_array($aArray) {
		$aFormatted = "array(";
		foreach ($aArray as $key => $sVal) {
			$aFormatted .= "\'$sVal\', ";
		}
		$aFormatted = substr($aFormatted, 0, strlen($aFormatted) - 2);
		return $aFormatted;
	}

	function generate_trid() {

		do {
			$trid = xtc_create_random_value(16, "digits");
			$trid =  chr(88).chr(84).chr(67) . $trid;
			$result = xtc_db_query("SELECT mb_TRID FROM payment_moneybookers WHERE mb_TRID = '".$trid."'");
		} while (mysql_num_rows($result));

		return $trid;

	}
}
?>