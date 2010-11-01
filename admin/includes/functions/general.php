<?php
/* --------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.156 2003/05/29); www.oscommerce.com
   (c) 2003	nextcommerce (general.php,v 1.35 2003/08/1); www.nextcommerce.org
   (c) 2006 XT-Commerce (general.php 1316 2005-10-21)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:

   Customers Status v3.x (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Enable_Disable_Categories 1.3                Autor: Mikel Williams | mikel@ladykatcostumes.com

   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
function clear_string($value) {
	$string = str_replace("'", '', $value);
	$string = str_replace(')', '', $string);
	$string = str_replace('(', '', $string);
	$array = explode(',', $string);
	return $array;
}

function check_stock($products_id) {
	unset ($stock_flag);
	$stock_query = xtc_db_query("SELECT products_quantity FROM ".TABLE_PRODUCTS." where products_id = '".$products_id."'");
	$stock_values = xtc_db_fetch_array($stock_query);
	if ($stock_values['products_quantity'] <= '0') {
		$stock_flag = 'true';
		$stock_warn = TEXT_WARN_MAIN;

		$attribute_stock_query = xtc_db_query("SELECT attributes_stock, options_values_id FROM ".TABLE_PRODUCTS_ATTRIBUTES." where products_id = '".$products_id."'");
		while ($attribute_stock_values = xtc_db_fetch_array($attribute_stock_query)) {
			if ($attribute_stock_values['attributes_stock'] <= '0') {
				$stock_flag = 'true';
				$which_attribute_query = xtDBquery("SELECT products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = '".$attribute_stock_values['options_values_id']."' AND language_id = '".$_SESSION['languages_id']."'");
				$which_attribute = xtc_db_fetch_array($which_attribute_query);
				$stock_warn .= ', '.$which_attribute['products_options_values_name'];
			}
		}
	}
	if ($stock_flag == 'true' && $products_id != '') {
		return '<div class="stock_warn">'.$stock_warn.'</div>';
	} else {
		return xtc_image(DIR_WS_IMAGES.'icon_status_green.gif', $stock_values['products_quantity'].' '.IMAGE_ICON_STATUS_GREEN_STOCK, 10, 10);
	}
}

// Set Categorie Status
function xtc_set_categories_status($categories_id, $status) {
	if ($status == '1') {
		return xtc_db_query("update ".TABLE_CATEGORIES." set categories_status = '1' where categories_id = '".$categories_id."'");
	}
	elseif ($status == '0') {
		return xtc_db_query("update ".TABLE_CATEGORIES." set categories_status = '0' where categories_id = '".$categories_id."'");
	} else {
		return -1;
	}
}

function xtc_set_groups($categories_id, $permission_array) {
	// get products in categorie
	$products_query = xtc_db_query("SELECT products_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id='".$categories_id."'");
	while ($products = xtc_db_fetch_array($products_query)) {
		xtc_db_perform(TABLE_PRODUCTS, $permission_array, 'update', 'products_id = \''.$products['products_id'].'\'');
	}
	// set status of categorie
	xtc_db_perform(TABLE_CATEGORIES, $permission_array, 'update', 'categories_id = \''.$categories_id.'\'');
	// look for deeper categories and go rekursiv
	$categories_query = xtc_db_query("SELECT categories_id FROM ".TABLE_CATEGORIES." where parent_id='".$categories_id."'");
	while ($categories = xtc_db_fetch_array($categories_query)) {
		xtc_set_groups($categories['categories_id'], $permission_array);
	}
}

// Set Admin Access Rights
function xtc_set_admin_access($fieldname, $status, $cID) {
	if ($status == '1') {
		return xtc_db_query("update ".TABLE_ADMIN_ACCESS." set ".$fieldname." = '1' where customers_id = '".$cID."'");
	} else {
		return xtc_db_query("update ".TABLE_ADMIN_ACCESS." set ".$fieldname." = '0' where customers_id = '".$cID."'");
	}
}

// Check whether a referer has enough permission to open an admin page
function xtc_check_permission($pagename) {
	if ($pagename != 'index') {
		$access_permission_query = xtc_db_query("select ".$pagename." from ".TABLE_ADMIN_ACCESS." where customers_id = '".$_SESSION['customer_id']."'");
		$access_permission = xtc_db_fetch_array($access_permission_query);

		if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($access_permission[$pagename] == '1')) {
			return true;
		} else {
			return false;
		}
	} else {
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

// Redirect to another page or site
function xtc_redirect($url) {
	global $logger;
	header('Location: '.$url);

	if (STORE_PAGE_PARSE_TIME == 'true') {
		if (!is_object($logger))
			$logger = new logger;
		$logger->timer_stop();
	}

	exit;
}

function xtc_customers_name($customers_id) {
	$customers = xtc_db_query("select customers_firstname, customers_lastname from ".TABLE_CUSTOMERS." where customers_id = '".$customers_id."'");
	$customers_values = xtc_db_fetch_array($customers);

	return $customers_values['customers_firstname'].' '.$customers_values['customers_lastname'];
}

function xtc_get_path($current_category_id = '') {
	global $cPath_array;

	if ($current_category_id == '') {
		$cPath_new = implode('_', $cPath_array);
	} else {
		if (sizeof($cPath_array) == 0) {
			$cPath_new = $current_category_id;
		} else {
			$cPath_new = '';
			$last_category_query = xtc_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id = '".$cPath_array[(sizeof($cPath_array) - 1)]."'");
			$last_category = xtc_db_fetch_array($last_category_query);
			$current_category_query = xtc_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id = '".$current_category_id."'");
			$current_category = xtc_db_fetch_array($current_category_query);
			if ($last_category['parent_id'] == $current_category['parent_id']) {
				for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i ++) {
					$cPath_new .= '_'.$cPath_array[$i];
				}
			} else {
				for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i ++) {
					$cPath_new .= '_'.$cPath_array[$i];
				}
			}
			$cPath_new .= '_'.$current_category_id;
			if (substr($cPath_new, 0, 1) == '_') {
				$cPath_new = substr($cPath_new, 1);
			}
		}
	}

	return 'cPath='.$cPath_new;
}

function xtc_get_all_get_params($exclude_array = '') {

	if ($exclude_array == '')
		$exclude_array = array ();
	$get_url = '';

	reset($_GET);
	while (list ($key, $value) = each($_GET)) {
		if (($key != session_name()) && ($key != 'error') && (!xtc_in_array($key, $exclude_array)))
			$get_url .= $key.'='.$value.'&';
	}

	return $get_url;
}

function xtc_date_long($raw_date) {
	if (($raw_date == '0000-00-00 00:00:00') || ($raw_date == ''))
		return false;

	$year = (int) substr($raw_date, 0, 4);
	$month = (int) substr($raw_date, 5, 2);
	$day = (int) substr($raw_date, 8, 2);
	$hour = (int) substr($raw_date, 11, 2);
	$minute = (int) substr($raw_date, 14, 2);
	$second = (int) substr($raw_date, 17, 2);

	return strftime(DATE_FORMAT_LONG, mktime($hour, $minute, $second, $month, $day, $year));
}

// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
function xtc_date_short($raw_date) {
	if (($raw_date == '0000-00-00 00:00:00') || ($raw_date == ''))
		return false;

	$year = substr($raw_date, 0, 4);
	$month = (int) substr($raw_date, 5, 2);
	$day = (int) substr($raw_date, 8, 2);
	$hour = (int) substr($raw_date, 11, 2);
	$minute = (int) substr($raw_date, 14, 2);
	$second = (int) substr($raw_date, 17, 2);

	if (@ date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
		return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
	} else {
		return preg_replace('/2037/'.'$', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037))); // Hetfield - 2009-08-19 - replaced deprecated function ereg_replace with preg_replace to be ready for PHP >= 5.3
	}

}

function xtc_datetime_short($raw_datetime) {
	if (($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == ''))
		return false;

	$year = (int) substr($raw_datetime, 0, 4);
	$month = (int) substr($raw_datetime, 5, 2);
	$day = (int) substr($raw_datetime, 8, 2);
	$hour = (int) substr($raw_datetime, 11, 2);
	$minute = (int) substr($raw_datetime, 14, 2);
	$second = (int) substr($raw_datetime, 17, 2);

	return strftime(DATE_TIME_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
}

function xtc_array_merge($array1, $array2, $array3 = '') {
	if (!is_array($array1)) { $array1 = array (); }
	if (!is_array($array2)) { $array2 = array (); }
	if (!is_array($array3)) { $array3 = array (); }
	if (function_exists('array_merge')) {
		$array_merged = array_merge($array1, $array2, $array3);
	} else {
		while (list ($key, $val) = each($array1))
			$array_merged[$key] = $val;
		while (list ($key, $val) = each($array2))
			$array_merged[$key] = $val;
		if (sizeof($array3) > 0)
			while (list ($key, $val) = each($array3))
				$array_merged[$key] = $val;
	}

	return (array) $array_merged;
}

function xtc_in_array($lookup_value, $lookup_array) {
	if (function_exists('in_array')) {
		if (in_array($lookup_value, $lookup_array))
			return true;
	} else {
		reset($lookup_array);
		while (list ($key, $value) = each($lookup_array)) {
			if ($value == $lookup_value)
				return true;
		}
	}

	return false;
}

function xtc_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {

	if (!is_array($category_tree_array))
		$category_tree_array = array ();
	if ((sizeof($category_tree_array) < 1) && ($exclude != '0'))
		$category_tree_array[] = array ('id' => '0', 'text' => TEXT_TOP);

	if ($include_itself) {
		$category_query = xtc_db_query("select cd.categories_name from ".TABLE_CATEGORIES_DESCRIPTION." cd where cd.language_id = '".$_SESSION['languages_id']."' and cd.categories_id = '".$parent_id."'");
		$category = xtc_db_fetch_array($category_query);
		$category_tree_array[] = array ('id' => $parent_id, 'text' => $category['categories_name']);
	}

	$categories_query = xtc_db_query("select c.categories_id, cd.categories_name, c.parent_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and cd.language_id = '".$_SESSION['languages_id']."' and c.parent_id = '".$parent_id."' order by c.sort_order, cd.categories_name");
	while ($categories = xtc_db_fetch_array($categories_query)) {
		if ($exclude != $categories['categories_id'])
			$category_tree_array[] = array ('id' => $categories['categories_id'], 'text' => $spacing.$categories['categories_name']);
		$category_tree_array = xtc_get_category_tree($categories['categories_id'], $spacing.'&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
	}

	return $category_tree_array;
}

function xtc_draw_products_pull_down($name, $parameters = '', $exclude = '') {
	global $currencies;

	if ($exclude == '') {
		$exclude = array ();
	}
	$select_string = '<select name="'.$name.'"';
	if ($parameters) {
		$select_string .= ' '.$parameters;
	}
	$select_string .= '>';
	$products_query = xtc_db_query("select p.products_id, pd.products_name,p.products_tax_class_id, p.products_price from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = pd.products_id and pd.language_id = '".$_SESSION['languages_id']."' order by products_name");
	while ($products = xtc_db_fetch_array($products_query)) {
		if (!xtc_in_array($products['products_id'], $exclude)) {
			//brutto admin:
			if (PRICE_IS_BRUTTO == 'true') {
				$products['products_price'] = xtc_round($products['products_price'] * ((100 + xtc_get_tax_rate($products['products_tax_class_id'])) / 100), PRICE_PRECISION);
			}
			$select_string .= '<option value="'.$products['products_id'].'">'.$products['products_name'].' ('.xtc_round($products['products_price'], PRICE_PRECISION).')</option>';
		}
	}
	$select_string .= '</select>';

	return $select_string;
}

function xtc_options_name($options_id) {

	$options = xtc_db_query("select products_options_name from ".TABLE_PRODUCTS_OPTIONS." where products_options_id = '".$options_id."' and language_id = '".$_SESSION['languages_id']."'");
	$options_values = xtc_db_fetch_array($options);

	return $options_values['products_options_name'];
}

function xtc_values_name($values_id) {

	$values = xtc_db_query("select products_options_values_name from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".$values_id."' and language_id = '".$_SESSION['languages_id']."'");
	$values_values = xtc_db_fetch_array($values);

	return $values_values['products_options_values_name'];
}

function xtc_info_image($image, $alt, $width = '', $height = '') {
	if (($image) && (file_exists(DIR_FS_CATALOG_IMAGES.$image))) {
		$image = xtc_image(DIR_WS_CATALOG_IMAGES.$image, $alt, $width, $height);
	} else {
		$image = TEXT_IMAGE_NONEXISTENT;
	}

	return $image;
}

function xtc_info_image_c($image, $alt, $width = '', $height = '') {
	if (($image) && (file_exists(DIR_FS_CATALOG_IMAGES.'categories/'.$image))) {
		$image = xtc_image(DIR_WS_CATALOG_IMAGES.'categories/'.$image, $alt, $width, $height);
	} else {
		$image = TEXT_IMAGE_NONEXISTENT;
	}

	return $image;
}

function xtc_product_thumb_image($image, $alt, $width = '', $height = '') {
	if (($image) && (file_exists(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image))) {
		$image = xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES.$image, $alt, $width, $height);
	} else {
		$image = TEXT_IMAGE_NONEXISTENT;
	}

	return $image;
}

function xtc_break_string($string, $len, $break_char = '-') {
	$l = 0;
	$output = '';
	for ($i = 0; $i < strlen($string); $i ++) {
		$char = substr($string, $i, 1);
		if ($char != ' ') {
			$l ++;
		} else {
			$l = 0;
		}
		if ($l > $len) {
			$l = 1;
			$output .= $break_char;
		}
		$output .= $char;
	}

	return $output;
}

function xtc_get_country_name($country_id) {
	$country_query = xtc_db_query("select countries_name from ".TABLE_COUNTRIES." where countries_id = '".$country_id."'");

	if (!xtc_db_num_rows($country_query)) {
		return $country_id;
	} else {
		$country = xtc_db_fetch_array($country_query);
		return $country['countries_name'];
	}
}

function xtc_get_zone_name($country_id, $zone_id, $default_zone) {
	$zone_query = xtc_db_query("select zone_name from ".TABLE_ZONES." where zone_country_id = '".$country_id."' and zone_id = '".$zone_id."'");
	if (xtc_db_num_rows($zone_query)) {
		$zone = xtc_db_fetch_array($zone_query);
		return $zone['zone_name'];
	} else {
		return $default_zone;
	}
}

function xtc_browser_detect($component) {

	return stristr($_SERVER['HTTP_USER_AGENT'], $component);
}

function xtc_tax_classes_pull_down($parameters, $selected = '') {
	$select_string = '<select '.$parameters.'>';
	$classes_query = xtc_db_query("select tax_class_id, tax_class_title from ".TABLE_TAX_CLASS." order by tax_class_title");
	while ($classes = xtc_db_fetch_array($classes_query)) {
		$select_string .= '<option value="'.$classes['tax_class_id'].'"';
		if ($selected == $classes['tax_class_id'])
			$select_string .= ' SELECTED';
		$select_string .= '>'.$classes['tax_class_title'].'</option>';
	}
	$select_string .= '</select>';

	return $select_string;
}

function xtc_geo_zones_pull_down($parameters, $selected = '') {
	$select_string = '<select '.$parameters.'>';
	$zones_query = xtc_db_query("select geo_zone_id, geo_zone_name from ".TABLE_GEO_ZONES." order by geo_zone_name");
	while ($zones = xtc_db_fetch_array($zones_query)) {
		$select_string .= '<option value="'.$zones['geo_zone_id'].'"';
		if ($selected == $zones['geo_zone_id'])
			$select_string .= ' SELECTED';
		$select_string .= '>'.$zones['geo_zone_name'].'</option>';
	}
	$select_string .= '</select>';

	return $select_string;
}

function xtc_get_geo_zone_name($geo_zone_id) {
	$zones_query = xtc_db_query("select geo_zone_name from ".TABLE_GEO_ZONES." where geo_zone_id = '".$geo_zone_id."'");

	if (!xtc_db_num_rows($zones_query)) {
		$geo_zone_name = $geo_zone_id;
	} else {
		$zones = xtc_db_fetch_array($zones_query);
		$geo_zone_name = $zones['geo_zone_name'];
	}

	return $geo_zone_name;
}

function xtc_address_format($address_format_id, $address, $html, $boln, $eoln) {
	$address_format_query = xtc_db_query("select address_format as format from ".TABLE_ADDRESS_FORMAT." where address_format_id = '".$address_format_id."'");
	$address_format = xtc_db_fetch_array($address_format_query);

	$company = isset($address['company']) ? addslashes($address['company']) : '';
	$firstname = isset($address['firstname']) ? addslashes($address['firstname']) : '';
	$cid = isset($address['csID']) ? addslashes($address['csID']) : '';
	$lastname = isset($address['lastname']) ? addslashes($address['lastname']) : '';
	$street = isset($address['street_address']) ? addslashes($address['street_address']) : '';
	$suburb = isset($address['suburb']) ? addslashes($address['suburb']) : '';
	$city = isset($address['city']) ? addslashes($address['city']) : '';
	$state = isset($address['state']) ? addslashes($address['state']) : '';
	$country_id = isset($address['country_id']) ? $address['country_id'] : '';
	$zone_id = isset($address['zone_id']) ? $address['zone_id'] : '';
	$postcode = isset($address['postcode']) ? addslashes($address['postcode']) : '';
	$zip = $postcode;
	$country = isset($address['country_id']) ? xtc_get_country_name($country_id) : '';
	$state = xtc_get_zone_code($country_id, $zone_id, $state);

	if ($html) {
		// HTML Mode
		$HR = '<hr />';
		$hr = '<hr />';
		if (($boln == '') && ($eoln == "\n")) { // Values not specified, use rational defaults
			$CR = '<br />';
			$cr = '<br />';
			$eoln = $cr;
		} else { // Use values supplied
			$CR = $eoln.$boln;
			$cr = $CR;
		}
	} else {
		// Text Mode
		$CR = $eoln;
		$cr = $CR;
		$HR = '----------------------------------------';
		$hr = '----------------------------------------';
	}

	$statecomma = '';
	$streets = $street;
	if ($suburb != '')
		$streets = $street.$cr.$suburb;
	if ($firstname == '')
		$firstname = addslashes($address['name']);
	if ($country == '')
		$country = addslashes($address['country']);
	if ($state != '')
		$statecomma = $state.', ';

	$fmt = $address_format['format'];
	eval ("\$address = \"$fmt\";");
	$address = stripslashes($address);

	if ((ACCOUNT_COMPANY == 'true') && (xtc_not_null($company))) {
		$address = $company.$cr.$address;
	}

	return $address;
}

////////////////////////////////////////////////////////////////////////////////////////////////
//
// Function    : xtc_get_zone_code
//
// Arguments   : country           country code string
//               zone              state/province zone_id
//               def_state         default string if zone==0
//
// Return      : state_prov_code   state/province code
//
// Description : Function to retrieve the state/province code (as in FL for Florida etc)
//
////////////////////////////////////////////////////////////////////////////////////////////////
function xtc_get_zone_code($country, $zone, $def_state) {

	$state_prov_query = xtc_db_query("select zone_code from ".TABLE_ZONES." where zone_country_id = '".$country."' and zone_id = '".$zone."'");

	if (!xtc_db_num_rows($state_prov_query)) {
		$state_prov_code = $def_state;
	} else {
		$state_prov_values = xtc_db_fetch_array($state_prov_query);
		$state_prov_code = $state_prov_values['zone_code'];
	}

	return $state_prov_code;
}

function xtc_get_uprid($prid, $params) {
	$uprid = $prid;
	if ((is_array($params)) && (!strstr($prid, '{'))) {
		while (list ($option, $value) = each($params)) {
			$uprid = $uprid.'{'.$option.'}'.$value;
		}
	}

	return $uprid;
}

function xtc_get_prid($uprid) {
	$pieces = explode('{', $uprid);

	return $pieces[0];
}

function xtc_get_languages() {
// BOF - Tomcraft - 2009-11-08 - Added option to deactivate languages
	//$languages_query = xtc_db_query("select languages_id, name, code, image, directory from ".TABLE_LANGUAGES." order by sort_order");
	$languages_query = xtc_db_query("select languages_id, name, code, image, directory from ".TABLE_LANGUAGES." where status = '1' order by sort_order");
// EOF - Tomcraft - 2009-11-08 - Added option to deactivate languages
	while ($languages = xtc_db_fetch_array($languages_query)) {
		$languages_array[] = array (
			'id' => $languages['languages_id'],
			'name' => $languages['name'],
			'code' => $languages['code'],
			'image' => $languages['image'],
			'directory' => $languages['directory']
			);
	}

	return $languages_array;
}

function xtc_get_categories_name($category_id, $language_id) {
	$category_query = xtc_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$category_id."' and language_id = '".$language_id."'");
	$category = xtc_db_fetch_array($category_query);

	return $category['categories_name'];
}

function xtc_get_categories_heading_title($category_id, $language_id) {
	$category_query = xtc_db_query("select categories_heading_title from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$category_id."' and language_id = '".$language_id."'");
	$category = xtc_db_fetch_array($category_query);
	return $category['categories_heading_title'];
}

function xtc_get_categories_description($category_id, $language_id) {
	$category_query = xtc_db_query("select categories_description from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$category_id."' and language_id = '".$language_id."'");
	$category = xtc_db_fetch_array($category_query);

	return $category['categories_description'];
}

function xtc_get_categories_meta_title($category_id, $language_id) {
	$category_query = xtc_db_query("select categories_meta_title from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$category_id."' and language_id = '".$language_id."'");
	$category = xtc_db_fetch_array($category_query);

	return $category['categories_meta_title'];
}

function xtc_get_categories_meta_description($category_id, $language_id) {
	$category_query = xtc_db_query("select categories_meta_description from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$category_id."' and language_id = '".$language_id."'");
	$category = xtc_db_fetch_array($category_query);

	return $category['categories_meta_description'];
}

function xtc_get_categories_meta_keywords($category_id, $language_id) {
	$category_query = xtc_db_query("select categories_meta_keywords from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$category_id."' and language_id = '".$language_id."'");
	$category = xtc_db_fetch_array($category_query);

	return $category['categories_meta_keywords'];
}

function xtc_get_orders_status_name($orders_status_id, $language_id = '') {

	if (!$language_id)
		$language_id = $_SESSION['languages_id'];
	$orders_status_query = xtc_db_query("select orders_status_name from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$orders_status_id."' and language_id = '".$language_id."'");
	$orders_status = xtc_db_fetch_array($orders_status_query);

	return $orders_status['orders_status_name'];
}

function xtc_get_cross_sell_name($cross_sell_group, $language_id = '') {

	if (!$language_id)
		$language_id = $_SESSION['languages_id'];
	$cross_sell_query = xtc_db_query("select groupname from ".TABLE_PRODUCTS_XSELL_GROUPS." where products_xsell_grp_name_id = '".$cross_sell_group."' and language_id = '".$language_id."'");
	$cross_sell = xtc_db_fetch_array($cross_sell_query);

	return $cross_sell['groupname'];
}



function xtc_get_shipping_status_name($shipping_status_id, $language_id = '') {

	if (!$language_id)
		$language_id = $_SESSION['languages_id'];
	$shipping_status_query = xtc_db_query("select shipping_status_name from ".TABLE_SHIPPING_STATUS." where shipping_status_id = '".$shipping_status_id."' and language_id = '".$language_id."'");
	$shipping_status = xtc_db_fetch_array($shipping_status_query);

	return $shipping_status['shipping_status_name'];
}

function xtc_get_orders_status() {

	$orders_status_array = array ();
	$orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$_SESSION['languages_id']."' order by orders_status_id");
	while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
		$orders_status_array[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	}

	return $orders_status_array;
}

function xtc_get_cross_sell_groups() {

	$cross_sell_array = array ();
	$cross_sell_query = xtc_db_query("select products_xsell_grp_name_id, groupname from ".TABLE_PRODUCTS_XSELL_GROUPS." where language_id = '".$_SESSION['languages_id']."' order by products_xsell_grp_name_id");
	while ($cross_sell = xtc_db_fetch_array($cross_sell_query)) {
		$cross_sell_array[] = array ('id' => $cross_sell['products_xsell_grp_name_id'], 'text' => $cross_sell['groupname']);
	}

	return $cross_sell_array;
}

function xtc_get_products_vpe_name($products_vpe_id, $language_id = '') {

	if (!$language_id)
		$language_id = $_SESSION['languages_id'];
	$products_vpe_query = xtc_db_query("select products_vpe_name from ".TABLE_PRODUCTS_VPE." where products_vpe_id = '".$products_vpe_id."' and language_id = '".$language_id."'");
	$products_vpe = xtc_db_fetch_array($products_vpe_query);

	return $products_vpe['products_vpe_name'];
}

function xtc_get_shipping_status() {

	$shipping_status_array = array ();
	$shipping_status_query = xtc_db_query("select shipping_status_id, shipping_status_name from ".TABLE_SHIPPING_STATUS." where language_id = '".$_SESSION['languages_id']."' order by shipping_status_id");
	while ($shipping_status = xtc_db_fetch_array($shipping_status_query)) {
		$shipping_status_array[] = array ('id' => $shipping_status['shipping_status_id'], 'text' => $shipping_status['shipping_status_name']);
	}

	return $shipping_status_array;
}

function xtc_get_products_name($product_id, $language_id = 0) {

	if ($language_id == 0)
		$language_id = $_SESSION['languages_id'];
	$product_query = xtc_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$product_id."' and language_id = '".$language_id."'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_name'];
}

function xtc_get_products_description($product_id, $language_id) {
	$product_query = xtc_db_query("select products_description from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$product_id."' and language_id = '".$language_id."'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_description'];
}

function xtc_get_products_short_description($product_id, $language_id) {
	$product_query = xtc_db_query("select products_short_description from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$product_id."' and language_id = '".$language_id."'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_short_description'];
}

function xtc_get_products_keywords($product_id, $language_id) {
	$product_query = xtc_db_query("select products_keywords from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$product_id."' and language_id = '".$language_id."'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_keywords'];
}

function xtc_get_products_meta_title($product_id, $language_id) {
	$product_query = xtc_db_query("select products_meta_title from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$product_id."' and language_id = '".$language_id."'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_meta_title'];
}

function xtc_get_products_meta_description($product_id, $language_id) {
	$product_query = xtc_db_query("select products_meta_description from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$product_id."' and language_id = '".$language_id."'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_meta_description'];
}

function xtc_get_products_meta_keywords($product_id, $language_id) {
	$product_query = xtc_db_query("select products_meta_keywords from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$product_id."' and language_id = '".$language_id."'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_meta_keywords'];
}

function xtc_get_products_url($product_id, $language_id) {
	$product_query = xtc_db_query("select products_url from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$product_id."' and language_id = '".$language_id."'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_url'];
}

// Return the manufacturers URL in the needed language
// TABLES: manufacturers_info
function xtc_get_manufacturer_url($manufacturer_id, $language_id) {
	$manufacturer_query = xtc_db_query("select manufacturers_url from ".TABLE_MANUFACTURERS_INFO." where manufacturers_id = '".$manufacturer_id."' and languages_id = '".$language_id."'");
	$manufacturer = xtc_db_fetch_array($manufacturer_query);

	return $manufacturer['manufacturers_url'];
}

// Wrapper for class_exists() function
// This function is not available in all PHP versions so we test it before using it.
function xtc_class_exists($class_name) {
	if (function_exists('class_exists')) {
		return class_exists($class_name);
	} else {
		return true;
	}
}

// Returns an array with countries
// TABLES: countries
function xtc_get_countries($default = '') {
	$countries_array = array ();
	if ($default) {
		$countries_array[] = array ('id' => STORE_COUNTRY, 'text' => $default);
	}
	$countries_query = xtc_db_query("select countries_id, countries_name from ".TABLE_COUNTRIES." order by countries_name");
	while ($countries = xtc_db_fetch_array($countries_query)) {
		$countries_array[] = array ('id' => $countries['countries_id'], 'text' => $countries['countries_name']);
	}

	return $countries_array;
}

// return an array with country zones
function xtc_get_country_zones($country_id) {
	$zones_array = array ();
	$zones_query = xtc_db_query("select zone_id, zone_name from ".TABLE_ZONES." where zone_country_id = '".$country_id."' order by zone_name");
	while ($zones = xtc_db_fetch_array($zones_query)) {
		$zones_array[] = array ('id' => $zones['zone_id'], 'text' => $zones['zone_name']);
	}

	return $zones_array;
}

function xtc_prepare_country_zones_pull_down($country_id = '') {
		// preset the width of the drop-down for Netscape
	$pre = '';
	if ((!xtc_browser_detect('MSIE')) && (xtc_browser_detect('Mozilla/4'))) {
		for ($i = 0; $i < 45; $i ++)
			$pre .= '&nbsp;';
	}

	$zones = xtc_get_country_zones($country_id);

	if (sizeof($zones) > 0) {
		$zones_select = array (array ('id' => '', 'text' => PLEASE_SELECT));
		$zones = xtc_array_merge($zones_select, $zones);
	} else {
		$zones = array (array ('id' => '', 'text' => TYPE_BELOW));
		// create dummy options for Netscape to preset the height of the drop-down
		if ((!xtc_browser_detect('MSIE')) && (xtc_browser_detect('Mozilla/4'))) {
			for ($i = 0; $i < 9; $i ++) {
				$zones[] = array ('id' => '', 'text' => $pre);
			}
		}
	}

	return $zones;
}

// Get list of address_format_id's
function xtc_get_address_formats() {
	$address_format_query = xtc_db_query("select address_format_id from ".TABLE_ADDRESS_FORMAT." order by address_format_id");
	$address_format_array = array ();
	while ($address_format_values = xtc_db_fetch_array($address_format_query)) {
		$address_format_array[] = array ('id' => $address_format_values['address_format_id'], 'text' => $address_format_values['address_format_id']);
	}
	return $address_format_array;
}

// Alias function for Store configuration values in the Administration Tool
function xtc_cfg_pull_down_country_list($country_id) {
	return xtc_draw_pull_down_menu('configuration_value', xtc_get_countries(), $country_id);
}

function xtc_cfg_pull_down_zone_list($zone_id) {
	return xtc_draw_pull_down_menu('configuration_value', xtc_get_country_zones(STORE_COUNTRY), $zone_id);
}

function xtc_cfg_pull_down_tax_classes($tax_class_id, $key = '') {
	$name = (($key) ? 'configuration['.$key.']' : 'configuration_value');

	$tax_class_array = array (array ('id' => '0', 'text' => TEXT_NONE));
	$tax_class_query = xtc_db_query("select tax_class_id, tax_class_title from ".TABLE_TAX_CLASS." order by tax_class_title");
	while ($tax_class = xtc_db_fetch_array($tax_class_query)) {
		$tax_class_array[] = array ('id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']);
	}

	return xtc_draw_pull_down_menu($name, $tax_class_array, $tax_class_id);
}

// Function to read in text area in admin
//BOF - web28- 2010-07-06 - added missing code
//function xtc_cfg_textarea($text) {
	//return xtc_draw_textarea_field('configuration_value', false, 35, 5, $text);
//}
function xtc_cfg_textarea($text, $key = '') {
    $name = (isset($key) ? 'configuration[' . $key . ']' : 'configuration_value'); //DokuMan - set undefined $key
    return xtc_draw_textarea_field($name, false, 35, 3, $text, 'class="textareaModule"');
}
//EOF - web28- 2010-07-06 - added missing code

function xtc_cfg_get_zone_name($zone_id) {
	$zone_query = xtc_db_query("select zone_name from ".TABLE_ZONES." where zone_id = '".$zone_id."'");

	if (!xtc_db_num_rows($zone_query)) {
		return $zone_id;
	} else {
		$zone = xtc_db_fetch_array($zone_query);
		return $zone['zone_name'];
	}
}

// Sets the status of a banner
function xtc_set_banner_status($banners_id, $status) {
	if ($status == '1') {
		return xtc_db_query("update ".TABLE_BANNERS." set status = '1', expires_impressions = NULL, expires_date = NULL, date_status_change = NULL where banners_id = '".$banners_id."'");
	}
	elseif ($status == '0') {
		return xtc_db_query("update ".TABLE_BANNERS." set status = '0', date_status_change = now() where banners_id = '".$banners_id."'");
	} else {
		return -1;
	}
}

// Sets the status of a product on special
function xtc_set_specials_status($specials_id, $status) {
	if ($status == '1') {
		return xtc_db_query("update ".TABLE_SPECIALS." set status = '1', expires_date = NULL, date_status_change = NULL where specials_id = '".$specials_id."'");
	}
	elseif ($status == '0') {
		return xtc_db_query("update ".TABLE_SPECIALS." set status = '0', date_status_change = now() where specials_id = '".$specials_id."'");
	} else {
		return -1;
	}
}

// Sets timeout for the current script.
// Cant be used in safe mode.
function xtc_set_time_limit($limit) {
	if (!get_cfg_var('safe_mode')) {
		@ set_time_limit($limit);
	}
}

// Alias function for Store configuration values in the Administration Tool
function xtc_cfg_select_option($select_array, $key_value, $key = '') {
  $string = '';
	for ($i = 0, $n = sizeof($select_array); $i < $n; $i ++) {
		$name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
		// BOF vr - 2010-02-04 admin configuration pages 3 column layout - modified by web28 2010-06-09
		/*$string .= '<br /><input type="radio" name="'.$name.'" value="'.$select_array[$i].'"';
		if ($key_value == $select_array[$i])
			$string .= ' CHECKED';
		$string .= '> '.$select_array[$i];*/
		if ($i == 0)
			$string .= '<input type="radio" name="'.$name.'" value="'.$select_array[$i].'"';
		else
			$string .= '<br /><input type="radio" name="'.$name.'" value="'.$select_array[$i].'"';
		if ($key_value == $select_array[$i])
			$string .= ' CHECKED';
		$string .= '> '.$select_array[$i];
		// EOF vr - 2010-02-04 admin configuration pages 3 column layout - modified by web28 2010-06-09
	}

	return $string;
}

