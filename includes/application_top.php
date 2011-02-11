<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.273 2003/05/19); www.oscommerce.com
   (c) 2003 nextcommerce (application_top.php,v 1.54 2003/08/25); www.nextcommerce.org
   (c) 2006 XT-Commerce (application_top.php 1194 2010-08-22)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c) Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
// BOF - Hendrik - 2010-08-22 - xajax support
define('XAJAX_SUPPORT','false'); // 'true' );     // if you extend the system with features needed xajax support switch on 'true'
define('XAJAX_SUPPORT_TEST','false'); // 'true' );     // this includes any little test feature to checkout xajax is woking properly, switch it on 'false' for regular running
// EOF - Hendrik - 2010-08-22 - xajax support

// start the timer for the page parse time log
define('PAGE_PARSE_START_TIME', microtime());

// set the level of error reporting
error_reporting(E_ALL);

// Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
if (file_exists('includes/local/configure.php')) {
  include ('includes/local/configure.php');
} else {
  include ('includes/configure.php');
}

//BOF - DokuMan - 2010-11-17 - Added Debug-Log-Class - thx to franky
include(DIR_WS_CLASSES.'class.debug.php');
$log = new debug;
//EOF - DokuMan - 2010-11-17 - Added Debug-Log-Class - thx to franky

// BOF - Tomcraft - 2009-11-08 - FIX for PHP5.3 date_default_timezone_set
if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
date_default_timezone_set('Europe/Berlin');
}
// EOF - Tomcraft - 2009-11-08 - FIX for PHP5.3 date_default_timezone_set

$php4_3_10 = (0 == version_compare(phpversion(), "4.3.10"));
define('PHP4_3_10', $php4_3_10);
// define the project version
define('PROJECT_VERSION', 'xtcModified');

// BOF - Tomcraft - 2009-11-09 - Added missing definition for TAX_DECIMAL_PLACES
define('TAX_DECIMAL_PLACES', 0);
// EOF - Tomcraft - 2009-11-09 - Added missing definition for TAX_DECIMAL_PLACES

// set the type of request (secure or not)
//BOF - web28 - 2010-09-03 - added native support for SSL-proxy connections
//$request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';
if (file_exists('includes/request_type.php')) {
  include ('includes/request_type.php');
} else $request_type = 'NONSSL';
//EOF - web28 - 2010-09-03 - added native support for SSL-proxy connections

// set php_self in the local scope
//BOF - GTB - 2010-11-26 - Security Fix - PHP_SELF
$PHP_SELF = $_SERVER['SCRIPT_NAME'];
/*$PHP_SELF = $_SERVER['PHP_SELF'];
//--- SHOPSTAT -------------------------//
if (preg_match("/\.html$/",$PHP_SELF) )
    {
    if(!preg_match("/\.html$/",$_SERVER['SCRIPT_NAME']))
        {
        $PHP_SELF = $_SERVER['SCRIPT_NAME'];
        }
    elseif(!preg_match("/\.html$/",$_SERVER['SCRIPT_FILENAME']))
        {
        $PHP_SELF = $_SERVER['SCRIPT_FILENAME'];
        }
    }
//--- SHOPSTAT -------------------------//*/
//EOF - GTB - 2010-11-26 - Security Fix - PHP_SELF

//BOF - GTB/web28 - 2010-09-15 - Security Fix - Base
$ssl_proxy = '';
if ($request_type == 'SSL' && ENABLE_SSL == true && defined('USE_SSL_PROXY') && USE_SSL_PROXY == true) $ssl_proxy = '/' . $_SERVER['HTTP_HOST'];
define('DIR_WS_BASE', $ssl_proxy . preg_replace('/\\' . DIRECTORY_SEPARATOR . '\/|\/\//', '/', dirname($PHP_SELF) . '/'));
//EOF - GTB/web28 - 2010-09-15 - Security Fix - Base

// include the list of project filenames
require (DIR_WS_INCLUDES.'filenames.php');

// include the list of project database tables
require (DIR_WS_INCLUDES.'database_tables.php');

