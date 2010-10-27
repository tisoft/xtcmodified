<?php
/* --------------------------------------------------------------
   $Id$   

   XT-Commerce - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce; www.oscommerce.com 
   (c) 2003      nextcommerce; www.nextcommerce.org
   (c) 2006      xt:Commerce; www.xt-commerce.com

   Released under the GNU General Public License 
   --------------------------------------------------------------
   Third Party contribution:

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
  class order {
    var $info, $totals, $products, $customer, $delivery;

    function order($order_id) {
      $this->info = array();
      $this->totals = array();
      $this->products = array();
      $this->customer = array();
      $this->delivery = array();

      $this->query($order_id);
    }

    function query($order_id) {
	
	//BOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
	  /*
      $order_query = xtc_db_query("select customers_name,
                                   customers_cid,
                                   customers_lastname,
                                   customers_firstname,                                   
                                   customers_id,
                                   customers_vat_id,
                                   customers_company,
                                   customers_street_address,
                                   customers_suburb,
                                   customers_city,
                                   customers_postcode,
                                   customers_state,
                                   customers_country,
                                   customers_telephone,
                                   customers_email_address,
                                   customers_address_format_id,
                                   delivery_name,
                                   delivery_company,
                                   delivery_street_address,
                                   delivery_suburb,
                                   delivery_city,
                                   delivery_postcode,
                                   delivery_state,
                                   delivery_country,
                                   delivery_address_format_id,
                                   billing_name,
                                   billing_company,
                                   billing_street_address,
                                   billing_suburb,
                                   billing_city,
                                   billing_postcode,
                                   billing_state,
                                   billing_country,
                                   billing_address_format_id,
                                   payment_method,
                                   payment_class,
				                  shipping_class,
				                  cc_type,
                                   cc_owner,
                                   cc_number,
                                   cc_expires,
                                   cc_cvv,
                                   comments,
                                   currency,
                                   currency_value,
                                   date_purchased,
                                   orders_status,
                                   last_modified,
                                   customers_status,
                                   customers_status_name,
                                   customers_status_image,
                                   customers_ip,
                                   language,
                                   customers_status_discount
                                   from " . TABLE_ORDERS . " where
                                   orders_id = '" . xtc_db_input($order_id) . "'");
 */
	  $order_query = xtc_db_query("SELECT
                                   *
                                   FROM " . TABLE_ORDERS . " WHERE
                                   orders_id = '" . xtc_db_input($order_id) . "'");
								   
	  //BOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
								   
      $order = xtc_db_fetch_array($order_query);
	  
	  //BOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
      //$totals_query = xtc_db_query("select title, text from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . xtc_db_input($order_id) . "' order by sort_order");
      $totals_query = xtc_db_query("select title, text, value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . xtc_db_input($order_id) . "' order by sort_order");
      //EOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
	  while ($totals = xtc_db_fetch_array($totals_query)) {
        $this->totals[] = array('title' => $totals['title'],
								//BOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
								'value' => $totals['value'],
								//EOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
                                'text' => $totals['text']);
      }

      $this->info = array('currency' => $order['currency'],
                          'currency_value' => $order['currency_value'],
                          'payment_method' => $order['payment_method'],
                          'payment_class' => $order['payment_class'],
                          'shipping_class' => $order['shipping_class'],
                          'status' => $order['customers_status'],
                          'status_name' => $order['customers_status_name'],
                          'status_image' => $order['customers_status_image'],
                          'status_discount' => $order['customers_status_discount'],
                          'cc_type' => $order['cc_type'],
                          'cc_owner' => $order['cc_owner'],
                          'cc_number' => $order['cc_number'],
                          'cc_expires' => $order['cc_expires'],
                          'cc_cvv' => $order['cc_cvv'],
                          'comments' => $order['comments'],
                          'language' => $order['language'],
                          'date_purchased' => $order['date_purchased'],
                          'orders_status' => $order['orders_status'],
                          'last_modified' => $order['last_modified']);

      $this->customer = array('name' => $order['customers_name'],
                              'lastname' => $order['customers_lastname'],
                              'firstname' => $order['customers_firstname'],
                              'company' => $order['customers_company'],
                              'csID' => $order['customers_cid'],
                              'vat_id' => $order['customers_vat_id'],                               
                              'shop_id' => $order['shop_id'], 
                              'ID' => $order['customers_id'],
                              'cIP' => $order['customers_ip'],
                              'street_address' => $order['customers_street_address'],
                              'suburb' => $order['customers_suburb'],
                              'city' => $order['customers_city'],
                              'postcode' => $order['customers_postcode'],
                              'state' => $order['customers_state'],
                              'country' => $order['customers_country'],
                              'format_id' => $order['customers_address_format_id'],
                              'telephone' => $order['customers_telephone'],
                              'email_address' => $order['customers_email_address']);

      $this->delivery = array('name' => $order['delivery_name'],
							  //BOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
							  'firstname' => $order['delivery_firstname'],
                              'lastname' => $order['delivery_lastname'],
							  //EOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
                              'company' => $order['delivery_company'],
                              'street_address' => $order['delivery_street_address'],
                              'suburb' => $order['delivery_suburb'],
                              'city' => $order['delivery_city'],
                              'postcode' => $order['delivery_postcode'],
                              'state' => $order['delivery_state'],
                              'country' => $order['delivery_country'],
							  //BOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
							  'country_iso_2' => $order['delivery_country_iso_code_2'],
							  //EOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
                              'format_id' => $order['delivery_address_format_id']);

      $this->billing = array('name' => $order['billing_name'],
                             'company' => $order['billing_company'],
                             'street_address' => $order['billing_street_address'],
                             'suburb' => $order['billing_suburb'],
                             'city' => $order['billing_city'],
                             'postcode' => $order['billing_postcode'],
                             'state' => $order['billing_state'],
                             'country' => $order['billing_country'],
                             'format_id' => $order['billing_address_format_id']);

      $index = 0;
      $orders_products_query = xtc_db_query("select
                                                 orders_products_id,products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price,allow_tax, products_discount_made
                                             from
                                                 " . TABLE_ORDERS_PRODUCTS . "
                                             where
                                                 orders_id ='" . xtc_db_input($order_id) . "'");

      while ($orders_products = xtc_db_fetch_array($orders_products_query)) {
        $this->products[$index] = array('qty' => $orders_products['products_quantity'],
                                        'name' => $orders_products['products_name'],
                                        'id' => $orders_products['products_id'],
                                        'opid' => $orders_products['orders_products_id'],                                        
                                        'model' => $orders_products['products_model'],
                                        'tax' => $orders_products['products_tax'],
                                        'price' => $orders_products['products_price'],
                                        'discount' => $orders_products['products_discount_made'],
                                        'final_price' => $orders_products['final_price'],
					'allow_tax' => $orders_products['allow_tax']);

        $subindex = 0;
        $attributes_query = xtc_db_query("select products_options, products_options_values, options_values_price, price_prefix from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . xtc_db_input($order_id) . "' and orders_products_id = '" . $orders_products['orders_products_id'] . "'");
        if (xtc_db_num_rows($attributes_query)) {
          while ($attributes = xtc_db_fetch_array($attributes_query)) {
            $this->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options'],
                                                                     'value' => $attributes['products_options_values'],
                                                                     'prefix' => $attributes['price_prefix'],
                                                                     'price' => $attributes['options_values_price']);

            $subindex++;
          }
        }
        $index++;
      }
    }
//BOF - web28 - 2010-03-20 - Send Order by Admin	
	function getOrderData($oID) {
    	global $xtPrice;
    	
    	require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
    	
    	$order_query = "SELECT
	        				products_id,
	        				orders_products_id,
	        				products_model,
	        				products_name,
	        				final_price,
	        			  	products_shipping_time,
	        				products_quantity
	        				FROM ".TABLE_ORDERS_PRODUCTS."
	        				WHERE orders_id='".(int) $oID."'";
		$order_data = array ();
		$order_query = xtc_db_query($order_query);
		while ($order_data_values = xtc_db_fetch_array($order_query)) {
			$attributes_query = "SELECT
			        				products_options,
			        				products_options_values,
			        				price_prefix,
			        				options_values_price
			        				FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES."
			        				WHERE orders_products_id='".$order_data_values['orders_products_id']."'
									order by orders_products_attributes_id";
			$attributes_data = '';
			$attributes_model = '';
			$attributes_query = xtc_db_query($attributes_query);
			while ($attributes_data_values = xtc_db_fetch_array($attributes_query)) {
				$attributes_data .= '<br />'.$attributes_data_values['products_options'].':'.$attributes_data_values['products_options_values'];
				$attributes_model .= '<br />'.xtc_get_attributes_model($order_data_values['products_id'], $attributes_data_values['products_options_values'],$attributes_data_values['products_options']);

			}
			
			$order_data[] = array ('PRODUCTS_MODEL' => $order_data_values['products_model'], 
								   'PRODUCTS_NAME' => $order_data_values['products_name'],
								   'PRODUCTS_SHIPPING_TIME' => $order_data_values['products_shipping_time'], 
								   'PRODUCTS_ATTRIBUTES' => $attributes_data, 
								   'PRODUCTS_ATTRIBUTES_MODEL' => $attributes_model, 
								   'PRODUCTS_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price'], true),
								   'PRODUCTS_SINGLE_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price']/$order_data_values['products_quantity'], true), 
								   'PRODUCTS_QTY' => $order_data_values['products_quantity']);

		}
		
		return $order_data;
    	
    }
    
    function getTotalData($oID) {
    	global $xtPrice,$db;
    	
    	// get order_total data
		//BOF - web28 - 2010-10-27- Korrektur aller Tippfehler oder -> order
		$order_total_query = "SELECT
		  					title,
		  					text,
		                    class,
		                    value,
		  					sort_order
		  					FROM ".TABLE_ORDERS_TOTAL."
		  					WHERE orders_id='".(int) $oID."'
		  					ORDER BY sort_order ASC";

		$order_total = array ();
		$order_total_query = xtc_db_query($order_total_query);
		while ($order_total_values = xtc_db_fetch_array($order_total_query)) {

			$order_total[] = array (
									'TITLE' => $order_total_values['title'], 
									'CLASS' => $order_total_values['class'], 
									'VALUE' => $order_total_values['value'], 
									'TEXT' => $order_total_values['text']
									);
			
			if ($order_total_values['class'] == 'ot_total')			
				$total = $order_total_values['value'];
			
			//BOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin	
			if ($order_total_values['class'] == 'ot_shipping')				
				$shipping = $order_total_values['value'];
			//EOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin			

		}
		//BOF - web28 - 2010-10-27- Korrektur aller Tippfehler oder -> order
		
		//BOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
		//return array('data'=>$order_total,'total'=>$total);
		return array('data'=>$order_total,'total'=>$total, 'shipping'=>$shipping); 
		//EOF - web28 - 2010-06-10 - PayPal IPN Modul Email send by admin
	
    }
//BOF - web28 - 2010-03-20 - Send Order by Admin		
  }
?>