// Alias function for module configuration keys
function xtc_mod_select_option($select_array, $key_name, $key_value) {
	reset($select_array);
	while (list ($key, $value) = each($select_array)) {
		if (is_int($key))
			$key = $value;
		$string .= '<br /><input type="radio" name="configuration['.$key_name.']" value="'.$key.'"';
		if ($key_value == $key)
			$string .= ' CHECKED';
		$string .= '> '.$value;
	}

	return $string;
}

// Retreive server information
function xtc_get_system_information() {

	$db_query = xtc_db_query("select now() as datetime");
	$db = xtc_db_fetch_array($db_query);

	list ($system, $host, $kernel) = preg_split('/[\s,]+/', @ exec('uname -a'), 5);

	return array ('date' => xtc_datetime_short(date('Y-m-d H:i:s')), 'system' => $system, 'kernel' => $kernel, 'host' => $host, 'ip' => gethostbyname($host), 'uptime' => @ exec('uptime'), 'http_server' => $_SERVER['SERVER_SOFTWARE'], 'php' => PHP_VERSION, 'zend' => (function_exists('zend_version') ? zend_version() : ''), 'db_server' => DB_SERVER, 'db_ip' => gethostbyname(DB_SERVER), 'db_version' => 'MySQL '. (function_exists('mysql_get_server_info') ? mysql_get_server_info() : ''), 'db_date' => xtc_datetime_short($db['datetime']));
}

