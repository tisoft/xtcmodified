<?php
/* -----------------------------------------------------------------------------------------
   $Id$   

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_count_modules.inc.php,v 1.3 2003/08/13); www.nextcommerce.org 
   (c) 2006 XT-Commerce (xtc_count_modules.inc.php 899 2005-04-29)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
function xtc_count_modules($modules = '') {
    $count = 0;

    if (empty($modules)) return $count;

    $modules_array = explode(';', $modules); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3

    for ($i=0, $n=sizeof($modules_array); $i<$n; $i++) {
      $class = substr($modules_array[$i], 0, strrpos($modules_array[$i], '.'));

      //BOF - DokuMan - 2010-08-24 - set undefined index
      //if (is_object($GLOBALS[$class])) {
      if (isset($GLOBALS[$class]) && is_object($GLOBALS[$class])) {
      //EOF - DokuMan - 2010-08-24 - set undefined index
        if ($GLOBALS[$class]->enabled) {
          $count++;
        }
      }
    }

  return $count;
}
?>