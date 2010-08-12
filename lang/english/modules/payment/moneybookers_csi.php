<?php

/* -----------------------------------------------------------------------------------------
   $Id: moneybookers_csi.php 38 2009-01-22 14:46:06Z mzanier $

   xt:Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2009 xt:Commerce GmbH

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

define('MODULE_PAYMENT_MONEYBOOKERS_CSI_TEXT_TITLE', 'CartaSi');
$_var = 'CartaSi via Moneybookers';
if (_PAYMENT_MONEYBOOKERS_EMAILID=='') {
	$_var.='<br /><br /><b><font color="red">Please setup moneybookers.com configuration first! (Configuration -> xt:C Partner -> Moneybookers.com)!</font></b>';
}
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_TEXT_DESCRIPTION', $_var);
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_NOCURRENCY_ERROR', 'There\'s no Moneybookers accepted currency installed!');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_ERRORTEXT1', 'payment_error=');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_TEXT_INFO','');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_ERRORTEXT2', '&error=There was an error during your payment at Moneybookers!');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_ORDER_TEXT', 'Date of the order: ');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_TEXT_ERROR', 'Payment error!');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_CONFIRMATION_TEXT', 'Thank you for your order!');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_TRANSACTION_FAILED_TEXT', 'Your payment transaction at Moneybookers has failed. Please try again, or select an other payment option!');


define('MODULE_PAYMENT_MONEYBOOKERS_CSI_STATUS_TITLE', 'Enable Moneybookers');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_STATUS_DESC', 'Do you want to accept payments through Moneybookers?<br /><br /><img src="images/icon_arrow_right.gif"> <b><a href="http://www.xt-commerce.com/index.php?option=com_content&task=view&id=76&lang=en" target="_blank">Help / Explanation</a></b>');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_SORT_ORDER_TITLE', 'Sort order of display.');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_ZONE_TITLE', 'Payment Zone');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_ALLOWED_TITLE' , 'Allowed Zones');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_ALLOWED_DESC' , 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');

// BOF - Hendrik - 2010-08-11 - exlusion config for shipping modules
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_NEG_SHIPPING_TITLE', 'Exclusion in case of shipping');
define('MODULE_PAYMENT_MONEYBOOKERS_CSI_NEG_SHIPPING_DESC', 'deactivate this payment if one of these shippingtypes are selected (list separated by comma)');
// EOF - Hendrik - 2010-08-11 - exlusion config for shipping modules
?>