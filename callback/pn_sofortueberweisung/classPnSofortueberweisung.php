<?php

/**
 * @version sofort�isung.de 1.0 - $Date: 2010-03-22 12:53:09 +0100 (Mo, 22 Mrz 2010) $
 * @author Payment Network AG (integration@payment-network.com)
 * @link http://www.payment-network.com/
 * 
 * Copyright (c) 2010 Payment Network AG
 *
 * $Id: classPnSofortueberweisung.php 99 2010-03-22 11:53:09Z thoma $
 * 
 */
class classPnSofortueberweisung {

	var $hashfunction;
	var $password;
	var $password2;
	var $formActionUrl;
	var $version;



	function classPnSofortueberweisung($password = '', $hashfunction = 'sha1') {

		$this->password = $password;
		$this->password2 = '';
		$this->hashfunction = $hashfunction;
		$this->version = 'pn_generic_1.0';
		$this->formActionUrl = 'https://www.sofortueberweisung.de/payment/start?';


		return true;
	}

	/**
	 * @param int $userId
	 * @param int $projectId
	 * @param string $amount
	 * @param string $currency EUR|CHF
	 * @param string [optional] $reason1
	 * @param string [optional] $reason2
	 * @param string [optional] $userVariable0
	 * @param string [optional] $userVariable1
	 * @param string [optional] $userVariable2
	 * @param string [optional] $userVariable3
	 * @param string [optional] $userVariable4
	 * @param string [optional] $userVariable5
	 * @param string [optional] $senderHolder
	 * @param string [optional] $senderAccountNumber
	 * @param string [optional] $senderBankCode
	 * @param string [optional] $senderCountryId
	 * @return string url with urlencoded variables
	 */
	function getPaymentUrl($userId, $projectId, $amount, $currency,
	$reason1 = '' , $reason2 = '' , $userVariable0 = '' , $userVariable1 = '' , $userVariable2 = '' ,
	$userVariable3 = '' , $userVariable4 = '' , $userVariable5 = '', 
	$senderHolder = '', $senderAccountNumber = '', $senderBankCode = '', $senderCountryId = ''){


		$data = $this->getPaymentParameters($userId, $projectId, $amount, $currency,
				$reason1, $reason2, $userVariable0, $userVariable1, $userVariable2,
				$userVariable3, $userVariable4, $userVariable5, 
				$senderHolder, $senderAccountNumber, $senderBankCode, $senderCountryId);

		$dataString = '';
		foreach ($data as $key => $value) {
			$dataString .= $key.'='.urlencode($value).'&';
		}
		$dataString = substr($dataString, 0, -1); //remove last &

		return $this->formActionUrl.$dataString;
	}

	/**
	 * @param int $userId
	 * @param int $projectId
	 * @param string $amount
	 * @param string $currency EUR|CHF
	 * @param string [optional] $reason1
	 * @param string [optional] $reason2
	 * @param string [optional] $userVariable0
	 * @param string [optional] $userVariable1
	 * @param string [optional] $userVariable2
	 * @param string [optional] $userVariable3
	 * @param string [optional] $userVariable4
	 * @param string [optional] $userVariable5
	 * @param string [optional] $senderHolder
	 * @param string [optional] $senderAccountNumber
	 * @param string [optional] $senderBankCode
	 * @param string [optional] $senderCountryId
	 * @return array array with parameters for payment message
	 */	
	function getPaymentParameters($userId, $projectId, $amount, $currency,
	$reason1 = '' , $reason2 = '' , $userVariable0 = '' , $userVariable1 = '' , $userVariable2 = '' ,
	$userVariable3 = '' , $userVariable4 = '' , $userVariable5 = '', 
	$senderHolder = '', $senderAccountNumber = '', $senderBankCode = '', $senderCountryId = '') {

	$tmparray = array(
		$userId,
		$projectId,
		$senderHolder,
		$senderAccountNumber,
		$senderBankCode,
		$senderCountryId,
		$amount,
		$currency,
		$reason1,
		$reason2,
		$userVariable0,
		$userVariable1,
		$userVariable2,
		$userVariable3,
		$userVariable4,
		$userVariable5,
		$this->password);

		$hash = $this->generateHash(implode("|", $tmparray));

		$data['user_id'] = $userId;
		$data['project_id'] = $projectId;
		$data['amount'] = $amount;
		$data['currency_id'] = $currency;
		$data['reason_1'] = $reason1;
		$data['reason_2'] = $reason2;
		$data['user_variable_0'] = $userVariable0;
		$data['user_variable_1'] = $userVariable1;
		$data['user_variable_2'] = $userVariable2;
		$data['user_variable_3'] = $userVariable3;
		$data['user_variable_4'] = $userVariable4;
		$data['user_variable_5'] = $userVariable5;
		$data['hash'] = $hash;
		$data['encoding'] = 'iso-8859-1';
		$data['payment_module'] = $this->version;
		$data['interface_version'] = $this->version;
		
		return $data;
	}

