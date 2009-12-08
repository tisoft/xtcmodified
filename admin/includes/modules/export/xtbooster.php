<?php
##
## xt:booster #version# für xt:Commerce.
## Copyright (c) 2008 xt:booster Limited
##
## Licensed under GNU/GPL
##
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

define('MODULE_XTBOOSTER_TEXT_DESCRIPTION', 'xt:booster eBay-Connector f&uuml;r xt:Commerce');
define('MODULE_XTBOOSTER_TEXT_TITLE', 'xt:booster eBay-Connector');
define('MODULE_XTBOOSTER_STATUS_DESC','Modulstatus');
define('MODULE_XTBOOSTER_STATUS_TITLE','Status');

define('MODULE_XTBOOSTER_SHOPKEY_TITLE','ShopKey');
define('MODULE_XTBOOSTER_SHOPKEY_DESC','');
define('MODULE_XTBOOSTER_STDSITE_TITLE','Standard-Site');
define('MODULE_XTBOOSTER_STDSITE_DESC','');
define('MODULE_XTBOOSTER_STDCURRENCY_TITLE','Standard-W&auml;hrung');
define('MODULE_XTBOOSTER_STDCURRENCY_DESC','');

define('MODULE_XTBOOSTER_STDPLZ_TITLE','Standard Artikelstandort (PLZ)');
define('MODULE_XTBOOSTER_STDPLZ_DESC','');
define('MODULE_XTBOOSTER_STDSTANDORT_TITLE','Standard Artikelstandort');
define('MODULE_XTBOOSTER_STDSTANDORT_DESC','');


class xtbooster
{
	var $code, $title, $description, $enabled;

    function xtbooster() {
		$this->code = 'xtbooster';
		$this->title = MODULE_XTBOOSTER_TEXT_TITLE;
		$this->description = MODULE_XTBOOSTER_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_XTBOOSTER_SORT_ORDER;
		$this->enabled = ((MODULE_XTBOOSTER_STATUS == 'True') ? true : false);
	}

	function process($file) {
	}