function xtc_array_shift(& $array) {
	if (function_exists('array_shift')) {
		return array_shift($array);
	} else {
		$i = 0;
		$shifted_array = array ();
		reset($array);
		while (list ($key, $value) = each($array)) {
			if ($i > 0) {
				$shifted_array[$key] = $value;
			} else {
				$return = $array[$key];
			}
			$i ++;
		}
		$array = $shifted_array;

		return $return;
	}
}

function xtc_array_reverse($array) {
	if (function_exists('array_reverse')) {
		return array_reverse($array);
	} else {
		$reversed_array = array ();
		for ($i = sizeof($array) - 1; $i >= 0; $i --) {
			$reversed_array[] = $array[$i];
		}
		return $reversed_array;
	}
}

function xtc_generate_category_path($id, $from = 'category', $categories_array = '', $index = 0) {

	if (!is_array($categories_array))
		$categories_array = array ();

	if ($from == 'product') {
		$categories_query = xtc_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id = '".$id."'");
		while ($categories = xtc_db_fetch_array($categories_query)) {
			if ($categories['categories_id'] == '0') {
				$categories_array[$index][] = array ('id' => '0', 'text' => TEXT_TOP);
			} else {
				$category_query = xtc_db_query("select cd.categories_name, c.parent_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = '".$categories['categories_id']."' and c.categories_id = cd.categories_id and cd.language_id = '".$_SESSION['languages_id']."'");
				$category = xtc_db_fetch_array($category_query);
				$categories_array[$index][] = array ('id' => $categories['categories_id'], 'text' => $category['categories_name']);
				if ((xtc_not_null($category['parent_id'])) && ($category['parent_id'] != '0'))
					$categories_array = xtc_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
				$categories_array[$index] = xtc_array_reverse($categories_array[$index]);
			}
			$index ++;
		}
	}
	elseif ($from == 'category') {
		$category_query = xtc_db_query("select cd.categories_name, c.parent_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = '".$id."' and c.categories_id = cd.categories_id and cd.language_id = '".$_SESSION['languages_id']."'");
		$category = xtc_db_fetch_array($category_query);
		$categories_array[$index][] = array ('id' => $id, 'text' => $category['categories_name']);
		if ((xtc_not_null($category['parent_id'])) && ($category['parent_id'] != '0'))
			$categories_array = xtc_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
	}

	return $categories_array;
}

