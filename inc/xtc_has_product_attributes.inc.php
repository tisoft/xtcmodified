<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_has_product_attributes.inc.php 1009 2005-07-11 16:19:29Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_has_product_attributes.inc.php,v 1.3 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// Check if product has attributes
  function xtc_has_product_attributes($products_id) {
    $attributes_query = "select count(*) as count from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . $products_id . "'";
    $attributes_query  = xtDBquery($attributes_query);
    $attributes = xtc_db_fetch_array($attributes_query,true);

    if ($attributes['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }
 ?>
