<?php
/**
 *
 *
 * @version Sofortüberweisung 1.9  05.06.2007
 * @author Henri Schmidhuber  info@in-solution.de
 * @copyright 2006 - 2007 Henri Schmidhuber
 * @link http://www.in-solution.de
 * @link http://www.xt-commerce.com
 * @link http://www.sofort-ueberweisung.de
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 *
 ***********************************************************************************
 * this file contains code based on:
 * (c) 2000 - 2001 The Exchange Project
 * (c) 2001 - 2003 osCommerce, Open Source E-Commerce Solutions
 * (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org
 * (c) 2003 - 2006 XT-Commerce
 * Released under the GNU General Public License
 ***********************************************************************************
 *
 */

  class sofortueberweisung {
    var $code, $title, $description, $enabled;

// class constructor
    function sofortueberweisung() {
      global $order;

      $this->code = 'sofortueberweisung';
      $this->title = MODULE_PAYMENT_SOFORTUEBERWEISUNG_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_SOFORTUEBERWEISUNG_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_SOFORTUEBERWEISUNG_STATUS == 'True') ? true : false);
      $this->redirect = ((MODULE_PAYMENT_SOFORTUEBERWEISUNG_REDIRECT == 'True') ? true : false);
      $this->disable_column_right = ((MODULE_PAYMENT_SOFORTUEBERWEISUNG_DISABLE_COLUMN_RIGHT == 'True') ? true : false);
      $this->disable_column_left = ((MODULE_PAYMENT_SOFORTUEBERWEISUNG_DISABLE_COLUMN_LEFT == 'True') ? true : false);

      if (is_object($order)) $this->update_status();

      $this->email_footer = MODULE_PAYMENT_SOFORTUEBERWEISUNG_TEXT_EMAIL_FOOTER;
      $this->text_redirect = MODULE_PAYMENT_SOFORTUEBERWEISUNG_TEXT_REDIRECT;
      $this->form_action_url_after_success = 'https://www.sofort-ueberweisung.de/payment.php';
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_SOFORTUEBERWEISUNG_ZONE > 0) ) {
        $check_flag = false;
        $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_SOFORTUEBERWEISUNG_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = xtc_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      return array('id' => $this->code,
                   'module' => $this->title,
                   'fields' => array(array('title' => MODULE_PAYMENT_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION_CHECKOUT_PAYMENT)));
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return array('title' => MODULE_PAYMENT_SOFORTUEBERWEISUNG_TEXT_DESCRIPTION_CHECKOUT_CONFIRMATION);
    }

    function process_button() {
      global $order, $xtPrice;
  		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
	  		$total = $order->info['total'] + $order->info['tax'];
		  } else {
			  $total = $order->info['total'];
		  }

      // Fix for XTC Bug
      //
      // $order->info['total'] is in 'after_process' String with currency -> 1.02 EUR
      // so it has to be set here
      $_SESSION['sofortueberweisung_total'] = round($xtPrice->xtcCalculateCurrEx($total, 'EUR'), $xtPrice->get_decimal_places('EUR'));
    }

    function before_process() {
      return false;
    }

    function after_process() {
      global $order, $xtPrice, $mail_error, $order_total_modules;

      $_SESSION['cart']->reset(true);
      // unregister session variables used during checkout
      unset($_SESSION['sendto']);
      unset($_SESSION['billto']);
      unset($_SESSION['shipping']);
      unset($_SESSION['payment']);
      unset($_SESSION['comments']);
      unset($_SESSION['last_order']);
      //GV Code Start
      if(isset($_SESSION['credit_covers'])) unset($_SESSION['credit_covers']);
      $order_total_modules->clear_posts();//ICW ADDED FOR CREDIT CLASS SYSTEM
      // GV Code End

      if (!isset($mail_error)) {
        xtc_redirect(xtc_href_link('sofortueberweisung_checkout_success.php', 'sofortueberweisung_payment=' . $this->code, 'SSL'));
      }
      else {
        echo $mail_error;
      }
      exit();
      return false;
    }

    function checkout_success($insert_id) {
      $customer_id = $_SESSION['customer_id'];
      require_once (DIR_WS_CLASSES.'order.php');
      $order =  new order($insert_id);
      $parameter= array();
      $return = '';
		  $parameter['kdnr']	= MODULE_PAYMENT_SOFORTUEBERWEISUNG_KDNR;  // Repräsentiert Ihre Kundennummer bei der Sofortüberweisung
		  $parameter['projekt'] = MODULE_PAYMENT_SOFORTUEBERWEISUNG_PROJEKT;  // Die verantwortliche Projektnummer bei der Sofortüberweisung, zu der die Zahlung gehört
      $parameter['betrag'] = $_SESSION['sofortueberweisung_total'];
      // number_format($order->info['total'] *  $currencies->get_value('EUR'), 2, '.','');   // Beziffert den Zahlungsbetrag, der an Sie übermittelt werden soll
      $vzweck1 = str_replace('{{orderid}}', $insert_id, MODULE_PAYMENT_SOFORTUEBERWEISUNG_TEXT_V_ZWECK_1);
      $vzweck2 = str_replace('{{orderid}}', $insert_id, MODULE_PAYMENT_SOFORTUEBERWEISUNG_TEXT_V_ZWECK_2);

      $vzweck1 = str_replace('{{order_date}}', strftime(DATE_FORMAT_SHORT), $vzweck1);
      $vzweck2 = str_replace('{{order_date}}', strftime(DATE_FORMAT_SHORT), $vzweck2);

      $vzweck1 = str_replace('{{customer_id}}', $customer_id, $vzweck1);
      $vzweck2 = str_replace('{{customer_id}}', $customer_id, $vzweck2);

      $vzweck1 = str_replace('{{customer_cid}}', $order->customer['csID'], $vzweck1);
      $vzweck2 = str_replace('{{customer_cid}}', $order->customer['csID'], $vzweck2);

      $vzweck1 = str_replace('{{customer_name}}', $order->customer['name'], $vzweck1);
      $vzweck2 = str_replace('{{customer_name}}', $order->customer['name'], $vzweck2);

      $vzweck1 = str_replace('{{customer_company}}', $order->customer['company'], $vzweck1);
      $vzweck2 = str_replace('{{customer_company}}', $order->customer['company'], $vzweck2);

      $vzweck1 = str_replace('{{customer_email}}', $order->customer['email_address'], $vzweck1);
      $vzweck2 = str_replace('{{customer_email}}', $order->customer['email_address'], $vzweck2);

      // Kürzen auf 27 Zeichen
      $vzweck1 = substr($vzweck1, 0, 27);
      $vzweck2 = substr($vzweck2, 0, 27);

      $parameter['v_zweck_1'] = xtc_parse_input_field_data($vzweck1, array('"' => '&quot;'));  // Definieren Sie hier Ihre Verwendungszwecke
      $parameter['v_zweck_2'] = xtc_parse_input_field_data($vzweck2, array('"' => '&quot;'));  // Definieren Sie hier Ihre Verwendungszwecke

		  $parameter['kunden_var_0'] = xtc_parse_input_field_data($insert_id, array('"' => '&quot;'));;  // Eindeutige Identifikation der Zahlung, z.B. Session ID oder Auftragsnummer.
		  $parameter['kunden_var_1'] = xtc_parse_input_field_data($customer_id, array('"' => '&quot;'));;  // Eindeutige Identifikation der Zahlung, z.B. Session ID oder Auftragsnummer.
		  $parameter['kunden_var_2'] = xtc_parse_input_field_data(xtc_session_id(), array('"' => '&quot;'));;
		  $parameter['kunden_var_3'] = xtc_parse_input_field_data($cart->cartID, array('"' => '&quot;'));;
		  $parameter['kunden_var_4'] = '';
		  $parameter['kunden_var_5'] = '';


		  if (strlen(MODULE_PAYMENT_SOFORTUEBERWEISUNG_INPUT_PASSWORT) > 0) {
        $tmparray = array(
          $parameter['betrag'],
          $parameter['v_zweck_1'],
          $parameter['v_zweck_2'],
          '', // von_konto_inhaber
          '', // von_konto_nr
          '', // von_konto_blz
          $parameter['kunden_var_0'],
          $parameter['kunden_var_1'],
          $parameter['kunden_var_2'],
          $parameter['kunden_var_3'],
          $parameter['kunden_var_4'],
          $parameter['kunden_var_5'],
          MODULE_PAYMENT_SOFORTUEBERWEISUNG_INPUT_PASSWORT);
		   $parameter['key'] = md5(implode("|", $tmparray));
		  }

      foreach ($parameter as $key => $value) {
        $return .= xtc_draw_hidden_field($key, $value). "\n";
      }
		  return $return;
    }

    function get_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      // Update Configuration Key Length 64 is often too short
      xtc_db_query('ALTER TABLE ' . TABLE_CONFIGURATION . ' CHANGE configuration_key configuration_key VARCHAR( 255 ) NOT NULL');
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ( 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_ALLOWED', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_KDNR', '10000', '6', '1', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_PROJEKT', '500000', '6', '1', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_INPUT_PASSWORT', 'abcdef', '6', '1', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_BNA_PASSWORT', '123456', '6', '1', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_SORT_ORDER', '0', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_ORDER_STATUS_ID', '0', '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_STORE_TRANSACTION_DETAILS', 'False', '6', '6', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_REDIRECT', 'True', '6', '4', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_SOFORTUEBERWEISUNG_STATUS', 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_ALLOWED', 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_ZONE', 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_ORDER_STATUS_ID', 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_SORT_ORDER', 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_KDNR', 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_PROJEKT', 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_INPUT_PASSWORT', 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_BNA_PASSWORT', 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_REDIRECT', 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_STORE_TRANSACTION_DETAILS');
    }
  }
?>