function xtc_output_generated_category_path($id, $from = 'category') {
	$calculated_category_path_string = '';
	$calculated_category_path = xtc_generate_category_path($id, $from);
	for ($i = 0, $n = sizeof($calculated_category_path); $i < $n; $i ++) {
		for ($j = 0, $k = sizeof($calculated_category_path[$i]); $j < $k; $j ++) {
			$calculated_category_path_string .= $calculated_category_path[$i][$j]['text'].'&nbsp;&gt;&nbsp;';
		}
		$calculated_category_path_string = substr($calculated_category_path_string, 0, -16).'<br />';
	}
	$calculated_category_path_string = substr($calculated_category_path_string, 0, -6); //DokuMan - remove <br /> from description

	if (strlen($calculated_category_path_string) < 1)
		$calculated_category_path_string = TEXT_TOP;

	return $calculated_category_path_string;
}

//deletes all product image files by filename
function xtc_del_image_file($image) {
	if (file_exists(DIR_FS_CATALOG_POPUP_IMAGES.$image)) {
		@ unlink(DIR_FS_CATALOG_POPUP_IMAGES.$image);
	}
	if (file_exists(DIR_FS_CATALOG_ORIGINAL_IMAGES.$image)) {
		@ unlink(DIR_FS_CATALOG_ORIGINAL_IMAGES.$image);
	}
	if (file_exists(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image)) {
		@ unlink(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$image);
	}
	if (file_exists(DIR_FS_CATALOG_INFO_IMAGES.$image)) {
		@ unlink(DIR_FS_CATALOG_INFO_IMAGES.$image);
	}
}

