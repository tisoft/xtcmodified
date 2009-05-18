<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_oe_customer_infos.inc.php

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   XTC-Bestellbearbeitung:
   http://www.xtc-webservice.de / Matthias Hinsche
   info@xtc-webservice.de

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (xtc_get_products_price.inc.php,v 1.13 2003/08/20); www.nextcommerce.org
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  function  xtc_oe_customer_infos($customers_id) {

    $customer_query = xtc_db_query("select a.entry_country_id, a.entry_zone_id from " . TABLE_CUSTOMERS . " c, " . TABLE_ADDRESS_BOOK . " a where c.customers_id  = '" . $customers_id . "' and c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id");
    $customer = xtc_db_fetch_array($customer_query);


	$customer_info_array = array('country_id' => $customer['entry_country_id'],
                                 'zone_id' => $customer['entry_zone_id']);

return $customer_info_array;
  }
 ?>