<?php
/* --------------------------------------------------------------
   $Id: start.php 893 2005-04-27 11:44:16Z gwinger $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (start.php,v 1.1 2003/08/19); www.nextcommerce.org
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
 
  define('HEADING_TITLE','Willkommen');  
  define('ATTENTION_TITLE','! ACHTUNG !');
  
  // text for Warnings:
  define('TEXT_FILE_WARNING','<b>WARNUNG:</b><br />Folgende Dateien sind vom Server beschreibbar. Bitte &auml;ndern Sie die Zugriffsrechte (Permissions) dieser Datei aus Sicherheitsgr&uuml;nden. <b>(444)</b> in unix, <b>(read-only)</b> in Win32.');
  define('TEXT_FOLDER_WARNING','<b>WARNUNG:</b><br />Folgende Verzeichnisse m&uuml;ssen vom Server beschreibbar sein. Bitte &auml;ndern Sie die Zugriffsrechte (Permissions) dieser Verzeichnisse. <b>(777)</b> in unix, <b>(read-write)</b> in Win32.');
  define('REPORT_GENERATED_FOR','Report f&uuml;r:');
  define('REPORT_GENERATED_ON','Erstellt am:');
  define('FIRST_VISIT_ON','Erster Besuch:');
  define('HEADING_QUICK_STATS','Kurz&uuml;bersicht');
  define('VISITS_TODAY','Besuche heute:');
  define('UNIQUE_TODAY','Einzelne Besucher:');
  define('DAILY_AVERAGE','T&auml;glicher Durchschnitt:');
  define('TOTAL_VISITS','Besuche insgesammt:');
  define('TOTAL_UNIQUE','Einzelbesucher insgesammt:');
  define('TOP_REFFERER','Top Refferer:');
  define('TOP_ENGINE','Top Suchmaschine:');
  define('DAY_SUMMARY','30 Tage &Uuml;bersicht:');
  define('VERY_LAST_VISITORS','Letzte 10 Besucher:');
  define('TODAY_VISITORS','Besucher von heute:');
  define('LAST_VISITORS','Letzte 100 Besucher:');
  define('ALL_LAST_VISITORS','Alle Besucher:');
  define('DATE_TIME','Datum / Uhrzeit:');
  define('IP_ADRESS','IP Adresse:');
  define('OPERATING_SYSTEM','Betriebssystem:');
  define('REFFERING_HOST','Referring Host:');
  define('ENTRY_PAGE','Einstiegsseite:');
  define('HOURLY_TRAFFIC_SUMMARY','St&uuml;ndliche Traffic Zusammenfassung');
  define('WEB_BROWSER_SUMMARY','Web Browser &Uuml;bersicht');
  define('OPERATING_SYSTEM_SUMMARY','Betriebssystem &Uuml;bersicht');
  define('TOP_REFERRERS','Top 10 Referrer');
  define('TOP_HOSTS','Top Ten Hosts');
  define('LIST_ALL','Alle anzeigen');    
  define('SEARCH_ENGINE_SUMMARY','Suchmaschinen &Uuml;bersicht');
  define('SEARCH_ENGINE_SUMMARY_TEXT',' ( Prozentangaben basieren auf die Gesamtzahl der Besuche &uuml;ber Suchmaschinen. )');
  define('SEARCH_QUERY_SUMMARY','Suchanfragen &Uuml;bersicht');
  define('SEARCH_QUERY_SUMMARY_TEXT',' ) ( Prozentangaben basieren auf die Gesamtzahl der Suchanfragen die geloggt wurden. )');
  define('REFERRING_URL','Refferrer Url');
  define('HITS','Hits');
  define('PERCENTAGE','Prozentanteil');
  define('HOST','Host');
	
// NEU HINZUGEFUEGT 04.12.2008 - Neue Startseite im Admin BOF	
	
	define('HEADING_TITLE', 'Bestellungen');
	define('HEADING_TITLE_SEARCH', 'Bestell-Nr.:');
	define('HEADING_TITLE_STATUS', 'Status:');
	define('TABLE_HEADING_AFTERBUY', 'Afterbuy'); //Dokuman - 2009-05-27 - added missing definition
	define('TABLE_HEADING_CUSTOMERS', 'Kunden');
	define('TABLE_HEADING_ORDER_TOTAL', 'Gesamtwert');
	define('TABLE_HEADING_DATE_PURCHASED', 'Bestelldatum');
	define('TABLE_HEADING_STATUS', 'Status');
	define('TABLE_HEADING_ACTION', 'Aktion');
	define('TABLE_HEADING_QUANTITY', 'Anzahl');
	define('TABLE_HEADING_PRODUCTS_MODEL', 'Artikel-Nr.');
	define('TABLE_HEADING_PRODUCTS', 'Artikel');
	define('TABLE_HEADING_TAX', 'MwSt.');
	define('TABLE_HEADING_TOTAL', 'Gesamtsumme');
	define('TABLE_HEADING_STATUS', 'Status');
	define('TABLE_HEADING_DATE_ADDED', 'hinzugef&uuml;gt am:');
	define('ENTRY_CUSTOMER', 'Kunde:');
	define('TEXT_DATE_ORDER_CREATED', 'Bestelldatum:');
	define('TEXT_INFO_PAYMENT_METHOD', 'Zahlungsweise:');
	define('TEXT_VALIDATING','Nicht best&auml;tigt');
	define('TEXT_ALL_ORDERS', 'Alle Bestellungen');
	define('TEXT_NO_ORDER_HISTORY', 'Keine Bestellhistorie verf&uuml;gbar');
	define('TEXT_DATE_ORDER_LAST_MODIFIED','Letzte &Auml;nderung');
    
// NEU HINZUGEFUEGT 04.12.2008 - Neue Startseite im Admin EOF

define('TEXT_DATE_ORDER_LAST_MODIFIED','Letzte &Auml;nderung: ');

// BOF - Tomcraft - 2009-11-25 - Added missing definitions for /admin/start.php/
define('TOTAL_CUSTOMERS','Kunden gesamt');
define('TOTAL_SUBSCRIBERS','Newsletter Abos');
define('TOTAL_PRODUCTS_ACTIVE','Aktive Artikel');
define('TOTAL_PRODUCTS_INACTIVE','Inaktive Artikel');
define('TOTAL_PRODUCTS','Artikel gesamt');
define('TOTAL_SPECIALS','Sonderangebote');
// EOF - Tomcraft - 2009-11-25 - Added missing definitions for /admin/start.php/
?>