<?php
/* -----------------------------------------------------------------------------------------
   $Id: luup_webpayments.php 998 2006-06-09 14:18:20Z mz $   

   xt:Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2006 xt:Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2005 Contopronto AS

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

//error_reporting(E_ERROR | E_WARNING | E_PARSE);

class luup_webpayments {
	// private members
	var $proxy,
	    $client,
	    $wsdl;
	
	// properties
	var $merchantId,
	    $merchantKey,
	    $errorCode,
	    $transactionId;
		
	/*
	* class constructor
	*
	* - $soapPath:	Path to directory where nusoap files are located. 
	*		If null or empty executing nusoap is assumed to reside in executing folder.
	*
	* - $wsdl: 	Use to override wsdl address to LUUP WebPayments web service
	*/
	function luup_webpayments( $soapPath, $luupwsdl = '' ) {
		
		$this->debug=false;
		
	    $this->wsdl = 'https://secure.contopronto.com/webpayments/payments.asmx?wsdl';
	    
	    if($soapPath == null )
	    	$soapPath = '';
	    
	    // allow override of wsdl address
	    if($luupwsdl != null && $luupwsdl != '')
	    	$this->wsdl = $luupwsdl;

	    require_once($soapPath . 'nusoap.php');
	    
	    $this->client = new soapclientw($this->wsdl,'wsdl');
	    $this->proxy = $this->client->getProxy();
	}
	
	// Called before each web service method call.
	function init()
	{
	    if( !isset($this->merchantId) && !isset($this->merchantKey) )
	    	die("LUUP merchant credentials not set!");
	    else {
	        $MerchantID='<MerchantID>'.$this->merchantId.'</MerchantID>';
    		$MerchantKey='<Key>'.$this->merchantKey.'</Key>';
	        $this->header="<AuthenticationHeader xmlns=\"http://luup.com/webpayments\">".$MerchantID.$MerchantKey."</AuthenticationHeader>";
	        $this->proxy->setHeaders($this->header);
	        $this->transactionId = '';
	        $this->errorCode = '';
	        return true;
	    }
	}
	
	//--------------------------------------------------------------------
	//	METHODS
	//--------------------------------------------------------------------
	
	/* VERIFYUSER - Query if user is registered with LUUP
	*
	* - $countryCode	3 letter(iso) countrycode
	* - $user		User id (mobile phone or username)
	*
	* Returns true if request is successful. If false, inspect $errorCode property of class.
	*/
	function verifyUser( $countryCode, $user )
	{	    
	    $this->init();

	    $param=array(
		'CountryCode'=>$countryCode,
		'User'=>$user
	    );


	    $result = $this->proxy->VerifyUser($param);

	    if( $this->proxy->getError() ){
		echo "error: ".$this->proxy->getError();
		return false;
	    }
	    else {
	    	$this->errorCode = $result['ErrorCode'];
		
		if($this->errorCode == 0)
		    return true;
		else
		    return false;
	    }
	    
	}
	
	
	
	/* AUTHENTICATEUSER - LUUP issues verification code to user.
	*
	* - $countryCode	3 letter(iso) countrycode
	* - $user		User id (mobile phone or username)
	* - $pin		User's PIN-code
	*
	* Returns true if request is successful. If false, inspect $errorCode property of class.
	*/
	function authenticateUser( $countryCode, $user, $pin )
	{
	    $this->init();
	    
	    $param=array(
	    	'CountryCode'=>$countryCode,
	    	'User'=>$user,
	    	'PinCode'=>$pin
	    );
	    
	
	    $result = $this->proxy->AuthenticateUser($param);
	
	    if( $this->proxy->getError() ){
	    	echo "error: ".$this->proxy->getError();
	    	return false;
	    }
	    else {
		$this->errorCode = $result['ErrorCode'];

		if($this->errorCode == 0)
		    return true;
		else
		    return false;
	    }
	}
	
	
	// 
	/* AUTHENTICATEUSERAGE - as authenticateUser + confirm that user is above a given age
	*
	* - $countryCode	3 letter(iso) countrycode
	* - $user		User id (mobile phone or username)
	* - $pin		User's PIN-code
	* - $age		Minimum age limit
	*
	* Returns true if request is successful. If false, inspect $errorCode property of class.
	*/
	function authenticateUserAge( $countryCode, $user, $pin, $age )
	{
	    $this->init();

	    $param=array(
		'CountryCode'=>$countryCode,
		'User'=>$user,
		'PinCode'=>$pin,
		'AgeLimit'=>$age
	    );


	    $result = $this->proxy->AuthenticateUserWithAgeLimit($param);

	    if( $this->proxy->getError() ){
		echo "error: ".$this->proxy->getError();
		return false;
	    }
	    else {
		$this->errorCode = $result['ErrorCode'];

		if($this->errorCode == 0)
		    return true;
		else
		    return false;
	    }
	}
	
	
	/* COLLECTPAYMENT - Collects a payment immediately
	*
	* - $countryCode	3 letter(iso) countrycode
	* - $user		User id (mobile phone or username)
	* - $currency		3 letter(iso) currency code
	* - $amount		Payment amount
	* - $paymentRef		Reference visible to payer and collector
	* - $merchantRef	Reference visible to collevtor only
	* - $code		Verification code (issued to user by authenticateUser)
	*
	* Returns true if request is successful. If false, inspect $errorCode property of class.
	* TransactionID is stored in property $transactionId.
	*/
	function collectPayment( $countryCode, $user, $currency, $amount, $paymentRef, $merchantRef, $code ) {

	    $this->init();
	    
	    $param=array(
	    		'CountryCode'=>$countryCode,
	    		'User'=>$user,
	    		'CurrencyCode'=>$currency,
	    		'Amount'=>$amount,
	    		'PaymentReference'=>$paymentRef,
	    		'MerchantReference'=>$merchantRef,
	    		'VerificationCode'=>$code
	    );
	    
	    $result = $this->proxy->CollectPayment($param);
	    
	    if( $this->proxy->getError() ) {
	    	echo "error: ".$this->proxy->getError();
	    	return false;
	    }
	    else {
		$this->errorCode = $result['ErrorCode'];
		
		// debugging:
		if ($this->debug) {
		$message = 'Request:<br> <pre>'. htmlspecialchars($this->proxy->request, ENT_QUOTES).'</pre>';
		$message .= '<br>Response:<br> <pre>' . htmlspecialchars($this->proxy->response, ENT_QUOTES).'</pre>';
		
		xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', 'LUUPAY DEBUG MESSAGE', $message, $message);
		
		
		}
		
		
		

		if($this->errorCode == 0){
		    $this->transactionId = $result['TransactionID'];
		    return true;
		}
		else
		    return false;
	    }
	}
	
	
	/* RESERVEPAYMENT - Reserves a payment until given expiry date (or if null: max time according to LUUP)
	*
	* - $countryCode	3 letter(iso) countrycode
	* - $user		User id (mobile phone or username)
	* - $currency		3 letter(iso) currency code
	* - $amount		Payment amount
	* - $paymentRef		Reference visible to payer and collector
	* - $merchantRef	Reference visible to collevtor only
	* - $code		Verification code (issued to user by authenticateUser)
	* - $expires		Expiry date. Payment status is pending until this date and time (dd.mm.yyyy hh:mm:ss)
	*
	* returns true if successful. If false, inspect $errorCode property of class.
	* TransactionID is stored in property $transactionId.
	*/
	function reservePayment( $countryCode, $user, $currency, $amount, $paymentRef, $merchantRef, $code, $expires ) {

	    $this->init();

	    $param=array(
		'CountryCode'=> $countryCode,
		'User'=> $user,
		'CurrencyCode'=> $currency,
		'Amount'=> $amount,
		'PaymentReference'=> $paymentRef,
		'MerchantReference'=> $merchantRef,
		'VerificationCode'=> $code,
		'ExpiryTime' => $expires
	    );
	    
	    $result = $this->proxy->ReservePayment($param);

	    if( $this->proxy->getError() ) {
		echo "error: ".$this->proxy->getError();
		return false;
	    }
	    if( $result['ErrorCode'] == 0 ){
	    	$this->transactionId = $result['TransactionID'];
	    	return true;
	    	}
	    else {
		$this->errorCode = $result['ErrorCode'];
		return false;
	    }
	}
	
	
	/* COMPLETEPAYMENT - Completes a reserved payment
	*
	* - $transID	Payment transaction id
	*
	* Returns true if request is successful. If false, inspect $errorCode property of class.
	*/
	function completePayment( $transId ) {

	    $this->init();

	    $param=array(
		'TransactionID'=>$transId
	    );

	    $result = $this->proxy->CompletePayment($param);

	    if( $this->proxy->getError() ) {
		echo "error: ".$this->proxy->getError();
		return false;
	    }
	    else {
		$this->errorCode = $result['ErrorCode'];

		if($this->errorCode == 0)
		    return true;
		else
		    return false;
	    }
	}
	
	
	/* COMPLETEPAYMENTAMOUNT - Completes a reserved payment, with altered(reduced only) amount
	*
	* - $transID	Payment transaction id
	*
	* Returns true if request is successful. If false, inspect $errorCode property of class.
	*/
	function completePaymentAmount( $transId, $amount ) {

	    $this->init();

	    $param=array(
		'TransactionID'=>$transId,
		'Amount'=>$amount
	    );

	    $result = $this->proxy->CompletePaymentAmount($param);

	    if( $this->proxy->getError() ) {
		echo "error: ".$this->proxy->getError();
		return false;
	    }
	    else {
		$this->errorCode = $result['ErrorCode'];

		if($this->errorCode == 0)
		    return true;
		else
		    return false;
	    }
	}
	
	
	/* CANCELPAYMENT - Cancels a reserved payment
	*
	* - $transID	Payment transaction id
	*
	* Returns true if request is successful. If false, inspect $errorCode property of class.
	*/
	function cancelPayment( $transId ) {
	
	    $this->init();

	    $param=array(
		'TransactionID'=>$transId
	    );

	    $result = $this->proxy->CancelPayment($param);

	    if( $this->proxy->getError() ) {
		echo "error: ".$this->proxy->getError();
		return false;
	    }
	    else {
		$this->errorCode = $result['ErrorCode'];

		if($this->errorCode == 0)
		    return true;
		else
		    return false;
	    }
	}
	
	
	/* REFUNDPAYMENT - Refunds a completed/collected payment
	*
	* - $transID	Payment transaction id
	*
	* Returns true if request is successful. If false, inspect $errorCode property of class.
	*/
	function refundPayment( $transId ) {

	    $this->init();

	    $param=array(
		'TransactionID'=>$transId
	    );

	    $result = $this->proxy->RefundPayment($param);

	    if( $this->proxy->getError() ) {
		echo "error: ".$this->proxy->getError();
		return false;
	    }
	    else {
		$this->errorCode = $result['ErrorCode'];

		if($this->errorCode == 0)
		    return true;
		else
		    return false;
	    }
	}
	
	/* PAYMENTSTATUS - Gets the current status of a payment
	*
	* - $transID	Payment transaction id
	* - &$status	Status code passed by reference. Pass initial empty variable. 
	*
	* Returns true if request is successful. If false, inspect $errorCode property of class.
	*/
	function paymentStatus( $transId, &$status ) {

	    $this->init();

	    $param=array(
		  'TransactionID'=>$transId
	    );

	    $result = $this->proxy->PaymentStatus($param);

	    if( $this->proxy->getError() ) {
		  echo "error: ".$this->proxy->getError();
		  return false;
	    }
	    else {
		  $this->errorCode = $result['ErrorCode'];

		if($this->errorCode == 0){
		    $status = $result['PaymentState'];
		    return true;
		}
		else
            return false;
	    }
	}
	
	/* ISEREFERREDUSER - is a user referred by other user? (to use for marketing purposes)
	*
	* - $countryCode	3 letter(iso) countrycode
	* - $user		User id (mobile phone or username)
	* - &$isReferred	Passed by reference - true or false
	*
	* Returns true if request is successful. If false, inspect $errorCode property of class.
	*  - evaluate $isReferred param if request is successful.
	*/
	function isReferredUser( $countryCode, $user, &$isReferred ) {

	    $this->init();

	    $param=array(
		'CountryCode'=>$countryCode,
		'User'=>$user
	    );

	    $result = $this->proxy->IsReferredUser($param);

	    if( $this->proxy->getError() ) {
		echo "error: ".$this->proxy->getError();
		return false;
	    }
	    else {
		$this->errorCode = $result['ErrorCode'];

		if($this->errorCode == 0){
		    $isReferred = $result['IsReferred'];
		    return true;
		}
		else
		    return false;
	    }
	}
}
	
?>