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

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_TEXT_TITLE',  xtc_image(DIR_WS_IMAGES . 'icons/sofortueberweisung_icon.gif', 'Vorkasse / Überweisung mit Option Sofortüberweisung', '', '', ' align="middle"' ) . 'Vorkasse / Überweisung mit Option Sofortüberweisung');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_TEXT_DESCRIPTION', '<div align="center">' . (MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_STATUS != 'True' ? '<a class="button" onClick="this.blur();" href=' . xtc_catalog_href_link('sofortueberweisung_install.php', 'install=sofortueberweisungvorkasse', 'SSL') . '>' . xtc_image(DIR_WS_IMAGES . 'icons/sofortueberweisung_autoinstaller.gif', 'Autoinstaller (empfohlen)') . '</a><br>' : '') . '<b>Vorkasse / Überweisung mit Option Sofortüberweisung</b><br>  Das Modul "Vorkasse / Überweisung" entspricht dem aus dem Shop bekannten "moneyorder" Modul. Nach Abschluss der Bestellung bekommt der Kunde in der Checkout Success Seite jedoch zusätzlich Information und einen Link zum Bezahlen via Sofortüberweisung angezeigt. Die Bestellung wird immer in die Datenbank geschrieben, auch wenn der Kunde den Bezahlvorgang abbricht</div>');

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_TEXT_DESCRIPTION_CHECKOUT_PAYMENT', '');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_TEXT_DESCRIPTION_CHECKOUT_CONFIRMATION', 'Zahlbar an:<br>IN-Solution<br> KntNr 1234<br> Blz: 1234');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_ALLOWED_TITLE', 'Erlaubte Zonen');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_STATUS_TITLE', 'Sofortüberweisung Vorkasse Modus aktivieren');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_STATUS_DESC', 'Bezahlung per Vorkasse mit integrierter Sofortüberweisung acceptieren?');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_ZONE_TITLE', 'Zahlungszone');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_ORDER_STATUS_ID_DESC', 'Bestellstatus nach Eingang Erfolgsbenachrichtigung');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_KDNR_TITLE', 'Kundennummer');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_KDNR_DESC', 'Ihre Kundennummer bei der Sofortüberweisung');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_PROJEKT_TITLE', 'Projektnummer');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_PROJEKT_DESC', 'Die verantwortliche Projektnummer bei der Sofortüberweisung, zu der die Zahlung gehört');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_INPUT_PASSWORT_TITLE', 'Inputpasswort');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_INPUT_PASSWORT_DESC', 'Das Input-Passwort (unter "Nicht änderbare Parameter / Input-Passwort")');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_BNA_PASSWORT_TITLE', 'Benachrichtungspasswort');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_BNA_PASSWORT_DESC', 'Das Benachrichtungspasswort (unter "Benachrichtigungen / Passwort für Benachrichtigungen")<br>Variablenname für Passwort (Default: pw) und Variablenname für Text (Default: text) bitte die Standardwerte (pw und text) eintragen');

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_STORE_TR_DETAILS_TITLE', 'Transactiondetails speichern');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_STORE_TR_DETAILS_DESC', 'Transactionsdetails bei Benachrichtigung in das Kommentarfeld speichern (zum debuggen, ist für Kunden via Konto sichtbar)');


 // im Verwendungszweck werden folgende Platzhalter ersetzt:
 // {{orderid}}  durch Bestellnummer (nicht bei directes bezahlen)
 // {{order_date}} durch Bestelldatum
 // {{customer_id}} durch Kundennummer der Datenbank
 // {{customer_cid}} durch Kundennummer des Admins
 // {{customer_name}}  durch Kundenname
 // {{customer_company}}  durch Kundenfirma
 // {{customer_email}} durch Email des Kunden

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_TEXT_V_ZWECK_1', 'Bestellung bei ' . STORE_NAME);  // max 27 Zeichen
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_TEXT_V_ZWECK_2', 'Nr: {{orderid}} Kd-Nr. {{customer_id}}'); // max 27 Zeichen

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_TEXT_EMAIL_FOOTER', "Bitte überweisen Sie den Betrag an:\n KntNr. 12345\n BLZ 1234\n \n\nAdressat:\n" . STORE_NAME_ADDRESS . "\n\n" . 'Ihre Bestellung wird nicht versandt, bis wir das Geld erhalten haben!');

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_TEXT_SUCCESS_HEADING', 'Wer mit Sofortüberweisung bezahlt, erhält die Ware auch sofort!');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_TEXT_SUCCESS_INFORMATION', 'Sie haben jetzt die Möglichkeit, Ihre Bestellung mit Sofortüberweisung zu bezahlen.');

?>