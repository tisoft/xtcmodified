<?php

/* -----------------------------------------------------------------------------------------
   $Id: moneybookers_ideal.php 38 2009-01-22 14:46:06Z mzanier $

   xt:Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2009 xt:Commerce GmbH

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TEXT_TITLE', 'iDeal');
$_var = 'iDeal via Moneybookers';
if (_PAYMENT_MONEYBOOKERS_EMAILID=='') {
	$_var.='<br /><br /><b><font color="red">Please setup moneybookers.com configuration first! (Configuration -> xt:C Partner -> Moneybookers.com)!</font></b>';
}
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TEXT_DESCRIPTION', $_var);
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_NOCURRENCY_ERROR', 'There\'s no Moneybookers accepted currency installed!');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ERRORTEXT1', 'payment_error=');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TEXT_INFO','');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ERRORTEXT2', '&error=There was an error during your payment at Moneybookers!');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ORDER_TEXT', 'Date of the order: ');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TEXT_ERROR', 'Payment error!');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_CONFIRMATION_TEXT', 'Thank you for your order!');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TRANSACTION_FAILED_TEXT', 'Your payment transaction at Moneybookers has failed. Please try again, or select an other payment option!');



define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_STATUS_TITLE', 'Enable Moneybookers');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_STATUS_DESC', 'Do you want to accept payments through Moneybookers?<br /><br /><img src="images/icon_arrow_right.gif"> <b><a href="http://www.xt-commerce.com/index.php?option=com_content&task=view&id=76&lang=en" target="_blank">Help / Explanation</a></b>');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_SORT_ORDER_TITLE', 'Sort order of display.');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ZONE_TITLE', 'Payment Zone');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ALLOWED_TITLE' , 'Allowed Zones');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
?>