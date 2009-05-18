<?php
/**
 *
 *
 * @version Sofortüberweisung 1.9  05.06.2007
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

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_TITLE',  xtc_image(DIR_WS_IMAGES . 'icons/sofortueberweisung_icon.gif', 'Sofortüberweisung direkt (empfohlen)', '', '', ' align="middle"' ) . 'Sofortüberweisung direkt (empfohlen)');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_DESCRIPTION', '<div align="center">' . (MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS != 'True' ? '<a class="button" onClick="this.blur();" href=' . xtc_catalog_href_link('sofortueberweisung_install.php', 'install=sofortueberweisungredirect', 'SSL') . '>' . xtc_image(DIR_WS_IMAGES . 'icons/sofortueberweisung_autoinstaller.gif', 'Autoinstaller (empfohlen)') . '</a><br>' : '') .  '<b>Sofortüberweisung direkt (empfohlen)</b><br>Der Kunde wird vor Abschluss des Bestellvorgangs zur Sofortüberweisungseite geleitet. Mit Abschluss der Zahlung wird die Bestellung in die Shopdatenbank geschrieben. Bricht der Kunde ab kommt er zurück zur  Zahlungsausswahlseite des Shops.<br><b>Hinweis zu diesem Modul:</b><br>Schliest der Kunde bei Sofortüberweisung den Browser, bzw. scheitert der Rücksprung wird keine Bestellung im Shop aufgenommen.</small><br><b>Bei gleichzeitiger Verwendung mit einem der anderen Sofortüberweisungsmodule muß ein eigenes Projekt bei Sofortüberweisung angelegt werden.</b>');

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ALLOWED_TITLE', 'Erlaubte Zonen');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS_TITLE', 'Sofortüberweisung Direkter (redirect) Modus aktivieren');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS_DESC', 'Bezahlung per Vorkasse mit integrierter Sofortüberweisung acceptieren?');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ZONE_TITLE', 'Zahlungszone');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ORDER_STATUS_ID_DESC', 'Bestellstatus nach Eingang Erfolgsbenachrichtigung');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_KDNR_TITLE', 'Kundennummer');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_KDNR_DESC', 'Ihre Kundennummer bei der Sofortüberweisung');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_PROJEKT_TITLE', 'Projektnummer');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_PROJEKT_DESC', 'Die verantwortliche Projektnummer bei der Sofortüberweisung, zu der die Zahlung gehört');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_INPUT_PASSWORT_TITLE', 'Inputpasswort');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_INPUT_PASSWORT_DESC', 'Das Input-Passwort (unter "Nicht änderbare Parameter / Input-Passwort")');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_CNT_PASSWORT_TITLE', 'Contentpasswort');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_CNT_PASSWORT_DESC', 'Das Contentpasswort (unter "Content-Passwort")<br>Variablenname für Passwort (Default: pw) und Variablenname für Text (Default: text) bitte die Standardwerte (pw und text) eintragen');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STORE_TRANSACTION_DETAILS_TITLE', 'Transactiondetails speichern');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STORE_TRANSACTION_DETAILS_DESC', 'Transactionsdetails bei Benachrichtigung in das Kommentarfeld speichern (zum debuggen, ist für Kunden via Konto sichtbar)');

  // checkout_payment Informationen via Bild
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_DESCRIPTION_CHECKOUT_PAYMENT', '
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><a href="#" onclick="window.open(\'https://www.sofort-ueberweisung.de/paynetag/anbieter/download/informationen.html\', \'Popup\',\'toolbar=yes,status=no,menubar=no,scrollbars=yes,width=690,height=500\'); return false;"><img src="'.'templates/'.CURRENT_TEMPLATE.'/buttons/' . $_SESSION['language'].'/sofortueberweisung.gif" alt="Sofortüberweisung"></a></td>
      </tr>
    </table>');
  // checkout_payment Informationen via Text
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_DESCRIPTION_CHECKOUT_CONFIRMATION', '
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="main">Sofortüberweisung beschleunigt den Warenversand Ihrer Bestellung um Tage. Außerdem können Sie mit Sofortüberweisung bequem während Ihrer Bestellung die Online-Zahlung durchführen.<br>Unser Service ist für Sie als Kunde kostenlos, es fallen lediglich die Gebühren (Überweisungsgebühr) Ihrer Hausbank an.<br><a href="#" onclick="window.open(\'https://www.sofort-ueberweisung.de/paynetag/anbieter/download/informationen.html\', \'Popup\',\'toolbar=yes,status=no,menubar=no,scrollbars=yes,width=690,height=500\'); return false;"><b>Mehr Informationen</b></a></td>
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

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_REDIRECT', 'Sie werden nun zu Sofortueberweisung.de weitergeleitet. Sollte dies nicht geschehen bitte den Button drücken');

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_ERROR_HEADING', 'Folgender Fehler wurde von Sofortüberweisung während des Prozesses gemeldet:');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_ERROR_MESSAGE', 'Zahlung via Sofortüberweisung ist leider nicht möglich, oder wurde auf Kundenwunsch abgebrochen. Bitte wählen sie ein andere Zahlungsweise.');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_CHECK_ERROR', 'Sofortüberweisungs Transaktionscheck fehlgeschlagen. Bitte manuell überprüfen');

  ?>