<?php

/* -----------------------------------------------------------------------------------------
   $Id: amoneybookers.php 85 2007-01-14 15:19:44Z mzanier $

   xt:Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2007 xt:Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TEXT_TITLE', 'iDEAL');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TEXT_DESCRIPTION', 'iDEAL &uuml;ber Moneybookers<br /><img src="images/icon_arrow_right.gif"> <b><a href="http://www.xt-commerce.com/index.php?option=com_content&task=view&id=76&lang=de" target="_blank">Hilfe zu Einstellungen</a></b>');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_NOCURRENCY_ERROR', 'Es ist keine von Moneybookers akzeptierte W&auml;hrung installiert!');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ERRORTEXT1', 'payment_error=');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TEXT_INFO', '');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ERRORTEXT2', '&error=Fehler w&auml;hrend Ihrer Bezahlung bei Moneybookers!');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ORDER_TEXT', 'Bestelldatum: ');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TEXT_ERROR', 'Fehler bei Zahlung!');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_CONFIRMATION_TEXT', 'Danke f&uuml;r Ihre Bestellung!');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TRANSACTION_FAILED_TEXT', 'Ihre Zahlungstransaktion bei moneybookers.com ist fehlgeschlagen. Bitte versuchen Sie es nochmal, oder w&auml;hlen Sie eine andere Zahlungsm&ouml;glichkeit!');


define('MB_TEXT_MBDATE', 'Letzte Aktualisierung:');
define('MB_TEXT_MBTID', 'TR ID:');
define('MB_TEXT_MBERRTXT', 'Status:');

define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_PROCESSED_STATUS_ID_TITLE', 'Bestellstatus - Processed');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_PROCESSED_STATUS_ID_DESC', '');

define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_PENDING_STATUS_ID_TITLE', 'Bestellstatus - Sheduled');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_PENDING_STATUS_ID_DESC', '');

define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_CANCELED_STATUS_ID_TITLE', 'Bestellstatus - Canceled');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_CANCELED_STATUS_ID_DESC', '');

define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TMP_STATUS_ID_TITLE', 'Bestellstatus - Temp');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_TMP_STATUS_ID_DESC', '');

define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ICONS_TITLE', 'Icons');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ICONS_DESC', '');

define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_STATUS_TITLE', 'Moneybookers aktivieren');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_STATUS_DESC', 'M&ouml;chten Sie Zahlungen per Moneybookers akzeptieren?');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_EMAILID_TITLE', 'Moneybookers eMail Adresse');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_EMAILID_DESC', 'eMail Adresse, die bei Moneybookers registriert ist. <br /><font color="#ff0000">* Erforderlich</font>');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_PWD_TITLE', 'Moneybookers Geheimwort');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_PWD_DESC', 'Geben Sie Ihr Moneybookers Geheimwort ein (dies ist nicht ihr Passwort!)');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_MERCHANTID_TITLE', 'H&auml;ndler ID ');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_MERCHANTID_DESC', 'Ihre pers&ouml;nliche H&auml;ndler ID bei Moneybookers <br /><font color="#ff0000">* Erforderlich</font>');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_CURRENCY_TITLE', 'Transaktionsw&auml;hrung');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_CURRENCY_DESC', 'Die W&auml;hrung, in der der Zahlungsvorgang abgewickelt wird. Wenn Ihre gew&auml;hlte W&auml;hrung nicht bei Moneybookers verf&uuml;gbar ist, wird diese W&auml;hrung ausgew&auml;hlt.');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_LANGUAGE_TITLE', 'Transaktionssprache');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_LANGUAGE_DESC', 'Die Sprache, in der der Zahlungsvorgang abgewickelt wird. Wenn Ihre gew&auml;hlte Shopsprache nicht bei Moneybookers verf&uuml;gbar ist, wird diese Sprache ausgew&auml;hlt.');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ZONE_TITLE', 'Zahlungszone');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_MONEYBOOKERS_IDEAL_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
?>