// SQL caching dir
define('SQL_CACHEDIR', DIR_FS_CATALOG.'cache/');

// Store DB-Querys in a Log File
//define('STORE_DB_TRANSACTIONS', 'false'); //DokuMan - 2010-10-29 - constant already defined in database

// graduated prices model or products assigned ?
define('GRADUATED_ASSIGN', 'true');

// include used functions

// Database
require_once (DIR_FS_INC.'xtc_db_connect.inc.php');
require_once (DIR_FS_INC.'xtc_db_close.inc.php');
require_once (DIR_FS_INC.'xtc_db_error.inc.php');
require_once (DIR_FS_INC.'xtc_db_perform.inc.php');
require_once (DIR_FS_INC.'xtc_db_query.inc.php');
require_once (DIR_FS_INC.'xtc_db_queryCached.inc.php');
require_once (DIR_FS_INC.'xtc_db_fetch_array.inc.php');
require_once (DIR_FS_INC.'xtc_db_num_rows.inc.php');
require_once (DIR_FS_INC.'xtc_db_data_seek.inc.php');
require_once (DIR_FS_INC.'xtc_db_insert_id.inc.php');
require_once (DIR_FS_INC.'xtc_db_free_result.inc.php');
require_once (DIR_FS_INC.'xtc_db_fetch_fields.inc.php');
require_once (DIR_FS_INC.'xtc_db_output.inc.php');
require_once (DIR_FS_INC.'xtc_db_input.inc.php');
require_once (DIR_FS_INC.'xtc_db_prepare_input.inc.php');

// html basics
require_once (DIR_FS_INC.'xtc_href_link.inc.php');
require_once (DIR_FS_INC.'xtc_php_mail.inc.php');

require_once (DIR_FS_INC.'xtc_product_link.inc.php');
require_once (DIR_FS_INC.'xtc_category_link.inc.php');
require_once (DIR_FS_INC.'xtc_manufacturer_link.inc.php');

// html functions
require_once (DIR_FS_INC.'xtc_draw_checkbox_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_form.inc.php');
require_once (DIR_FS_INC.'xtc_draw_hidden_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_input_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_password_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_pull_down_menu.inc.php');
require_once (DIR_FS_INC.'xtc_draw_radio_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_selection_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_separator.inc.php');
require_once (DIR_FS_INC.'xtc_draw_textarea_field.inc.php');
require_once (DIR_FS_INC.'xtc_image_button.inc.php');

require_once (DIR_FS_INC.'xtc_not_null.inc.php');
require_once (DIR_FS_INC.'xtc_update_whos_online.inc.php');
require_once (DIR_FS_INC.'xtc_activate_banners.inc.php');
require_once (DIR_FS_INC.'xtc_expire_banners.inc.php');
require_once (DIR_FS_INC.'xtc_expire_specials.inc.php');
require_once (DIR_FS_INC.'xtc_parse_category_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_product_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_top_level_domain.inc.php');
require_once (DIR_FS_INC.'xtc_get_category_path.inc.php');

