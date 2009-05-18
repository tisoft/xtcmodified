<?php
/**
 *
 *
 * @version Sofort�berweisung 1.9  05.06.2007
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

  class sofortueberweisungredirect {
    var $code, $title, $description, $enabled;

// class constructor
    function sofortueberweisungredirect() {
      global $order;

      $this->code = 'sofortueberweisungredirect';
      $this->title = MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS == 'True') ? true : false);

      if (is_object($order)) $this->update_status();

      $this->email_footer = '';
      $this->text_redirect = MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_REDIRECT;
      $this->form_action_url = 'https://www.sofort-ueberweisung.de/payment.php';
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ZONE > 0) ) {
        $check_flag = false;
        $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
                   'fields' => array(array('title' => MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_DESCRIPTION_CHECKOUT_PAYMENT)));
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return array('title' => MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_DESCRIPTION_CHECKOUT_CONFIRMATION);
    }

    function process_button() {
      global $order, $xtPrice, $cart;
  		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
	  		$total = $order->info['total'] + $order->info['tax'];
		  } else {
			  $total = $order->info['total'];
		  }
      // Fix for XTC Bug
      // $order->info['total'] is in 'before_process' String without Tax
      // so it has to be set here

      $_SESSION['sofortueberweisung_total'] =  number_format($xtPrice->xtcCalculateCurrEx($total, 'EUR'), $xtPrice->get_decimal_places('EUR'), '.', '');

      $customer_id = $_SESSION['customer_id'];
     // $order =
      $parameter= array();
      $return = '';
		  $parameter['kdnr']	= MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_KDNR;  // Repr�sentiert Ihre Kundennummer bei der Sofort�berweisung
		  $parameter['projekt'] = MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_PROJEKT;  // Die verantwortliche Projektnummer bei der Sofort�berweisung, zu der die Zahlung geh�rt
      $parameter['betrag'] = $_SESSION['sofortueberweisung_total'];
      // number_format($order->info['total'] *  $currencies->get_value('EUR'), 2, '.','');   // Beziffert den Zahlungsbetrag, der an Sie �bermittelt werden soll
      $vzweck1 = str_replace('{{orderid}}', $insert_id, MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_V_ZWECK_1);
      $vzweck2 = str_replace('{{orderid}}', $insert_id, MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_V_ZWECK_2);

      $vzweck1 = str_replace('{{order_date}}', strftime(DATE_FORMAT_SHORT), $vzweck1);
      $vzweck2 = str_replace('{{order_date}}', strftime(DATE_FORMAT_SHORT), $vzweck2);

      $vzweck1 = str_replace('{{customer_id}}', $customer_id, $vzweck1);
      $vzweck2 = str_replace('{{customer_id}}', $customer_id, $vzweck2);

      $vzweck1 = str_replace('{{customer_cid}}', $order->customer['csID'], $vzweck1);
      $vzweck2 = str_replace('{{customer_cid}}', $order->customer['csID'], $vzweck2);

      $vzweck1 = str_replace('{{customer_name}}', $order->customer['firstname'] . ' ' . $order->customer['lastname'], $vzweck1);
      $vzweck2 = str_replace('{{customer_name}}', $order->customer['firstname'] . ' ' . $order->customer['lastname'], $vzweck2);

      $vzweck1 = str_replace('{{customer_company}}', $order->customer['company'], $vzweck1);
      $vzweck2 = str_replace('{{customer_company}}', $order->customer['company'], $vzweck2);

      $vzweck1 = str_replace('{{customer_email}}', $order->customer['email_address'], $vzweck1);
      $vzweck2 = str_replace('{{customer_email}}', $order->customer['email_address'], $vzweck2);

      // K�rzen auf 27 Zeichen
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

      if (strlen(MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_INPUT_PASSWORT) > 0) {
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
          MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_INPUT_PASSWORT);
		   $parameter['key'] = md5(implode("|", $tmparray));
		  }

		  $process_button_string = '';
      foreach ($parameter as $key => $value) {
        $process_button_string .= xtc_draw_hidden_field($key, $value). "\n";
      }
      return $process_button_string;

    }

    function before_process() {
      global $order, $xtPrice;
      $md5var4 = md5($_GET['sovar3'] . MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_CNT_PASSWORT);
      // Statusupdate nur wenn keine Cart�nderung vorgenommen
     // Valid returns are i.e. 13.12 or 13,12 changes sometimes
      if ($md5var4 == $_GET['sovar4'] && ($_GET['betrag'] == number_format($_SESSION['sofortueberweisung_total'], 2, ',', '') || $_GET['betrag'] == number_format($_SESSION['sofortueberweisung_total'], 2, '.', '') ) ) {
        // we have an verified order
        if ( (int)MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ORDER_STATUS_ID > 0) {
          $this->order_status = MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ORDER_STATUS_ID;
          $order->info['order_status'] = MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ORDER_STATUS_ID;
        }
      } else {
        $order->info['comments'] .= "\n" . MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_CHECK_ERROR . '\n' . $_GET['betrag'] .'!=' . $_SESSION['sofortueberweisung_total'] ;
      }
      if (MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STORE_TRANSACTION_DETAILS == 'True') {
        $order->info['comments'] .= "\n" . serialize($_REQUEST);
      }

      return false;
    }


    function after_process() {

      return false;
    }

    function checkout_success($insert_id) {
		  return false;
    }

    function get_error() {
      $error = false;
      if (!empty($_GET['payment_error'])) {
        $error = array('title' => MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_ERROR_HEADING,
                      'error' => MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_TEXT_ERROR_MESSAGE);
      }
      return $error;

    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      // Update Configuration Key Length 64 is often too short
      xtc_db_query('ALTER TABLE ' . TABLE_CONFIGURATION . ' CHANGE configuration_key configuration_key VARCHAR( 255 ) NOT NULL');
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ( 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ALLOWED', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_KDNR', '10000', '6', '1', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_PROJEKT', '500000', '6', '1', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_INPUT_PASSWORT', 'abcdef', '6', '1', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_CNT_PASSWORT', '123456', '6', '1', now());");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_SORT_ORDER', '0.9', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ORDER_STATUS_ID', '0', '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STORE_TRANSACTION_DETAILS', 'False', '6', '6', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS', 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ALLOWED', 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ZONE', 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ORDER_STATUS_ID', 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_SORT_ORDER', 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_KDNR', 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_PROJEKT', 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_INPUT_PASSWORT', 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_CNT_PASSWORT', 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STORE_TRANSACTION_DETAILS');
    }
  }
?>
