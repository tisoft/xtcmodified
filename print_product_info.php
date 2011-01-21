<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com 
   (c) 2003   nextcommerce (print_product_info.php,v 1.16 2003/08/25); www.nextcommerce.org
   (c) 2006 XT-Commerce

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

// include needed functions
require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');

$smarty = new Smarty;
//BOF - web28 - 2010-07-09 - define smarty template path
//BOF - GTB - 2010-08-03 - Security Fix - Base
$smarty->assign('tpl_path',DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
//$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
//EOF - GTB - 2010-08-03 - Security Fix - Base
//EOF - web28 - 2010-07-09 - define smarty template path

//BOF - web28 - 2010-08-13 - define missing charset
$smarty->assign('charset', $_SESSION['language_charset'] ); 
//EOF - web28 - 2010-08-13 - define missing charset

$module_content = array();
$product_info_query = xtc_db_query("select * FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_status = '1' and p.products_id = '".(int) $_GET['products_id']."' and pd.products_id = p.products_id and pd.language_id = '".(int) $_SESSION['languages_id']."'");
$product_info = xtc_db_fetch_array($product_info_query);

$products_price = $xtPrice->xtcGetPrice($product_info['products_id'], $format = true, 1, $product_info['products_tax_class_id'], $product_info['products_price'], 1);

$products_attributes_query = xtc_db_query("select count(*) as total from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".(int) $_GET['products_id']."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."'");
$products_attributes = xtc_db_fetch_array($products_attributes_query);
if ($products_attributes['total'] > 0) {
  $products_options_name_query = xtc_db_query("select distinct popt.products_options_id, popt.products_options_name from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".(int) $_GET['products_id']."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."' order by popt.products_options_name");
  while ($products_options_name = xtc_db_fetch_array($products_options_name_query)) {
    $selected = 0;

    $products_options_query = xtc_db_query("select pov.products_options_values_id,
                        pov.products_options_values_name,
                        pa.options_values_price,
                        pa.price_prefix,pa.attributes_stock,
                        pa.attributes_model
                        from ".TABLE_PRODUCTS_ATTRIBUTES." pa,
                        ".TABLE_PRODUCTS_OPTIONS_VALUES." pov
                        where pa.products_id = '".(int) $_GET['products_id']."'
                        and pa.options_id = '".$products_options_name['products_options_id']."'
                        and pa.options_values_id = pov.products_options_values_id
                        and pov.language_id = '".(int) $_SESSION['languages_id']."'
                        order by pa.sortorder");
    while ($products_options = xtc_db_fetch_array($products_options_query)) {
      $module_content[] = array ('GROUP' => $products_options_name['products_options_name'], 'NAME' => $products_options['products_options_values_name']);

      if ($products_options['options_values_price'] != '0') {

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
          $tax_rate = $xtPrice->TAX[$product_info['products_tax_class_id']];
          $products_options['options_values_price'] = xtc_add_tax($products_options['options_values_price'], $xtPrice->TAX[$product_info['products_tax_class_id']]);
        }
        if ($_SESSION['customers_status']['customers_status_show_price'] == 1) {
          $module_content[sizeof($module_content) - 1]['NAME'] .= ' ('.$products_options['price_prefix'].$xtPrice->xtcFormat($products_options['options_values_price'], true,0,true).')';
        }
      }
    }
  }
}

// assign language to template for caching
$smarty->assign('language', $_SESSION['language']);

// BOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
/*
$image = '';
if ($product_info['products_image'] != '') {
  $image = DIR_WS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$product_info['products_image'];
}
*/
//BOF - web28 - 2010-07-09 - image path corection
//$image = DIR_WS_CATALOG.$product->productImage($product_info['products_image'], 'thumbnail');
$image = $product->productImage($product_info['products_image'], 'thumbnail');
//EOF - web28 - 2010-07-09 - image path corection
// EOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
if ($_SESSION['customers_status']['customers_status_show_price'] != 0) {
  $tax_rate = $xtPrice->TAX[$product_info['products_tax_class_id']];
  // price incl tax
  if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
    $smarty->assign('PRODUCTS_TAX_INFO', sprintf(TAX_INFO_INCL, $tax_rate.' %'));
  }
  // excl tax + tax at checkout
  if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
    $smarty->assign('PRODUCTS_TAX_INFO', sprintf(TAX_INFO_ADD, $tax_rate.' %'));
  }
  // excl tax
  if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
    $smarty->assign('PRODUCTS_TAX_INFO', sprintf(TAX_INFO_EXCL, $tax_rate.' %'));
  }
}
$smarty->assign('PRODUCTS_NAME', $product_info['products_name']);
$smarty->assign('PRODUCTS_EAN', $product_info['products_ean']);
$smarty->assign('PRODUCTS_QUANTITY', $product_info['products_quantity']);
$smarty->assign('PRODUCTS_WEIGHT', $product_info['products_weight']);
$smarty->assign('PRODUCTS_STATUS', $product_info['products_status']);
$smarty->assign('PRODUCTS_ORDERED', $product_info['products_ordered']);
$smarty->assign('PRODUCTS_MODEL', $product_info['products_model']);
$smarty->assign('PRODUCTS_DESCRIPTION', $product_info['products_description']);
$smarty->assign('PRODUCTS_IMAGE', $image);
$smarty->assign('PRODUCTS_PRICE', $products_price['formated']);
if (ACTIVATE_SHIPPING_STATUS == 'true') {
  $smarty->assign('SHIPPING_NAME', $main->getShippingStatusName($product_info['products_shippingtime']));
  if (isset($shipping_status['image']) && $shipping_status['image'] != '')
    $smarty->assign('SHIPPING_IMAGE', $main->getShippingStatusImage($product_info['products_shippingtime']));
}
if (SHOW_SHIPPING == 'true')
//BOF - Tomcraft - 2010-04-03 - unified popups with scrollbars and make them resizable
  //$smarty->assign('PRODUCTS_SHIPPING_LINK', ' '.SHIPPING_EXCL.'<a href="javascript:newWin=void(window.open(\''.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS).'\', \'popup\', \'toolbar=0, width=640, height=600\'))"> '.SHIPPING_COSTS.'</a>');
  $smarty->assign('PRODUCTS_SHIPPING_LINK', ' '.SHIPPING_EXCL.'<a href="javascript:newWin=void(window.open(\''.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS).'\', \'popup\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width=640, height=600\'))"> '.SHIPPING_COSTS.'</a>');