function xtc_remove_order($order_id, $restock = false) {
	if ($restock == 'on') {
		$order_query = xtc_db_query("select products_id, products_quantity from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".xtc_db_input($order_id)."'");
		while ($order = xtc_db_fetch_array($order_query)) {
			xtc_db_query("update ".TABLE_PRODUCTS." set products_quantity = products_quantity + ".$order['products_quantity'].", products_ordered = products_ordered - ".$order['products_quantity']." where products_id = '".$order['products_id']."'");
		}
	}

	xtc_db_query("delete from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($order_id)."'");
	xtc_db_query("delete from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".xtc_db_input($order_id)."'");
	xtc_db_query("delete from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".xtc_db_input($order_id)."'");
	xtc_db_query("delete from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".xtc_db_input($order_id)."'");
	xtc_db_query("delete from ".TABLE_ORDERS_TOTAL." where orders_id = '".xtc_db_input($order_id)."'");
}

function xtc_reset_cache_block($cache_block) {
	global $cache_blocks;

	for ($i = 0, $n = sizeof($cache_blocks); $i < $n; $i ++) {
		if ($cache_blocks[$i]['code'] == $cache_block) {
			if ($cache_blocks[$i]['multiple']) {
				if ($dir = @ opendir(DIR_FS_CACHE)) {
					while ($cache_file = readdir($dir)) {
						$cached_file = $cache_blocks[$i]['file'];
						$languages = xtc_get_languages();
						for ($j = 0, $k = sizeof($languages); $j < $k; $j ++) {
							$cached_file_unlink = preg_replace('/-language/i', '-'.$languages[$j]['directory'], $cached_file); // Hetfield - 2009-08-19 - replaced deprecated function ereg_replace with preg_replace to be ready for PHP >= 5.3
							if (preg_match('/^'.$cached_file_unlink.'/', $cache_file)) { // Hetfield - 2009-08-19 - replaced deprecated function ereg with preg_match to be ready for PHP >= 5.3
								@ unlink(DIR_FS_CACHE.$cache_file);
							}
						}
					}
					closedir($dir);
				}
			} else {
				$cached_file = $cache_blocks[$i]['file'];
				$languages = xtc_get_languages();
				for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
					$cached_file = preg_replace('/-language/i', '-'.$languages[$i]['directory'], $cached_file); // Hetfield - 2009-08-19 - replaced deprecated function ereg_replace with preg_replace to be ready for PHP >= 5.3
					@ unlink(DIR_FS_CACHE.$cached_file);
				}
			}
			break;
		}
	}
}

