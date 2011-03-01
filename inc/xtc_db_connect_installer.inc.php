<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(database.php,v 1.2 2002/03/02); www.oscommerce.com
   (c) 2003 nextcommerce (xtc_db_connect_installer.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2006 XT-Commerce (xtc_db_connect_installer.inc.php 899 2005-04-29)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  function xtc_db_connect_installer($server, $username, $password, $link = 'db_link') {
    global $$link, $db_error;

    $db_error = false;

    if (!$server) {
      $db_error = 'No Server selected.';
      return false;
    }

    $$link = @mysql_connect($server, $username, $password) or $db_error = mysql_error();

    // BOF - vr - 2010-01-01 - Disable "STRICT" mode for MySQL 5!
    //$vers = @mysql_get_server_info();
    //if(substr($vers,0,1) > 4) @mysql_query("SET SESSION sql_mode='MYSQL40'");
    if(version_compare(@mysql_get_server_info(), '5.0.0', '>=')) {
      @mysql_query("SET SESSION sql_mode=''");
    }
    // EOF - vr - 2010-01-01 - Disable "STRICT" mode for MySQL 5!

    // BOF - Dokuman - 2011-03-01 - get ready for UTF8
    /*
    if(function_exists('mysql_set_charset') == true) {
      mysql_set_charset('utf8');
    } else {
      mysql_query('set names utf8');
    }
    */
    // EOF - Dokuman - 2011-03-01 - get ready for UTF8

    return $$link;
  }
 ?>