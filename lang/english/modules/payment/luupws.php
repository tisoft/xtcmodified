<?php
/* -----------------------------------------------------------------------------------------
   $Id: luupws.php 998 2006-06-09 14:18:20Z mz $   

   xt:Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2006 xt:Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2002-2003 osCommerce(LUUPws.php, v3.0 2005/11/15); www.oscommerce.com 

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  define('MODULE_PAYMENT_LUUPWS_TEXT_COUNTRIES', 'DEU|Germany');
  
  define('MODULE_PAYMENT_LUUPWS_TEXT_TITLE', '<font color="#ff0000"><b>LUUPAY</b></font>');
  define('MODULE_PAYMENT_LUUPWS_TEXT_TITLE_SHOP', '<font color="#2A0075"><b>LUUPAY</b></font> : Your Money is getting Mobil. Easy, fast and safe!');
  define('MODULE_PAYMENT_LUUPWS_TEXT_DESCRIPTION', ' LUUPAY Konto<br><br><b>!Attention!</b>Specials for xt:Commerce Users: <a href="http://www.xt-commerce.com/index.php?option=com_content&task=view&id=28&Itemid=43" target="_new">[Link]</a>');
  define('MODULE_PAYMENT_LUUPWS_TEXT_LINK_REGISTER', 'No LUUPAY Account ? Go to <a href="https://www.luupay.de/Signup.aspx?c=de" target="_blank"><span style="font-weight: normal;"><u>LUUPAY</u></span></a> and get your Account.');
  
  // labels, etc

  define('MODULE_PAYMENT_LUUPWS_TEXT_REGISTERED_IN', 'Registered in:');
  define('MODULE_PAYMENT_LUUPWS_TEXT_USERID', 'Mobile no/username:' );
  define('MODULE_PAYMENT_LUUPWS_TEXT_PIN', 'LUUPAY-PIN:' );
  define('MODULE_PAYMENT_LUUPWS_TEXT_VERIFICATION_CODE', 'LUUPAY-Verification code:');

  

  define('MODULE_PAYMENT_LUUPWS_TEXT_CONTINUE', 'Continue' );

  

  define('MODULE_PAYMENT_LUUPWS_TEXT_STEP1', 'Step 1 of 2:' );
  define('MODULE_PAYMENT_LUUPWS_TEXT_STEP2', 'Step 2 of 2:' );
  define('MODULE_PAYMENT_LUUPWS_TEXT_STEP1_DESCRIPTION', 'Enter your mobilenumber or LUUPAY-Username' );
  define('MODULE_PAYMENT_LUUPWS_TEXT_STEP2_DESCRIPTION', 'LUUPAY has sent a verification code to your phone, please enter the code below' );

  

  // javascript validation

  define('MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_USER', '* You must enter your phone number or username\n');
  define('MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_PIN', '* You must enter your LUUPAY-PIN (4 digits)\n');
  define('MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_CODE', '* You must enter your LUUPAY-verification code (8 digits)\n');



  // error texts

  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_NO_EURO_CONVERSION_VALUE', 'Invalid currency - no conversion value for Euro');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_MESSAGE', 'Request failed: ' ); 
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_UNKNOWN', 'An unknown error occurred');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_101', 'LUUPAY rejected the request because of missing or bad data.');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_201', 'LUUPAY could not authenticate the merchant. Please notify the shop owner.');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_202', 'You have entered an invalid user or LUUPAY-PIN number. Please check your spelling and try again. If you do not have a LUUP account you can register at https://www.luupay.de/Signup.aspx?c=de .');

  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_203', 'The verification code was not valid');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_206', 'The merchant\'s configurations may be incorrect. Notify the shop owner.');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_301', 'The transaction could not be completed. You may have insufficent funds.Have you registered or not activated your credit card? Log in to www.luupay.de for more info');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_401', 'Internal error in LUUPAY');

  

  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR', 'Payment Error!');

  //define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_MESSAGE', 'There has been an error processing your payment. Please try again.');


  // infobox text

  define('MODULE_BOXES_LUUP_TITLE', 'Payments by');
  
 
  
  define('MODULE_PAYMENT_LUUPWS_SORT_ORDER_TITLE','Sort order of display');
  define('MODULE_PAYMENT_LUUPWS_SORT_ORDER_DESC','Sort order of display. Lowest is displayed first.');
  
    define('MODULE_PAYMENT_LUUPWS_STATUS_TITLE','Enable LUUPAY Module');
  define('MODULE_PAYMENT_LUUPWS_STATUS_DESC','Do you want to accept LUUPAY payments?');
  
    define('MODULE_PAYMENT_LUUPWS_MERCHANT_ID_TITLE','Merchant Id');
  define('MODULE_PAYMENT_LUUPWS_MERCHANT_ID_DESC','Your LUUPAY shop ID');
  
    define('MODULE_PAYMENT_LUUPWS_MERCHANT_KEY_TITLE','Merchant password');
  define('MODULE_PAYMENT_LUUPWS_MERCHANT_KEY_DESC','Your merchant system password');
  
    define('MODULE_PAYMENT_LUUPWS_TESTMODE_TITLE','Run in test mode');
  define('MODULE_PAYMENT_LUUPWS_TESTMODE_DESC','Set to True to run in test mode');
  
    define('MODULE_PAYMENT_LUUPWS_ORDER_STATUS_ID_TITLE','Set Order Status');
  define('MODULE_PAYMENT_LUUPWS_ORDER_STATUS_ID_DESC','Set the status of orders made with this payment module to this value');
  
    define('MODULE_PAYMENT_LUUPWS_PAYMENT_COLLECTION_TITLE','Payment type');
  define('MODULE_PAYMENT_LUUPWS_PAYMENT_COLLECTION_DESC','Select payment collection type');
  
    define('MODULE_PAYMENT_LUUPWS_USE_DB_TITLE','Uses admin extension');
  define('MODULE_PAYMENT_LUUPWS_USE_DB_DESC','Is the LUUPAY admin extension installed?');
  
    define('MODULE_PAYMENT_LUUPWS_ZONE_TITLE','Payment Zone');
  define('MODULE_PAYMENT_LUUPWS_ZONE_DESC','If a zone is selected, only enable this payment method for that zone.');
  
    define('MODULE_PAYMENT_LUUPWS_ALLOWED_TITLE' , 'Allowed zones');
define('MODULE_PAYMENT_LUUPWS_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
  
  
    // Admin extension
        define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_STATUS', 'Payment status:');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_TRANSACTION_ID', 'Transaction id:');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_ACTION', 'Update payment:');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_FAILED', '<span class="messageStackError">The webservice request failed</span>');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_CANCELLED', '<span class="messageStackSuccess">Payment is cancelled</span>');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_REFUNDED', '<span class="messageStackSuccess">Payment is refunded</span>');  
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_COMPLETED', '<span class="messageStackSuccess">Payment is completed</span>');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_BUTTON_REFUND', '<input type="submit" name="luup_request" value="Refund">');  
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_BUTTON_PENDING', '<input type="submit" name="luup_request" value="Collect">&nbsp;<input type="submit" name="luup_request" value="Cancel">'); 
      
?>