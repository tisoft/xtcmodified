<?php
/* -----------------------------------------------------------------------------------------
   $Id$   

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(compatibility.php,v 1.19 2003/04/09); www.oscommerce.com 
   (c) 2003  nextcommerce (compatibility.php,v 1.5 2003/08/13); www.nextcommerce.org 
   (c) 2006 XT-Commerce (compatibility.php 899 2005-04-29)

   Released under the GNU General Public License
   Modified by Marco Canini, <m.canini@libero.it>
   Fixed a bug with arrays in $HTTP_xxx_VARS
   ---------------------------------------------------------------------------------------*/

  ////
  // Recursively handle magic_quotes_gpc turned off.
  // This is due to the possibility of have an array in
  // $HTTP_xxx_VARS
  // Ie, products attributes
  function do_magic_quotes_gpc(&$ar) {
    if (!is_array($ar)) return false;

    while (list($key, $value) = each($ar)) {
      if (is_array($value)) {
        do_magic_quotes_gpc($value);
      } else {
        $ar[$key] = addslashes($value);
      }
    }
  }

  // $HTTP_xxx_VARS are always set on php4
  if (!is_array($_GET)) $_GET = array();
  if (!is_array($_POST)) $_POST = array();
  if (!is_array($_COOKIE)) $_COOKIE = array();

  // handle magic_quotes_gpc turned off.
  if (!get_magic_quotes_gpc()) {
    do_magic_quotes_gpc($_GET);
    do_magic_quotes_gpc($_POST);
    do_magic_quotes_gpc($_COOKIE);
  }

//BOF - DokuMan - 2010-01-06 set default timezone if none exists (PHP 5.3 throws an E_WARNING)
  if ((strlen(ini_get('date.timezone')) < 1) && function_exists('date_default_timezone_set')) {
    date_default_timezone_set(@date_default_timezone_get());
  }
//EOF - DokuMan - 2010-01-06 set default timezone if none exists (PHP 5.3 throws an E_WARNING)

//BOF - DokuMan - 2011-01-06 - remove PHP3 compatiblity code
//array_splice()
//in_array()
//array_reverse()
//is_null()
//constant()
//is_numeric()
//array_merge()
//array_slice()
//array_map()
//str_repeat()
//EOF - DokuMan - 2011-01-06 - remove PHP3 compatiblity code

  //checkdnsrr on Windows plattforms available from PHP <= 5.3.0
  if (!function_exists('checkdnsrr')) {
    function checkdnsrr($host, $type) {
      if(xtc_not_null($host) && xtc_not_null($type)) {
        @exec("nslookup -type=" . escapeshellarg($type) . " " . escapeshellarg($host), $output); // DokuMan - 2011-01-06 - added escapeshellarg
        while(list($k, $line) = each($output)) {
          if(preg_match("/^$host/i", $line)) { // Hetfield - 2009-08-19 - replaced deprecated function eregi with preg_match to be ready for PHP >= 5.3
            return true;
          }
        }
      }
      return false;
    }
  }
?>