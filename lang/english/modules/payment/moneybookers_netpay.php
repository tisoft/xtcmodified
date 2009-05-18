<?php

/* -----------------------------------------------------------------------------------------
   $Id: amoneybookers.php 85 2007-01-14 15:19:44Z mzanier $

   xt:Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2007 xt:Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_TEXT_TITLE', 'Netpay');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_TEXT_DESCRIPTION', 'Netpay via Moneybookers');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_NOCURRENCY_ERROR', 'There\'s no Moneybookers accepted currency installed!');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_ERRORTEXT1', 'payment_error=');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_TEXT_INFO','');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_ERRORTEXT2', '&error=There was an error during your payment at Moneybookers!');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_ORDER_TEXT', 'Date of the order: ');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_TEXT_ERROR', 'Payment error!');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_CONFIRMATION_TEXT', 'Thank you for your order!');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_TRANSACTION_FAILED_TEXT', 'Your payment transaction at Moneybookers has failed. Please try again, or select an other payment option!');


define('MB_TEXT_MBDATE', 'Last Change:');
define('MB_TEXT_MBTID', 'TR ID:');
define('MB_TEXT_MBERRTXT', 'Status:');

define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_PROCESSED_STATUS_ID_TITLE', 'Order status - Processed');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_PROCESSED_STATUS_ID_DESC', '');

define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_PENDING_STATUS_ID_TITLE', 'Order status - Sheduled');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_PENDING_STATUS_ID_DESC', '');

define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_CANCELED_STATUS_ID_TITLE', 'Order status - Canceled');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_CANCELED_STATUS_ID_DESC', '');

define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_TMP_STATUS_ID_TITLE', 'Order status - Temp');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_TMP_STATUS_ID_DESC', '');

define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_ICONS_TITLE', 'Icons');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_ICONS_DESC', '');

define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_STATUS_TITLE', 'Enable Moneybookers');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_STATUS_DESC', 'Do you want to accept payments through Moneybookers?<br /><br /><img src="images/icon_arrow_right.gif"> <b><a href="http://www.xt-commerce.com/index.php?option=com_content&task=view&id=76&lang=en" target="_blank">Help / Explanation</a></b>');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_EMAILID_TITLE', 'Email Address');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_EMAILID_DESC', 'Email address you have registered with Moneybookers. <font color="#ff0000">* Required</font>');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_PWD_TITLE', 'Moneybookers Secret Word');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_PWD_DESC', 'The secret word can be found in your Moneybookers profile (this is not your password!)');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_MERCHANTID_TITLE', 'Merchant ID');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_MERCHANTID_DESC', 'Merchant ID of your Moneybookers account <font color="#ff0000">* Required</font>');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_SORT_ORDER_TITLE', 'Sort order of display.');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_CURRENCY_TITLE', 'Transaction Currency');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_CURRENCY_DESC', 'If the user\'s currency that is not available at Moneybookers this currency will be used for the payment.');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_LANGUAGE_TITLE', 'Transaction Language');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_LANGUAGE_DESC', 'If the user\'s language is not available at Moneybookers this language will be used for the payment.');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_ZONE_TITLE', 'Payment Zone');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_ALLOWED_TITLE' , 'Allowed Zones');
define('MODULE_PAYMENT_MONEYBOOKERS_NETPAY_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
?>