//EOF - Tomcraft - 2010-04-03 - unified popups with scrollbars and make them resizable

//BOF - DokuMan - 2011-01-21 - print_product_info content shall not be indexed by search engines -> set canonical tag in /template/.../module/print_product_info.html 
if ( intval($_GET['products_id']) > 0 ) {
  $sProdLink = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link(intval($_GET['products_id']), $product_info['products_name']), 'NONSSL', false);
  $smarty->assign('sProdLink', $sProdLink);
}
//EOF - DokuMan - 2011-01-21 - print_product_info content shall not be indexed by search engines -> set canonical tag in /template/.../module/print_product_info.html 

$discount = 0.00;
if ($_SESSION['customers_status']['customers_status_public'] == 1 && $_SESSION['customers_status']['customers_status_discount'] != '0.00') {
  $discount = $_SESSION['customers_status']['customers_status_discount'];
  if ($product_info['products_discount_allowed'] < $_SESSION['customers_status']['customers_status_discount'])
    $discount = $product_info['products_discount_allowed'];
  if ($discount != '0.00')
    $smarty->assign('PRODUCTS_DISCOUNT', $discount.'%');
}

if ($product_info['products_vpe_status'] == 1 && $product_info['products_vpe_value'] != 0.0 && $products_price['plain'] > 0)
  $smarty->assign('PRODUCTS_VPE', $xtPrice->xtcFormat($products_price['plain'] * (1 / $product_info['products_vpe_value']), true).TXT_PER.xtc_get_vpe_name($product_info['products_vpe']));
$smarty->assign('module_content', $module_content);

//more images - by Novalis
$mo_images = xtc_get_products_mo_images($product_info['products_id']);
if (is_array($mo_images)) {
  //BOF MORE IMAGES ARRAY
  $more_images_data = array();
  foreach ($mo_images as $img) {
      //BOF - web28 - 2010-07-09 - image path corection
    //$mo_img = DIR_WS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$img['image_name'];
    $mo_img = DIR_WS_THUMBNAIL_IMAGES.$img['image_name'];    
    $smarty->assign('PRODUCTS_IMAGE_'.$img['image_nr'], $mo_img);
    //$more_images_data[] = array ('PRODUCTS_IMAGE' => DIR_WS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$img['image_name']);
    $more_images_data[] = array ('PRODUCTS_IMAGE' => DIR_WS_THUMBNAIL_IMAGES.$img['image_name']);
    //EOF - web28 - 2010-07-09 - image path corection
  }
  $smarty->assign('more_images', $more_images_data);
  //EOF MORE IMAGES ARRAY
}

// set cache ID
 if (!CacheCheck()) {
  $smarty->caching = 0;
} else {
  $smarty->caching = 1;
  $smarty->cache_lifetime = CACHE_LIFETIME;
  $smarty->cache_modified_check = CACHE_CHECK;
}
$cache_id = $_SESSION['language'].'_'.$product_info['products_id'];
$smarty->display(CURRENT_TEMPLATE.'/module/print_product_info.html', $cache_id);
?>