	function display() {
		return array('text' => 
						'<br>' . xtc_button(BUTTON_REVIEW_APPROVE) . '&nbsp;' .
						xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=xtbooster')));
    }

    function check() {
		if(!isset($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_XTBOOSTER_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_XTBOOSTER_STATUS', 'True',  '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_XTBOOSTER_SHOPKEY', '', '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_XTBOOSTER_STDSITE', 'DE',  '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_XTBOOSTER_STDCURRENCY', 'EUR',  '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_XTBOOSTER_STDSTANDORT', '', '6', '1', '', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_XTBOOSTER_STDPLZ', '', '6', '1', '', now())");

		/* Table Structur */
xtc_db_query("
CREATE TABLE IF NOT EXISTS `xtb_auctions` (
  `XTB_ITEM_ID` bigint(12) NOT NULL auto_increment,
  `products_id` int(11) unsigned NOT NULL,
  `TITLE` varchar(55) NOT NULL,
  `SUBTITLE` varchar(55) NOT NULL,
  `DESCRIPTION` text NOT NULL,
  `CAT_PRIMARY` int(8) NOT NULL,
  `CAT_SECONDARY` int(8) NOT NULL,
  `PICTUREURL` varchar(255) NOT NULL,
  `SCHEDULETIME` varchar(25) NOT NULL,
  `STARTPRICE` float(12,2) NOT NULL,
  `BUYITNOWPRICE` float(12,2) NOT NULL,
  `CURRENCY` char(3) NOT NULL,
  `COUNTRY` varchar(55) NOT NULL,
  `TYPE` enum('Chinese','Dutch','Express','FixedPriceItem','StoresFixedPrice') NOT NULL,
  `QUANTITY` int(8) unsigned NOT NULL,
  `DURATION` enum('Days_3','Days_5','Days_7','Days_10','Days_30','GTC') NOT NULL,
  `LOCATION` varchar(45) NOT NULL,
  `POSTALCODE` varchar(10) NOT NULL,
  `_EBAY_ITEM_ID` varchar(255) NOT NULL,
  `_EBAY_LISTING_FEE` float(12,2) NOT NULL,
  `_EBAY_START_TIME` bigint(12) unsigned NOT NULL,
  `_EBAY_END_TIME` bigint(12) unsigned NOT NULL,
  `_EBAY_STATUS` enum('active','successful','unsuccessful') NOT NULL DEFAULT 'active',
  `_EBAY_QUANTITY_BUYED` int(8) NOT NULL,
  `_EBAY_MARKETPLACE` char(2) NOT NULL default 'DE',
  `QUANTITY_CHECKED_OUT` int(8) unsigned NOT NULL,
  `LISTINGENHANCEMENTS` varchar(255) NOT NULL,
  `GALLERY_PICTUREURL` varchar(255) NOT NULL,
  `GALLERYTYPE` varchar(20) NOT NULL,
  `_XTB_ITEM_HASH` varchar(32) NOT NULL,
  PRIMARY KEY  (`XTB_ITEM_ID`),
  KEY `products_id` (`products_id`)
) AUTO_INCREMENT=1
");
xtc_db_query("
CREATE TABLE IF NOT EXISTS `xtb_transactions` (
  `XTB_TX_ID` bigint(12) unsigned NOT NULL auto_increment,
  `XTB_ITEM_ID` bigint(12) unsigned NOT NULL,
  `XTB_KEY` varchar(32) NOT NULL,
  `XTB_AMOUNTPAID` float(12,2) NOT NULL,
  `XTB_AMOUNTPAID_CURRENCY` char(3) NOT NULL,
  `XTB_QUANTITYPURCHASED` int(8) NOT NULL,
  `XTB_EBAY_USERID` varchar(255) NOT NULL,
  `XTB_EBAY_EMAIL` varchar(255) NOT NULL,
  `XTB_EBAY_SITE` char(2) NOT NULL,
  `XTB_EBAY_NAME` varchar(255) NOT NULL,
  `XTB_EBAY_STREET` varchar(255) NOT NULL,
  `XTB_EBAY_CITY` varchar(255) NOT NULL,
  `XTB_EBAY_STATEORPROVINCE` varchar(255) NOT NULL,
  `XTB_EBAY_COUNTRYNAME` varchar(255) NOT NULL,
  `XTB_EBAY_PHONE` varchar(255) NOT NULL,
  `XTB_EBAY_POSTALCODE` varchar(20) NOT NULL,
  `XTB_CHECKOUT_TS` bigint(12) unsigned NOT NULL,
  `XTC_ORDER_ID` bigint(12) unsigned NOT NULL,
  `XTB_EBAY_TS` bigint(12) NOT NULL,
  `XTB_REDIRECT_USER_TO` enum('basket','product','create_guest_account','create_account') NOT NULL default 'basket',
  `XTB_ALLOW_USER_CHQTY` enum('true','false') NOT NULL default 'true',
  PRIMARY KEY  (`XTB_TX_ID`)
) AUTO_INCREMENT=1
");

xtc_db_query("ALTER TABLE `admin_access` ADD `xtbooster` INT( 1 ) NOT NULL");
xtc_db_query("UPDATE `admin_access` SET `xtbooster` = '1' WHERE `customers_id` = '1'");
xtc_db_query("UPDATE `admin_access` SET `xtbooster` = '1' WHERE `customers_id` = '".$_SESSION['customer_id']."'");
xtc_db_query("ALTER TABLE `products` ADD `products_ebay_quantity` INT( 4 ) NOT NULL DEFAULT 0 AFTER `products_quantity`");


	}

	function remove() {
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
		xtc_db_query("ALTER TABLE `admin_access` DROP `xtbooster`");
	}

	function keys() {
		return array('MODULE_XTBOOSTER_STATUS','MODULE_XTBOOSTER_SHOPKEY','MODULE_XTBOOSTER_STDSITE','MODULE_XTBOOSTER_STDCURRENCY', 'MODULE_XTBOOSTER_STDPLZ', 'MODULE_XTBOOSTER_STDSTANDORT');
	}
}
?>
