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
  Copyright (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Pl�nkers
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
  define('MODULE_PAYMENT_ICLEAR_TEXT_ERROR_MESSAGE', 'Bei der &Uuml;berp&uuml;fung Ihres iclear Rechnungskaufes ist ein Fehler aufgetreten! Bitte versuchen Sie es nochmal oder w&auml;hlen Sie eine andere Zahlungsweise.');
  define('MODULE_PAYMENT_ICLEAR_TEXT_INFO','');
  define('MODULE_PAYMENT_ICLEAR_ALLOWED_TITLE' , 'Erlaubte Zonen');
  define('MODULE_PAYMENT_ICLEAR_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');

  define('MODULE_PAYMENT_ICLEAR_STATUS_TITLE', 'Allow iclear');
  define('MODULE_PAYMENT_ICLEAR_STATUS_DESC', 'Wollen Sie Zahlungen per iclear Rechnungskauf anbieten?');

  define('MODULE_PAYMENT_ICLEAR_ID_TITLE', 'Merchant ID');
  define('MODULE_PAYMENT_ICLEAR_ID_DESC', 'Your merchant ID at EuroCoin iclear.');

  define('MODULE_PAYMENT_ICLEAR_SORT_ORDER_TITLE', 'Reihenfolge der Anzeige.');
  define('MODULE_PAYMENT_ICLEAR_SORT_ORDER_DESC', 'Niedrigste wird zuerst angezeigt.');

  define('MODULE_PAYMENT_ICLEAR_ZONE_TITLE', 'Zone f&uuml;r diese Zahlungsweise');
  define('MODULE_PAYMENT_ICLEAR_ZONE_DESC', 'Wenn Sie eine Zone ausw&auml;hlen, wird diese Zahlungsweise nur in dieser Zone angeboten.');

  define('MODULE_PAYMENT_ICLEAR_ORDER_STATUS_ID_TITLE', 'Order Status');
  define('MODULE_PAYMENT_ICLEAR_ORDER_STATUS_ID_DESC', 'Festlegung des Status f&uuml;r Bestellungen, welche mit dieser Zahlungsweise durchgef&uuml;hrt werden.');

  define('MODULE_PAYMENT_ICLEAR_SHIPPING_TAX_TITLE', 'MWST Satz Versandkosten');
  define('MODULE_PAYMENT_ICLEAR_SHIPPING_TAX_DESC', 'Festlegen mit welchem MWST Satz Versandkosten erstellt werden, falls Versandkosten ohne MWST Satz angegeben sind.');
  
  // BOF - Hendrik - 2010-08-09 - exlusion config for shipping modules
  define('MODULE_PAYMENT_ICLEAR_NEG_SHIPPING_TITLE', 'Ausschlu&szlig; bei Versandmodulen');
  define('MODULE_PAYMENT_ICLEAR_NEG_SHIPPING_DESC', 'Dieses Zahlungsmodul deaktivieren wenn Versandmodul gew&auml;hlt (Komma separierte Liste)');
  // EOF - Hendrik - 2010-08-09 - exlusion config for shipping modules
?>