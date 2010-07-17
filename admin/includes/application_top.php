<?php
/* --------------------------------------------------------------
   $Id: application_top.php 1323 2005-10-27 17:58:08Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.158 2003/03/22); www.oscommerce.com
   (c) 2003	 nextcommerce (application_top.php,v 1.46 2003/08/24); www.nextcommerce.org 

   Released under the GNU General Public License 
   --------------------------------------------------------------
   Third Party contribution:

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  // Start the clock for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());
  
  // security
  define('_VALID_XTC',true);

  // Set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE);

  // Disable use_trans_sid as xtc_href_link() does this manually
  if (function_exists('ini_set')) {
    ini_set('session.use_trans_sid', 0);
  }

  // Set the local configuration parameters - mainly for developers or the main-configure
  if (file_exists('includes/local/configure.php')) {
    include('includes/local/configure.php');
  } else {
    require('includes/configure.php');
  }

// BOF - Tomcraft - 2009-11-08 - FIX for PHP5.3 date_default_timezone_set
  if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
	date_default_timezone_set('Europe/Berlin');
  }
// EOF - Tomcraft - 2009-11-08 - FIX for PHP5.3 date_default_timezone_set

  define('SQL_CACHEDIR',DIR_FS_CATALOG.'cache/');

  // Define the project version
  define('PROJECT_VERSION', 'xtcModified v1.05 dated: 2010-07-18');

// BOF - Tomcraft - 2009-11-09 - Added missing definition for TAX_DECIMAL_PLACES
  define('TAX_DECIMAL_PLACES', 0);
// EOF - Tomcraft - 2009-11-09 - Added missing definition for TAX_DECIMAL_PLACES

  // Set the length of the redeem code, the longer the more secure
  define('SECURITY_CODE_LENGTH', '6');

  // Used in the "Backup Manager" to compress backups
  define('LOCAL_EXE_GZIP', '/usr/bin/gzip');
  define('LOCAL_EXE_GUNZIP', '/usr/bin/gunzip');
  define('LOCAL_EXE_ZIP', '/usr/local/bin/zip');
  define('LOCAL_EXE_UNZIP', '/usr/local/bin/unzip');

  // define the filenames used in the project
  define('FILENAME_ACCOUNTING', 'accounting.php');
  define('FILENAME_BACKUP', 'backup.php');
  define('FILENAME_BANNER_MANAGER', 'banner_manager.php');
  define('FILENAME_BANNER_STATISTICS', 'banner_statistics.php');
  define('FILENAME_CACHE', 'cache.php');
  define('FILENAME_CAMPAIGNS', 'campaigns.php');
  define('FILENAME_CATALOG_ACCOUNT_HISTORY_INFO', 'account_history_info.php');
  define('FILENAME_CATALOG_NEWSLETTER', 'newsletter.php');
  define('FILENAME_CATEGORIES', 'categories.php');
  define('FILENAME_CONFIGURATION', 'configuration.php');
  define('FILENAME_COUNTRIES', 'countries.php');
  define('FILENAME_CURRENCIES', 'currencies.php');
  define('FILENAME_CUSTOMERS', 'customers.php');
  define('FILENAME_CUSTOMERS_STATUS', 'customers_status.php');
  define('FILENAME_DEFAULT', 'start.php');
  define('FILENAME_DEFINE_LANGUAGE', 'define_language.php');
  define('FILENAME_FORMS', 'forms.php');
  define('FILENAME_FORM_VALUES', 'form_values.php');
  define('FILENAME_GEO_ZONES', 'geo_zones.php');
  define('FILENAME_LANGUAGES', 'languages.php');
  define('FILENAME_MAIL', 'mail.php');
  define('FILENAME_MANUFACTURERS', 'manufacturers.php');
  define('FILENAME_MODULES', 'modules.php');
  define('FILENAME_ORDERS', 'orders.php');
  define('FILENAME_ORDERS_INVOICE', 'invoice.php');
  define('FILENAME_ORDERS_PACKINGSLIP', 'packingslip.php');
  define('FILENAME_ORDERS_STATUS', 'orders_status.php');
  define('FILENAME_ORDERS_EDIT', 'orders_edit.php');
  define('FILENAME_POPUP_IMAGE', 'popup_image.php');
  define('FILENAME_PRODUCTS_ATTRIBUTES', 'products_attributes.php');
  define('FILENAME_PRODUCTS_EXPECTED', 'products_expected.php');
  define('FILENAME_REVIEWS', 'reviews.php');
  define('FILENAME_SERVER_INFO', 'server_info.php');
  define('FILENAME_SHIPPING_MODULES', 'shipping_modules.php');
  define('FILENAME_SPECIALS', 'specials.php');
  define('FILENAME_STATS_CUSTOMERS', 'stats_customers.php');
  define('FILENAME_STATS_PRODUCTS_PURCHASED', 'stats_products_purchased.php');
  define('FILENAME_STATS_PRODUCTS_VIEWED', 'stats_products_viewed.php');
  define('FILENAME_TAX_CLASSES', 'tax_classes.php');
  define('FILENAME_TAX_RATES', 'tax_rates.php');
  define('FILENAME_WHOS_ONLINE', 'whos_online.php');
  define('FILENAME_ZONES', 'zones.php');
  define('FILENAME_START', 'start.php');
  define('FILENAME_STATS_STOCK_WARNING', 'stats_stock_warning.php');
  define('FILENAME_TPL_BOXES','templates_boxes.php');
  define('FILENAME_TPL_MODULES','templates_modules.php');
  define('FILENAME_NEW_ATTRIBUTES','new_attributes.php');
  define('FILENAME_LOGOUT','../logoff.php');
  define('FILENAME_LOGIN','../login.php');
  define('FILENAME_CREATE_ACCOUNT','create_account.php');
  define('FILENAME_CREATE_ACCOUNT_SUCCESS','create_account_success.php');
  define('FILENAME_CUSTOMER_MEMO','customer_memo.php');
  define('FILENAME_CONTENT_MANAGER','content_manager.php');
  define('FILENAME_CONTENT_PREVIEW','content_preview.php');
  define('FILENAME_SECURITY_CHECK','security_check.php');
  define('FILENAME_PRINT_ORDER','print_order.php');
  define('FILENAME_CREDITS','credits.php');
  define('FILENAME_PRINT_PACKINGSLIP','print_packingslip.php');
  define('FILENAME_MODULE_NEWSLETTER','module_newsletter.php');
  define('FILENAME_GV_QUEUE', 'gv_queue.php');
  define('FILENAME_GV_MAIL', 'gv_mail.php');
  define('FILENAME_GV_SENT', 'gv_sent.php');
  define('FILENAME_COUPON_ADMIN', 'coupon_admin.php');
  define('FILENAME_POPUP_MEMO', 'popup_memo.php');
  define('FILENAME_SHIPPING_STATUS', 'shipping_status.php');
  define('FILENAME_SALES_REPORT','stats_sales_report.php');
  define('FILENAME_MODULE_EXPORT','module_export.php');
  define('FILENAME_EASY_POPULATE','easypopulate.php');
  define('FILENAME_BLACKLIST', 'blacklist.php');
  define('FILENAME_PRODUCTS_VPE','products_vpe.php');
  define('FILENAME_CAMPAIGNS_REPORT','stats_campaigns.php');
  define('FILENAME_XSELL_GROUPS','cross_sell_groups.php');

  // GOOGLE SITEMAP - JUNG GESTALTEN - 07.10.2008
  define('FILENAME_GOOGLE_SITEMAP', '../google_sitemap.php'); 
  
  // BOF - web28 - 2010-05-06 - PayPal API Modul
  define('FILENAME_PAYPAL','paypal.php');
  define('FILENAME_PAYPAL_CHECKOUT', 'paypal_checkout.php');
  // EOF - web28 - 2010-05-06 - PayPal API Modul

  // define the database table names used in the project
  // BOF - 2010-01-20 - vr - revised unified version based on database_tables.php and admin/incudes/application_top.php,
  // list of TABLE MAPPINGS is now MAINTAINED in database_tables.php ONLY
  require_once('../' . DIR_WS_INCLUDES . 'database_tables.php');
  // BOF - 2010-01-20 vr - revised unified version based on database_tables.php and admin/incudes/application_top.php,

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_close.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_error.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_queryCached.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_perform.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_data_seek.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_insert_id.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_free_result.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_fetch_fields.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_output.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_input.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_prepare_input.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_ip_address.inc.php');
  require_once(DIR_FS_INC . 'xtc_setcookie.inc.php');
  require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
  require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
  require_once(DIR_FS_INC . 'xtc_add_tax.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_qty.inc.php');
  require_once(DIR_FS_INC . 'xtc_product_link.inc.php');
  require_once(DIR_FS_INC . 'xtc_cleanName.inc.php');


  // customization for the design layout
  define('BOX_WIDTH', 125); // how wide the boxes should be in pixels (default: 125)

  // Define how do we update currency exchange rates
  // Possible values are 'oanda' 'xe' or ''
  define('CURRENCY_SERVER_PRIMARY', 'oanda');
  define('CURRENCY_SERVER_BACKUP', 'xe');
  
  // Use the DB-Logger
  define('STORE_DB_TRANSACTIONS', 'false');

  // include the database functions
//  require(DIR_WS_FUNCTIONS . 'database.php');

  // make a connection to the database... now
  xtc_db_connect() or die('Unable to connect to database server!');

  // set application wide parameters
  $configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION . '');
// Paypal API Modul �nderungen - Cache im Admin AUS!
  while ($configuration = xtc_db_fetch_array($configuration_query)) {
    if($configuration['cfgKey']=='DB_CACHE'):
      define("DB_CACHE", "false");
    else:
      define($configuration['cfgKey'], $configuration['cfgValue']);
    endif;
  }

  define('FILENAME_IMAGEMANIPULATOR',IMAGE_MANIPULATOR);
    function xtDBquery($query) {
       if (DB_CACHE=='true') {
             $result=xtc_db_queryCached($query);
             //echo 'cached query: '.$query.'<br />';
          } else {
             $result=xtc_db_query($query);
    }
    return $result;
  }


  // initialize the logger class
  require(DIR_WS_CLASSES . 'logger.php');

  // include shopping cart class
  require(DIR_WS_CLASSES . 'shopping_cart.php');

  // some code to solve compatibility issues
  require(DIR_WS_FUNCTIONS . 'compatibility.php');

  require(DIR_WS_FUNCTIONS . 'general.php');


  // define how the session functions will be used
  require(DIR_WS_FUNCTIONS . 'sessions.php');

    // define our general functions used application-wide
  require(DIR_WS_FUNCTIONS . 'html_output.php');

  // set the session name and save path
  session_name('XTCsid');
	if (STORE_SESSIONS != 'mysql') session_save_path(SESSION_WRITE_DIRECTORY);

  // set the session cookie parameters
  if (function_exists('session_set_cookie_params')) {
    session_set_cookie_params(0, '/', (xtc_not_null($current_domain) ? '.' . $current_domain : ''));
  } elseif (function_exists('ini_set')) {
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_domain', (xtc_not_null($current_domain) ? '.' . $current_domain : ''));
  }

  // set the session ID if it exists
  if (isset($_POST[session_name()])) {
    session_id($_POST[session_name()]);
  } elseif ( ($request_type == 'SSL') && isset($_GET[session_name()]) ) {
    session_id($_GET[session_name()]);
  }

  // start the session
  $session_started = false;
  if (SESSION_FORCE_COOKIE_USE == 'True') {
    xtc_setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*30, '/', $current_domain);

	//BOF - Hetfield - 2009-08-16 - fix for some admin-login problems
	//if (isset($HTTP_COOKIE_VARS['cookie_test'])) {
	if (isset($_COOKIE['cookie_test'])) {
	//EOF - Hetfield - 2009-08-16 - fix for some admin-login problems
      session_start();
      $session_started = true;
    }
  } elseif (CHECK_CLIENT_AGENT == 'True') {
    $user_agent = strtolower(getenv('HTTP_USER_AGENT'));
    $spider_flag = false;

    if ($spider_flag == false) {
      session_start();
      $session_started = true;
    }
  } else {
    session_start();
    $session_started = true;
  }

  // verify the ssl_session_id if the feature is enabled
  if ( ($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true) ) {
    $ssl_session_id = getenv('SSL_SESSION_ID');
    if (!isset($_SESSION['SESSION_SSL_ID'])) {  // Hetfield - 2009-08-19 - removed deprecated function session_is_registered to be ready for PHP >= 5.3
      $_SESSION['SESSION_SSL_ID'] = $ssl_session_id;
    }

    if ($_SESSION['SESSION_SSL_ID'] != $ssl_session_id) {
      session_destroy();
      xtc_redirect(xtc_href_link(FILENAME_SSL_CHECK));
    }
  }

  // verify the browser user agent if the feature is enabled
if (SESSION_CHECK_USER_AGENT == 'True') {
	$http_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$http_user_agent2 = strtolower(getenv("HTTP_USER_AGENT"));
	$http_user_agent = ($http_user_agent == $http_user_agent2) ? $http_user_agent : $http_user_agent.';'.$http_user_agent2;
	if (!isset($_SESSION['SESSION_USER_AGENT'])) {
		$_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
	}

	if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
		session_destroy();
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	} 
}


  // verify the IP address if the feature is enabled
  if (SESSION_CHECK_IP_ADDRESS == 'True') {
    $ip_address = xtc_get_ip_address();
    if (!isset($_SESSION['SESSION_IP_ADDRESS'])) { // Hetfield - 2009-08-19 - removed deprecated function session_is_registered to be ready for PHP >= 5.3
      $_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
    }

    if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
      session_destroy();
      xtc_redirect(xtc_href_link(FILENAME_LOGIN));
    }
  }

  // set the language
  if (!isset($_SESSION['language']) || isset($_GET['language'])) {

    include(DIR_WS_CLASSES . 'language.php');
    $lng = new language($_GET['language']);

    if (!isset($_GET['language'])) $lng->get_browser_language();

    $_SESSION['language'] = $lng->language['directory'];
    $_SESSION['languages_id'] = $lng->language['id'];
  }

  // include the language translations
  require(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.$_SESSION['language'] . '.php');
  require(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/buttons.php');
  $current_page = preg_split('/\?/', basename($_SERVER['PHP_SELF'])); $current_page = $current_page[0]; // for BadBlue(Win32) webserver compatibility  // Hetfield - 2009-08-18 - replaced deprecated function split with preg_split to be ready for PHP >= 5.3
  if (file_exists(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.$current_page)) {
    include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.  $current_page);
  }

  // write customers status in session
  require('../' . DIR_WS_INCLUDES . 'write_customers_status.php');


  // for tracking of customers
  $_SESSION['user_info'] = array();
  if (!$_SESSION['user_info']['user_ip']) {
    $_SESSION['user_info']['user_ip'] = $_SERVER['REMOTE_ADDR'];
//    $user_info['user_ip_date'] =  value will be in fact added when login ;
    $_SESSION['user_info']['user_host'] = gethostbyaddr( $_SERVER['REMOTE_ADDR'] );;
    $_SESSION['user_info']['advertiser'] = $_GET['ad'];
    $_SESSION['user_info']['referer_url'] = $_SERVER['HTTP_REFERER'];
  }


  // define our localization functions
  require(DIR_WS_FUNCTIONS . 'localization.php');

  // Include validation functions (right now only email address)
  //require(DIR_WS_FUNCTIONS . 'validations.php');

  // setup our boxes
  require(DIR_WS_CLASSES . 'table_block.php');
  require(DIR_WS_CLASSES . 'box.php');

  // initialize the message stack for output messages
  require(DIR_WS_CLASSES . 'message_stack.php');
  $messageStack = new messageStack;

  // split-page-results
  require(DIR_WS_CLASSES . 'split_page_results.php');

  // entry/item info classes
  require(DIR_WS_CLASSES . 'object_info.php');


  // file uploading class
  require(DIR_WS_CLASSES . 'upload.php');

  // calculate category path
  if (isset($_GET['cPath'])) {
    $cPath = $_GET['cPath'];
  } else {
    $cPath = '';
  }
  if (strlen($cPath) > 0) {
    $cPath_array = explode('_', $cPath);
    $current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
  } else {
    $current_category_id = 0;
  }

  // default open navigation box
  if (!isset($_SESSION['selected_box'])) {
    $_SESSION['selected_box'] = 'configuration';
  }
  if (isset($_GET['selected_box'])) {
    $_SESSION['selected_box'] = $_GET['selected_box'];
  }

  // the following cache blocks are used in the Tools->Cache section
  // ('language' in the filename is automatically replaced by available languages)
  $cache_blocks = array(array('title' => TEXT_CACHE_CATEGORIES, 'code' => 'categories', 'file' => 'categories_box-language.cache', 'multiple' => true),
                        array('title' => TEXT_CACHE_MANUFACTURERS, 'code' => 'manufacturers', 'file' => 'manufacturers_box-language.cache', 'multiple' => true),
                        array('title' => TEXT_CACHE_ALSO_PURCHASED, 'code' => 'also_purchased', 'file' => 'also_purchased-language.cache', 'multiple' => true)
                       );

  // check if a default currency is set
  if (!defined('DEFAULT_CURRENCY')) {
    $messageStack->add(ERROR_NO_DEFAULT_CURRENCY_DEFINED, 'error');
  }

  // check if a default language is set
  if (!defined('DEFAULT_LANGUAGE')) {
    $messageStack->add(ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
  }

  // for Customers Status
  xtc_get_customers_statuses();



  $pagename = strtok($current_page, '.');
  if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN));
  }

  if (xtc_check_permission($pagename) == '0') {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN));
  }


    // Include Template Engine
// BOF - Tomcraft - 2009-05-26 - update smarty template engine to 2.6.26
//  require(DIR_FS_CATALOG.DIR_WS_CLASSES . 'Smarty_2.6.22/Smarty.class.php');
  require(DIR_FS_CATALOG.DIR_WS_CLASSES . 'Smarty_2.6.26/Smarty.class.php');
// EOF - Tomcraft - 2009-05-26 - update smarty template engine to 2.6.26

// BOF - Tomcraft - 2009-11-28 - Included xs:booster
  define('FILENAME_XTBOOSTER','xtbooster.php');
// EOF - Tomcraft - 2009-11-28 - Included xs:booster
?>