require_once (DIR_FS_INC.'xtc_get_parent_categories.inc.php');
require_once (DIR_FS_INC.'xtc_redirect.inc.php');
require_once (DIR_FS_INC.'xtc_get_uprid.inc.php');
require_once (DIR_FS_INC.'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC.'xtc_has_product_attributes.inc.php');
require_once (DIR_FS_INC.'xtc_image.inc.php');
require_once (DIR_FS_INC.'xtc_check_stock_attributes.inc.php');
require_once (DIR_FS_INC.'xtc_currency_exists.inc.php');
require_once (DIR_FS_INC.'xtc_remove_non_numeric.inc.php');
require_once (DIR_FS_INC.'xtc_get_ip_address.inc.php');
require_once (DIR_FS_INC.'xtc_setcookie.inc.php');
require_once (DIR_FS_INC.'xtc_check_agent.inc.php');
require_once (DIR_FS_INC.'xtc_count_cart.inc.php');
require_once (DIR_FS_INC.'xtc_get_qty.inc.php');
require_once (DIR_FS_INC.'create_coupon_code.inc.php');
require_once (DIR_FS_INC.'xtc_gv_account_update.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate_from_desc.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
require_once (DIR_FS_INC.'xtc_add_tax.inc.php');
require_once (DIR_FS_INC.'xtc_cleanName.inc.php');
require_once (DIR_FS_INC.'xtc_calculate_tax.inc.php');
require_once (DIR_FS_INC.'xtc_input_validation.inc.php');
require_once (DIR_FS_INC.'xtc_js_lang.php');

// make a connection to the database... now
xtc_db_connect() or die('Unable to connect to database server!');

$configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from '.TABLE_CONFIGURATION);
while ($configuration = xtc_db_fetch_array($configuration_query)) {
  define($configuration['cfgKey'], $configuration['cfgValue']);
}

//BOF - GTB - 2011-01-20 - SSEQ-Lib integration
require_once (DIR_FS_EXTERNAL . 'sseq-lib/seq_lib.php');
//EOF - GTB - 2011-01-20 - SSEQ-Lib integration

// Below are some defines which affect the way the discount coupon/gift voucher system work
// Be careful when editing them.
//
// Set the length of the redeem code, the longer the more secure
// Kommt eigentlich schon aus der Table configuration
//if(SECURITY_CODE_LENGTH=='') //DokuMan - 2010-10-29 - constant already defined in database
//  define('SECURITY_CODE_LENGTH', '10'); //DokuMan - 2010-10-29 - constant already defined in database
// The settings below determine whether a new customer receives an incentive when they first signup
//
// Set the amount of a Gift Voucher that the new signup will receive, set to 0 for none
//  define('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT', '10');  // placed in the admin configuration mystore
//
// Set the coupon ID that will be sent by email to a new signup, if no id is set then no email :)
//  define('NEW_SIGNUP_DISCOUNT_COUPON', '3'); // placed in the admin configuration mystore
require_once (DIR_WS_CLASSES.'class.phpmailer.php');
if (EMAIL_TRANSPORT == 'smtp')
  require_once (DIR_WS_CLASSES.'class.smtp.php');

// set the application parameters

function xtDBquery($query) {
  if (defined('DB_CACHE') && DB_CACHE) { //Dokuman - 2011-02-11 - check for defined DB_CACHE
//      echo  'cached query: '.$query.'<br />';
    $result = xtc_db_queryCached($query);
  } else {
//      echo '::'.$query .'<br />';
    $result = xtc_db_query($query);
  }
  return $result;
}

function CacheCheck() {
  if (USE_CACHE == 'false') return false;
  if (!isset($_COOKIE['XTCsid'])) return false;
  return true;
}

// if gzip_compression is enabled, start to buffer the output
// BOF - h-h-h - 2011-02-03 - add gzip_off for downloads.php
//if ((GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && (PHP_VERSION >= '4')) {
if ((!isset($gzip_off) || !$gzip_off) && (GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && (PHP_VERSION >= '4')) {
// EOF - h-h-h - 2011-02-03 - add gzip_off for downloads.php
  if (($ini_zlib_output_compression = (int) ini_get('zlib.output_compression')) < 1) {
    ob_start('ob_gzhandler');
  } else {
    ini_set('zlib.output_compression_level', GZIP_LEVEL);
  }
}
//--- SHOPSTAT -------------------------//
/*
// set the HTTP GET parameters manually if search_engine_friendly_urls is enabled
if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
// BOF - Tomcraft - 2009-10-25 - made capable for 1und1
  $pathinfo=((getenv('PATH_INFO')=='')?$_SERVER['ORIG_PATH_INFO']:getenv('PATH_INFO'));
// BOF - Tomcraft - 2009-10-25 - replaced deprecated function ereg with preg_match
//  if(ereg('.php',$pathinfo)):
  if(preg_match('/.php/',$pathinfo)):
// EOF - Tomcraft - 2009-10-25 - replaced deprecated function ereg with preg_match
    $PATH_INFO = substr(stristr('.php', $pathinfo),1);
  else:
    $PATH_INFO=$pathinfo;
  endif;
// EOF - Tomcraft - 2009-10-25 - made capable for 1und1
  if (strlen(getenv('PATH_INFO')) > 1) {
    $GET_array = array ();
    $PHP_SELF = str_replace(getenv('PATH_INFO'), '', $PHP_SELF);
    $vars = explode('/', substr(getenv('PATH_INFO'), 1));
    for ($i = 0, $n = sizeof($vars); $i < $n; $i ++) {
      if (strpos($vars[$i], '[]')) {
        $GET_array[substr($vars[$i], 0, -2)][] = $vars[$i +1];
      } else {
// BOF - Tomcraft - 2009-06-03 - fix magic quotes security issue
//                                $_GET[$key] = $value;
        $_GET[$vars[$i]] = htmlspecialchars($vars[$i +1]);
        if(get_magic_quotes_gpc()) $_GET[$vars[$i]] = addslashes($_GET[$vars[$i]]); // security Patch 20.11.2008
// EOF - Tomcraft - 2009-06-03 - fix magic quotes security issue
      }
      $i ++;
    }

    if (sizeof($GET_array) > 0) {
      while (list ($key, $value) = each($GET_array)) {
        $_GET[$key] = htmlspecialchars($value);
// BOF - Tomcraft - 2009-06-03 - fix magic quotes security issue
//                                $_GET[$key] = $value;
        if(get_magic_quotes_gpc()) $_GET[$key] = addslashes($_GET[$key]); // security Patch 20.11.2008
// EOF - Tomcraft - 2009-06-03 - fix magic quotes security issue
      }
    }
  }
  if($PHP_SELF=='')$PHP_SELF='/index.php';
}
*/
//--- SHOPSTAT -------------------------//

// check GET/POST/COOKIE VARS
require (DIR_WS_CLASSES.'class.inputfilter.php');
$InputFilter = new InputFilter();

// BOF - Hetfield - 2009-08-16 - correct inputfilter security-patch and remove double replacing
//$_GET = $InputFilter->process($_GET, true);
//$_POST = $InputFilter->process($_POST);
$_GET = $InputFilter->process($_GET);
$_POST = $InputFilter->process($_POST);
$_REQUEST = $InputFilter->process($_REQUEST);
$_GET = $InputFilter->safeSQL($_GET);
$_POST = $InputFilter->safeSQL($_POST);
$_REQUEST = $InputFilter->safeSQL($_REQUEST);
// EOF - Hetfield - 2009-08-16 - correct inputfilter security-patch and remove double replacing

//BOF - GTB - 2011-01-20 - SSEQ-Lib integration
function_exists (SEQ_SANITIZE) ? SEQ_SANITIZE (DIR_FS_EXTERNAL . 'sseq-filter/xtcmodified.txt', true) : '';
//EOF - GTB - 2011-01-20 - SSEQ-Lib integration

// set the top level domains
$http_domain = xtc_get_top_level_domain(HTTP_SERVER);
$https_domain = xtc_get_top_level_domain(HTTPS_SERVER);
$current_domain = (($request_type == 'NONSSL') ? $http_domain : $https_domain);

// include shopping cart class
require (DIR_WS_CLASSES.'shopping_cart.php');

// include navigation history class
require (DIR_WS_CLASSES.'navigation_history.php');

// some code to solve compatibility issues
require (DIR_WS_FUNCTIONS.'compatibility.php');

// define how the session functions will be used
require (DIR_WS_FUNCTIONS.'sessions.php');

// set the session name and save path
session_name('XTCsid');
if (STORE_SESSIONS != 'mysql') session_save_path(SESSION_WRITE_DIRECTORY);

// set the session cookie parameters
if (function_exists('session_set_cookie_params')) {
  session_set_cookie_params(0, '/', (xtc_not_null($current_domain) ? '.'.$current_domain : ''));
}
elseif (function_exists('ini_set')) {
  ini_set('session.cookie_lifetime', '0');
  ini_set('session.cookie_path', '/');
  ini_set('session.cookie_domain', (xtc_not_null($current_domain) ? '.'.$current_domain : ''));
}
// set the session ID if it exists
if (isset ($_POST[session_name()])) {
  session_id($_POST[session_name()]);
}
elseif (($request_type == 'SSL') && isset ($_GET[session_name()])) {
  session_id($_GET[session_name()]);
}

//BOF - DokuMan - 2011-01-06 - set session.use_only_cookies when force cookie is enabled
@ini_set('session.use_only_cookies', (SESSION_FORCE_COOKIE_USE == 'True') ? 1 : 0);
//EOF - DokuMan - 2011-01-06 - set session.use_only_cookies when force cookie is enabled

// BOF - Tomcraft - 2011-01-2011 - Fix tracking in Firefox not always working properly
/*
// start the session
$session_started = false;
if (SESSION_FORCE_COOKIE_USE == 'True') {
  xtc_setcookie('cookie_test', 'please_accept_for_session', time() + 60 * 60 * 24 * 30, '/', $current_domain);

  if (isset ($_COOKIE['cookie_test'])) {
    session_start();
    include (DIR_WS_INCLUDES.'tracking.php');
    $session_started = true;
  }
} else {
  session_start();
  include (DIR_WS_INCLUDES.'tracking.php');
  $session_started = true;
}
*/
// start the session
$session_started = false;
if (SESSION_FORCE_COOKIE_USE == 'True') {
  xtc_setcookie('cookie_test', 'please_accept_for_session', time() + 60 * 60 * 24 * 30, '/', $current_domain);

  if (isset ($_COOKIE['cookie_test'])) {
    session_start();
    $session_started = true;
  }
} else {
  session_start();
  $session_started = true;
}
include (DIR_WS_INCLUDES.'tracking.php');
// EOF - Tomcraft - 2011-01-2011 - Fix tracking in Firefox not always working properly

//BOF - GTB - 2011-01-20 - SSEQ-Lib integration
function_exists (SEQ_SECURE_SESSION) ? SEQ_SECURE_SESSION() : '';
//EOF - GTB - 2011-01-20 - SSEQ-Lib integration

// check the Agent
$truncate_session_id = false;
if (CHECK_CLIENT_AGENT) {
  if (xtc_check_agent() == 1) {
    $truncate_session_id = true;
  }
}

// verify the ssl_session_id if the feature is enabled
if (($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true)) {
  $ssl_session_id = getenv('SSL_SESSION_ID');
  if (!isset($_SESSION['SSL_SESSION_ID'])) { // Hetfield - 2009-08-19 - removed deprecated function session_is_registered to be ready for PHP >= 5.3
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
  if (!isset ($_SESSION['SESSION_USER_AGENT'])) {
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
  if (!isset ($_SESSION['SESSION_IP_ADDRESS'])) {
    $_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
  }

  if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
    session_destroy();
    xtc_redirect(xtc_href_link(FILENAME_LOGIN));
  }
}

//BOF - DokuMan - 2010-05-20
// Redirect search engines with session id to the same url without session id to prevent indexing session id urls
if ( $truncate_session_id == true ) {
  if (preg_match('/' . xtc_session_name() . '/i', $_SERVER['REQUEST_URI']) ){
    $location = xtc_href_link(basename($_SERVER['SCRIPT_NAME']), xtc_get_all_get_params(array(xtc_session_name())), 'NONSSL', false);
    header("HTTP/1.0 301 Moved Permanently");
    header("Location: $location");
  }
}

if (!(preg_match('/^[a-z0-9]{26}$/i', session_id()) || preg_match('/^[a-z0-9]{32}$/i', session_id()))) {
    // Thanks to HHGAG ;-)
    session_regenerate_id(true);
}
//EOF - DokuMan - 2010-05-20

// set the language
if (!isset ($_SESSION['language']) || isset ($_GET['language'])) {

  include (DIR_WS_CLASSES.'language.php');
  //BOF - DokuMan - 2010-09-17 - Undefined index: language on first request
  /*
  $lng = new language(xtc_input_validation($_GET['language'], 'char', ''));
  if (!isset ($_GET['language']))
    $lng->get_browser_language();
  */
  if (!isset ($_GET['language'])) {
    $lng = new language(xtc_input_validation('', 'char', ''));
    $lng->get_browser_language();
  } else {
    $lng = new language(xtc_input_validation($_GET['language'], 'char', ''));
  }
  //EOF - DokuMan - 2010-09-17 - Undefined index: language on first request
  $_SESSION['language'] = $lng->language['directory'];
  $_SESSION['languages_id'] = $lng->language['id'];
  $_SESSION['language_charset'] = $lng->language['language_charset'];
  $_SESSION['language_code'] = $lng->language['code'];
}

if (isset($_SESSION['language']) && !isset($_SESSION['language_charset'])) {

  include (DIR_WS_CLASSES.'language.php');
  $lng = new language(xtc_input_validation($_SESSION['language'], 'char', ''));

  $_SESSION['language'] = $lng->language['directory'];
  $_SESSION['languages_id'] = $lng->language['id'];
  $_SESSION['language_charset'] = $lng->language['language_charset'];
  $_SESSION['language_code'] = $lng->language['code'];
}

//BOF - GTB - 2011-01-20 - SSEQ-Lib integration
function_exists (SEQ_SECURE_SESSION) ? SEQ_SECURE_SESSION() : '';
//EOF - GTB - 2011-01-20 - SSEQ-Lib integration

// include the language translations
require (DIR_WS_LANGUAGES.$_SESSION['language'].'/'.$_SESSION['language'].'.php');

// currency
if (!isset ($_SESSION['currency']) || isset ($_GET['currency']) || ((USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (LANGUAGE_CURRENCY != $_SESSION['currency']))) {

  if (isset ($_GET['currency'])) {
    if (!$_SESSION['currency'] = xtc_currency_exists($_GET['currency']))
      $_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
  } else {
    $_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
  }
}
if (isset ($_SESSION['currency']) && $_SESSION['currency'] == '') {
  $_SESSION['currency'] = DEFAULT_CURRENCY;
}

// write customers status in session
require (DIR_WS_INCLUDES.'write_customers_status.php');

// testing new price class
require (DIR_WS_CLASSES.'main.php');
$main = new main();

require (DIR_WS_CLASSES.'xtcPrice.php');
$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);

//--- SHOPSTAT -------------------------//
    $shopstat_ref = __FILE__;
    //BOF - GTB - 2011-01-20 - move Shopstat to external directory
    //require("shopstat/shopstat.php");
    require(DIR_FS_EXTERNAL . 'shopstat/shopstat.php');
    //EOF - GTB - 2011-01-20 - move Shopstat to external directory
//--- SHOPSTAT -------------------------//

// econda tracking
if (TRACKING_ECONDA_ACTIVE=='true') {
	//BOF - GTB - 2011-02-01 - move Econda to external directory
  //require(DIR_WS_INCLUDES . 'econda/class.econda304SP2.php');
  require(DIR_FS_EXTERNAL . 'econda/class.econda304SP2.php');
  //EOF - GTB - 2011-02-01 - move Econda to external directory
  $econda = new econda();
}

// BOF - DokuMan - 2011-01-21 - Fix notices when PayPal API Modul is not enabled
// BOF - Tomcraft - 2009-10-03 - Paypal Express Modul
//require_once (DIR_WS_CLASSES.'paypal_checkout.php');
//$o_paypal = new paypal_checkout();
if (defined('PAYPAL_API_VERSION')) {
    require_once (DIR_WS_CLASSES . 'paypal_checkout.php');
    $o_paypal = new paypal_checkout();
}
// EOF - Tomcraft - 2009-10-03 - Paypal Express Modul
// EOF - DokuMan - 2011-01-21 - Fix notices when PayPal API Modul is not enabled

require (DIR_WS_INCLUDES.FILENAME_CART_ACTIONS);
// create the shopping cart & fix the cart if necesary
if (!isset($_SESSION['cart']) || !is_object($_SESSION['cart'])) { //DokuMan - 2010-02-28 - set undefined variable cart
  $_SESSION['cart'] = new shoppingCart();
}

// include the who's online functions
xtc_update_whos_online();

// split-page-results
require (DIR_WS_CLASSES.'split_page_results.php');

// infobox
require (DIR_WS_CLASSES.'boxes.php');

// auto activate and expire banners
xtc_activate_banners();
xtc_expire_banners();

// auto expire special products
xtc_expire_specials();
require (DIR_WS_CLASSES.'product.php');
// new p URLS
if (isset ($_GET['info'])) {
  $site = explode('_', $_GET['info']);
  $pID = $site[0];
  $actual_products_id = (int) str_replace('p', '', $pID);
  $product = new product($actual_products_id);
} // also check for old 3.0.3 URLS
elseif (isset($_GET['products_id'])) {
  $actual_products_id = (int) $_GET['products_id'];
  $product = new product($actual_products_id);

}
//BOF - DokuMan - 2010-02-25 - check for defined variable: product
//if (!is_object($product)) {
if (!isset($product) || !is_object($product)) {
//EOF - DokuMan - 2010-02-25 - check for defined variable: product
  $product = new product();
}

// new c URLS
if (isset ($_GET['cat'])) {
  $site = explode('_', $_GET['cat']);
  $cID = $site[0];
  $cID = str_replace('c', '', $cID);
  $_GET['cPath'] = xtc_get_category_path($cID);
}
// new m URLS
if (isset ($_GET['manu'])) {
  $site = explode('_', $_GET['manu']);
  $mID = $site[0];
  $mID = (int)str_replace('m', '', $mID);
  $_GET['manufacturers_id'] = $mID;
}

// calculate category path
if (isset ($_GET['cPath'])) {
  $cPath = xtc_input_validation($_GET['cPath'], 'cPath', '');
}
elseif (is_object($product) && !isset ($_GET['manufacturers_id'])) {
  if ($product->isProduct()) {
    $cPath = xtc_get_product_path($actual_products_id);
  } else {
    $cPath = '';
  }
} else {
  $cPath = '';
}

if (xtc_not_null($cPath)) {
  $cPath_array = xtc_parse_category_path($cPath);
  $cPath = implode('_', $cPath_array);
  $current_category_id = $cPath_array[(sizeof($cPath_array) - 1)];
} else {
  $current_category_id = 0;
}

// include the breadcrumb class and start the breadcrumb trail
require (DIR_WS_CLASSES.'breadcrumb.php');
$breadcrumb = new breadcrumb;

// BOF - GTB - 2010-27-08 - Session Fixation for Breadcrumb
if (DIR_WS_CATALOG == '/') {
  $breadcrumb->add(HEADER_TITLE_TOP, xtc_href_link(FILENAME_DEFAULT));
  $link_index = HEADER_TITLE_TOP; //web28 - 2010-11-13 - define link_index
} else {
  // BOF - web28/GTB - 2010-11-13 - change breadcrumb startpage link - GTB removed target="_blank"
  //$breadcrumb->add(HEADER_TITLE_TOP, HTTP_SERVER);
  $breadcrumb->add(HEADER_TITLE_TOP, xtc_href_link('../'));
  // BOF - web28/GTB - 2010-11-13 - change breadcrumb startpage link
  $breadcrumb->add(HEADER_TITLE_CATALOG, xtc_href_link(FILENAME_DEFAULT));
  $link_index = HEADER_TITLE_CATALOG; //web28 - 2010-11-13 - define link_index
}
// EOF - GTB - 2010-27-08 - Session Fixation for Breadcrumb

// add category names or the manufacturer name to the breadcrumb trail
if (isset ($cPath_array)) {
  $group_check = '';
  for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i ++) {
    if (GROUP_CHECK == 'true') {
      $group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
    }
    $categories_query = xtDBquery("select cd.categories_name
                                   from ".TABLE_CATEGORIES_DESCRIPTION." cd,
                                        ".TABLE_CATEGORIES." c
                                   where cd.categories_id = '".$cPath_array[$i]."'
                                   and c.categories_id=cd.categories_id
                                        ".$group_check."
                                   and cd.language_id='".(int) $_SESSION['languages_id']."'");
    if (xtc_db_num_rows($categories_query,true) > 0) {
      $categories = xtc_db_fetch_array($categories_query,true);

      $breadcrumb->add($categories['categories_name'], xtc_href_link(FILENAME_DEFAULT, xtc_category_link($cPath_array[$i], $categories['categories_name'])));
    } else {
      break;
    }
  }
}
//elseif (xtc_not_null($_GET['manufacturers_id'])) {
elseif (isset($_GET['manufacturers_id']) && xtc_not_null($_GET['manufacturers_id'])) { //DokuMan - 2010-02-26 - set undefined variable manufacturers_id
  $manufacturers_query = xtDBquery("select manufacturers_name from ".TABLE_MANUFACTURERS." where manufacturers_id = '".(int) $_GET['manufacturers_id']."'");
  $manufacturers = xtc_db_fetch_array($manufacturers_query, true);

  $breadcrumb->add($manufacturers['manufacturers_name'], xtc_href_link(FILENAME_DEFAULT, xtc_manufacturer_link((int) $_GET['manufacturers_id'], $manufacturers['manufacturers_name'])));
}

// add the products model/name to the breadcrumb trail
if ($product->isProduct()) {
// BOF - Tomcraft - 2009-10-25 - replaced model-number with products_name in breadcrumb navigation
//  $breadcrumb->add($product->getBreadcrumbModel(), xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product->data['products_id'], $product->data['products_name'])));
  $breadcrumb->add($product->data['products_name'], xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product->data['products_id'], $product->data['products_name'])));
// EOF - Tomcraft - 2009-10-25 - replaced model-number with products_name in breadcrumb navigation
}

// initialize the message stack for output messages
require (DIR_WS_CLASSES.'message_stack.php');
$messageStack = new messageStack;

// set which precautions should be checked
define('WARN_INSTALL_EXISTENCE', 'true');
define('WARN_CONFIG_WRITEABLE', 'true');
define('WARN_SESSION_DIRECTORY_NOT_WRITEABLE', 'true');
define('WARN_SESSION_AUTO_START', 'true');
define('WARN_DOWNLOAD_DIRECTORY_NOT_READABLE', 'true');

// Include Template Engine Version 2.6.26
//BOF - GTB - 2011-01-20 - move Smarty to external directory
//require (DIR_WS_CLASSES.'Smarty_2.6.26/Smarty.class.php');
require (DIR_FS_EXTERNAL.'smarty/Smarty.class.php');
//EOF - GTB - 2011-01-20 - move Smarty to external directory

if (isset ($_SESSION['customer_id'])) {
  $account_type_query = xtc_db_query("SELECT account_type,
                                             customers_default_address_id
                                      FROM ".TABLE_CUSTOMERS."
                                      WHERE customers_id = '".(int) $_SESSION['customer_id']."'");
  $account_type = xtc_db_fetch_array($account_type_query);

  // check if zone id is unset bug #0000169
  if (!isset ($_SESSION['customer_country_id'])) {
    $zone_query = xtc_db_query("SELECT entry_country_id
                                FROM ".TABLE_ADDRESS_BOOK."
                                WHERE customers_id='".(int) $_SESSION['customer_id']."'
                                AND address_book_id='".$account_type['customers_default_address_id']."'");

    $zone = xtc_db_fetch_array($zone_query);
    $_SESSION['customer_country_id'] = $zone['entry_country_id'];
  }
  $_SESSION['account_type'] = $account_type['account_type'];
} else {
  $_SESSION['account_type'] = '0';
}

// modification for nre graduated system
unset ($_SESSION['actual_content']);

// econda tracking
if (TRACKING_ECONDA_ACTIVE=='true') {
	//BOF - GTB - 2011-02-01 - move Econda to external directory
  //require(DIR_WS_INCLUDES . 'econda/emos.php');
  require(DIR_FS_EXTERNAL . 'econda/emos.php');
  //EOF - GTB - 2011-02-01 - move Econda to external directory
}

xtc_count_cart();
?>
