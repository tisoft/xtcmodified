<?php
/* --------------------------------------------------------------
   $Id: new_attributes.php 1313 2005-10-18 15:49:15Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(new_attributes); www.oscommerce.com
   (c) 2003	 nextcommerce (new_attributes.php,v 1.13 2003/08/21); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   New Attribute Manager v4b				Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   copy attributes                          Autor: Hubi | http://www.netz-designer.de

   Released under the GNU General Public License 
   --------------------------------------------------------------*/ 

  
  require('includes/application_top.php');
  require(DIR_WS_MODULES.'new_attributes_config.php');
  require(DIR_FS_INC .'xtc_findTitle.inc.php');
  require_once(DIR_FS_INC . 'xtc_format_filesize.inc.php');

  if ( isset($cPathID) && $_POST['action'] == 'change') {
    include(DIR_WS_MODULES.'new_attributes_change.php');

    xtc_redirect( './' . FILENAME_CATEGORIES . '?cPath=' . $cPathID . '&pID=' . $_POST['current_product_id'] );
  }

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script type="text/javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php

// BOF - Tomcraft - 2009-11-11 - NEW SORT SELECTION
  if ($_GET['option_order_by'] && !isset($_POST['action'])) {
      $pageTitle = TITLE_EDIT.': ' . xtc_findTitle($_GET['current_product_id'], $languageFilter);
      include(DIR_WS_MODULES.'new_attributes_include.php');
  }
  
  if (!isset($_GET['option_order_by'])) {
// EOF - Tomcraft - 2009-11-11 - NEW SORT SELECTION
  switch($_POST['action']) {
    case 'edit':
      if ($_POST['copy_product_id'] != 0) {
          $attrib_query = xtc_db_query("SELECT products_id, options_id, options_values_id, options_values_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix, sortorder FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = " . $_POST['copy_product_id']);
          while ($attrib_res = xtc_db_fetch_array($attrib_query)) {
              xtc_db_query("INSERT into ".TABLE_PRODUCTS_ATTRIBUTES." (products_id, options_id, options_values_id, options_values_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix, sortorder) VALUES ('" . $_POST['current_product_id'] . "', '" . $attrib_res['options_id'] . "', '" . $attrib_res['options_values_id'] . "', '" . $attrib_res['options_values_price'] . "', '" . $attrib_res['price_prefix'] . "', '" . $attrib_res['attributes_model'] . "', '" . $attrib_res['attributes_stock'] . "', '" . $attrib_res['options_values_weight'] . "', '" . $attrib_res['weight_prefix'] . "', '" . $attrib_res['sortorder'] . "')");
          }
      }
      $pageTitle = TITLE_EDIT.': ' . xtc_findTitle($_POST['current_product_id'], $languageFilter);
      include(DIR_WS_MODULES.'new_attributes_include.php');
      break;

    case 'change':
      $pageTitle = TITLE_UPDATED;
      include(DIR_WS_MODULES.'new_attributes_change.php');
      include(DIR_WS_MODULES.'new_attributes_select.php');
      break;

    default:
      $pageTitle = TITLE_EDIT;
      include(DIR_WS_MODULES.'new_attributes_select.php');
      break;
  }
// BOF - Tomcraft - 2009-11-11 - NEW SORT SELECTION
  }
// EOF - Tomcraft - 2009-11-11 - NEW SORT SELECTION
?>
    </table></td>
  </tr>
<!-- BOF - Tomcraft - 2009-06-10 - added missing table close tag -->
</table>
<!-- EOF - Tomcraft - 2009-06-10 - added missing table close tag -->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>