<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(database.php,v 1.19 2003/03/22); www.oscommerce.com
   (c) 2003 nextcommerce (xtc_db_query.inc.php,v 1.4 2003/08/13); www.nextcommerce.org
   (c) 2006 XT-Commerce (xtc_db_query.inc.php 1195 2005-08-28)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  // include needed functions
  include_once(DIR_FS_INC . 'xtc_db_error.inc.php');

  function xtc_db_query($query, $link = 'db_link') {
    global $$link;

    // BOF - Dokuman - 2011-03-01 - get ready for UTF8
    /*
    mysql_query("SET names 'utf8'");
    mysql_query("SET CHARACTER SET 'utf8'");
    */
    // EOF - Dokuman - 2011-03-01 - get ready for UTF8

    //$queryStartTime = array_sum(explode(" ",microtime()));
    $result = mysql_query($query, $$link) or xtc_db_error($query, mysql_errno(), mysql_error());
    //$queryEndTime = array_sum(explode(" ",microtime()));
    //$processTime = $queryEndTime - $queryStartTime;
    //echo 'time: '.$processTime.' Query: '.$query.'<br />';

    //BOF - DokuMan - 2010-02-25 - also check for defined STORE_DB_TRANSACTIONS constant
    //if (STORE_DB_TRANSACTIONS == 'true') {
    if (defined('STORE_DB_TRANSACTIONS') && STORE_DB_TRANSACTIONS == 'true') {
       error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    //EOF - DokuMan - 2010-02-25 - also check for defined STORE_DB_TRANSACTIONS constant
       $result_error = mysql_error();
       error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    return $result;
  }
?>