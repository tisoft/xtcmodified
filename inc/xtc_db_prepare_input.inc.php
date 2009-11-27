<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_db_prepare_input.inc.php 527 2009-11-27 15:30:42Z dis $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(database.php,v 1.19 2003/03/22); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_db_prepare_input.inc.php,v 1.4 2003/08/13); www.nextcommerce.org
   (c) 2009 Ruhrmedia.de - Rekona GbR

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/  
  function xtc_db_prepare_input($string) {
  	$rv = '';
    if (is_string($string)) {
      $rv = trim(stripslashes($string));
      return ai_clean_db_input($rv);
    } elseif (is_array($string)) {
      reset($string);
      while (list($key, $value) = each($string)) {
        $string[$key] = xtc_db_prepare_input($value);
      }
      return ai_clean_db_input($string);
    } else {
      return ($string);
    }
  }
  
  function ai_clean_db_input($string) {
  	$rv = $string;
  	if(preg_match('/select.*from/i', $rv)) {
  		$rv = preg_replace('/(.*?)\s*(?:union)?\s*select.*from.*/i', '$1', $rv);
  		if($lc = substr($rv, strlen($rv) - 1, 1)) {
  			if($lc  == "'" || $lc == '"') {
  			 $rv = substr($rv, 0, strlen($rv) -1);
  			}
  		}
  	}
  	return $rv;
  }
 ?>