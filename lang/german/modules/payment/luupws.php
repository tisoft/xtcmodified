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

  define('MODULE_PAYMENT_LUUPWS_TEXT_COUNTRIES', 'DEU|Deutschland');
  
  define('MODULE_PAYMENT_LUUPWS_TEXT_TITLE', '<font color="#ff0000"><strong>LUUPAY</strong></font>');
  define('MODULE_PAYMENT_LUUPWS_TEXT_TITLE_SHOP', '<font color="#2A0075"><strong>LUUPAY</strong></font> : Dein Geld wird mobil. Einfach, schnell und sicher!');
  define('MODULE_PAYMENT_LUUPWS_TEXT_DESCRIPTION', ' LUUPAY Konto<br /><br /><strong>!Achtung!</strong> Als xt:Commerce User erhalten Sie Sonderkonditionen, Details siehe <a href="http://www.xt-commerce.com/index.php?option=com_content&task=view&id=28&Itemid=43" target="_new">[Link]</a>');
  define('MODULE_PAYMENT_LUUPWS_TEXT_LINK_REGISTER', 'LUUPAY hat eine E-Geld-Lizenz und ist unabh&auml;ngig ob Vertrags-, Prepaid- oder Dienst-Handy. Die Abwicklung der Zahlungen erfolgt &uuml;ber das kostenfreie LUUPAY-Konto. Keine laufenden Kosten, keine Kontof&uuml;hrungskosten, keine Verpflichtungen und kein Abonnement. Noch kein Kunde? Einfach <a href="https://www.luupay.de/Signup.aspx?c=de" target="_blank"><span style="font-weight: normal;"><u>hier anmelden</u></span></a>.');
  
  // labels, etc
  define('MODULE_PAYMENT_LUUPWS_TEXT_REGISTERED_IN', 'Registriert in:');
  define('MODULE_PAYMENT_LUUPWS_TEXT_USERID', 'Handy-Nr/Benutzername:' );
  define('MODULE_PAYMENT_LUUPWS_TEXT_PIN', 'LUUPAY-PIN:' );
  define('MODULE_PAYMENT_LUUPWS_TEXT_VERIFICATION_CODE', 'LUUPAY-Verifizierungscode:');
  
  define('MODULE_PAYMENT_LUUPWS_TEXT_CONTINUE', 'Weiter' );
  
  define('MODULE_PAYMENT_LUUPWS_TEXT_STEP1', 'Schritt 1 von 2:' );
  define('MODULE_PAYMENT_LUUPWS_TEXT_STEP2', 'Schritt 2 von 2:' );
  define('MODULE_PAYMENT_LUUPWS_TEXT_STEP1_DESCRIPTION', 'Deine Handynummer oder Deinen LUUPAY-Benutzernamen' );
  define('MODULE_PAYMENT_LUUPWS_TEXT_STEP2_DESCRIPTION', 'LUUPAY hat Dir soeben per SMS Deinen LUUPAY-Verifizierungscode f&uuml;r diesen Einkauf auf Dein Handy gesandt. Bitte einfach hier eingeben.' );
  
  // javascript validation
  define('MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_USER', ' * Du musst Deine Handynummer oder Deinen Benutzernamen eingeben\n');
  define('MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_PIN', ' * Du musst Deine LUUPAY-PIN eingeben (4 Ziffern)\n');
  define('MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_CODE', ' * Du musst den LUUPAY-Verifizierungscode eingeben (8 Ziffern)\n');

  // error texts
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_NO_EURO_CONVERSION_VALUE', 'Falsche Waehrung - keine Umrechnung in Euro moeglich');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_MESSAGE', 'Versuch fehlgeschlagen: ' ); 
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_UNKNOWN', 'Unbekannter Fehler');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_101', 'LUUPAY kann die Anfrage nicht bearbeiten. Fehlende Daten.');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_201', 'Der Haendler konnte nicht identifiziert werden.');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_202', 'Du hast einen falschen Benutzernamen oder LUUPAY-PIN eingegeben. Bitte versuche es erneut. Falls Du noch nicht bei LUUPAY registriert bist, gehe auf https://www.luupay.de/Signup.aspx?c=de .');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_203', 'Ungueltiger Verifizierungscode');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_206', 'Ein Fehler ist aufgetreten beim Haendler. Bitte den Haendler benachrichtigen.');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_301', 'Die Transaktion konnte nicht beendet werden. Vielleicht langt Dein Guthaben nicht aus. Gehe zu www.luupay.de und Ueberpruefe Deinen Kontostand');
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_401', 'LUUPAY interner Fehler');
  
  define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR', 'Fehler im Bezahlvorgang!');
  //define('MODULE_PAYMENT_LUUPWS_TEXT_ERROR_MESSAGE', 'Beim Bearbeiten deiner Transaktion ist ein Fehler aufgetreten, bitte versuche es erneut.');
  
  define('MODULE_PAYMENT_LUUPWS_ALLOWED_TITLE' , 'Erlaubte Zonen');
  define('MODULE_PAYMENT_LUUPWS_ALLOWED_DESC' , 'Geben Sie <strong>einzeln</strong> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
  
  
  // infobox text
  define('MODULE_BOXES_LUUP_TITLE', 'Bezahlt mit');
  
 
  
  define('MODULE_PAYMENT_LUUPWS_SORT_ORDER_TITLE','Anzeigereihenfolge');
  define('MODULE_PAYMENT_LUUPWS_SORT_ORDER_DESC','Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt');
  
    define('MODULE_PAYMENT_LUUPWS_STATUS_TITLE','LUUPAY Modul aktivieren');
  define('MODULE_PAYMENT_LUUPWS_STATUS_DESC','M&ouml;chten Sie Zahlungen per LUUPAY akzeptieren?');
  
    define('MODULE_PAYMENT_LUUPWS_MERCHANT_ID_TITLE','H&auml;ndler ID');
  define('MODULE_PAYMENT_LUUPWS_MERCHANT_ID_DESC','Ihre LUUPAY Shop ID');
  
    define('MODULE_PAYMENT_LUUPWS_MERCHANT_KEY_TITLE','H&auml;ndler Passwort');
  define('MODULE_PAYMENT_LUUPWS_MERCHANT_KEY_DESC','Ihr LUUPAY H&auml;ndler Passwort');
  
    define('MODULE_PAYMENT_LUUPWS_TESTMODE_TITLE','Testmodus');
  define('MODULE_PAYMENT_LUUPWS_TESTMODE_DESC','Testmodus mit Testw&auml;hrung');
  
    define('MODULE_PAYMENT_LUUPWS_ORDER_STATUS_ID_TITLE','Bestellstatus festlegen');
  define('MODULE_PAYMENT_LUUPWS_ORDER_STATUS_ID_DESC','Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');
  
    define('MODULE_PAYMENT_LUUPWS_PAYMENT_COLLECTION_TITLE','Payment type');
  define('MODULE_PAYMENT_LUUPWS_PAYMENT_COLLECTION_DESC','Select payment collection type');
  
    define('MODULE_PAYMENT_LUUPWS_USE_DB_TITLE','Uses admin extension');
  define('MODULE_PAYMENT_LUUPWS_USE_DB_DESC','Is the LUUPAY admin extension installed?');
  
    define('MODULE_PAYMENT_LUUPWS_ZONE_TITLE','Zahlungszone');
  define('MODULE_PAYMENT_LUUPWS_ZONE_DESC','Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
  
  // Admin extension
       define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_STATUS', 'Zahlungsstatus:');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_TRANSACTION_ID', 'Transaktions ID:');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_ACTION', 'Zahlung aktualisieren:');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_FAILED', '<span class="messageStackError">Webservice Error</span>');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_CANCELLED', '<span class="messageStackSuccess">Zahlung wurde storniert</span>');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_REFUNDED', '<span class="messageStackSuccess">Zahlung wurde erstattet</span>');  
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_COMPLETED', '<span class="messageStackSuccess">Zahlung wurde durchgef&uuml;hrt</span>');
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_BUTTON_REFUND', '<input type="submit" name="luup_request" value="Refund">');  
      define('MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_BUTTON_PENDING', '<input type="submit" name="luup_request" value="Collect">&nbsp;<input type="submit" name="luup_request" value="Cancel">'); 

?>