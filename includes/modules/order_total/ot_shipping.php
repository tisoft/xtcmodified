<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_shipping.php,v 1.15 2003/02/07); www.oscommerce.com
   (c) 2003	nextcommerce (ot_shipping.php,v 1.13 2003/08/24); www.nextcommerce.org
   (c) 2006 xt:Commerce (ot_shipping.php 1002 2005-07-10); www.xt-commerce.de

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  class ot_shipping {
    var $title, $output;

    function ot_shipping() {
    	global $xtPrice;
      $this->code = 'ot_shipping';
      $this->title = MODULE_ORDER_TOTAL_SHIPPING_TITLE;
      $this->description = MODULE_ORDER_TOTAL_SHIPPING_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_SHIPPING_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER;
      $this->output = array();
    }

    function process() {
      global $order, $xtPrice;

      if (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true') {
        switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
          case 'national':
            if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true; break;
          case 'international':
            if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true; break;
          case 'both':
            $pass = true; break;
          default:
            $pass = false; break;
        }

        if ( ($pass == true) && ( ($order->info['total'] - $order->info['shipping_cost']) >= $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER,false,0,true)) ) {
          $order->info['shipping_method'] = $this->title;
          $order->info['total'] -= $order->info['shipping_cost'];
          $order->info['shipping_cost'] = 0;
        }
      }

      $module = substr($_SESSION['shipping']['id'], 0, strpos($_SESSION['shipping']['id'], '_'));

      if (xtc_not_null($order->info['shipping_method'])) {
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
        // price with tax
          //BOF - DokuMan - 2010-09-06 - Trying to get property of non-object
          $tax_class = 0;
          if (isset($GLOBALS[$module]->tax_class) && isset($GLOBALS[$module]->tax_class)) {
            $tax_class = $GLOBALS[$module]->tax_class;
          }
          $shipping_tax = xtc_get_tax_rate($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $shipping_tax_description = xtc_get_tax_description($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          //$shipping_tax = xtc_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          //$shipping_tax_description = xtc_get_tax_description($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          //EOF - DokuMan - 2010-09-06 - Trying to get property of non-object
          $tax = $xtPrice->xtcFormat(xtc_add_tax($order->info['shipping_cost'], $shipping_tax),false,0,false)-$order->info['shipping_cost'];
          $tax = $xtPrice->xtcFormat($tax,false,0,true);
          $order->info['shipping_cost'] = xtc_add_tax($order->info['shipping_cost'], $shipping_tax);
          $order->info['tax'] += $tax;
          //BOF - DokuMan - 2010-09-06 - set correct order of VAT display, added .TAX_SHORT_DISPLAY
          if (!isset($order->info['tax_groups'][TAX_ADD_TAX . $shipping_tax_description .TAX_SHORT_DISPLAY])) $order->info['tax_groups'][TAX_ADD_TAX . $shipping_tax_description .TAX_SHORT_DISPLAY] = 0;
          $order->info['tax_groups'][TAX_ADD_TAX . $shipping_tax_description .TAX_SHORT_DISPLAY] += $tax;
          //EOF - DokuMan - 2010-09-06 - set correct order of VAT display, added .TAX_SHORT_DISPLAY
          $order->info['total'] += $tax;

        } else {
	        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            //BOF - DokuMan - 2010-09-06 - Trying to get property of non-object
	          $tax_class = 0;
	          if (isset($GLOBALS[$module]->tax_class) && isset($GLOBALS[$module]->tax_class)) {
	            $tax_class = $GLOBALS[$module]->tax_class;
	          }
	          $shipping_tax = xtc_get_tax_rate($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
	          $shipping_tax_description = xtc_get_tax_description($tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
	          //$shipping_tax = xtc_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
	          //$shipping_tax_description = xtc_get_tax_description($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
	          //EOF - DokuMan - 2010-09-06 - Trying to get property of non-object
	          $tax = $xtPrice->xtcFormat(xtc_add_tax($order->info['shipping_cost'], $shipping_tax),false,0,false)-$order->info['shipping_cost'];
	          $tax = $xtPrice->xtcFormat($tax,false,0,true);
	          $order->info['tax'] = $order->info['tax'] += $tax;
	          //BOF - DokuMan - 2010-09-06 - set correct order of VAT display, added .TAX_SHORT_DISPLAY
	          if (!isset($order->info['tax_groups'][TAX_NO_TAX . $shipping_tax_description .TAX_SHORT_DISPLAY])) $order->info['tax_groups'][TAX_NO_TAX . $shipping_tax_description] = 0;
	          $order->info['tax_groups'][TAX_NO_TAX . $shipping_tax_description .TAX_SHORT_DISPLAY] += $tax;
	          //EOF - DokuMan - 2010-09-06 - set correct order of VAT display, added .TAX_SHORT_DISPLAY
	        }
        }
        $this->output[] = array('title' => $order->info['shipping_method'] . ':',
                                'text' => $xtPrice->xtcFormat($order->info['shipping_cost'], true,0,true),
                                'value' => $xtPrice->xtcFormat($order->info['shipping_cost'], false,0,true));
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_SHIPPING_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER', 'MODULE_ORDER_TOTAL_SHIPPING_DESTINATION', 'MODULE_ORDER_TOTAL_SHIPPING_TAX_CLASS');
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'true','6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', '3','6', '2', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING', 'false','6', '3', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER', '50', '6', '4', 'currencies->format', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_DESTINATION', 'national','6', '5', 'xtc_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_SHIPPING_TAX_CLASS', '0','6', '7', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>