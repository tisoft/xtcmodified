<?php
/* -----------------------------------------------------------------------------------------
   $Id: luupws.php 998 2006-06-09 14:18:20Z mz $   

   xt:Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2006 xt:Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2002-2003 osCommerce(LUUPws.php, v0.1 2005/11/09); www.oscommerce.com 
   (c) Eskil Hauge (eskil@luup.com)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/


//error_reporting(E_ERROR | E_WARNING | E_PARSE );
//error_reporting(E_NONE);

class luupws {
    var $code, $title, $description, $sort_order, $enabled, $version, $luup, $transaction_id;

// class constructor
    function luupws() {
      global $order;
      $this->code = 'luupws';
      $this->version = '3.0';
      $this->logo = xtc_image(DIR_WS_ICONS.'luupay.gif');
      $this->title = MODULE_PAYMENT_LUUPWS_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_LUUPWS_TEXT_DESCRIPTION;
      
      // curl check
      
      $this->description.='<br>';
      if (!function_exists(curl_init)) {
      	$this->description.='<font color="#ff0000">ERROR: NO CURL INSTALLED</font>';
      	$this->description.='<br>Please contact your admin to enable cURL library on your server!';
      }
      
      
      
      $this->sort_order = MODULE_PAYMENT_LUUPWS_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_LUUPWS_STATUS == 'True') ? true : false); 
      $this->debug=false;
      if ((int)MODULE_PAYMENT_LUUPWS_ORDER_STATUS_ID > 0) {
              $this->order_status = MODULE_PAYMENT_LUUPWS_ORDER_STATUS_ID;
      }
      
      if (is_object($order)) $this->update_status();
      
    }
    
 ////////////////////////////////////
 // CUSTOM METHODS - LUUP specific //
 ////////////////////////////////////
 
    function luup_init_ws()
    {
    	// TODO: dynamic loading of vars

    	if (!file_exists(DIR_WS_INCLUDES.'nusoap/luup_webpayments.php')) {
    		$client_path =DIR_FS_CATALOG.DIR_WS_INCLUDES.'nusoap/luup_webpayments.php';
    	} else {
    		$client_path =DIR_WS_INCLUDES.'nusoap/luup_webpayments.php';
    	}
    		
    	
    	
      	require_once($client_path);
		$this->client = new luup_webpayments( 'lib/' );
		$this->client->merchantId = MODULE_PAYMENT_LUUPWS_MERCHANT_ID;
        $this->client->merchantKey = MODULE_PAYMENT_LUUPWS_MERCHANT_KEY;
    }
    
    
    // for use in admin custom orders page
    function luup_refundPayment( $transactionId ){
    	$this->luup_init_ws();
    	return $this->client->refundPayment( $transactionId );
    }
    
    
    // for use in admin custom orders page
    function luup_cancelPayment( $transactionId ){
	$this->luup_init_ws();
	return $this->client->cancelPayment( $transactionId );
    }
    
    
    // for use in admin custom orders page
    function luup_completePayment( $transactionId ){
    	$this->luup_init_ws();
    	return $this->client->completePayment( $transactionId );
    }
    
    
    function luup_get_countries()
    {
    	// string format is "country|code country|code etc"
    	
    	$countries = explode( ",", MODULE_PAYMENT_LUUPWS_TEXT_COUNTRIES );
    	
    	foreach($countries as $item) {
    		$arr = explode( "|", $item );
    		$countries_list[] = array( 'id' => $arr[0], 'text' => $arr[1] );
    		
    	}
    	
    	return $countries_list;
    }
    
    
    // Creates a temporary unique order reference - format: custId 01/12 17:04
    // Used as merchant reference (visible only to merchant in LUUP merchant web transaction overview).
    // (reason: osCommerce order has no orderId until after the payment is completed)
    function luup_orderno($customer_id){
    	return "$customer_id " . date("d/m H:i");
    }
    
    
    // examines selected currency and sets currency to EUR if chosen currency is not supported by LUUP.
    function luup_validate_currency( $curr ){
	if (!in_array($curr, array('NOK', 'EUR', 'GBP'))) {
	$curr = 'EUR';
	}

	return $curr;
    }
    
    
    // generate exiry time based on number of days for payment reservation set in module configuration
    // (currently not visualised in configuration)
    function luup_get_expiry_time(){
    	/*
    	if( isset(MODULE_PAYMENT_LUUPWS_PAYMENT_EXPIRY_DAYS) && is_numeric(MODULE_PAYMENT_LUUPWS_PAYMENT_EXPIRY_DAYS) ){
    		$expires = time() + (MODULE_PAYMENT_LUUPWS_PAYMENT_EXPIRY_DAYS * 24 * 60 * 60);
    		return date( "Y-m-d\TH:i:s", $expires );
    	}
    	else*/ return '2099-12-12T12:00:00'; // if not set, generate a max value that will be overridden by LUUP system policy
    }
    
    
    // insert payment information into LUUP table
    function luup_db_insert($order_id){
    	if(MODULE_PAYMENT_LUUPWS_PAYMENT_COLLECTION == 'Reserved')
    		$status = 'Pending';
    	else
    		$status = 'Completed';
    	
        xtc_db_query('insert into LUUP(transaction_id, payment_status, order_id) values("'.
		$this->transaction_id .'", "'. 
		$status . '", '.$order_id.')');
    }
    

