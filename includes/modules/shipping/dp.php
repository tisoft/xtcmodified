<?php
/* -----------------------------------------------------------------------------------------
   $Id: dp.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(dp.php,v 1.36 2003/03/09 02:14:35); www.oscommerce.com 
   (c) 2003	 nextcommerce (dp.php,v 1.12 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   German Post (Deutsche Post WorldNet)         	Autor:	Copyright (C) 2002 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers | http://www.themedia.at & http://www.oscommerce.at

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   enhanced on 2010-12-08 18:17:30Z franky_n
   ---------------------------------------------------------------------------------------*/

  class dp {
    var $code, $title, $description, $icon, $enabled, $num_dp;


    function dp() {
      global $order;

      $this->code = 'dp';
      $this->title = MODULE_SHIPPING_DP_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_DP_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_DP_SORT_ORDER;
      $this->icon = DIR_WS_ICONS . 'shipping_dp.gif';
      $this->tax_class = MODULE_SHIPPING_DP_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_DP_STATUS == 'True') ? true : false);
      $this->num_dp = MODULE_SHIPPING_DP_NUMBER_ZONES;
      
      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_DP_ZONE > 0) ) {
        $check_flag = false;
        $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_DP_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = xtc_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
      
      $check_zones_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE_SHIPPING_DP_COUNTRIES_%'");
      $check_zones_rows_query = xtc_db_num_rows($check_zones_query);
      
      if ($check_zones_rows_query != $this->num_dp) {
        $this->install_zones($this->num_dp);
      }
      
    }

/**
 * class methods
 */
    function quote($method = '') {
      global $order, $shipping_weight, $shipping_num_boxes;

      $dest_country = $order->delivery['country']['iso_code_2'];
      $dest_zone = 0;
      $error = false;

      for ($i=1; $i<=$this->num_dp; $i++) {
        $countries_table = constant('MODULE_SHIPPING_DP_COUNTRIES_' . $i);
        $country_zones = explode(",", $countries_table); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3
        if (in_array($dest_country, $country_zones)) {
          $dest_zone = $i;
          break;
        }
      }

      if ($dest_zone == 0) {
        $error = true;
      } else {
        $shipping = -1;
        $dp_cost = constant('MODULE_SHIPPING_DP_COST_' . $i);

        $dp_table = preg_split("/[:,]/" , $dp_cost); // Hetfield - 2009-08-18 - replaced deprecated function split with preg_split to be ready for PHP >= 5.3
        for ($i=0; $i<sizeof($dp_table); $i+=2) {
          if ($shipping_weight <= $dp_table[$i]) {
            $shipping = $dp_table[$i+1];
            $shipping_method = MODULE_SHIPPING_DP_TEXT_WAY . ' ' . $dest_country . ': ';
            break;
          }
        }

        if ($shipping == -1) {
          $shipping_cost = 0;
          $shipping_method = MODULE_SHIPPING_DP_UNDEFINED_RATE;
        } else {
          $shipping_cost = ($shipping + MODULE_SHIPPING_DP_HANDLING);
        }
      }

      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_DP_TEXT_TITLE,
                            'methods' => array(array('id' => $this->code,
                                                     'title' => $shipping_method . ' (' . $shipping_num_boxes . ' x ' . $shipping_weight . ' ' . MODULE_SHIPPING_DP_TEXT_UNITS .')',
                                                     'cost' => $shipping_cost * $shipping_num_boxes)));

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = xtc_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (xtc_not_null($this->icon)) $this->quotes['icon'] = xtc_image($this->icon, $this->title);

      if ($error == true) $this->quotes['error'] = MODULE_SHIPPING_DP_INVALID_ZONE;

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_DP_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_SHIPPING_DP_STATUS', 'True', '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_HANDLING', '0', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_DP_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_DP_ZONE', '0', '6', '0', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_SORT_ORDER', '0', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_ALLOWED', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_NUMBER_ZONES', '0', '6', '0', now())");
    }


    function install_zones($number_of_zones = '1') {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'MODULE_SHIPPING_DP_COUNTRIES_%'");
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'MODULE_SHIPPING_DP_COST_%'");

      for ($i = 1; $i <= $number_of_zones; $i ++) {
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_COUNTRIES_".$i."', 'DE', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DP_COST_".$i."', '5:6.70,10:9.70,20:13.00', '6', '0', now())");
      }
      
      if ($number_of_zones >=1) {
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'AD,AT,BE,CZ,DK,FO,FI,FR,GR,GL,IE,IT,LI,LU,MC,NL,PL,PT,SM,SK,SE,CH,VA,GB,SP' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_1'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '5:16.50,10:20.50,20:28.50' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_1'");
      }
      if ($number_of_zones >=2) {
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'AL,AM,AZ,BY,BA,BG,HR,CY,GE,GI,HU,IS,KZ,LT,MK,MT,MD,NO,SI,UA,TR,YU,RU,RO,LV,EE' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_2'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '5:25.00,10:35.00,20:45.00' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_2'");
      }
      if ($number_of_zones >=3) {
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'DZ,BH,CA,EG,IR,IQ,IL,JO,KW,LB,LY,OM,SA,SY,US,AE,YE,MA,QA,TN,PM' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_3'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '5:29.00,10:39.00,20:59.00' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_3'");
      }
      if ($number_of_zones >=4) {
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'AF,AS,AO,AI,AG,AR,AW,AU,BS,BD,BB,BZ,BJ,BM,BT,BO,BW,BR,IO,BN,BF,BI,KH,CM,CV,KY,CF,TD,CL,CN,CC,CO,KM,CG,CR,CI,CU,DM,DO,EC,SV,ER,ET,FK,FJ,GF,PF,GA,GM,GH,GD,GP,GT,GN,GW,GY,HT,HN,HK,IN,ID,JM,JP,KE,KI,KG,KP,KR,LA,LS' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_4'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '5:35.00,10:50.00,20:80.00' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_4'");
      }
      if ($number_of_zones >=5) {
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'MO,MG,MW,MY,MV,ML,MQ,MR,MU,MX,MN,MS,MZ,MM,NA,NR,NP,AN,NC,NZ,NI,NE,NG,PK,PA,PG,PY,PE,PH,PN,RE,KN,LC,VC,SN,SC,SL,SO,LK,SR,SZ,ZA,SG,TG,TH,TZ,TT,TO,TM,TV,VN,WF,VE,UG,UZ,UY,ST,SH,SD,TW,GQ,LR,DJ,CG,RW,ZM,ZW' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_5'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '5:35.00,10:50.00,20:80.00' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_5'");
      }
      if ($number_of_zones >=6) {
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = 'DE' WHERE configuration_key = 'MODULE_SHIPPING_DP_COUNTRIES_6'");
        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '5:6.70,10:9.70,20:13.00' WHERE  configuration_key = 'MODULE_SHIPPING_DP_COST_6'");
      }
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      $keys = array('MODULE_SHIPPING_DP_STATUS', 'MODULE_SHIPPING_DP_HANDLING','MODULE_SHIPPING_DP_ALLOWED', 'MODULE_SHIPPING_DP_TAX_CLASS', 'MODULE_SHIPPING_DP_ZONE', 'MODULE_SHIPPING_DP_SORT_ORDER','MODULE_SHIPPING_DP_NUMBER_ZONES');

      for ($i = 1; $i <= $this->num_dp; $i ++) {
        $keys[count($keys)] = 'MODULE_SHIPPING_DP_COUNTRIES_' . $i;
        $keys[count($keys)] = 'MODULE_SHIPPING_DP_COST_' . $i;
      }

      return $keys;
    }
  }
?>