function xtc_get_file_permissions($mode) {
	// determine type
	if (($mode & 0xC000) == 0xC000) { // unix domain socket
		$type = 's';
	}
	elseif (($mode & 0x4000) == 0x4000) { // directory
		$type = 'd';
	}
	elseif (($mode & 0xA000) == 0xA000) { // symbolic link
		$type = 'l';
	}
	elseif (($mode & 0x8000) == 0x8000) { // regular file
		$type = '-';
	}
	elseif (($mode & 0x6000) == 0x6000) { //bBlock special file
		$type = 'b';
	}
	elseif (($mode & 0x2000) == 0x2000) { // character special file
		$type = 'c';
	}
	elseif (($mode & 0x1000) == 0x1000) { // named pipe
		$type = 'p';
	} else { // unknown
		$type = '?';
	}

	// determine permissions
	$owner['read'] = ($mode & 00400) ? 'r' : '-';
	$owner['write'] = ($mode & 00200) ? 'w' : '-';
	$owner['execute'] = ($mode & 00100) ? 'x' : '-';
	$group['read'] = ($mode & 00040) ? 'r' : '-';
	$group['write'] = ($mode & 00020) ? 'w' : '-';
	$group['execute'] = ($mode & 00010) ? 'x' : '-';
	$world['read'] = ($mode & 00004) ? 'r' : '-';
	$world['write'] = ($mode & 00002) ? 'w' : '-';
	$world['execute'] = ($mode & 00001) ? 'x' : '-';

	// adjust for SUID, SGID and sticky bit
	if ($mode & 0x800)
		$owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
	if ($mode & 0x400)
		$group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
	if ($mode & 0x200)
		$world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

	return $type.$owner['read'].$owner['write'].$owner['execute'].$group['read'].$group['write'].$group['execute'].$world['read'].$world['write'].$world['execute'];
}

function xtc_array_slice($array, $offset, $length = '0') {
	if (function_exists('array_slice')) {
		return array_slice($array, $offset, $length);
	} else {
		$length = abs($length);
		if ($length == 0) {
			$high = sizeof($array);
		} else {
			$high = $offset + $length;
		}

		for ($i = $offset; $i < $high; $i ++) {
			$new_array[$i - $offset] = $array[$i];
		}

		return $new_array;
	}
}

function xtc_remove($source) {
	global $messageStack, $xtc_remove_error;

	if (isset ($xtc_remove_error))
		$xtc_remove_error = false;

	if (is_dir($source)) {
		$dir = dir($source);
		while ($file = $dir->read()) {
			if (($file != '.') && ($file != '..')) {
				if (is_writeable($source.'/'.$file)) {
					xtc_remove($source.'/'.$file);
				} else {
					$messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source.'/'.$file), 'error');
					$xtc_remove_error = true;
				}
			}
		}
		$dir->close();

		if (is_writeable($source)) {
			rmdir($source);
		} else {
			$messageStack->add(sprintf(ERROR_DIRECTORY_NOT_REMOVEABLE, $source), 'error');
			$xtc_remove_error = true;
		}
	} else {
		if (is_writeable($source)) {
			unlink($source);
		} else {
			$messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source), 'error');
			$xtc_remove_error = true;
		}
	}
}

// Wrapper for constant() function
// Needed because its only available in PHP 4.0.4 and higher.
function xtc_constant($constant) {
	if (function_exists('constant')) {
		$temp = constant($constant);
	} else {
		eval ("\$temp=$constant;");
	}
	return $temp;
}

// Output the tax percentage with optional padded decimals
function xtc_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
	if (strpos($value, '.')) {
		$loop = true;
		while ($loop) {
			if (substr($value, -1) == '0') {
				$value = substr($value, 0, -1);
			} else {
				$loop = false;
				if (substr($value, -1) == '.') {
					$value = substr($value, 0, -1);
				}
			}
		}
	}

	if ($padding > 0) {
		if ($decimal_pos = strpos($value, '.')) {
			$decimals = strlen(substr($value, ($decimal_pos +1)));
			for ($i = $decimals; $i < $padding; $i ++) {
				$value .= '0';
			}
		} else {
			$value .= '.';
			for ($i = 0; $i < $padding; $i ++) {
				$value .= '0';
			}
		}
	}

	return $value;
}

function xtc_get_tax_class_title($tax_class_id) {
	if ($tax_class_id == '0') {
		return TEXT_NONE;
	} else {
		$classes_query = xtc_db_query("select tax_class_title from ".TABLE_TAX_CLASS." where tax_class_id = '".$tax_class_id."'");
		$classes = xtc_db_fetch_array($classes_query);

		return $classes['tax_class_title'];
	}
}

function xtc_banner_image_extension() {
	if (function_exists('imagetypes')) {
		if (imagetypes() & IMG_PNG) {
			return 'png';
		}
		elseif (imagetypes() & IMG_JPG) {
			return 'jpg';
		}
		elseif (imagetypes() & IMG_GIF) {
			return 'gif';
		}
	}
	elseif (function_exists('imagecreatefrompng') && function_exists('imagepng')) {
		return 'png';
	}
	elseif (function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) {
		return 'jpg';
	}
	elseif (function_exists('imagecreatefromgif') && function_exists('imagegif')) {
		return 'gif';
	}

	return false;
}

// Wrapper function for round()
function xtc_round($value, $precision) {
	return round($value, $precision);
}

// Calculates Tax rounding the result
function xtc_calculate_tax($price, $tax) {
	global $currencies;
	return xtc_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
}

function xtc_call_function($function, $parameter, $object = '') {
	if ($object == '') {
		return call_user_func($function, $parameter);
	} else {
		return call_user_func(array ($object, $function), $parameter);
	}
}

function xtc_get_zone_class_title($zone_class_id) {
	if ($zone_class_id == '0') {
		return TEXT_NONE;
	} else {
		$classes_query = xtc_db_query("select geo_zone_name from ".TABLE_GEO_ZONES." where geo_zone_id = '".$zone_class_id."'");
		$classes = xtc_db_fetch_array($classes_query);

		return $classes['geo_zone_name'];
	}
}

function xtc_cfg_pull_down_template_sets() {
	$name = (isset($key) ? 'configuration['.$key.']' : 'configuration_value'); //DokuMan - set undefined $key
	if ($dir = opendir(DIR_FS_CATALOG.'templates/')) {
		while (($templates = readdir($dir)) !== false) {
			if (is_dir(DIR_FS_CATALOG.'templates/'."//".$templates) and ($templates != "CVS") and ($templates != ".") and ($templates != "..")) {
				$templates_array[] = array ('id' => $templates, 'text' => $templates);
			}
		}
		closedir($dir);
		sort($templates_array);
		return xtc_draw_pull_down_menu($name, $templates_array, CURRENT_TEMPLATE);
	}
}

