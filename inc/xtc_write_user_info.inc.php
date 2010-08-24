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
   (c) 2003	 nextcommerce (xtc_write_user_info.inc.php,v 1.4 2003/08/13); www.nextcommerce.org 
   (c) 2006 XT-Commerce (xtc_write_user_info.inc.php 899 2005-04-29)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  function xtc_write_user_info($customer_id) {

      $sql_data_array = array('customers_id' => $customer_id,
                              'customers_ip' => $_SESSION['tracking']['ip'],
                              'customers_ip_date' => 'now()',
                              'customers_host' => isset($_SESSION['tracking']['http_referer']['host']) ? $_SESSION['tracking']['http_referer']['host'] : '',
                              'customers_advertiser' => isset($_SESSION['tracking']['refID']) ? $_SESSION['tracking']['refID'] : '',
                              'customers_referer_url' => isset($_SESSION['tracking']['http_referer']['host']) ? $_SESSION['tracking']['http_referer']['host'].$_SESSION['tracking']['http_referer']['path'] : '',
                              );

      xtc_db_perform(TABLE_CUSTOMERS_IP, $sql_data_array);
    return -1;
  }
?>