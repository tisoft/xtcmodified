<?php
/* -----------------------------------------------------------------------------------------
   $Id: dhl.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(dhl.php,v 1.02 2003/02/18 03:37:00); www.oscommerce.com
   (c) 2003	 nextcommerce (dhl.php,v 1.4 2003/08/13); www.nextcommerce.org 

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   dhl_austria_1.02       	Autor:	Copyright (C) 2002 - 2003 TheMedia, Dipl.-Ing Thomas Pl�nkers | http://www.themedia.at & http://www.oscommerce.at

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   

define('MODULE_SHIPPING_DHL_TEXT_TITLE', 'DHL &Ouml;sterreich');
define('MODULE_SHIPPING_DHL_TEXT_DESCRIPTION', 'DHL WORLDWIDE EXPRESS &Ouml;sterreich');
define('MODULE_SHIPPING_DHL_TEXT_WAY', 'Versand nach');
define('MODULE_SHIPPING_DHL_TEXT_UNITS', 'kg');
define('MODULE_SHIPPING_DHL_INVALID_ZONE', 'Es ist leider kein Versand in dieses Land m&ouml;glich');
define('MODULE_SHIPPING_DHL_UNDEFINED_RATE', 'Die Versandkosten k&ouml;nnen im Moment nicht errechnet werden');

define('MODULE_SHIPPING_DHL_STATUS_TITLE' , 'DHL WORLDWIDE EXPRESS &Ouuml;sterreich');
define('MODULE_SHIPPING_DHL_STATUS_DESC' , 'Wollen Sie den Versand &uuml;ber DHL WORLDWIDE EXPRESS &Ouml;sterreich anbieten?');
define('MODULE_SHIPPING_DHL_HANDLING_TITLE' , 'Handling Fee');
define('MODULE_SHIPPING_DHL_HANDLING_DESC' , 'Bearbeitungsgeb&uuml;hr f&uuml;r diese Versandart in Euro');
define('MODULE_SHIPPING_DHL_TAX_CLASS_TITLE' , 'Steuersatz');
define('MODULE_SHIPPING_DHL_TAX_CLASS_DESC' , 'W&auml;hlen Sie den MwSt.-Satz f&uuml;r diese Versandart aus.');
define('MODULE_SHIPPING_DHL_ZONE_TITLE' , 'Versand Zone');
define('MODULE_SHIPPING_DHL_ZONE_DESC' , 'Wenn Sie eine Zone ausw&auml;hlen, wird diese Versandart nur in dieser Zone angeboten.');
define('MODULE_SHIPPING_DHL_SORT_ORDER_TITLE' , 'Reihenfolge der Anzeige');
define('MODULE_SHIPPING_DHL_SORT_ORDER_DESC' , 'Niedrigste wird zuerst angezeigt.');
define('MODULE_SHIPPING_DHL_ALLOWED_TITLE' , 'Einzelne Versandzonen');
define('MODULE_SHIPPING_DHL_ALLOWED_DESC' , 'Geben Sie <b>einzeln</b> die Zonen an, in welche ein Versand m&ouml;glich sein soll. zb AT,DE');
define('MODULE_SHIPPING_DHL_COUNTRIES_1_TITLE' , 'Tarifzone 0 L&auml;nder');
define('MODULE_SHIPPING_DHL_COUNTRIES_1_DESC' , 'Inlandszone');
define('MODULE_SHIPPING_DHL_COST_ECX_1_TITLE' , 'Tariftabelle f&uuml;r Zone 0 bis 10 kg ECX');
define('MODULE_SHIPPING_DHL_COST_ECX_1_DESC' , 'Tarif Tabelle f&uuml;r die Zone 0,  auf <b>\'ECX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_MDX_1_TITLE' , 'Tariftabelle f&uuml;r Zone 0 bis 10 kg MDX');
define('MODULE_SHIPPING_DHL_COST_MDX_1_DESC' , 'Tarif Tabelle f&uuml;r die Zone 0,  auf <b>\'MDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_SDX_1_TITLE' , 'Tariftabelle f&uuml;r Zone 0 bis 10 kg SDX');
define('MODULE_SHIPPING_DHL_COST_SDX_1_DESC' , 'Tarif Tabelle f&uuml;r die Zone 0,  auf <b>\'SDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_STEP_ECX_20_1_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_20_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_ECX_30_1_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_30_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_ECX_50_1_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_50_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_ECX_51_1_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_51_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_1_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_1_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_1_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_1_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_1_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_1_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_1_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_1_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_1_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_COUNTRIES_2_TITLE' , 'Tarifzone 1 L&auml;nder');
define('MODULE_SHIPPING_DHL_COUNTRIES_2_DESC' , 'Durch Komma getrennt Liste der L&auml;nder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 1 sind.');
define('MODULE_SHIPPING_DHL_COST_ECX_2_TITLE' , 'Tariftabelle f&uuml;r Zone 1 bis 10 kg ECX');
define('MODULE_SHIPPING_DHL_COST_ECX_2_DESC' , 'Tarif Tabelle f&uuml;r die Zone 1,  auf <b>\'ECX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_MDX_2_TITLE' , 'Tariftabelle f&uuml;r Zone 1 bis 10 kg MDX');
define('MODULE_SHIPPING_DHL_COST_MDX_2_DESC' , 'Tarif Tabelle f&uuml;r die Zone 1,  auf <b>\'MDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_SDX_2_TITLE' , 'Tariftabelle f&uuml;r Zone 1 bis 10 kg SDX');
define('MODULE_SHIPPING_DHL_COST_SDX_2_DESC' , 'Tarif Tabelle f&uuml;r die Zone 1,  auf <b>\'SDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_STEP_ECX_20_2_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_20_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_ECX_30_2_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_30_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_ECX_50_2_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_50_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_ECX_51_2_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_51_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_2_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_2_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_2_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_2_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_2_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_2_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_2_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_2_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_2_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_COUNTRIES_3_TITLE' , 'Tarifzone 2 L&auml;nder');
define('MODULE_SHIPPING_DHL_COUNTRIES_3_DESC' , 'Durch Komma getrennt Liste der L&auml;nder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 2 sind.');
define('MODULE_SHIPPING_DHL_COST_ECX_3_TITLE' , 'Tariftabelle f&uuml;r Zone 2 bis 10 kg ECX');
define('MODULE_SHIPPING_DHL_COST_ECX_3_DESC' , 'Tarif Tabelle f&uuml;r die Zone 2,  auf <b>\'ECX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_MDX_3_TITLE' , 'Tariftabelle f&uuml;r Zone 2 bis 10 kg MDX');
define('MODULE_SHIPPING_DHL_COST_MDX_3_DESC' , 'Tarif Tabelle f&uuml;r die Zone 2,  auf <b>\'MDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_SDX_3_TITLE' , 'Tariftabelle f&uuml;r Zone 2 bis 10 kg SDX');
define('MODULE_SHIPPING_DHL_COST_SDX_3_DESC' , 'Tarif Tabelle f&uuml;r die Zone 2,  auf <b>\'SDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_STEP_ECX_20_3_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_20_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_ECX_30_3_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_30_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_ECX_50_3_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_50_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_ECX_51_3_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg ECX');
define('MODULE_SHIPPING_DHL_STEP_ECX_51_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_3_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_3_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_3_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_3_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_3_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_3_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_3_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_3_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_3_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_COUNTRIES_4_TITLE' , 'Tarifzone 3 L&auml;nder');
define('MODULE_SHIPPING_DHL_COUNTRIES_4_DESC' , 'Durch Komma getrennt Liste der L&auml;nder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 3 sind.');
define('MODULE_SHIPPING_DHL_COST_DOX_4_TITLE' , 'Tariftabelle f&uuml;r Zone 3 bis 10 kg DOX');
define('MODULE_SHIPPING_DHL_COST_DOX_4_DESC' , 'Tarif Tabelle f&uuml;r die Zone 3,  auf <b>\'DOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_WPX_4_TITLE' , 'Tariftabelle f&uuml;r Zone 3 bis 10 kg WPX');
define('MODULE_SHIPPING_DHL_COST_WPX_4_DESC' , 'Tarif Tabelle f&uuml;r die Zone 3,  auf <b>\'WPX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_MDX_4_TITLE' , 'Tariftabelle f&uuml;r Zone 3 bis 10 kg MDX');
define('MODULE_SHIPPING_DHL_COST_MDX_4_DESC' , 'Tarif Tabelle f&uuml;r die Zone 3,  auf <b>\'MDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_SDX_4_TITLE' , 'Tariftabelle f&uuml;r Zone 3 bis 10 kg SDX');
define('MODULE_SHIPPING_DHL_COST_SDX_4_DESC' , 'Tarif Tabelle f&uuml;r die Zone 3,  auf <b>\'SDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_4_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_4_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_4_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_4_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_4_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_4_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_4_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_4_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_4_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_4_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_4_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_4_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_4_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_4_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_4_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_4_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_4_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_COUNTRIES_5_TITLE' , 'Tarifzone 4 L&auml;nder');
define('MODULE_SHIPPING_DHL_COUNTRIES_5_DESC' , 'Durch Komma getrennt Liste der L&auml;nder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 4 sind.');
define('MODULE_SHIPPING_DHL_COST_DOX_5_TITLE' , 'Tariftabelle f&uuml;r Zone 4 bis 10 kg DOX');
define('MODULE_SHIPPING_DHL_COST_DOX_5_DESC' , 'Tarif Tabelle f&uuml;r die Zone 4,  auf <b>\'DOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_WPX_5_TITLE' , 'Tariftabelle f&uuml;r Zone 4 bis 10 kg WPX');
define('MODULE_SHIPPING_DHL_COST_WPX_5_DESC' , 'Tarif Tabelle f&uuml;r die Zone 4,  auf <b>\'WPX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_MDX_5_TITLE' , 'Tariftabelle f&uuml;r Zone 4 bis 10 kg MDX');
define('MODULE_SHIPPING_DHL_COST_MDX_5_DESC' , 'Tarif Tabelle f&uuml;r die Zone 4,  auf <b>\'MDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_SDX_5_TITLE' , 'Tariftabelle f&uuml;r Zone 4 bis 10 kg SDX');
define('MODULE_SHIPPING_DHL_COST_SDX_5_DESC' , 'Tarif Tabelle f&uuml;r die Zone 4,  auf <b>\'SDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_5_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_5_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_5_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_5_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_5_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_5_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_5_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_5_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_5_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_5_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_5_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_5_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_5_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_5_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_5_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_5_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_5_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_COUNTRIES_6_TITLE' , 'Tarifzone 5 L&auml;nder');
define('MODULE_SHIPPING_DHL_COUNTRIES_6_DESC' , 'Durch Komma getrennt Liste der L&auml;nder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 5 sind.');
define('MODULE_SHIPPING_DHL_COST_DOX_6_TITLE' , 'Tariftabelle f&uuml;r Zone 5 bis 10 kg DOX');
define('MODULE_SHIPPING_DHL_COST_DOX_6_DESC' , 'Tarif Tabelle f&uuml;r die Zone 5,  auf <b>\'DOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_WPX_6_TITLE' , 'Tariftabelle f&uuml;r Zone 5 bis 10 kg WPX');
define('MODULE_SHIPPING_DHL_COST_WPX_6_DESC' , 'Tarif Tabelle f&uuml;r die Zone 5,  auf <b>\'WPX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_6_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_6_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_6_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_6_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_6_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_6_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_6_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_6_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_6_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_6_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_6_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_6_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_6_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_6_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_6_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_6_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_COUNTRIES_7_TITLE' , 'Tarifzone 6 L&auml;nder');
define('MODULE_SHIPPING_DHL_COUNTRIES_7_DESC' , 'Durch Komma getrennt Liste der L&auml;nder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 6 sind.');
define('MODULE_SHIPPING_DHL_COST_DOX_7_TITLE' , 'Tariftabelle f&uuml;r Zone 6 bis 10 kg DOX');
define('MODULE_SHIPPING_DHL_COST_DOX_7_DESC' , 'Tarif Tabelle f&uuml;r die Zone 6,  auf <b>\'DOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_WPX_7_TITLE' , 'Tariftabelle f&uuml;r Zone 6 bis 10 kg WPX');
define('MODULE_SHIPPING_DHL_COST_WPX_7_DESC' , 'Tarif Tabelle f&uuml;r die Zone 6,  auf <b>\'WPX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_7_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_7_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_7_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_7_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_7_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_7_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_7_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_7_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_7_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_7_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_7_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_7_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_7_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_7_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_7_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_7_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_COUNTRIES_8_TITLE' , 'Tarifzone 7 L&auml;nder');
define('MODULE_SHIPPING_DHL_COUNTRIES_8_DESC' , 'Durch Komma getrennt Liste der L&auml;nder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 7 sind.');
define('MODULE_SHIPPING_DHL_COST_DOX_8_TITLE' , 'Tariftabelle f&uuml;r Zone 7 bis 10 kg DOX');
define('MODULE_SHIPPING_DHL_COST_DOX_8_DESC' , 'Tarif Tabelle f&uuml;r die Zone 7,  auf <b>\'DOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_WPX_8_TITLE' , 'Tariftabelle f&uuml;r Zone 7 bis 10 kg WPX');
define('MODULE_SHIPPING_DHL_COST_WPX_8_DESC' , 'Tarif Tabelle f&uuml;r die Zone 7,  auf <b>\'WPX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_MDX_8_TITLE' , 'Tariftabelle f&uuml;r Zone 7 bis 10 kg MDX');
define('MODULE_SHIPPING_DHL_COST_MDX_8_DESC' , 'Tarif Tabelle f&uuml;r die Zone 7,  auf <b>\'MDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_SDX_8_TITLE' , 'Tariftabelle f&uuml;r Zone 7 bis 10 kg SDX');
define('MODULE_SHIPPING_DHL_COST_SDX_8_DESC' , 'Tarif Tabelle f&uuml;r die Zone 7,  auf <b>\'SDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_8_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_8_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_8_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_8_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_8_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_8_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_8_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_8_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_8_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_8_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_8_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_8_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_8_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_8_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_8_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_8_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_8_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_COUNTRIES_9_TITLE' , 'Tarifzone 8 L&auml;nder');
define('MODULE_SHIPPING_DHL_COUNTRIES_9_DESC' , 'Durch Komma getrennt Liste der L&auml;nder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 8 sind.');
define('MODULE_SHIPPING_DHL_COST_DOX_9_TITLE' , 'Tariftabelle f&uuml;r Zone 8 bis 10 kg DOX');
define('MODULE_SHIPPING_DHL_COST_DOX_9_DESC' , 'Tarif Tabelle f&uuml;r die Zone 8,  auf <b>\'DOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_WPX_9_TITLE' , 'Tariftabelle f&uuml;r Zone 8 bis 10 kg WPX');
define('MODULE_SHIPPING_DHL_COST_WPX_9_DESC' , 'Tarif Tabelle f&uuml;r die Zone 8,  auf <b>\'WPX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_MDX_9_TITLE' , 'Tariftabelle f&uuml;r Zone 8 bis 10 kg MDX');
define('MODULE_SHIPPING_DHL_COST_MDX_9_DESC' , 'Tarif Tabelle f&uuml;r die Zone 8,  auf <b>\'MDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_SDX_9_TITLE' , 'Tariftabelle f&uuml;r Zone 8 bis 10 kg SDX');
define('MODULE_SHIPPING_DHL_COST_SDX_9_DESC' , 'Tarif Tabelle f&uuml;r die Zone 8,  auf <b>\'SDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_9_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_9_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_9_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_9_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_9_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_9_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_9_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_9_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_9_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_9_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_9_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_9_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_9_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_9_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_9_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_9_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_9_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_COUNTRIES_10_TITLE' , 'Tarifzone 8 L&auml;nder');
define('MODULE_SHIPPING_DHL_COUNTRIES_10_DESC' , 'Durch Komma getrennt Liste der L&auml;nder als zwei Zeichen ISO-Code Landeskennzahlen, die Teil der Zone 8 sind.');
define('MODULE_SHIPPING_DHL_COST_DOX_10_TITLE' , 'Tariftabelle f&uuml;r Zone 8 bis 10 kg DOX');
define('MODULE_SHIPPING_DHL_COST_DOX_10_DESC' , 'Tarif Tabelle f&uuml;r die Zone 8,  auf <b>\'DOX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_WPX_10_TITLE' , 'Tariftabelle f&uuml;r Zone 8 bis 10 kg WPX');
define('MODULE_SHIPPING_DHL_COST_WPX_10_DESC' , 'Tarif Tabelle f&uuml;r die Zone 8,  auf <b>\'WPX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_MDX_10_TITLE' , 'Tariftabelle f&uuml;r Zone 8 bis 10 kg MDX');
define('MODULE_SHIPPING_DHL_COST_MDX_10_DESC' , 'Tarif Tabelle f&uuml;r die Zone 8,  auf <b>\'MDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_COST_SDX_10_TITLE' , 'Tariftabelle f&uuml;r Zone 8 bis 10 kg SDX');
define('MODULE_SHIPPING_DHL_COST_SDX_10_DESC' , 'Tarif Tabelle f&uuml;r die Zone 8,  auf <b>\'SDX\'</b> bis 10 kg Versandgewicht.');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_10_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_20_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_10_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_30_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_10_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_50_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_10_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg DOX');
define('MODULE_SHIPPING_DHL_STEP_DOX_51_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_10_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_20_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_10_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_30_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_10_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_50_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_10_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg WPX');
define('MODULE_SHIPPING_DHL_STEP_WPX_51_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_10_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_20_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_10_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_30_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_10_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_50_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_10_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg MDX');
define('MODULE_SHIPPING_DHL_STEP_MDX_51_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_10_TITLE' , 'Erh&ouml;hungszuschlag bis 20 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_20_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_10_TITLE' , 'Erh&ouml;hungszuschlag bis 30 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_30_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_10_TITLE' , 'Erh&ouml;hungszuschlag bis 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_50_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_10_TITLE' , 'Erh&ouml;hungszuschlag ab 50 kg SDX');
define('MODULE_SHIPPING_DHL_STEP_SDX_51_10_DESC' , 'Erh&ouml;hungszuschlag pro &uuml;bersteigende 0,50 kg in EUR');
?>
