<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
  -----------------------------------------------------------------------------------------
  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(default.php,v 1.84 2003/05/07); www.oscommerce.com
  (c) 2003  nextcommerce (default.php,v 1.11 2003/08/22); www.nextcommerce.org
  (c) 2006 xt:Commerce (cross_selling.php 1243 2005-09-25); www.xt-commerce.de

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Enable_Disable_Categories 1.3        Autor: Mikel Williams | mikel@ladykatcostumes.com
  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs...by=date#dirlist

  Released under the GNU General Public License
  ---------------------------------------------------------------------------------------*/

$default_smarty = new smarty;
//BOF - GTB - 2010-08-03 - Security Fix - Base
$default_smarty->assign('tpl_path',DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/');
//$default_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
//EOF - GTB - 2010-08-03 - Security Fix - Base
$default_smarty->assign('session', session_id());
$main_content = '';
$group_check = '';
$fsk_lock = '';
// include needed functions
require_once (DIR_FS_INC.'xtc_customer_greeting.inc.php');
require_once (DIR_FS_INC.'xtc_get_path.inc.php');
require_once (DIR_FS_INC.'xtc_check_categories_status.inc.php');

//BOF - Dokuman - 2009-10-02 - removed feature, due to wrong links in category on "last viewed"
//$_SESSION['lastpath'] = $_GET['cPath'];
//EOF - Dokuman - 2009-10-02 - removed feature, due to wrong links in category on "last viewed"

if (xtc_check_categories_status($current_category_id) >= 1) {

  $error = CATEGORIE_NOT_FOUND;
  include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);

} else {

  if ($category_depth == 'nested') {
    if (GROUP_CHECK == 'true') {
    $group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
    }
    $category_query = "select cd.categories_description,
                              cd.categories_name,
                              cd.categories_heading_title,
                              c.categories_template,
                              c.categories_image from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
                              where c.categories_id = '".$current_category_id."'
                              and cd.categories_id = '".$current_category_id."'
                              ".$group_check."
                              and cd.language_id = '".(int) $_SESSION['languages_id']."'";
    $category_query = xtDBquery($category_query);

    $category = xtc_db_fetch_array($category_query, true);

    if (isset ($cPath) && preg_match('/_/', $cPath)) { // Hetfield - 2009-08-19 - replaced deprecated function ereg with preg_match to be ready for PHP >= 5.3
    // check to see if there are deeper categories within the current category
    $category_links = array_reverse($cPath_array);
    for ($i = 0, $n = sizeof($category_links); $i < $n; $i ++) {
      if (GROUP_CHECK == 'true') {
        $group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
      }
      $categories_query = "select cd.categories_description,
                                  c.categories_id,
                                  cd.categories_name,
                                  cd.categories_heading_title,
                                  c.categories_image,
                                  c.parent_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
                                  where c.categories_status = '1'
                                  and c.parent_id = '".$category_links[$i]."'
                                  and c.categories_id = cd.categories_id
                                  ".$group_check."
                                  and cd.language_id = '".(int) $_SESSION['languages_id']."'
                                  order by sort_order, cd.categories_name";
      $categories_query = xtDBquery($categories_query);

  // BOF - Dokuman - 22.07.2009 - avoid else-condition
  /*
      if (xtc_db_num_rows($categories_query, true) < 1) {
      // do nothing, go through the loop
      } else {
      break; // we've found the deepest category the customer is in
      }
  */
      if ( xtc_db_num_rows($categories_query, true) >= 1 ) {
        break; // we've found the deepest category the customer is in
      }
  // EOF - Dokuman - 22.07.2009 - avoid else-condition

    }
    } else {
    if (GROUP_CHECK == 'true') {
      $group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
    }
    $categories_query = "select cd.categories_description,
                                c.categories_id,
                                cd.categories_name,
                                cd.categories_heading_title,
                                c.categories_image,
                                c.parent_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
                                where c.categories_status = '1'
                                and c.parent_id = '".$current_category_id."'
                                and c.categories_id = cd.categories_id
                                ".$group_check."
                                and cd.language_id = '".(int) $_SESSION['languages_id']."'
                                order by sort_order, cd.categories_name";
    $categories_query = xtDBquery($categories_query);
    }

    $rows = 0;
    while ($categories = xtc_db_fetch_array($categories_query, true)) {
    $rows ++;

    $cPath_new = xtc_category_link($categories['categories_id'],$categories['categories_name']);

    $width = (int) (100 / MAX_DISPLAY_CATEGORIES_PER_ROW).'%';
    $image = '';
    if ($categories['categories_image'] != '') {
      $image = DIR_WS_IMAGES.'categories/'.$categories['categories_image'];
  // BOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
      if(!file_exists($image)) $image = DIR_WS_IMAGES.'categories/noimage.gif';
  // EOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
    }

    $categories_content[] = array ('CATEGORIES_NAME' => $categories['categories_name'],
                                   'CATEGORIES_HEADING_TITLE' => $categories['categories_heading_title'],
                                   'CATEGORIES_IMAGE' => $image,
                                   'CATEGORIES_LINK' => xtc_href_link(FILENAME_DEFAULT, $cPath_new),
                                   'CATEGORIES_DESCRIPTION' => $categories['categories_description']);
    }
    $new_products_category_id = $current_category_id;
    include (DIR_WS_MODULES.FILENAME_NEW_PRODUCTS);

    $image = '';
    if ($category['categories_image'] != '') {
      $image = DIR_WS_IMAGES.'categories/'.$category['categories_image'];
    }
    $default_smarty->assign('CATEGORIES_NAME', $category['categories_name']);
    $default_smarty->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);

    $default_smarty->assign('CATEGORIES_IMAGE', $image);
    $default_smarty->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);

    $default_smarty->assign('language', $_SESSION['language']);
    $default_smarty->assign('module_content', $categories_content);

    // get default template
    if ($category['categories_template'] == '' || $category['categories_template'] == 'default') {
      $files = array ();
      if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/')) {
        while (($file = readdir($dir)) !== false) {
    // BOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
          //if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
          if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/'.$file) and (substr($file, -5) == ".html") and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
    // EOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
    // BOF - web28 - 2010-07-12 - sort templates array
            //$files[] = array ('id' => $file, 'text' => $file);
            $files[] = $file;
          } //if
        } // while
        closedir($dir);
      }
      sort($files);
      //$category['categories_template'] = $files[0]['id'];
      $category['categories_template'] = $files[0];
    // EOF - web28 - 2010-07-12 - sort templates array
    }

    $default_smarty->caching = 0;
    $main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/categorie_listing/'.$category['categories_template']);
    $smarty->assign('main_content', $main_content);
  }
  //elseif ($category_depth == 'products' || $_GET['manufacturers_id']) {
  elseif ($category_depth == 'products' || (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] > 0)) { //DokuMan - 2010-02-26 - Undefined index: manufacturers_id

    //fsk18 lock
    if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
    $fsk_lock = ' and p.products_fsk18!=1';
    }
    // show the products of a specified manufacturer
    if (isset ($_GET['manufacturers_id'])) {
      if (isset ($_GET['filter_id']) && xtc_not_null($_GET['filter_id'])) {

      // sorting query
      $sorting_query = xtDBquery("SELECT products_sorting,
                                         products_sorting2
                                         FROM ".TABLE_CATEGORIES."
                                         where categories_id='".(int) $_GET['filter_id']."'");
      $sorting_data = xtc_db_fetch_array($sorting_query,true);
      if (!$sorting_data['products_sorting'])
      $sorting_data['products_sorting'] = 'pd.products_name';
      $sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
      // We are asked to show only a specific category
      if (GROUP_CHECK == 'true') {
      $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
      }

      //BOF - DokuMan - remove unnecessary "left join ".TABLE_SPECIALS." from SELECT
      /*
      $listing_sql = "select DISTINCT p.products_fsk18,
                                    p.products_shippingtime,
                                    p.products_model,
                                    pd.products_name,
                                    p.products_ean,
                                    p.products_price,
                                    p.products_tax_class_id,
                                    m.manufacturers_name,
                                    p.products_quantity,
                                    p.products_image,
                                    p.products_weight,
                                    pd.products_short_description,
                                    pd.products_description,
                                    p.products_id,
                                    p.manufacturers_id,
                                    p.products_price,
                                    p.products_vpe,
                                    p.products_vpe_status,
                                    p.products_vpe_value,
                                    p.products_discount_allowed,
                                    p.products_tax_class_id
                                    from ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_MANUFACTURERS." m, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p left join ".TABLE_SPECIALS." s on p.products_id = s.products_id
                                    where p.products_status = '1'
                                    and p.manufacturers_id = m.manufacturers_id
                                    and m.manufacturers_id = '".(int) $_GET['manufacturers_id']."'
                                    and p.products_id = p2c.products_id
                                    and pd.products_id = p2c.products_id
                                    ".$group_check."
                                    ".$fsk_lock."
                                    and pd.language_id = '".(int) $_SESSION['languages_id']."'
                                    and p2c.categories_id = '".(int) $_GET['filter_id']."'".$sorting;
      */
      $listing_sql = "select DISTINCT p.products_fsk18,
                                    p.products_shippingtime,
                                    p.products_model,
                                    pd.products_name,
                                    p.products_ean,
                                    p.products_price,
                                    p.products_tax_class_id,
                                    m.manufacturers_name,
                                    p.products_quantity,
                                    p.products_image,
                                    p.products_weight,
                                    pd.products_short_description,
                                    pd.products_description,
                                    p.products_id,
                                    p.manufacturers_id,
                                    p.products_price,
                                    p.products_vpe,
                                    p.products_vpe_status,
                                    p.products_vpe_value,
                                    p.products_discount_allowed,
                                    p.products_tax_class_id
                                    from ".TABLE_PRODUCTS_DESCRIPTION." pd, 
                                    ".TABLE_MANUFACTURERS." m,
                                    ".TABLE_PRODUCTS_TO_CATEGORIES." p2c,
                                    ".TABLE_PRODUCTS." p
                                    where p.products_status = '1'
                                    and p.manufacturers_id = m.manufacturers_id
                                    and m.manufacturers_id = '".(int) $_GET['manufacturers_id']."'
                                    and p.products_id = p2c.products_id
                                    and pd.products_id = p2c.products_id
                                    ".$group_check."
                                    ".$fsk_lock."
                                    and pd.language_id = '".(int) $_SESSION['languages_id']."'
                                    and p2c.categories_id = '".(int) $_GET['filter_id']."'".$sorting;
  //EOF - DokuMan - remove unneeded "left join ".TABLE_SPECIALS." from SELECT

    } else {
      // We show them all
      if (GROUP_CHECK == 'true') {
      $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
      }

      //BOF - DokuMan - remove unnecessary "left join ".TABLE_SPECIALS." from SELECT
      /*
      $listing_sql = "select p.products_fsk18,
                              p.products_shippingtime,
                              p.products_model,
                              p.products_ean,
                              pd.products_name,
                              p.products_id,
                              p.products_price,
                              m.manufacturers_name,
                              p.products_quantity,
                              p.products_image,
                              p.products_weight,
                              pd.products_short_description,
                              pd.products_description,
                              p.manufacturers_id,
                              p.products_vpe,
                              p.products_vpe_status,
                              p.products_vpe_value,
                              p.products_discount_allowed,
                              p.products_tax_class_id
                              from ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_MANUFACTURERS." m, ".TABLE_PRODUCTS." p left join ".TABLE_SPECIALS." s on p.products_id = s.products_id
                              where p.products_status = '1'
                              and pd.products_id = p.products_id
                              ".$group_check."
                              ".$fsk_lock."
                              and pd.language_id = '".(int) $_SESSION['languages_id']."'
                              and p.manufacturers_id = m.manufacturers_id
                              and m.manufacturers_id = '".(int) $_GET['manufacturers_id']."'";
  */
      $listing_sql = "select p.products_fsk18,
                              p.products_shippingtime,
                              p.products_model,
                              p.products_ean,
                              pd.products_name,
                              p.products_id,
                              p.products_price,
                              m.manufacturers_name,
                              p.products_quantity,
                              p.products_image,
                              p.products_weight,
                              pd.products_short_description,
                              pd.products_description,
                              p.manufacturers_id,
                              p.products_vpe,
                              p.products_vpe_status,
                              p.products_vpe_value,
                              p.products_discount_allowed,
                              p.products_tax_class_id
                              from ".TABLE_PRODUCTS_DESCRIPTION." pd,
                              ".TABLE_MANUFACTURERS." m,
                              ".TABLE_PRODUCTS." p
                              where p.products_status = '1'
                              and pd.products_id = p.products_id
                              ".$group_check."
                              ".$fsk_lock."
                              and pd.language_id = '".(int) $_SESSION['languages_id']."'
                              and p.manufacturers_id = m.manufacturers_id
                              and m.manufacturers_id = '".(int) $_GET['manufacturers_id']."'";
  //EOF - DokuMan - remove unnecessary "left join ".TABLE_SPECIALS." from SELECT

    }
    } else {
    // show the products in a given categorie
    if (isset ($_GET['filter_id']) && xtc_not_null($_GET['filter_id'])) {

      // sorting query
      $sorting_query = xtDBquery("SELECT products_sorting,
                                         products_sorting2 FROM ".TABLE_CATEGORIES."
                                         where categories_id='".$current_category_id."'");
      $sorting_data = xtc_db_fetch_array($sorting_query,true);
      if (!$sorting_data['products_sorting'])
        $sorting_data['products_sorting'] = 'pd.products_name';
      $sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
      // We are asked to show only specific catgeory
      if (GROUP_CHECK == 'true') {
        $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
      }

  //BOF - DokuMan - remove unnecessary "left join ".TABLE_SPECIALS." from SELECT
  /*
      $listing_sql = "select p.products_fsk18,
                                    p.products_shippingtime,
                                    p.products_model,
                                    p.products_ean,
                                    pd.products_name,
                                    p.products_id,
                                    m.manufacturers_name,
                                    p.products_quantity,
                                    p.products_image,
                                    p.products_weight,
                                    pd.products_short_description,
                                    pd.products_description,
                                    p.manufacturers_id,
                                    p.products_price,
                                    p.products_vpe,
                                    p.products_vpe_status,
                                    p.products_vpe_value,
                                    p.products_discount_allowed,
                                    p.products_tax_class_id
                                    from  ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_MANUFACTURERS." m, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p left join ".TABLE_SPECIALS." s on p.products_id = s.products_id
                                    where p.products_status = '1'
                                    and p.manufacturers_id = m.manufacturers_id
                                    and m.manufacturers_id = '".(int) $_GET['filter_id']."'
                                    and p.products_id = p2c.products_id
                                    and pd.products_id = p2c.products_id
                                    ".$group_check."
                                    ".$fsk_lock."
                                    and pd.language_id = '".(int) $_SESSION['languages_id']."'
                                    and p2c.categories_id = '".$current_category_id."'".$sorting;
  */
      $listing_sql = "select p.products_fsk18,
                                    p.products_shippingtime,
                                    p.products_model,
                                    p.products_ean,
                                    pd.products_name,
                                    p.products_id,
                                    m.manufacturers_name,
                                    p.products_quantity,
                                    p.products_image,
                                    p.products_weight,
                                    pd.products_short_description,
                                    pd.products_description,
                                    p.manufacturers_id,
                                    p.products_price,
                                    p.products_vpe,
                                    p.products_vpe_status,
                                    p.products_vpe_value,
                                    p.products_discount_allowed,
                                    p.products_tax_class_id
                                    from  ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_MANUFACTURERS." m, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p
                                    where p.products_status = '1'
                                    and p.manufacturers_id = m.manufacturers_id
                                    and m.manufacturers_id = '".(int) $_GET['filter_id']."'
                                    and p.products_id = p2c.products_id
                                    and pd.products_id = p2c.products_id
                                    ".$group_check."
                                    ".$fsk_lock."
                                    and pd.language_id = '".(int) $_SESSION['languages_id']."'
                                    and p2c.categories_id = '".$current_category_id."'".$sorting;

  //EOF - DokuMan - remove unnecessary "left join ".TABLE_SPECIALS." from SELECT

    } else {

      // sorting query
      $sorting_query = xtDBquery("SELECT products_sorting,
                                         products_sorting2 FROM ".TABLE_CATEGORIES."
                                         where categories_id='".$current_category_id."'");
      $sorting_data = xtc_db_fetch_array($sorting_query,true);
      if (!$sorting_data['products_sorting'])
      $sorting_data['products_sorting'] = 'pd.products_name';
      $sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
      // We show them all
      if (GROUP_CHECK == 'true') {
      $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
      }

  //BOF - DokuMan - remove unnecessary "left join ".TABLE_SPECIALS." from SELECT
  /*
      $listing_sql = "select p.products_fsk18,
                                    p.products_shippingtime,
                                    p.products_model,
                                    p.products_ean,
                                    pd.products_name,
                                    m.manufacturers_name,
                                    p.products_quantity,
                                    p.products_image,
                                    p.products_weight,
                                    pd.products_short_description,
                                    pd.products_description,
                                    p.products_id,
                                    p.manufacturers_id,
                                    p.products_price,
                                    p.products_vpe,
                                    p.products_vpe_status,
                                    p.products_vpe_value,
                                    p.products_discount_allowed,
                                    p.products_tax_class_id
                                    from  ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p left join ".TABLE_MANUFACTURERS." m on p.manufacturers_id = m.manufacturers_id
                                    left join ".TABLE_SPECIALS." s on p.products_id = s.products_id
                                    where p.products_status = '1'
                                    and p.products_id = p2c.products_id
                                    and pd.products_id = p2c.products_id
                                    ".$group_check."
                                    ".$fsk_lock."
                                    and pd.language_id = '".(int) $_SESSION['languages_id']."'
                                    and p2c.categories_id = '".$current_category_id."'".$sorting;
  */
      $listing_sql = "select p.products_fsk18,
                                    p.products_shippingtime,
                                    p.products_model,
                                    p.products_ean,
                                    pd.products_name,
                                    m.manufacturers_name,
                                    p.products_quantity,
                                    p.products_image,
                                    p.products_weight,
                                    pd.products_short_description,
                                    pd.products_description,
                                    p.products_id,
                                    p.manufacturers_id,
                                    p.products_price,
                                    p.products_vpe,
                                    p.products_vpe_status,
                                    p.products_vpe_value,
                                    p.products_discount_allowed,
                                    p.products_tax_class_id
                                    from  ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS." p left join ".TABLE_MANUFACTURERS." m on p.manufacturers_id = m.manufacturers_id
                                    where p.products_status = '1'
                                    and p.products_id = p2c.products_id
                                    and pd.products_id = p2c.products_id
                                    ".$group_check."
                                    ".$fsk_lock."
                                    and pd.language_id = '".(int) $_SESSION['languages_id']."'
                                    and p2c.categories_id = '".$current_category_id."'".$sorting;

  //EOF - DokuMan - remove unnecessary "left join ".TABLE_SPECIALS." from SELECT

    }
    }
    // optional Product List Filter
    // BOF - DokuMan - 2010-07-07 - change PRODUCT_FILTER_LIST to true/false
    //if (PRODUCT_LIST_FILTER > 0) {
    if (PRODUCT_LIST_FILTER == 'true') {
    // EOF - DokuMan - 2010-07-07 - change PRODUCT_FILTER_LIST to true/false
    if (isset ($_GET['manufacturers_id'])) {
      $filterlist_sql = "select distinct c.categories_id as id,
                                         cd.categories_name as name from ".TABLE_PRODUCTS." p,
                                         ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c,
                                         ".TABLE_CATEGORIES_DESCRIPTION." cd
                                         where p.products_status = '1'
                                         and p.products_id = p2c.products_id
                                         and p2c.categories_id = c.categories_id
                                         and p2c.categories_id = cd.categories_id
                                         and cd.language_id = '".(int) $_SESSION['languages_id']."'
                                         and p.manufacturers_id = '".(int) $_GET['manufacturers_id']."'
                                         order by cd.categories_name";
    } else {
      $filterlist_sql = "select distinct m.manufacturers_id as id,
                                         m.manufacturers_name as name
                                         from ".TABLE_PRODUCTS." p,
                                         ".TABLE_PRODUCTS_TO_CATEGORIES." p2c,
                                         ".TABLE_MANUFACTURERS." m
                                         where p.products_status = '1'
                                         and p.manufacturers_id = m.manufacturers_id
                                         and p.products_id = p2c.products_id
                                         and p2c.categories_id = '".$current_category_id."'
                                         order by m.manufacturers_name";
    }
    $filterlist_query = xtDBquery($filterlist_sql);
    if (xtc_db_num_rows($filterlist_query, true) > 1) {
      $manufacturer_dropdown = xtc_draw_form('filter', FILENAME_DEFAULT, 'get');
      if (isset ($_GET['manufacturers_id'])) {
        $manufacturer_dropdown .= xtc_draw_hidden_field('manufacturers_id', (int)$_GET['manufacturers_id']);
        $options = array (array ('text' => TEXT_ALL_CATEGORIES));
      } else {
        $manufacturer_dropdown .= xtc_draw_hidden_field('cat', $current_category_id);
        $options = array (array ('text' => TEXT_ALL_MANUFACTURERS));
      }
      $manufacturer_dropdown .= xtc_draw_hidden_field('sort', $_GET['sort']);
      $manufacturer_dropdown .= xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
      while ($filterlist = xtc_db_fetch_array($filterlist_query, true)) {
        $options[] = array ('id' => $filterlist['id'], 'text' => $filterlist['name']);
      }
      $manufacturer_dropdown .= xtc_draw_pull_down_menu('filter_id', $options, $_GET['filter_id'], 'onchange="this.form.submit()"');
      $manufacturer_dropdown .= '</form>'."\n";
    }
  }

    // Get the right image for the top-right
    //BOF - web28 - 2010-08-06 - BUGFIX no manufacturers image displayed -> modules/product_listing.php
    /*
    $image = DIR_WS_IMAGES.'table_background_list.gif';
    if (isset ($_GET['manufacturers_id'])) {
    $image = xtDBquery("select manufacturers_image from ".TABLE_MANUFACTURERS." where manufacturers_id = '".(int) $_GET['manufacturers_id']."'");
    $image = xtc_db_fetch_array($image,true);
    $image = $image['manufacturers_image'];
    }
    elseif ($current_category_id) {
    $image = xtDBquery("select categories_image from ".TABLE_CATEGORIES." where categories_id = '".$current_category_id."'");
    $image = xtc_db_fetch_array($image,true);
    $image = $image['categories_image'];
    }
    */
    //BOF - web28 - 2010-08-06 - BUGFIX no manufacturers image displayed -> modules/product_listing.php

    include (DIR_WS_MODULES.FILENAME_PRODUCT_LISTING);

  } else { // default page
    if (GROUP_CHECK == 'true') {
      $group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
    }
    $shop_content_query = xtDBquery("SELECT content_title,
                                            content_heading,
                                            content_text,
                                            content_file
                                            FROM ".TABLE_CONTENT_MANAGER."
                                            WHERE content_group='5'
                                            ".$group_check."
                                            AND languages_id='".$_SESSION['languages_id']."'");
    $shop_content_data = xtc_db_fetch_array($shop_content_query,true);

    // BOF - Dokuman - 22.07.2009 - added htmlspecialchars
    //  $default_smarty->assign('title', $shop_content_data['content_heading']);
    $default_smarty -> assign('title', htmlspecialchars($shop_content_data['content_heading']));
    // EOF - Dokuman - 22.07.2009 - added htmlspecialchars

    include (DIR_WS_INCLUDES.FILENAME_CENTER_MODULES);

    if ($shop_content_data['content_file'] != '') {
      ob_start();
      if (strpos($shop_content_data['content_file'], '.txt')) {
        echo '<pre>';
      }
      include (DIR_FS_CATALOG.'media/content/'.$shop_content_data['content_file']);
      if (strpos($shop_content_data['content_file'], '.txt')){
        echo '</pre>';
      }
      $shop_content_data['content_text'] = ob_get_contents();
      ob_end_clean();
    }

    $default_smarty->assign('text', str_replace('{$greeting}', xtc_customer_greeting(), $shop_content_data['content_text']));
    $default_smarty->assign('language', $_SESSION['language']);

    // set cache ID
    if (!CacheCheck()) {
      $default_smarty->caching = 0;
      $main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/main_content.html');
    } else {
      $default_smarty->caching = 1;
      $default_smarty->cache_lifetime = CACHE_LIFETIME;
      $default_smarty->cache_modified_check = CACHE_CHECK;
      $cache_id = $_SESSION['language'].$_SESSION['currency'].$_SESSION['customer_id'];
      $main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/main_content.html', $cache_id);
    }

    $smarty->assign('main_content', $main_content);
  }
}
?>