function xtc_cfg_pull_down_zone_classes($zone_class_id, $key = '') {
	$name = (($key) ? 'configuration['.$key.']' : 'configuration_value');

	$zone_class_array = array (array ('id' => '0', 'text' => TEXT_NONE));
	$zone_class_query = xtc_db_query("select geo_zone_id, geo_zone_name from ".TABLE_GEO_ZONES." order by geo_zone_name");
	while ($zone_class = xtc_db_fetch_array($zone_class_query)) {
		$zone_class_array[] = array ('id' => $zone_class['geo_zone_id'], 'text' => $zone_class['geo_zone_name']);
	}

	return xtc_draw_pull_down_menu($name, $zone_class_array, $zone_class_id);
}

function xtc_cfg_pull_down_order_statuses($order_status_id, $key = '') {

	$name = (($key) ? 'configuration['.$key.']' : 'configuration_value');

	$statuses_array = array (array ('id' => '1', 'text' => TEXT_DEFAULT));
	$statuses_query = xtc_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$_SESSION['languages_id']."' order by orders_status_name");
	while ($statuses = xtc_db_fetch_array($statuses_query)) {
		$statuses_array[] = array ('id' => $statuses['orders_status_id'], 'text' => $statuses['orders_status_name']);
	}

	return xtc_draw_pull_down_menu($name, $statuses_array, $order_status_id);
}

function xtc_get_order_status_name($order_status_id, $language_id = '') {

	if ($order_status_id < 1)
		return TEXT_DEFAULT;

	if (!is_numeric($language_id))
		$language_id = $_SESSION['languages_id'];

	$status_query = xtc_db_query("select orders_status_name from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$order_status_id."' and language_id = '".$language_id."'");
	$status = xtc_db_fetch_array($status_query);

	return $status['orders_status_name'];
}

// Return a random value
function xtc_rand($min = null, $max = null) {
	static $seeded;

	if (!$seeded) {
		mt_srand((double) microtime() * 1000000);
		$seeded = true;
	}

	if (isset ($min) && isset ($max)) {
		if ($min >= $max) {
			return $min;
		} else {
			return mt_rand($min, $max);
		}
	} else {
		return mt_rand();
	}
}

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
function xtc_convert_linefeeds($from, $to, $string) {
	if ((PHP_VERSION < "4.0.5") && is_array($from)) {
		return preg_replace('/('.implode('|', $from).')/', $to, $string); // Hetfield - 2009-08-19 - replaced deprecated function ereg_replace with preg_replace to be ready for PHP >= 5.3
	} else {
		return str_replace($from, $to, $string);
	}
}

// Return all customers statuses for a specified language_id and return an array(array())
// Use it to make pull_down_menu, checkbox....
function xtc_get_customers_statuses() {

	$customers_statuses_array = array (array ());
	//BOF - DokuMan - 2010-11-01 - Added missing fields customers_status_public and customers_status_graduated_prices
	$customers_statuses_query = xtc_db_query("select
                                            customers_status_id,
                                            customers_status_name,
                                            customers_status_public,
                                            customers_status_image,
                                            customers_status_discount,
                                            customers_status_ot_discount_flag,
                                            customers_status_ot_discount,
                                            customers_status_graduated_prices
                                            from ".TABLE_CUSTOMERS_STATUS."
                                            where language_id = '".$_SESSION['languages_id']."'
                                            order by customers_status_id");
	//EOF - DokuMan - 2010-11-01 - Added missing fields customers_status_public and customers_status_graduated_prices

	$i = 1; // this is changed from 0 to 1 in cs v1.2
	while ($customers_statuses = xtc_db_fetch_array($customers_statuses_query)) {
		$i = $customers_statuses['customers_status_id'];
		$customers_statuses_array[$i] = array (
		'id' => $customers_statuses['customers_status_id'],
		'text' => $customers_statuses['customers_status_name'],
		'csa_public' => $customers_statuses['customers_status_public'],
		'csa_image' => $customers_statuses['customers_status_image'],
		'csa_discount' => $customers_statuses['customers_status_discount'],
		'csa_ot_discount_flag' => $customers_statuses['customers_status_ot_discount_flag'],
		'csa_ot_discount' => $customers_statuses['customers_status_ot_discount'],
		'csa_graduated_prices' => $customers_statuses['customers_status_graduated_prices']
		);
	}
	return $customers_statuses_array;
}

function xtc_get_customer_status($customers_id) {

	$customer_status_array = array ();
	$customer_status_query = xtc_db_query("select customers_status, member_flag, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices  FROM ".TABLE_CUSTOMERS." left join ".TABLE_CUSTOMERS_STATUS." on customers_status = customers_status_id where customers_id='".$customers_id."' and language_id = '".$_SESSION['languages_id']."'");
	$customer_status_array = xtc_db_fetch_array($customer_status_query);
	return $customer_status_array;
}

function xtc_get_customers_status_name($customers_status_id, $language_id = '') {

	if (!$language_id)
		$language_id = $_SESSION['languages_id'];
	$customers_status_query = xtc_db_query("select customers_status_name from ".TABLE_CUSTOMERS_STATUS." where customers_status_id = '".$customers_status_id."' and language_id = '".$language_id."'");
	$customers_status = xtc_db_fetch_array($customers_status_query);
	return $customers_status['customers_status_name'];
}

//to set customers status in admin for default value, newsletter, guest...
function xtc_cfg_pull_down_customers_status_list($customers_status_id, $key = '') {
	$name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
	return xtc_draw_pull_down_menu($name, xtc_get_customers_statuses(), $customers_status_id);
}

// Function for collecting ip
// return all log info for a customer_id
function xtc_get_user_info($customer_id) {
	$user_info_array = xtc_db_query("select customers_ip, customers_ip_date, customers_host, customers_advertiser, customers_referer_url FROM ".TABLE_CUSTOMERS_IP." where customers_id = '".$customer_id."'");
	return $user_info_array;
}

//---------------------------------------------------------------kommt wieder raus spaeter!!
function xtc_get_uploaded_file($filename) {
	if (isset ($_FILES[$filename])) {
		$uploaded_file = array ('name' => $_FILES[$filename]['name'], 'type' => $_FILES[$filename]['type'], 'size' => $_FILES[$filename]['size'], 'tmp_name' => $_FILES[$filename]['tmp_name']);
	}
	elseif (isset ($_FILES[$filename])) {
		$uploaded_file = array ('name' => $_FILES[$filename]['name'], 'type' => $_FILES[$filename]['type'], 'size' => $_FILES[$filename]['size'], 'tmp_name' => $_FILES[$filename]['tmp_name']);
	} else {
		$uploaded_file = array ('name' => $GLOBALS[$filename.'_name'], 'type' => $GLOBALS[$filename.'_type'], 'size' => $GLOBALS[$filename.'_size'], 'tmp_name' => $GLOBALS[$filename]);
	}

	return $uploaded_file;
}

function get_group_price($group_id, $product_id) {
	// well, first try to get group price from database
	$group_price_query = xtc_db_query("SELECT personal_offer FROM ".TABLE_PERSONAL_OFFERS_BY.$group_id." WHERE products_id = '".$product_id."' and quantity=1");
	$group_price_data = xtc_db_fetch_array($group_price_query);
	// if we found a price, everything is ok if not, we will create new entry

	// if there is no entry, create one. if there are more entries. keep one, dropp rest.
	if (!xtc_db_num_rows($group_price_query)) {
		xtc_db_query("INSERT INTO ".TABLE_PERSONAL_OFFERS_BY.$group_id." (price_id, products_id, quantity, personal_offer) VALUES ('', '".$product_id."', '1', '0.00')");
		$group_price_query = xtc_db_query("SELECT personal_offer FROM ".TABLE_PERSONAL_OFFERS_BY.$group_id." WHERE products_id = '".$product_id."' ORDER BY quantity ASC");
		$group_price_data = xtc_db_fetch_array($group_price_query);

	} else
		if (xtc_db_num_rows($group_price_query) > 1) {
			while ($data = xtc_db_fetch_array($group_price_query)) {
				$group_price_data['personal_offer'] = $data['personal_offer'];
			}
			xtc_db_query("DELETE FROM ".TABLE_PERSONAL_OFFERS_BY.$group_id." WHERE products_id='".$product_id."' and quantity=1");
			xtc_db_query("INSERT INTO ".TABLE_PERSONAL_OFFERS_BY.$group_id." (price_id, products_id, quantity, personal_offer) VALUES ('', '".$product_id."', '1', '".$group_price_data['personal_offer']."')");
			$group_price_query = xtc_db_query("SELECT personal_offer FROM ".TABLE_PERSONAL_OFFERS_BY.$group_id." WHERE products_id = '".$product_id."' ORDER BY quantity ASC");
			$group_price_data = xtc_db_fetch_array($group_price_query);
		}

	return $group_price_data['personal_offer'];
}

function format_price($price_string, $price_special, $currency, $allow_tax, $tax_rate) {
	// calculate currencies
	$currencies_query = xtc_db_query("SELECT
	                                          symbol_left,
	                                          symbol_right,
	                                          decimal_places,
	                                          value
	                                      FROM
	                                          ".TABLE_CURRENCIES."
	                                      WHERE
	                                          code = '".$currency."'");
	$currencies_value = xtc_db_fetch_array($currencies_query);
	$currencies_data = array ();
	$currencies_data = array ('SYMBOL_LEFT' => $currencies_value['symbol_left'], 'SYMBOL_RIGHT' => $currencies_value['symbol_right'], 'DECIMAL_PLACES' => $currencies_value['decimal_places'], 'VALUE' => $currencies_value['value']);

	// round price
	if ($allow_tax == 1)
		$price_string = $price_string / ((100 + $tax_rate) / 100);
	$price_string = precision($price_string, $currencies_data['DECIMAL_PLACES']);
	if ($price_special == '1') {
		$price_string = $currencies_data['SYMBOL_LEFT'].' '.$price_string.' '.$currencies_data['SYMBOL_RIGHT'];
	}
	return $price_string;
}

function precision($number, $places) {
	$number = number_format($number, $places, '.', '');
	return $number;
}

function xtc_get_lang_definition($search_lang, $lang_array, $modifier) {
	$search_lang = $search_lang.$modifier;
	return $lang_array[$search_lang];
}

function xtc_CheckExt($filename, $ext) {
	$passed = FALSE;
	$testExt = "\.".$ext."$";
	if (preg_match('/'.$testExt.'/i', $filename)) { // Hetfield - 2009-08-19 - replaced deprecated function eregi with preg_match to be ready for PHP >= 5.3
		$passed = TRUE;
	}
	return $passed;
}

function xtc_get_status_users($status_id) {
	$status_query = xtc_db_query("SELECT count(customers_status) as count FROM ".TABLE_CUSTOMERS." WHERE customers_status = '".$status_id."'");
	$status_data = xtc_db_fetch_array($status_query);
	return $status_data['count'];
}
function xtc_mkdirs($path, $perm) {

	if (is_dir($path)) {
		return true;
	} else {

		//$path=dirname($path);
		if (!mkdir($path, $perm))
			return false;
		mkdir($path, $perm);
		return true;
	}
}

function xtc_spaceUsed($dir) {
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (is_dir($dir.$file) && $file != '.' && $file != '..') {
					xtc_spaceUsed($dir.$file.'/');
				} else {
					$GLOBALS['total'] += filesize($dir.$file);
				}
			}
			closedir($dh);
		}
	}
}

