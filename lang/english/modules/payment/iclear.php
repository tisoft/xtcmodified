<?php
/* -----------------------------------------------------------------------------------------
   $Id: iclear.php,v 1.1.1.1 2006/10/25 18:37:38 dis Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(iclear.php,v 1.02); www.oscommerce.com

   Released under the GNU General Public License

   Third Party contribution:

************************************************************************
  Copyright (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers
  http://www.themedia.at & http://www.oscommerce.at

  All rights reserved.

  This program is free software licensed under the GNU General Public License (GPL).

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
  USA

*************************************************************************
   ---------------------------------------------------------------------------------------*/

  define('MODULE_PAYMENT_ICLEAR_TEXT_TITLE', 'iclear Payment System');
  define('MODULE_PAYMENT_ICLEAR_TEXT_DESCRIPTION', 'iclear');
  define('MODULE_PAYMENT_ICLEAR_TEXT_ERROR_MESSAGE', 'There was an error during your payment at iclear! Please try again/ select another payment option.');
  define('MODULE_PAYMENT_ICLEAR_TEXT_INFO','');
  define('MODULE_PAYMENT_ICLEAR_ALLOWED_TITLE', 'Allowed zones');
  define('MODULE_PAYMENT_ICLEAR_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');

  define('MODULE_PAYMENT_ICLEAR_STATUS_TITLE', 'Allow iclear');
  define('MODULE_PAYMENT_ICLEAR_STATUS_DESC', 'Do you want to accept iclear payments?');

  define('MODULE_PAYMENT_ICLEAR_ID_TITLE', 'Merchant ID');
  define('MODULE_PAYMENT_ICLEAR_ID_DESC', 'Your merchant ID at iclear.');

  define('MODULE_PAYMENT_ICLEAR_SORT_ORDER_TITLE', 'Sort order');
  define('MODULE_PAYMENT_ICLEAR_SORT_ORDER_DESC', 'Sort order of the view. Lowest numeral will be displayed first');

  define('MODULE_PAYMENT_ICLEAR_ZONE_TITLE', 'Payment zone');
  define('MODULE_PAYMENT_ICLEAR_ZONE_DESC', 'If a zone is choosen, the payment method will be valid for this zone only.');

  define('MODULE_PAYMENT_ICLEAR_ORDER_STATUS_ID_TITLE', 'Set Order Status');
  define('MODULE_PAYMENT_ICLEAR_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');

  define('MODULE_PAYMENT_ICLEAR_SHIPPING_TAX_TITLE', 'Tax Class');
  define('MODULE_PAYMENT_ICLEAR_SHIPPING_TAX_DESC', 'Use the following tax class on the shipping fee.');
  
	// Hendrik - 09.08.2010 - exlusion config for shipping modules
	define('MODULE_PAYMENT_ICLEAR_NEG_SHIPPING_TITLE', 'Exclusion in case of shipping');
	define('MODULE_PAYMENT_ICLEAR_NEG_SHIPPING_DESC', 'deactivate this payment if one of these shippingtypes are selected (list separated by comma)');
  
?>