	/**
	 * 	checks server response and gets parameters  
	 *  @return $data array|string response parameters or ERROR_WRONG_HASH|ERROR_NO_ORDER_DETAILS if error
	 * 
	 */
	function getNotification(){

		$fields = array(
		'transaction', 'user_id', 'project_id', 
		'sender_holder', 'sender_account_number', 'sender_bank_code', 'sender_bank_name', 'sender_bank_bic', 'sender_iban', 'sender_country_id',	
		'recipient_holder',	'recipient_account_number', 'recipient_bank_code', 'recipient_bank_name', 'recipient_bank_bic',	'recipient_iban', 'recipient_country_id',
		'international_transaction', 'amount', 'currency_id', 
		'reason_1',	'reason_2',
		'security_criteria', 
		'user_variable_0',	'user_variable_1', 'user_variable_2', 'user_variable_3', 'user_variable_4',	'user_variable_5',
		'created'
		);

		$data = array();
		foreach($fields as $key) {
			$data[$key] = $_POST[$key];
		}
		$data['project_password'] = $this->password;
		if(empty($this->password)) {
			return "Error (SU201): Invalid password\n";
		}

		$validationhash = $this->generateHash(implode('|', $data));
		$messagehash = $_POST['hash'];

		$x_order_id = $data['user_variable_0'];
		$x_customer_id = $data['user_variable_1'];
		$data['amount'] = number_format($data['amount'], 2, '.', '');

		if (empty($x_order_id) || empty($x_customer_id)) {
			return "Error (SU101): No order-ID or customer-ID\n";
		}
		if ($validationhash != $_POST['hash']) {
			return "Error (SU202): Hash validation failed\n";
		}

		return $data;
	}

	/**
	 * checks wich hash algorithms are supported by the server
	 * and returns the best one
	 *
	 * @return sha512|sha256|sha1|md5|empty string
	 */
	function getSupportedHashAlgorithm() {

		$algorithms = $this->getSupportedHashAlgorithms();
		
		if(is_array($algorithms))
			return $algorithms[0];
		else
			return ''; //no hash function found
	}

	/**
	 * checks wich hash algorithms are supported by the server
	 *
	 * @return array with all supported algorithms, preferred as first one (index 0)
	 */
	function getSupportedHashAlgorithms() {
		
		$algorithms = array();

		if(function_exists('hash') && in_array('sha512', hash_algos()))
			$algorithms[] = 'sha512';
		
		if(function_exists('hash') && in_array('sha256', hash_algos()))
			$algorithms[] =  'sha256';
		
		if(function_exists('sha1'))	//deprecated
			$algorithms[] =  'sha1';
		
		if(function_exists('md5')) //deprecated
			$algorithms[] =  'md5';
			
		return $algorithms;
	}