function create_coupon_code($salt = "secret", $length = SECURITY_CODE_LENGTH) {
	$ccid = md5(uniqid("", "salt"));
	$ccid .= md5(uniqid("", "salt"));
	$ccid .= md5(uniqid("", "salt"));
	$ccid .= md5(uniqid("", "salt"));
	srand((double) microtime() * 1000000); // seed the random number generator
	$random_start = @ rand(0, (128 - $length));
	$good_result = 0;
	while ($good_result == 0) {
		$id1 = substr($ccid, $random_start, $length);
		$query = xtc_db_query("select coupon_code from ".TABLE_COUPONS." where coupon_code = '".$id1."'");
		if (xtc_db_num_rows($query) == 0)
			$good_result = 1;
	}
	return $id1;
}

// Update the Customers GV account
function xtc_gv_account_update($customer_id, $gv_id) {
	$customer_gv_query = xtc_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$customer_id."'");
	$coupon_gv_query = xtc_db_query("select coupon_amount from ".TABLE_COUPONS." where coupon_id = '".$gv_id."'");
	$coupon_gv = xtc_db_fetch_array($coupon_gv_query);
	if (xtc_db_num_rows($customer_gv_query) > 0) {
		$customer_gv = xtc_db_fetch_array($customer_gv_query);
		$new_gv_amount = $customer_gv['amount'] + $coupon_gv['coupon_amount'];
		$gv_query = xtc_db_query("update ".TABLE_COUPON_GV_CUSTOMER." set amount = '".$new_gv_amount."' where customer_id = '".$customer_id."'");
	} else {
		$gv_query = xtc_db_query("insert into ".TABLE_COUPON_GV_CUSTOMER." (customer_id, amount) values ('".$customer_id."', '".$coupon_gv['coupon_amount']."')");
	}
}

// Output a day/month/year dropdown selector
function xtc_draw_date_selector($prefix, $date = '') {
	$month_array = array ();
	$month_array[1] = _JANUARY;
	$month_array[2] = _FEBRUARY;
	$month_array[3] = _MARCH;
	$month_array[4] = _APRIL;
	$month_array[5] = _MAY;
	$month_array[6] = _JUNE;
	$month_array[7] = _JULY;
	$month_array[8] = _AUGUST;
	$month_array[9] = _SEPTEMBER;
	$month_array[10] = _OCTOBER;
	$month_array[11] = _NOVEMBER;
	$month_array[12] = _DECEMBER;
	$usedate = getdate($date);
	$day = $usedate['mday'];
	$month = $usedate['mon'];
	$year = $usedate['year'];
	$date_selector = '<select name="'.$prefix.'_day">';
	for ($i = 1; $i < 32; $i ++) {
		$date_selector .= '<option value="'.$i.'"';
		if ($i == $day)
			$date_selector .= 'selected';
		$date_selector .= '>'.$i.'</option>';
	}
	$date_selector .= '</select>';
	$date_selector .= '<select name="'.$prefix.'_month">';
	for ($i = 1; $i < 13; $i ++) {
		$date_selector .= '<option value="'.$i.'"';
		if ($i == $month)
			$date_selector .= 'selected';
		$date_selector .= '>'.$month_array[$i].'</option>';
	}
	$date_selector .= '</select>';
	$date_selector .= '<select name="'.$prefix.'_year">';
	for ($i = 2001; $i < 2019; $i ++) {
		$date_selector .= '<option value="'.$i.'"';
		if ($i == $year)
			$date_selector .= 'selected';
		$date_selector .= '>'.$i.'</option>';
	}
	$date_selector .= '</select>';
	return $date_selector;
}

function xtc_getDownloads() {

	$files = array ();

	$dir = DIR_FS_CATALOG.'download/';
	if ($fp = opendir($dir)) {
		while ($file = readdir($fp)) {
			if (is_file($dir.$file) && $file != '.htaccess') {
				$size = filesize($dir.$file);
				$files[] = array ('id' => $file, 'text' => $file.' | '.xtc_format_filesize($size), 'size' => $size, 'date' => date("F d Y H:i:s.", filemtime($dir.$file)));
			} //if
		} // while
		closedir($fp);
	}
	return $files;
}

function xtc_try_upload($file = '', $destination = '', $permissions = '777', $extensions = '') {
	$file_object = new upload($file, $destination, $permissions, $extensions);
	if ($file_object->filename != '')
		return $file_object;
	else
		return false;
}

function xtc_button($value, $type='submit', $parameter='') {
   return '<input type="'.$type.'" class="button" onclick="this.blur();" value="' . $value . '" ' . $parameter . ' >';
}

function xtc_button_link($value, $href='javascript:void(null)', $parameter='') {
   return '<a href="'.$href.'" class="button" onclick="this.blur()" '.$parameter.' >'.$value.'</a>';
}
?>