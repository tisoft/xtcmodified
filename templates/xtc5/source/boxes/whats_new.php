<?php
  /* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(whats_new.php,v 1.31 2003/02/10); www.oscommerce.com
   (c) 2003  nextcommerce (whats_new.php,v 1.12 2003/08/21); www.nextcommerce.org
   (c) 2006 XT-Commerce

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3 Autor: Mikel Williams | mikel@ladykatcostumes.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
  $box_smarty = new smarty;
  //BOF - GTB - 2010-08-03 - Security Fix - Base
  $box_smarty->assign('tpl_path',DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
  //$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
  //EOF - GTB - 2010-08-03 - Security Fix - Base
  $box_content = '';
  // include needed functions
  require_once (DIR_FS_INC.'xtc_random_select.inc.php');
  require_once (DIR_FS_INC.'xtc_rand.inc.php');
  require_once (DIR_FS_INC.'xtc_get_products_name.inc.php');

  //fsk18 lock
  $fsk_lock = '';
  if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
    $fsk_lock = ' and p.products_fsk18!=1';
  }
  //group check
  $group_check = '';
  if (GROUP_CHECK == 'true') {
    $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
  }
  //BOF - Hetfield - 2009-08-11 - #BUGFIX 0000374: Box Whats New 30 Tage BUG
  if (MAX_DISPLAY_NEW_PRODUCTS_DAYS != '0') {
    $date_new_products = date("Y.m.d", mktime(1, 1, 1, date("m"), date("d") - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date("Y")));
    $days = " and p.products_date_added > '".$date_new_products."' ";
  }
  if ($random_product = xtc_random_select("SELECT distinct
                                                  p.products_id,
                                                  p.products_image,
                                                  p.products_tax_class_id,
                                                  p.products_vpe,
                                                  p.products_vpe_status,
                                                  p.products_vpe_value,
                                                  p.products_price
                                             FROM ".TABLE_PRODUCTS." p,
                                                  ".TABLE_PRODUCTS_TO_CATEGORIES." p2c,
                                                  ".TABLE_CATEGORIES." c
                                            WHERE p.products_status=1
                                              AND p.products_id = p2c.products_id
                                                  " .(isset($_GET['products_id']) && (int)$_GET['products_id'] > 0 ? 'AND p.products_id != ' . (int)$_GET['products_id'] : '')."
                                              AND c.categories_id = p2c.categories_id
                                                  ".$group_check."
                                                  ".$fsk_lock."
                                                  ".$days."
                                              AND c.categories_status=1
                                            ORDER BY p.products_date_added desc
                                            LIMIT ".MAX_RANDOM_SELECT_NEW)) {
    //EOF - Hetfield - 2009-08-11 - #BUGFIX 0000374: Box Whats New 30 Tage BUG
    $whats_new_price = $xtPrice->xtcGetPrice($random_product['products_id'], $format = true, 1, $random_product['products_tax_class_id'], $random_product['products_price']);
  }
  //BOF - DokuMan - 2011-01-21 - Fix a notice when there is no new product
  //$random_product['products_name'] = xtc_get_products_name($random_product['products_id']);
  //if ($random_product && $random_product['products_name'] != '') {
  if(!empty($random_product['products_id'])) {
    $random_product['products_name'] = xtc_get_products_name($random_product['products_id']);
  }
  if (!empty($random_product['products_name'])) {
  //EOF - DokuMan - 2011-01-21 - Fix a notice when there is no new product
    $box_smarty->assign('box_content',$product->buildDataArray($random_product));
    $box_smarty->assign('LINK_NEW_PRODUCTS',xtc_href_link(FILENAME_PRODUCTS_NEW));
    $box_smarty->assign('language', $_SESSION['language']);
    // set cache ID
    if (!CacheCheck()) {
      $box_smarty->caching = 0;
      $box_whats_new = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_whatsnew.html');
    } else {
      $box_smarty->caching = 1;
      $box_smarty->cache_lifetime = CACHE_LIFETIME;
      $box_smarty->cache_modified_check = CACHE_CHECK;
      $cache_id = $_SESSION['language'].$random_product['products_id'].$_SESSION['customers_status']['customers_status_name'];
      $box_whats_new = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_whatsnew.html', $cache_id);
    }
    $smarty->assign('box_WHATSNEW', $box_whats_new);
  }
?>