	/**
	 * generates a html-page that sets post-parameters and redirects to the SU-autoinstaller
	 * sets password, password2 and hashfunction
	 *
	 * @param unknown_type $projectName
	 * @param unknown_type $projectHomepage
	 * @param unknown_type $projectEmail
	 * @param unknown_type $projectLanguage
	 * @param unknown_type $currency
	 * @param unknown_type $cancelLink
	 * @param unknown_type $successLink
	 * @param unknown_type $notificationLink
	 * @param unknown_type $backLink
	 * @return string
	 */
	function getAutoInstallPage($projectName, $projectHomepage, $projectEmail, $projectLanguage, $currency,
	$cancelLink, $successLink, $notificationLink, $backLink, $shopSystemId='208'){

		$this->password = $this->generateRandomValue(24);
		$this->password2 = $this->generateRandomValue(24);
		$this->hashfunction = $this->getSupportedHashAlgorithm();

		$html = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
	<title>Schnellregistrierung | sofortueberweisung.de</title>
</head>
<body onload="document.getElementById(\'form\').submit()">
	<form method="post" action="https://www.sofortueberweisung.de/payment/createNew/" id="form">
		<input type="hidden" name="project_name" value="'.$projectName.'">
		<input type="hidden" name="project_homepage" value="'.$projectHomepage.'">
		<input type="hidden" name="projectsnotification_email_email" value="'.$projectEmail.'">
		<input type="hidden" name="projectsnotification_email_activated" value="1">
		<input type="hidden" name="projectsnotification_email_language_id" value="'.$projectLanguage.'">
		<input type="hidden" name="projectssetting_interface_cancel_link" value="'.$cancelLink.'">
		<input type="hidden" name="projectssetting_interface_success_link_redirect" value="1">
		<input type="hidden" name="projectssetting_interface_success_link" value="'.$successLink.'">
		<input type="hidden" name="projectssetting_currency_id" value="'.$currency.'">
		<input type="hidden" name="projectssetting_locked_amount" value="1">
		<input type="hidden" name="projectssetting_locked_reason_1" value="1">
		<input type="hidden" name="projectssetting_locked_reason_2" value="1">
		<input type="hidden" name="projectssetting_interface_input_hash_check_enabled" value="1">
		<input type="hidden" name="projectssetting_project_password" value="'.$this->password.'">
		<input type="hidden" name="project_notification_password" value="'.$this->password2.'">
		<input type="hidden" name="project_shop_system_id" value="'.$shopSystemId.'">
		<input type="hidden" name="project_hash_algorithm" value="'.$this->hashfunction.'">
		<input type="hidden" name="user_shop_system_id" value="'.$shopSystemId.'">
		<input type="hidden" name="projectsnotification_http_activated" value="1">
		<input type="hidden" name="projectsnotification_http_url" value="'.$notificationLink.'">
		<input type="hidden" name="projectsnotification_http_method" value="1">
		<input type="hidden" name="backlink" value="'.$backLink.'">
		<input type="hidden" name="debug" value="0">
		<noscript><input type="submit"></noscript>
	</form>
</body>
</html>
';
		return $html;
	}

	/**
	 * @param string $data string to be hashed
	 * @return string the hash
	 */
	function generateHash($data){

		if($this->hashfunction == 'sha1')
			return sha1($data);
		if($this->hashfunction == 'md5')
			return md5($data);

		//mcrypt installed?
		if(function_exists('hash') && in_array($this->hashfunction, hash_algos()))
			return hash($this->hashfunction, $data);
			
		return false;
	}

	/**
	 * @param int [optional] $length length of return value, default 24
	 * @param string [optional] $type alpha|num|alphanum|mixed, default mixed
	 * @return string
	 */
	function generateRandomValue($length = 24, $type = 'mixed') {
		//if php >= 5.3 and openssl installed we will use its more secure random generator, output is base64: a-zA-Z0-9/+
		if($type == 'mixed' && function_exists('openssl_random_pseudo_bytes')) {
			$password = base64_encode(openssl_random_pseudo_bytes($length, $strong));
			if($strong == TRUE)
				return substr($password, 0, $length); //base64 is about 33% longer, so we need to truncate the result
		}		
		
		//fallback to mt_rand for php < 5.3
		
		//character classes
		$numericalCharacters = '0123456789'; //10 chars 0-9
		$alphaCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; //52 chars A-Za-z
		$specialCharacters = '!$%/()?+*~^,.-;:_|}][{=#@'; //25 chars, special ascii chars without < >'" 
		$characters = '';

		if($type == 'alpha')
			$characters = $alphaCharacters;
		elseif($type == 'num')
			$characters = $numericalCharacters;
		elseif($type == 'alphanum')
			$characters = $numericalCharacters.$alphaCharacters;
		elseif($type == 'mixed')
			$characters = $numericalCharacters.$alphaCharacters.$specialCharacters;
		else
			return false;

		$charactersLength = strlen($characters)-1;
		$randomValue = '';

		//select some random characters from all characters
		for ($i = 0; $i < $length; $i++) {
			$randomValue .= $characters[mt_rand(0, $charactersLength)];
		}

		return $randomValue;
	}
}

?>