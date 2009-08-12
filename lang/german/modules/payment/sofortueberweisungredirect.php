<?php
/**
 *
 *
 * @version Sofortueberweisung 1.9  05.06.2007
 * @author Henri Schmidhuber  info@in-solution.de
 * @copyright 2006 - 2007 Henri Schmidhuber
 * @link http://www.in-solution.de
 * @link http://www.xt-commerce.com
 * @link http://www.sofort-ueberweisung.de
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 *
 ***********************************************************************************
 * this file contains code based on:
 * (c) 2000 - 2001 The Exchange Project
 * (c) 2001 - 2003 osCommerce, Open Source E-Commerce Solutions
 * (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org
 * (c) 2003 - 2006 XT-Commerce
 * Released under the GNU General Public License
 ***********************************************************************************
 *
 */

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_TITLE',  xtc_image(DIR_WS_IMAGES . 'icons/sofortueberweisung_icon.gif', 'Sofort&uuml;berweisung direkt (empfohlen)', '', '', ' align="middle"' ) . 'Sofort&uuml;berweisung direkt (empfohlen)');
  // Not Installed and Admin?
  if (!defined('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS') && function_exists('xtc_catalog_href_link')) {
    define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_DESCRIPTION', '<div align="center"><a class="button" onClick="this.blur();" href=' . xtc_href_link('sofortueberweisung_install.php', 'install=sofortueberweisungredirect', 'SSL') . '>' . xtc_image(DIR_WS_IMAGES . 'icons/sofortueberweisung_autoinstaller.gif', 'Autoinstaller (empfohlen)') . '</a><br /><strong>Sofort&uuml;berweisung direkt (empfohlen)</strong><br />Der Kunde wird vor Abschluss des Bestellvorgangs zur Sofort&uuml;berweisungseite geleitet. Mit Abschluss der Zahlung wird die Bestellung in die Shopdatenbank geschrieben. Bricht der Kunde ab kommt er zur&uuml;ck zur  Zahlungsausswahlseite des Shops.<br /><strong>Hinweis zu diesem Modul:</strong><br />Schliest der Kunde bei Sofort&uuml;berweisung den Browser, bzw. scheitert der R&uuml;cksprung wird keine Bestellung im Shop aufgenommen.</small><br /><strong>Bei gleichzeitiger Verwendung mit einem der anderen Sofort&uuml;berweisungsmodule muss ein eigenes Projekt bei Sofort&uuml;berweisung angelegt werden.</strong>');
  } else {
    define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_DESCRIPTION', '<div align="center"><strong>Sofort&uuml;berweisung direkt (empfohlen)</strong><br />Der Kunde wird vor Abschluss des Bestellvorgangs zur Sofort&uuml;berweisungseite geleitet. Mit Abschluss der Zahlung wird die Bestellung in die Shopdatenbank geschrieben. Bricht der Kunde ab kommt er zur&uuml;ck zur  Zahlungsausswahlseite des Shops.<br /><strong>Hinweis zu diesem Modul:</strong><br />Schliest der Kunde bei Sofort&uuml;berweisung den Browser, bzw. scheitert der R&uuml;cksprung wird keine Bestellung im Shop aufgenommen.</small><br /><strong>Bei gleichzeitiger Verwendung mit einem der anderen Sofort&uuml;berweisungsmodule muss ein eigenes Projekt bei Sofort&uuml;berweisung angelegt werden.</strong>');
  }
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ALLOWED_TITLE', 'Erlaubte Zonen');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ALLOWED_DESC', 'Geben Sie <strong>einzeln</strong> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS_TITLE', 'Sofort&uuml;berweisung Direkter (redirect) Modus aktivieren');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS_DESC', 'Bezahlung per Vorkasse mit integrierter Sofort&uuml;berweisung acceptieren?');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ZONE_TITLE', 'Zahlungszone');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ORDER_STATUS_ID_DESC', 'Bestellstatus nach Eingang Erfolgsbenachrichtigung');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_KDNR_TITLE', 'Kundennummer');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_KDNR_DESC', 'Ihre Kundennummer bei der Sofort&uuml;berweisung');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_PROJEKT_TITLE', 'Projektnummer');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_PROJEKT_DESC', 'Die verantwortliche Projektnummer bei der Sofort&uuml;berweisung, zu der die Zahlung geh&ouml;rt');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_INPUT_PASSWORT_TITLE', 'Inputpasswort');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_INPUT_PASSWORT_DESC', 'Das Input-Passwort (unter "Nicht &auml;nderbare Parameter / Input-Passwort")');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_CNT_PASSWORT_TITLE', 'Contentpasswort');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_CNT_PASSWORT_DESC', 'Das Contentpasswort (unter "Content-Passwort")<br />Variablenname f&uuml;r Passwort (Default: pw) und Variablenname f&uuml;r Text (Default: text) bitte die Standardwerte (pw und text) eintragen');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STORE_TRANSACTION_DETAILS_TITLE', 'Transactiondetails speichern');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STORE_TRANSACTION_DETAILS_DESC', 'Transactionsdetails bei Benachrichtigung in das Kommentarfeld speichern (zum debuggen, ist f&uuml;r Kunden via Konto sichtbar)');

  // checkout_payment Informationen via Bild
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_DESCRIPTION_CHECKOUT_PAYMENT', '
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><a href="#" onclick="window.open(\'https://www.sofort-ueberweisung.de/paynetag/anbieter/download/informationen.html\', \'Popup\',\'toolbar=yes,status=no,menubar=no,scrollbars=yes,width=690,height=500\'); return false;"><img src="'.'templates/'.CURRENT_TEMPLATE.'/buttons/' . $_SESSION['language'].'/sofortueberweisung.gif" alt="Sofort&uuml;berweisung"></a></td>
      </tr>
    </table>');
  // checkout_payment Informationen via Text
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_DESCRIPTION_CHECKOUT_CONFIRMATION', '
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="main">Sofort&uuml;berweisung ist der kostenlose, <a href="#" onclick="window.open(\'https://www.sofortueberweisung.de/cms/index.php?plink=tuev-zertifikat&alink=sicherheit&fs=&l=0\', \'Popup\',\'toolbar=yes,status=no,menubar=no,scrollbars=yes,width=690,height=500\'); return false;">T&Uuml;V-zertifizierte</a> Zahlungsdienst der Payment Network AG. Ihre Vorteile: keine zus&auml;tzliche Registrierung, automatische Abbuchung von Ihrem Online-Bankkonto, h&ouml;chste Sicherheitsstandards und sofortiger Versand von Lagerware. F&uuml;r die Bezahlung mit Sofort&uuml;berweisung ben&ouml;tigen Sie Ihre eBanking Zugangsdaten, d.h. Bankverbindung, Kontonummer, PIN und TAN. Mehr Informationen finden Sie hier: <a href="#" onclick="window.open(\'https://www.sofort-ueberweisung.de/paynetag/anbieter/download/informationen.html\', \'Popup\',\'toolbar=yes,status=no,menubar=no,scrollbars=yes,width=690,height=500\'); return false;">www.sofortueberweisung.de</a>.</td>
      </tr>
    </table>');


 // im Verwendungszweck werden folgende Platzhalter ersetzt:
 // {{order_date}} durch Bestelldatum
 // {{customer_id}} durch Kundennummer der Datenbank
 // {{customer_cid}} durch Kundennummer des Admins
 // {{customer_name}}  durch Kundenname
 // {{customer_company}}  durch Kundenfirma
 // {{customer_email}} durch Email des Kunden
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_V_ZWECK_1', 'Bestellung bei ' . STORE_NAME);  // max 27 Zeichen
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_V_ZWECK_2', 'Kd-Nr. {{customer_id}}'); // max 27 Zeichen
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_EMAIL_FOOTER', '');

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_REDIRECT', 'Sie werden nun zu Sofortueberweisung.de weitergeleitet. Sollte dies nicht geschehen bitte den Button dr&uuml;cken');

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_ERROR_HEADING', 'Folgender Fehler wurde von Sofort&uuml;berweisung w&auml;hrend des Prozesses gemeldet:');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_ERROR_MESSAGE', 'Zahlung via Sofort&uuml;berweisung ist leider nicht m&ouml;glich, oder wurde auf Kundenwunsch abgebrochen. Bitte w&auml;hlen sie ein andere Zahlungsweise.');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_CHECK_ERROR', 'Sofort&uuml;berweisungs Transaktionscheck fehlgeschlagen. Bitte manuell &uuml;berpr&uuml;fen');

  ?>