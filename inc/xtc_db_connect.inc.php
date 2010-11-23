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
   (c) 2003	nextcommerce (xtc_db_connect.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2006 XT-Commerce (xtc_db_connect.inc.php 1248 2005-09-27)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  include_once(DIR_FS_INC . 'xtc_db_error.inc.php');

  function xtc_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
    global $$link;

    if (!function_exists('mysql_connect')) {
      die ('Call to undefined function: mysql_connect(). Please install the MySQL Connector for PHP');
    }

    if (USE_PCONNECT == 'true') {
      $$link = @mysql_pconnect($server, $username, $password);
    } else {
      $$link = @mysql_connect($server, $username, $password);
    }

    // BOF - vr - 2010-01-01 - Disable "STRICT" mode for MySQL 5!
    //$vers = @mysql_get_server_info();
    //if(substr($vers,0,1) > 4) @mysql_query("SET SESSION sql_mode='MYSQL40'");
    if(version_compare(@mysql_get_server_info(), '5.0.0', '>=')) {
      @mysql_query("SET SESSION sql_mode=''");
    }
    // EOF - vr - 2010-01-01 - Disable "STRICT" mode for MySQL 5!

    // BOF - Dokuman - 2010-11-23 - revised database connection for error reporting
    //if ($$link) mysql_select_db($database);
  	if ($$link) {
      if (!@mysql_select_db($database, $$link)) {
        xtc_db_error('', mysql_errno($$link), mysql_error($$link));
        die();
      }
    } else {
      xtc_db_error('', mysql_errno(), mysql_error());
      die();
    }
    // EOF - Dokuman - 2010-11-23 - revised database connection for error reporting

    return $$link;
  }
?>