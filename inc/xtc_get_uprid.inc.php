<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_get_uprid.inc.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_get_uprid.inc.php,v 1.3 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
// Return a product ID with attributes

  function xtc_get_uprid($prid, $params) {
// Start of changes for textfield
    $uprid = $prid;
    if ( (is_array($params)) && (!strstr($prid, '{')) ) {
      while (list($option, $value) = each($params)) {
        $uprid = $uprid . '{' . $option . '}' . htmlspecialchars(stripslashes(trim($value)), ENT_QUOTES);
// End of changes for textfield

    }
  } else {
//    $uprid = xtc_get_prid($prid);
      $uprid = htmlspecialchars(stripslashes($uprid), ENT_QUOTES);
  }

  return $uprid;
}
 ?>
