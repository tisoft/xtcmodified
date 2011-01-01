<?php
  /* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(specials.php,v 1.30 2003/02/10); www.oscommerce.com
   (c) 2003 nextcommerce (specials.php,v 1.10 2003/08/17); www.nextcommerce.org
   (c) 2006 XT-Commerce

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

  //fsk18 lock
  $fsk_lock = '';
  if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
    $fsk_lock = ' and p.products_fsk18!=1';
  }
  if (GROUP_CHECK == 'true') {
    $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
  }
  if ($random_product = xtc_random_select("SELECT
                                                  p.products_id,
                                                  pd.products_name,
                                                  p.products_price,
                                                  p.products_tax_class_id,
                                                  p.products_image,
                                                  s.expires_date,
                                                  p.products_vpe,
                                                  p.products_vpe_status,
                                                  p.products_vpe_value,
                                                  s.specials_new_products_price
                                             FROM ".TABLE_PRODUCTS." p,
                                                  ".TABLE_PRODUCTS_DESCRIPTION." pd,
                                                  ".TABLE_SPECIALS." s where p.products_status = '1'
                                              AND p.products_id = s.products_id
                                              AND pd.products_id = s.products_id
                                              AND pd.language_id = '".$_SESSION['languages_id']."'
                                              AND s.status = '1'
                                                  ".$group_check."
                                                  ".$fsk_lock."
                                         ORDER BY s.specials_date_added
                                       DESC LIMIT ".MAX_RANDOM_SELECT_SPECIALS)) {

    $box_smarty->assign('box_content',$product->buildDataArray($random_product));
    $box_smarty->assign('SPECIALS_LINK', xtc_href_link(FILENAME_SPECIALS));
    $box_smarty->assign('language', $_SESSION['language']);

    if ($random_product["products_id"] != '') {
      // set cache ID
      if (!CacheCheck()) {
        $box_smarty->caching = 0;
        $box_specials = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_specials.html');
      } else {
        $box_smarty->caching = 1;
        $box_smarty->cache_lifetime = CACHE_LIFETIME;
        $box_smarty->cache_modified_check = CACHE_CHECK;
        $cache_id = $_SESSION['language'].$random_product["products_id"].$_SESSION['customers_status']['customers_status_name'];
        $box_specials = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_specials.html', $cache_id);
      }
      $smarty->assign('box_SPECIALS', $box_specials);
    }
  }
?>