<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2006 XT-Commerce
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// BOF - web28 - 2010-10-16 -	Prevent duplicate content by SEO language switch
  require_once (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/inc/clear_language_param.inc.php');
// EOF - web28 - 2010-10-16 -	Prevent duplicate content by SEO language switch  
// BOF - Tomcraft - 2009-10-27 - Prevent duplicate content, see: http://www.gunnart.de/tipps-und-tricks/doppelten-content-vermeiden-productredirect-fuer-xtcommerce/
  require_once (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/inc/gunnart_productRedirect.inc.php');
// EOF - Tomcraft - 2009-10-27 - Prevent duplicate content, see: http://www.gunnart.de/tipps-und-tricks/doppelten-content-vermeiden-productredirect-fuer-xtcommerce/

  define('DIR_WS_BOXES',DIR_FS_CATALOG .'templates/'.CURRENT_TEMPLATE. '/source/boxes/');

  include(DIR_WS_BOXES . 'categories.php');
  include(DIR_WS_BOXES . 'manufacturers.php');
  if ($_SESSION['customers_status']['customers_status_show_price']!='0') {
  require(DIR_WS_BOXES . 'add_a_quickie.php');
  }
  require(DIR_WS_BOXES . 'last_viewed.php');
  if (substr(basename($PHP_SELF), 0,8) != 'advanced') {require(DIR_WS_BOXES . 'whats_new.php'); }
  require(DIR_WS_BOXES . 'search.php');
  require(DIR_WS_BOXES . 'content.php');
  require(DIR_WS_BOXES . 'information.php');
  include(DIR_WS_BOXES . 'languages.php');
  if ($_SESSION['customers_status']['customers_status_id'] == 0) include(DIR_WS_BOXES . 'admin.php');
  require(DIR_WS_BOXES . 'infobox.php');
  require(DIR_WS_BOXES . 'loginbox.php');
  include(DIR_WS_BOXES . 'newsletter.php');
  if ($_SESSION['customers_status']['customers_status_show_price'] == 1) include(DIR_WS_BOXES . 'shopping_cart.php');
  if ($product->isProduct()) include(DIR_WS_BOXES . 'manufacturer_info.php');

  if (isset($_SESSION['customer_id'])) include(DIR_WS_BOXES . 'order_history.php');

  if (!$product->isProduct()) {
    include(DIR_WS_BOXES . 'best_sellers.php');
  }

  if (!$product->isProduct()) {
    include(DIR_WS_BOXES . 'specials.php');
  }

  if ($_SESSION['customers_status']['customers_status_read_reviews'] == 1) require(DIR_WS_BOXES . 'reviews.php');

  if (substr(basename($PHP_SELF), 0, 8) != 'checkout') {

    include(DIR_WS_BOXES . 'currencies.php');
  }
//BOF - GTB - 2010-08-03 - Security Fix - Base
$smarty->assign('tpl_path',DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
//$smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
//EOF - GTB - 2010-08-03 - Security Fix - Base
?>