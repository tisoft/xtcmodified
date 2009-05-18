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

  require('includes/application_top.php');

  function xtc_create_random_value($length, $type = 'mixed') {
    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;

    $rand_value = '';
    while (strlen($rand_value) < $length) {
      if ($type == 'digits') {
        $char = xtc_rand(0,9);
      } else {
        $char = chr(xtc_rand(0,255));
      }
      if ($type == 'mixed') {
        if (eregi('^[a-z0-9]$', $char)) $rand_value .= $char;
      } elseif ($type == 'chars') {
        if (eregi('^[a-z]$', $char)) $rand_value .= $char;
      } elseif ($type == 'digits') {
        if (ereg('^[0-9]$', $char)) $rand_value .= $char;
      }
    }

    return $rand_value;
  }
  
  if ($_SESSION['customers_status']['customers_status_id'] != 0) die('Invalid Access');

  if (!empty($_GET['install'])) {
    switch ($_GET['install']) {
      case 'sofortueberweisung':
        // Module already installed
        if (MODULE_PAYMENT_SOFORTUEBERWEISUNG_STATUS == 'True') die('Modul bereits installiert<br><a href="' . xtc_href_link('modules.php', 'set=payment&module=sofortueberweisung', 'SSL') . '"> zurück zum ShopAdmin </a>');
        $parameter = array();
        $parameter['install'] = 'sofortueberweisung';
        $parameter['action'] = 'install';
        $parameter['input_passwort'] = xtc_create_random_value(12);
        $parameter['bna_passwort'] = xtc_create_random_value(12);
        $parameter['cnt_passwort'] = '';
        $get_parameter = '';
        $x = 0;
        while(list($key,$value) = each($parameter)) {
          if (empty($value)) continue;
          if ($x > 0) $get_parameter .= "&";
          $get_parameter .= $key . "=" . urlencode($value);
          $x++;
        }
        $backlink = xtc_href_link('sofortueberweisung_install.php', $get_parameter);
        $html_abortlink = xtc_catalog_href_link('sofortueberweisung_abort.php', session_name() . '=-KUNDEN_VAR_2-','SSL', false, false);
        $header_redir_url = xtc_catalog_href_link('checkout_success.php', session_name() . '=-KUNDEN_VAR_2-','SSL', false, false);
        $alert_http_url = xtc_catalog_href_link('sofortueberweisung_tracking.php','' ,'SSL', false, false);

        if ($_GET['action'] == 'install') {
          xtc_db_query('ALTER TABLE ' . TABLE_CONFIGURATION . ' CHANGE configuration_key configuration_key VARCHAR( 255 ) NOT NULL');
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ( 'MODULE_PAYMENT_SOFORTUEBERWEISUNG_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_ALLOWED', '', '6', '0', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_KDNR', '" . $_GET['kdnr'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_PROJEKT', '" . $_GET['projekt'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_INPUT_PASSWORT', '" . $_GET['input_passwort'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_BNA_PASSWORT', '" . $_GET['bna_passwort'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_SORT_ORDER', '0', '6', '0', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_ORDER_STATUS_ID', '0', '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_STORE_TRANSACTION_DETAILS', 'False', '6', '6', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNG_REDIRECT', 'True', '6', '4', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
          xtc_redirect(xtc_href_link('modules.php', 'set=payment&module=sofortueberweisung', 'SSL'));
          exit();
        }
        break;
      case 'sofortueberweisungvorkasse':
        // Module already installed
        if (MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_STATUS == 'True') die('Modul bereits installiert<br><a href="' . xtc_href_link_admin('admin/modules.php', 'set=payment&module=sofortueberweisungvorkasse', 'SSL') . '"> zurück zum ShopAdmin </a>');
        $parameter = array();
        $parameter['install'] = 'sofortueberweisungvorkasse';
        $parameter['action'] = 'install';
        $parameter['input_passwort'] = xtc_create_random_value(12);
        $parameter['bna_passwort'] = xtc_create_random_value(12);
        $parameter['cnt_passwort'] = '';
        $get_parameter = '';
        $x = 0;
        while(list($key,$value) = each($parameter)) {
          if (empty($value)) continue;
          if ($x > 0) $get_parameter .= "&";
          $get_parameter .= $key . "=" . urlencode($value);
          $x++;
        }
        $backlink = xtc_href_link('sofortueberweisung_install.php', $get_parameter);
        $html_abortlink = xtc_catalog_href_link('sofortueberweisung_abort.php', session_name() . '=-KUNDEN_VAR_2-','SSL', false, false);
        $header_redir_url = xtc_catalog_href_link('checkout_success.php', session_name() . '=-KUNDEN_VAR_2-','SSL', false, false);
        $alert_http_url = xtc_catalog_href_link('sofortueberweisung_tracking.php','' ,'SSL', false, false);

        if ($_GET['action'] == 'install') {
          xtc_db_query('ALTER TABLE ' . TABLE_CONFIGURATION . ' CHANGE configuration_key configuration_key VARCHAR( 255 ) NOT NULL');
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ( 'MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_ALLOWED', '', '6', '0', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_KDNR', '" . $_GET['kdnr'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_PROJEKT', '" . $_GET['projekt'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_INPUT_PASSWORT', '" . $_GET['input_passwort'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_BNA_PASSWORT', '" . $_GET['bna_passwort'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_SORT_ORDER', '0', '6', '0', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_ORDER_STATUS_ID', '0', '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGVORKASSE_STORE_TR_DETAILS', 'False', '6', '6', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
          xtc_redirect(xtc_href_link('modules.php', 'set=payment&module=sofortueberweisungvorkasse', 'SSL'));
          exit();
        }
        break;
      case 'sofortueberweisungredirect':
        // Module already installed
        if (MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS == 'True') die('Modul bereits installiert<br><a href="' . xtc_href_link_admin('admin/modules.php', 'set=payment&module=sofortueberweisungredirect', 'SSL') . '"> zurück zum ShopAdmin </a>');
        $parameter = array();
        $parameter['install'] = 'sofortueberweisungredirect';
        $parameter['action'] = 'install';
        $parameter['input_passwort'] = xtc_create_random_value(12);
        $parameter['bna_passwort'] = '';
        $parameter['cnt_passwort'] = xtc_create_random_value(12);
        $get_parameter = '';
        $x = 0;
        while(list($key,$value) = each($parameter)) {
          if (empty($value)) continue;
          if ($x > 0) $get_parameter .= "&";
          $get_parameter .= $key . "=" . urlencode($value);
          $x++;
        }
        $backlink = xtc_href_link('sofortueberweisung_install.php', $get_parameter);
        $html_abortlink = xtc_catalog_href_link('checkout_payment.php', 'payment_error=sofortueberweisungredirect&' . session_name() . '=-KUNDEN_VAR_2-','SSL', false, false);
        $header_redir_url = xtc_catalog_href_link('checkout_process.php', session_name() . '=-KUNDEN_VAR_2-&sovar3=-KUNDEN_VAR_3-&sovar4=-KUNDEN_VAR_3_MD5_PASS-&betrag=-BETRAG-','SSL', false, false);
        $alert_http_url = xtc_catalog_href_link('sofortueberweisung_tracking.php','' ,'SSL', false, false);

        if ($_GET['action'] == 'install') {

          xtc_db_query('ALTER TABLE ' . TABLE_CONFIGURATION . ' CHANGE configuration_key configuration_key VARCHAR( 255 ) NOT NULL');
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ( 'MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ALLOWED', '', '6', '0', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_KDNR', '" . $_GET['kdnr'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_PROJEKT', '" . $_GET['projekt'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_INPUT_PASSWORT', '" . $_GET['input_passwort'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_CNT_PASSWORT', '" . $_GET['cnt_passwort'] . "', '6', '1', now());");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_SORT_ORDER', '0.9', '6', '0', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_ORDER_STATUS_ID', '0', '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
          xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SOFORTUEBERWEISUNGREDIRECT_STORE_TRANSACTION_DETAILS', 'False', '6', '6', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now());");
          xtc_redirect(xtc_href_link('modules.php', 'set=payment&module=sofortueberweisungredirect', 'SSL'));
          exit();
        }
        break;
    }
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="https://www.sofortueberweisung.de/cms/modul/style.css">
<script type="text/javascript">
function init() {
	if(false) {
		document.getElementById('table1').style.display = '';
		document.getElementById('table2').style.display = '';
		document.getElementById('table3').style.display = '';
		document.getElementById('table4').style.display = '';
		document.getElementById('table5').style.display = '';
		document.getElementById('table6').style.display = '';
	}
}

function toggleTableById(id) {
	if(document.getElementById(id).style.display == 'none')
		document.getElementById(id).style.display = '';
	else
		document.getElementById(id).style.display = 'none';
}

function giveValue(value,name0, name1, name2, name3, name4) {
	if(name0 != '') document.getElementsByName(name0)[0].value = value;
	if(name1 != '') document.getElementsByName(name1)[0].value = value;
	if(name2 != '') document.getElementsByName(name2)[0].value = value;
	if(name3 != '') document.getElementsByName(name3)[0].value = value;
	if(name4 != '') document.getElementsByName(name4)[0].value = value;
}

function isInt(x) {
	var y=parseInt(x);
	if (isNaN(y)) return false;
	return x==y && x.toString()==y.toString();
}

function submitForm() {
	var is_error=false;
	var wert, wert1, wert2;

	wert = document.getElementById('sammel_input_project_name').value;
	if(!is_error) if(wert == '') {alert('Bitte Projektnamen eintragen'); is_error=true;}

	wert = document.getElementById('sammel_input_name').value;
	if(!is_error) if(wert == '') {alert('Bitte Namen eintragen'); is_error=true;}

	wert = document.getElementById('sammel_input_firma').value;
	if(!is_error) if(wert == '') {alert('Bitte Firma eintragen'); is_error=true;}

	wert = document.getElementById('sammel_input_strasse').value;
	if(!is_error) if(wert == '') {alert('Bitte Strasse eintragen'); is_error=true;}

	wert = document.getElementById('sammel_input_plz').value;
	if(!is_error) if(!isInt(wert) || wert == '' || wert.length!=5) {alert('PLZ muss aus 5 Ziffern bestehen'); is_error=true;}

	wert = document.getElementById('sammel_input_ort').value;
	if(!is_error) if(wert == '') {alert('Bitte Ort eintragen'); is_error=true;}

	wert = document.getElementById('sammel_input_land').value;
	if(!is_error) if(wert == '') {alert('Bitte Land eintragen'); is_error=true;}

	wert = document.getElementById('sammel_input_konto_inhaber').value;
	if(!is_error) if(wert == '') {alert('Bitte Kontoinhaber eintragen'); is_error=true;}

	wert = document.getElementById('sammel_input_kontonummer').value;
	if(!is_error) if(!isInt(wert) || wert == '') {alert('Kontonummer darf nur aus Ziffern bestehen'); is_error=true;}

	wert = document.getElementById('sammel_input_blz').value;
	if(!is_error) if(!isInt(wert) || wert == '' || wert.length!=8) {alert('BLZ muss aus 8 Ziffern bestehen'); is_error=true;}

	wert = document.getElementById('sammel_input_bank').value;
	if(!is_error) if(wert == '') {alert('Bitte Bankname eintragen'); is_error=true;}

	wert = document.getElementById('sammel_input_homepage').value;
	if(!is_error) if(wert == '') {alert('Bitte Homepage eintragen'); is_error=true;}

	wert = document.getElementById('sammel_input_email').value;
	if(!is_error) if(wert == '') {alert('Bitte Email-Adresse eintragen'); is_error=true;}

	//if(!is_error) if(document.getElementById('sammel_input_ustid').value == '' && document.getElementById('sammel_input_steuernummer').value == '') {alert('Bitte alle Pflichtfelder ausfüllen'); is_error=true;}

	wert1 = document.getElementById('sammel_input_telefon').value;
	wert2 = document.getElementById('sammel_input_mobil').value;
	if(!is_error) if(wert1 == '' && wert2 == '') {alert('Es muss mindestens ein telefonischer Kontakt angegeben sein'); is_error=true;}

	if(!is_error) document.getElementById('form').submit();
}
</script>
<style>
input {Font-family:tahoma,arial,verdana; font-size:11px; color:#666666; background-color:#FFFFFF; border:1px solid #808080;}
select {Font-family:tahoma,arial,verdana; font-size:11px; color:#666666; background-color:#FFFFFF; border:1px solid #808080;}
</style>
</head>
<body background="https://www.sofortueberweisung.de/cms/design/kachel.gif" leftmargin="0" topmargin="5" marginwidth="0" marginheight="0" onload="javascript:init()">

<table align="center" width="970" height="205" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <img src="https://www.sofortueberweisung.de/grafik/head_image.gif" />
    </td>
  </tr>
  <tr>
    <td>
	  <img src="https://www.sofortueberweisung.de/cms/p.gif" width=5 height=5 alt="" border=0 vspace=0 hspace=0>
	</td>
  </tr>
  <tr>
    <td>
      <table align="center" width="970" border="0" cellpadding="0" cellspacing="0">
	    <tr>
		  <td><img src="https://www.sofortueberweisung.de/cms/design/content_01.gif" width="190" height="10" alt="" border=0 vspace=0 hspace=0></td>
		  <td><img src="https://www.sofortueberweisung.de/cms/design/content_02.gif" width="212" height="10" alt="" border=0 vspace=0 hspace=0></td>
		  <td align="right"><img src="https://www.sofortueberweisung.de/cms/design/content_03.gif" width="568" height="10" alt="" border=0 vspace=0 hspace=0></td>
	    </tr>
	  </table>
	</td>
  </tr>
  <tr>
    <td bgcolor="white">
	<table width="100%" border="0">
	  <tr>
	    <td width="50%">
            <table border="0" cellpadding="2" cellspacing="0" style="padding-left: 20px;">
	          <tr><td>Projektname*</td><td><input id="sammel_input_project_name" size="40" type="text" onkeyup="javascript:giveValue(this.value,'project[projekt_name]', '', '', '', '')"></td></tr>
	          <tr><td>Projektbeschreibung</td><td><input size="40" type="text" onkeyup="javascript:giveValue(this.value,'project[projekt_beschreibung]', '', '', '', '' )"></td></tr>
              <tr><td colspan="2"><hr /></td></tr>
	          <tr><td>Anrede</td><td><input size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[r_anrede]','user[anspr_anrede]','project[anrede]', '', '')"></td></tr>
	          <tr><td>Name*</td><td><input id="sammel_input_name" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[r_name]','user[anspr_name]','project[name]', '', '')">
	          <tr><td>Firma*</td><td><input id="sammel_input_firma" size="40" type="text" value="<?php echo STORE_NAME ?>" onkeyup="javascript:giveValue(this.value,'user[firma]','user[r_firma]','project[firma]', '', '')"></td></tr>
	          <tr><td>Rechtsform</td><td>
				<select onchange="javascript:giveValue(this.value,'user[rechtsform]', '', '', '', '')">
					<option value="">Bitte auswählen...</option>
					<option value="AG" >AG</option>
					<option value="AG & Co. OHG" >AG & Co. OHG</option>
					<option value="ARGE" >ARGE</option>
					<option value="e.G." >e.G.</option>
					<option value="e.K." >e.K.</option>
					<option value="e.V." >e.V.</option>
					<option value="GbR" >GbR</option>
					<option value="GmbH" >GmbH</option>
					<option value="GmbH & Co. KG" >GmbH & Co. KG</option>
					<option value="GmbH & Co. OHG" >GmbH & Co. OHG</option>
					<option value="KG" >KG</option>
					<option value="KGaA" >KGaA</option>
					<option value="OHG" >OHG</option>
					<option value="Selbstständig" >Selbstständig</option>
					<option value="VvAG" >VvAG</option>
					<option value="Sonstige" >Sonstige</option>
				</select>
	          <tr><td>Strasse*</td><td><input id="sammel_input_strasse" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[strasse]','user[r_strasse]','project[strasse]', '', '')"></td></tr>
	          <tr><td>PLZ*</td><td><input maxlength="5" id="sammel_input_plz" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[plz]','user[r_plz]','project[plz]', '', '')"></td></tr>
	          <tr><td>Ort*</td><td><input id="sammel_input_ort" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[ort]','user[r_ort]','project[ort]', '', '')"></td></tr>
	          <tr><td>Land*</td><td><input id="sammel_input_land" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[land]','user[r_land]','project[land]', '', '')"></td></tr>
	          <tr><td>Telefon+</td><td><input id="sammel_input_telefon" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[telefon]','user[anspr_fon]','project[telefon]', '', '')"></td></tr>
	          <tr><td>Mobil+</td><td><input id="sammel_input_mobil" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[anspr_mobil]', '', '', '', '')"></td></tr>
	          <tr><td>Telefax</td><td><input size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[anspr_fax]','project[telefax]','user[telefax]', '', '')"></td></tr>
            <tr><td colspan="2"><hr /></td></tr>
			      <tr><td colspan="2"><b>Konto auf welches die Zahlungseingänge gutgeschrieben werden:</b></td></tr>
	          <tr><td>Konto Inhaber*</td><td><input id="sammel_input_konto_inhaber" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[elv_konto_inhaber]', 'project[an_konto_inhaber]', '', '', '')"></td></tr>
	          <tr><td>Kontonummer*</td><td><input maxlength="15"  id="sammel_input_kontonummer" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[elv_konto_nr]', 'project[an_konto_nr]', '', '', '')"></td></tr>
	          <tr><td>Bankleitzahl*</td><td><input maxlength="8" id="sammel_input_blz" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[elv_konto_blz]', 'project[an_konto_blz]', '', '', '')"></td></tr>
	          <tr><td>Name der Bank*</td><td><input id="sammel_input_bank" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[elv_konto_bank]', 'project[an_konto_bank]', '', '', '')"></td></tr>
            <tr><td colspan="2"><b>Hiermit beauftrage ich Sie, die fälligen <a href="https://www.sofortueberweisung.de/cms/index.php?plink=tarife&l=1&fs=" target="_blank">Beträge</a> per Lastschrift von meinem Konto einzuziehen.</b></td></tr>
	          <tr><td>Ustid</td><td><input id="sammel_input_ustid" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[ustid]', '', '', '', '')"></td></tr>
	          <tr><td>Steuernr</td><td><input id="sammel_input_steuernummer" size="40" type="text" onkeyup="javascript:giveValue(this.value,'user[steuernr]', '', '', '', '')"></td></tr>
            <tr><td colspan="2"><hr /></td></tr>
	          <tr><td>Homepage*</td><td><input id="sammel_input_homepage" size="40" type="text" value="<?php echo xtc_href_link(FILENAME_DEFAULT) ;?>" onkeyup="javascript:giveValue(this.value,'project[homepage]','user[homepage]', '', '', '')"></td></tr>
	          <tr><td>Email*</td><td><input id="sammel_input_email" size="40" type="text" value="<?php echo STORE_OWNER_EMAIL_ADDRESS; ?>" onkeyup="javascript:giveValue(this.value,'user[r_email]','user[anspr_email]','project[email]','user[email]','alert[alert_email_adresse]')"></td></tr>
			      <tr><td>Email-Benachrichtigungen bei Zahlungseingängen aktivieren:</td>
			        <td><input type="radio" name="sammel_input_email_flag" value="1" CHECKED onfocus="javascript:giveValue(this.value,'alert[alert_email_status]', '', '', '', '')"> Ja  <input type="radio" name="sammel_input_email_flag" value="0" onfocus="javascript:giveValue(this.value,'alert[alert_email_status]', '', '', '', '')"> Nein</td>
			      </tr>
        	  <tr>
	            <td colspan="2">
	              <input type="button" name="Absenden" value="Absenden" onclick="javascript:submitForm()"><br><b>Nach der Registrierung des Projekts bei Sofortüberweisung, unbedingt dem Link zurück zum Shop folgen!</b>
		        </td>
	          </tr>
	        </table>
		</td>
	    <td valign="top" align="left">
          <form method="post" action="https://www.sofort-ueberweisung.de/createnew.php" id="form">
          <table border="0" cellspacing="0">
		    <tr><td><a href="#" onclick="javascript:toggleTableById('table2')"><u>Alternative Rechnungsanschrift</u></a></td></tr>
			<tr><td>
		    <table border="0" cellspacing="0" id="table2" style="display:none">
              <tr><td>Anrede</td><td><input size="40" type="text" name="user[r_anrede]" value=""></td></tr>
              <tr><td>Firma</td><td><input size="40" type="text" name="user[r_firma]" value="<?php echo STORE_NAME ?>"></td></tr>
              <tr><td>Name</td><td><input size="40" type="text" name="user[r_name]" value=""></td></tr>
              <tr><td>Strasse</td><td><input size="40" type="text" name="user[r_strasse]" value=""></td></tr>
              <tr><td>Plz</td><td><input size="40" type="text" name="user[r_plz]" value=""></td></tr>
              <tr><td>Ort</td><td><input size="40" type="text" name="user[r_ort]" value=""></td></tr>
              <tr><td>Land</td><td><input size="40" type="text" name="user[r_land]" value=""></td></tr>
              <tr><td>Email</td><td><input size="40" type="text" name="user[r_email]" value="<?php echo STORE_OWNER_EMAIL_ADDRESS; ?>"></td></tr>
			</table>
			</td></tr>
          </table>
		  <table border="0" cellspacing="0">
		    <tr><td><a href="#" onclick="javascript:toggleTableById('table4')"><u>Alternativer Ansprechpartner</u></a></td></tr>
		    <tr><td>
              <table border="0" cellspacing="0" id="table4" style="display:none">
                <tr><td>Anrede</td><td><input size="40" type="text" name="user[anspr_anrede]" value=""></td><td>&nbsp;</td></tr>
                <tr><td>Name</td><td><input size="40" type="text" name="user[anspr_name]" value=""></td><td>&nbsp;</td></tr>
                <tr><td>Telefon</td><td><input size="40" type="text" name="user[anspr_fon]" value=""></td><td>&nbsp;</td></tr>
                <tr><td>Telefax</td><td><input size="40" type="text" name="user[anspr_fax]" value=""></td><td>&nbsp;</td></tr>
                <tr><td>Mobil</td><td><input size="40" type="text" name="user[anspr_mobil]" value=""></td><td>&nbsp;</td></tr>
                <tr><td>Email</td><td><input size="40" type="text" name="user[anspr_email]" value="<?php echo STORE_OWNER_EMAIL_ADDRESS; ?>"></td><td>&nbsp;</td></tr>
              </table>
		    </td></tr>
		  </table>
		</td>
	  </tr>
	  <tr><td style="padding-left: 20px;">* zeichnet Pflichtfelder aus<br />+ zeichnet Felder aus, bei denen mindestens eines gefüllt sein muss<br />Alle Zahlen sind ohne Leerzeichen einzugeben</td></tr>
    </table>
  </td></tr>
  <tr>
    <td>
	  <table width="970" border="0" cellspacing="0" cellpadding="0">
	    <tr>
		  <td width="190" valign="top"><img src="https://www.sofortueberweisung.de/cms/design/content_11.gif" width=190 height=10 alt="" border=0 vspace=0 hspace=0></td>
		  <td bgcolor="white"><img src="https://www.sofortueberweisung.de/cms/p.gif" width=5 height=10 alt="" border=0 vspace=0 hspace=0></td>
		  <td align="right" width="568"><img src="https://www.sofortueberweisung.de/cms/design/content_13.gif" width=568 height=10 alt="" border=0 vspace=0 hspace=0></td>
		</tr>
	  </table>
	</td>
  </tr>
</table>

<table border="1" id="table1" style="display:none;">
  <tr><td>Benutzername:</td><td><input type="text" name="user[user]" value=""></td><td>*</td></tr>
  <tr><td>Firma</td><td><input type="text" name="user[firma]" value="<?php echo STORE_NAME ?>"></td><td>*</td></tr>
  <tr><td>Rechtsform</td><td><input type="text" name="user[rechtsform]" value=""></td><td>&nbsp;</td></tr>
  <tr><td>Strasse</td><td><input type="text" name="user[strasse]" value=""></td><td>*</td></tr>
  <tr><td>Plz</td><td><input type="text" name="user[plz]" value=""></td><td>*</td></tr>
  <tr><td>Ort</td><td><input type="text" name="user[ort]" value=""></td><td>*</td></tr>
  <tr><td>Land</td><td><input type="text" name="user[land]" value=""></td><td>*</td></tr>
  </table>

  <table border="1" id="table3" style="display:none">
  <tr><td>Telefon</td><td><input type="text" name="user[telefon]" value=""></td><td>&nbsp;</td></tr>
  <tr><td>Telefax</td><td><input type="text" name="user[telefax]" value=""></td><td>&nbsp;</td></tr>
  <tr><td>Email</td><td><input type="text" name="user[email]" value="<?php echo STORE_OWNER_EMAIL_ADDRESS; ?>"></td><td>*</td></tr>
  <tr><td>Homepage</td><td><input type="text" name="user[homepage]" value="<?php echo xtc_href_link(FILENAME_DEFAULT) ;?>"></td><td>&nbsp;</td></tr>
  <tr><td>Ustid</td><td><input type="text" name="user[ustid]" value=""></td><td>*</td></tr>
  <tr><td>Steuernr</td><td><input type="text" name="user[steuernr]" value=""></td><td>*</td></tr>
  </table>

  <table border="1" id="table5" style="display:none;">
  <tr><td colspan=3>Konto, von dem die Gebühren von Sofort-Überweisung abgebucht werden:</td></tr>
  <tr><td>Konto Inhaber</td><td><input type="text" name="user[elv_konto_inhaber]" value=""></td><td>*</td></tr>
  <tr><td>Kontonr</td><td><input type="text" name="user[elv_konto_nr]" value=""></td><td>*</td></tr>
  <tr><td>Konto BLZ</td><td><input type="text" name="user[elv_konto_blz]" value=""></td><td>*</td></tr>
  <tr><td>Konto Bank</td><td><input type="text" name="user[elv_konto_bank]" value=""></td><td>*</td></tr>
  </table>

<table border="0" cellspacing="0" id="table6" style="display:none">
  <tr><td>Name</td><td><input type="text" name="project[projekt_name]" value=""></td><td>*</td></tr>
  <tr><td>Beschreibung</td><td><input type="text" name="project[projekt_beschreibung]" value=""></td><td>&nbsp;</td></tr>
  <tr><td>Anrede</td><td><input type="text" name="project[anrede]" value=""></td><td>*</td></tr>
  <tr><td>Firma</td><td><input type="text" name="project[firma]" value="<?php echo STORE_NAME ?>"></td><td>*</td></tr>
  <tr><td>Name</td><td><input type="text" name="project[name]" value=""></td><td>*</td></tr>
  <tr><td>Strasse</td><td><input type="text" name="project[strasse]" value=""></td><td>*</td></tr>
  <tr><td>Plz</td><td><input type="text" name="project[plz]" value=""></td><td>*</td></tr>
  <tr><td>Ort</td><td><input type="text" name="project[ort]" value=""></td><td>*</td></tr>
  <tr><td>Land</td><td><input type="text" name="project[land]" value=""></td><td>*</td></tr>
  <tr><td>Telefon</td><td><input type="text" name="project[telefon]" value=""></td><td>*</td></tr>
  <tr><td>Telefax</td><td><input type="text" name="project[telefax]" value=""></td><td>*</td></tr>
  <tr><td>Email</td><td><input type="text" name="project[email]" value="<?php echo STORE_OWNER_EMAIL_ADDRESS; ?>"></td><td>*</td></tr>
  <tr><td>Homepage</td><td><input type="text" name="project[homepage]" value="<?php echo xtc_href_link(FILENAME_DEFAULT) ;?>"></td><td>*</td></tr>
</table>

<table border="1" id="table7" style="display:none">
  <tr><td>Email</td><td><input type="text" name="alert[alert_email_adresse]" value="<?php echo STORE_OWNER_EMAIL_ADDRESS; ?>"></td><td>&nbsp;</td></tr>
  <tr><td>Email Benachrichtigung bei Zahlungseingang aktivieren</td><td><input type="text" name="alert[alert_email_status]" value="1"></td><td>&nbsp;</td></tr>
</table>

<input type="hidden" name="user[backlink]" value="<?php echo $backlink; ?>">
<input type="hidden" name="user[vpartner]" value="16">
<input type="hidden" name="project[html_abortlink]" value="<?php echo $html_abortlink; ?>">
<input type="hidden" name="project[header_redir_do]" value="1">
<input type="hidden" name="project[header_redir_url]" value="<?php echo $header_redir_url; ?>">
<input type="hidden" name="project[const_betrag]" value="1">
<input type="hidden" name="project[const_v_zweck_1]" value="1">
<input type="hidden" name="project[const_v_zweck_2]" value="1">
<input type="hidden" name="project[use_input_passwort]" value="1">
<input type="hidden" name="project[an_konto_inhaber]" value="">
<input type="hidden" name="project[an_konto_nr]" value="">
<input type="hidden" name="project[an_konto_blz]" value="">
<input type="hidden" name="project[an_konto_bank]" value="">
<input type="hidden" name="project[input_passwort]" value="<?php echo $parameter['input_passwort']; ?>">
<input type="hidden" name="project[content_passwort]" value="<?php echo $parameter['cnt_passwort']; ?>">
<input type="hidden" name="project[shopsystem]" value="<?php echo PROJECT_VERSION; ?>">
<input type="hidden" name="alert[alert_passwort]" value="<?php echo $parameter['bna_passwort']; ?>">
<input type="hidden" name="alert[alert_email_text_custom]" value="0">
<?php
if (ENABLE_SSL == true) {
?>
<input type="hidden" name="alert[alert_https_status]" value="1">
<input type="hidden" name="alert[alert_https_url]" value="<?php echo $alert_http_url; ?>">
<input type="hidden" name="alert[alert_https_method]" value="post">
<input type="hidden" name="alert[alert_https_var_text]" value="text">
<input type="hidden" name="alert[alert_https_var_pass]" value="pw">
<?php
} else {
?>
<input type="hidden" name="alert[alert_http_status]" value="1">
<input type="hidden" name="alert[alert_http_url]" value="<?php echo $alert_http_url; ?>">
<input type="hidden" name="alert[alert_http_method]" value="post">
<input type="hidden" name="alert[alert_http_var_text]" value="text">
<input type="hidden" name="alert[alert_http_var_pass]" value="pw">
<?php
}
?>

<input type="hidden" name="debug" value="1">
</form>

</body>
</html>