//////////////////////////////////////// ///////   
// CLASS METHODS (generic osc payment module) //
////////////////////////////////////////////////
    
    function update_status() {
        global $order;
    
  	if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_LUUPWS_ZONE > 0) ) {
    		$check_flag = false;
    		$check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_LUUPWS_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
    		while ($check = xtc_db_fetch_array($check_query)) {
      			if ($check['zone_id'] < 1) {
				$check_flag = true;
				break;
      			} elseif ($check['zone_id'] == $order->delivery['zone_id']) {
				$check_flag = true;
				break;
      			}
    		}

//    		if ($check_flag == false) {
//      			$this->enabled = false;
//   		}
    	}
    }
    
    // generate client-side input validation
    function javascript_validation() {

    	
    	// make sure to generate different validation based on page state
    	
    	if( !(isset($_GET['step']) && $_GET['step'] == 'step2') ) {
		$js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
			    '    var luup_userid = document.checkout_payment.luupws_userid.value;' . "\n" .
			    '    var luup_pin = document.checkout_payment.luupws_pin.value;' . "\n" .
			    '    if (luup_userid == "" || luup_userid.length < 5) {' . "\n" .
			    '      error_message = error_message + "' . MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_USER . '";' . "\n" .
			    '      error = 1;' . "\n" .
			    '    }' . "\n" .
			    '    if (luup_pin == "" || luup_pin.length < 4) {' . "\n" .
			    '      error_message = error_message + "' . MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_PIN . '";' . "\n" .
			    '      error = 1;' . "\n" .
			    '    }' . "\n" .
			    '  }' . "\n";
	 }
	 else{
	 	$js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
			    '    var luup_vercode = document.checkout_payment.luupws_verification_code.value;' . "\n" .
			    '    if (luup_vercode == "" || luup_vercode.length < 8) {' . "\n" .
			    '      error_message = error_message + "' . MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_CODE . '";' . "\n" .
			    '      error = 1;' . "\n" .
			    '    }' . "\n" .
			    '  }' . "\n";
	 }
	
      return $js;
    }

    
    // generate input fields on checkout_payment.php
    function selection() {
    //	global $_GET, $_POST;

    	$test_info = '';
    	if( MODULE_PAYMENT_LUUPWS_TESTMODE == 'True' )
		$test_info = ' (TESTING ONLY)';
    	
    	// render step 1: enter username + PIN
    	if( !(isset($_GET['step']) && $_GET['step'] == 'step2') ) {
		return array('id' => $this->code,
			   'module' => MODULE_PAYMENT_LUUPWS_TEXT_TITLE_SHOP.'&nbsp;&nbsp;&nbsp;&nbsp;' . $test_info,
			   'fields' => array(
			   		// title
					array(	'title' => $this->logo,
						'field' => MODULE_PAYMENT_LUUPWS_TEXT_LINK_REGISTER),				
					// title
					array(	'title' => '<hr noshade>',
						'field' =>  '<hr noshade>'),
					// title
					array(	'title' => MODULE_PAYMENT_LUUPWS_TEXT_STEP1,
						'field' => MODULE_PAYMENT_LUUPWS_TEXT_STEP1_DESCRIPTION),
					// countrycode
					array(	'title' => MODULE_PAYMENT_LUUPWS_TEXT_REGISTERED_IN,
						'field' => xtc_draw_pull_down_menu('luupws_country_code',$this->luup_get_countries())),
					// userid
					array(	'title' => MODULE_PAYMENT_LUUPWS_TEXT_USERID,
						'field' => xtc_draw_input_field('luupws_userid', '', 'maxlenght="20" size="15"')),
					// pin
					array(	'title' => MODULE_PAYMENT_LUUPWS_TEXT_PIN,
						'field' => xtc_draw_password_field('luupws_pin', '', 'maxlength="4" size="4"')),
					array(	'title' => '', 'field' => xtc_draw_hidden_field('step', 'step1'))
					// submit button
					/*
					array(	'title' => '',
						'field' => xtc_draw_input_field('', MODULE_PAYMENT_LUUPWS_TEXT_CONTINUE, '', 'submit'))
					*/
					));
	}
	// render step 2: enter verification code
        else {
		$country_code = $_GET['luupws_country_code'];
		$user = $_GET['luupws_userid'];
		return array('id' => $this->code,
				   'module' => MODULE_PAYMENT_LUUPWS_TEXT_TITLE_SHOP . $test_info,
				   'fields' => array(
				   		// title
					array(	'title' => $this->logo,
						'field' => ''),				
					// title
					array(	'title' => '<hr noshade>',
						'field' =>  '<hr noshade>'),
					// title
						// title
						array(	'title' => MODULE_PAYMENT_LUUPWS_TEXT_STEP2,
							'field' => MODULE_PAYMENT_LUUPWS_TEXT_STEP2_DESCRIPTION),
						// verification code
						array(	'title' => MODULE_PAYMENT_LUUPWS_TEXT_VERIFICATION_CODE,
							'field' => xtc_draw_input_field('luupws_verification_code', '', 'maxlength="8" size="8"')),
						// add previous input as hidden fields. Must have these for later.
						array(	'title' => '', 'field' => xtc_draw_hidden_field('luupws_country_code', $country_code)),
						array(	'title' => '', 'field' => xtc_draw_hidden_field('luupws_userid', $user)),
						array(	'title' => '', 'field' => xtc_draw_hidden_field('step', 'step2'))
						));
	}
    }


    // validate input from checkout_payment.php (processed in checkout_confirmation.php)
    function pre_confirmation_check() {
    	global $_POST;
	
	// skip this function if this is the second call-back to page (user completed step1)
	if(isset($_POST['step']) && $_POST['step']!='step1')
		return false;
	
	// check that there is a conversion value for Euro(default currency) if the selected currency is not supported.
	$my_currency = $this->luup_validate_currency( $_SESSION['currency'] );
	
//	if( !$currencies->is_set($my_currency) ){
//		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_LUUPWS_TEXT_ERROR_NO_EURO_CONVERSION_VALUE), 'NONSSL', true, false));
//	}
	
	$countrycode = $_POST['luupws_country_code'];
	$userid = $_POST['luupws_userid'];
	$pin = $_POST['luupws_pin'];
	
	if (!$this->debug) {
	
	$this->luup_init_ws();
	$success = $this->client->authenticateUser( $countrycode, $userid, $pin );
	
	if($success)
		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'step=step2&luupws_country_code='.$countrycode.'&luupws_userid='.$userid, 'SSL', true, false));
	else
		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code.'&error='.$this->client->errorCode, 'SSL', true, false));
	return;
	
	} else {
		
		echo 'luupws_country_code = '.$countrycode.'<br>';
		echo 'pin = '.$pin.'<br>';
		echo 'luupws_userid = '.$userid.'<br>';
		
	}
	
    }


    function confirmation() {
	return false;
    }
    
    
    // generates post values on checkout_confirmation.php
    function process_button() {
    	global $_POST;
    	
    	$process_button_string = 
		xtc_draw_hidden_field('luupws_verification_code', $_POST['luupws_verification_code']).
		xtc_draw_hidden_field('luupws_country_code', $_POST['luupws_country_code']).
		xtc_draw_hidden_field('luupws_userid', $_POST['luupws_userid']);
		
    	return $process_button_string;
    }

    
    // called before processing order in checkout_process.php
    // processes payment before order is updated/completed
    function before_process() {
	global $_POST, $order, $xtPrice, $customer_id;
	
	$countryCode = $_POST['luupws_country_code'];
	$userId = $_POST['luupws_userid'];
	$verificationCode = $_POST['luupws_verification_code'];
	
	$my_currency = $this->luup_validate_currency( $_SESSION['currency'] );
	
	// get order total value
	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
	} else {
			$total = $order->info['total'];
	}
	
	if ($_SESSION['currency'] == $my_currency) {
			$amount = round($total, $xtPrice->get_decimal_places($my_currency));
			$shipping = round($order->info['shipping_cost'], $xtPrice->get_decimal_places($my_currency));
	} else {
			$amount = round($xtPrice->xtcCalculateCurrEx($total, $my_currency), $xtPrice->get_decimal_places($my_currency));
			$shipping = round($xtPrice->xtcCalculateCurrEx($order->info['shipping_cost'], $my_currency), $xtPrice->get_decimal_places($my_currency));
	}
	
	if( MODULE_PAYMENT_LUUPWS_TESTMODE == 'True' ) // If testing, set currency to test currency unit (TCU)
		$curr = 'TCU';
	else
		$curr = $my_currency;
	
	$merchantRef = $this->luup_orderno($customer_id);
	
	$paymentRef = STORE_NAME;
	
	$expires = $this->luup_get_expiry_time();
	
	if (!$this->debug) {
		
	$this->luup_init_ws();
	
	if(MODULE_PAYMENT_LUUPWS_PAYMENT_COLLECTION == 'Immediate'){
		$success = $this->client->collectPayment($countryCode, $userId, $curr, $amount, $paymentRef, $merchantRef, $verificationCode);
	}	
	else{
		$success = $this->client->reservePayment($countryCode, $userId, $curr, $amount, $paymentRef, $merchantRef, $verificationCode, $expires);
	}
	
	if($success){		
		$this->transaction_id = $this->client->transactionId;
		
		if(MODULE_PAYMENT_LUUPWS_USE_DB != 'Yes')
			$order->info['comments'] .= 'LUUP transaction id: ' .$this->transaction_id .'<br>';
			
		if(MODULE_PAYMENT_LUUPWS_TESTMODE == 'True')
			$order->info['comments'] .= 'LUUP TEST ORDER - NOT VALID PAYMENT - DO NOT SHIP';
		
		return true;
		
	}
	else{
		if($this->client->errorCode == '203')
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'step=step2&payment_error='.$this->code.'&error='.$this->client->errorCode, 'SSL', true, false));
		else
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error='.$this->code.'&error='.$this->client->errorCode, 'SSL', true, false));
	}
	} else {  // debug
	
		echo 'CURR : '.$curr.'<br>';
		echo 'AMOUNT : '.$amount.'<br>';
		echo 'REF : '.$paymentRef.'<br>';
		echo 'MERCHANTREF : '.$merchantRef.'<br>';
		echo 'VER CODE : '.$verificationCode.'<br>';
		echo 'EXPIRES : '.$expires.'<br>';
	
	}
	
	
    }
    
    // any post processing after payment and order has been processed
    function after_process(){
	global $insert_id;
	
	// insert transaction data into LUUP table if enabled
	if(MODULE_PAYMENT_LUUPWS_USE_DB == 'Yes')
		$this->luup_db_insert($insert_id);
		
		if ($this->order_status)
			xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
		
		
    }
    
    
    // displays proper error text generated by LUUPws.php
    function get_error() {
        global $_GET, $language;
        
        // ensure we have all texts defined (and only once! - php>=4.0.1pl2)
//        require_once(DIR_WS_LANGUAGES . $language . '/modules/payment/'.$this->code.'.php');
        
        $error = '';
        $error_text['title'] = MODULE_PAYMENT_LUUPWS_TEXT_ERROR_MESSAGE;
    	if(isset($_GET['error']))
    		$error = urldecode($_GET['error']); // otherwise default error is displayed
    	switch($error){
		case '101':
			$error_text['error'] = MODULE_PAYMENT_LUUPWS_TEXT_ERROR_101;
			break;
		case '201':
			$error_text['error'] = MODULE_PAYMENT_LUUPWS_TEXT_ERROR_201;
			break;
		case '202':
			$error_text['error'] = MODULE_PAYMENT_LUUPWS_TEXT_ERROR_202;
			break;
		case '203':
			$error_text['error'] = MODULE_PAYMENT_LUUPWS_TEXT_ERROR_203;
			break;
		case '206':
			$error_text['error'] = MODULE_PAYMENT_LUUPWS_TEXT_ERROR_206;
			break;
		case '301':
			$error_text['error'] = MODULE_PAYMENT_LUUPWS_TEXT_ERROR_301;
			break;
		default: //other error
			$error_text['error'] = MODULE_PAYMENT_LUUPWS_TEXT_ERROR_UNKNOWN ." ($error)";
			break;
	}
	return $error_text;
    }
    
    	
    function check() {
	if (!isset($this->_check)) {
		$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_LUUPWS_STATUS'");
		$this->_check = xtc_db_num_rows($check_query);
	}
	return $this->_check;
    }
    
    
    function install() {
	xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_LUUPWS_SORT_ORDER', '0',  '6', '0' , now())");
	xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_LUUPWS_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
	xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_LUUPWS_ALLOWED', '', '6', '0', now())");
	xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_LUUPWS_MERCHANT_ID', 'NOR/yourshopid',  '6', '2', now())");
	xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_LUUPWS_MERCHANT_KEY', '', '6', '3', now())");
	xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_LUUPWS_TESTMODE', 'False', '6', '4', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
	xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_LUUPWS_ORDER_STATUS_ID', '0',  '6', '5', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
	xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_LUUPWS_PAYMENT_COLLECTION', 'Reserved', '6', '6', 'xtc_cfg_select_option(array(\'Immediate\', \'Reserved\'), ', now())");
	xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_LUUPWS_USE_DB', 'Yes',  '6', '7', 'xtc_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
	xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_LUUPWS_ZONE', '0', '6', '8', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");    
    xtc_db_query("DROP TABLE IF EXISTS LUUP");
	xtc_db_query("CREATE TABLE LUUP (transaction_id varchar(50) NOT NULL,payment_status varchar(10) NOT NULL,order_id int,PRIMARY KEY (transaction_id));");  
    }
    
    
    function remove() {
          xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
     }
     
    
     function keys() {
          return array('MODULE_PAYMENT_LUUPWS_SORT_ORDER', 'MODULE_PAYMENT_LUUPWS_ALLOWED',
          		'MODULE_PAYMENT_LUUPWS_STATUS',
          		'MODULE_PAYMENT_LUUPWS_ZONE',
          		'MODULE_PAYMENT_LUUPWS_MERCHANT_ID',
          		'MODULE_PAYMENT_LUUPWS_MERCHANT_KEY',
          		'MODULE_PAYMENT_LUUPWS_ORDER_STATUS_ID',
          		'MODULE_PAYMENT_LUUPWS_PAYMENT_COLLECTION',
          		'MODULE_PAYMENT_LUUPWS_USE_DB',
          		'MODULE_PAYMENT_LUUPWS_TESTMODE'
          		);
     }
 }

 ?>