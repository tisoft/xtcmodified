<?php
/* --------------------------------------------------------------
   $Id: install_step7.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   Released under the GNU General Public License
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (install_step7.php,v 1.26 2003/08/17); www.nextcommerce.org
   (c) 2009 xtcModified (install_step7.php,v 1.00 2009/07/13); www.www.xtc-modified.org 
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('../includes/configure.php'); 
  
  require('includes/application.php');
  
  require_once(DIR_FS_INC . 'xtc_rand.inc.php');
  require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');   
  require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
  require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_input.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
  require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
  require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');

  //BOF - web28 - 2010.02.11 - NEW LANGUAGE HANDLING IN application.php
  //include('language/'.$_SESSION['language'].'.php');
  include('language/'.$lang.'.php');
  //EOF - web28 - 2010.02.11 - NEW LANGUAGE HANDLING IN application.php
  
  // connect do database
  xtc_db_connect() or die('Unable to connect to database server!'); 
 
  // get configuration data
  $configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
  while ($configuration = xtc_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }

   $messageStack = new messageStack();
  
    $process = false;
  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    $process = true;
        
                $status_discount = xtc_db_prepare_input($_POST['STATUS_DISCOUNT']);
                $status_ot_discount_flag = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT_FLAG']);
                $status_ot_discount = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT']);
                $graduated_price = xtc_db_prepare_input($_POST['STATUS_GRADUATED_PRICE']);
                $show_price = xtc_db_prepare_input($_POST['STATUS_SHOW_PRICE']);
                $show_tax = xtc_db_prepare_input($_POST['STATUS_SHOW_TAX']);

        
                $status_discount2 = xtc_db_prepare_input($_POST['STATUS_DISCOUNT2']);
                $status_ot_discount_flag2 = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT_FLAG2']);
                $status_ot_discount2 = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT2']);
                $graduated_price2 = xtc_db_prepare_input($_POST['STATUS_GRADUATED_PRICE2']);
                $show_price2 = xtc_db_prepare_input($_POST['STATUS_SHOW_PRICE2']);
                $show_tax2 = xtc_db_prepare_input($_POST['STATUS_SHOW_TAX2']);

    $error = false;
        // default guests    
     if (strlen($status_discount) < '3') {
        $error = true;
        $messageStack->add('install_step7', ENTRY_DISCOUNT_ERROR);
        }
     if (strlen($status_ot_discount) < '3') {
        $error = true;
        $messageStack->add('install_step7', ENTRY_OT_DISCOUNT_ERROR);
        }
     if ( ($status_ot_discount_flag != '1') && ($status_ot_discount_flag != '0') ) {
        $error = true;

        $messageStack->add('install_step7', SELECT_OT_DISCOUNT_ERROR);
        }
     if ( ($graduated_price != '1') && ($graduated_price != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_GRADUATED_ERROR);
        }
     if ( ($show_price != '1') && ($show_price != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_PRICE_ERROR);
        }
     if ( ($show_tax != '1') && ($show_tax != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_TAX_ERROR);
        }
        
        // default customers
     if (strlen($status_discount2) < '3') {
        $error = true;
      $messageStack->add('install_step7', ENTRY_DISCOUNT_ERROR2);
        }        
     if (strlen($status_ot_discount2) < '3') {
          $error = true;
          $messageStack->add('install_step7', ENTRY_OT_DISCOUNT_ERROR2);
        }
     if ( ($status_ot_discount_flag2 != '1') && ($status_ot_discount_flag2 != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_OT_DISCOUNT_ERROR2);
        }
     if ( ($graduated_price2 != '1') && ($graduated_price2 != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_GRADUATED_ERROR2);
        }
     if ( ($show_price2 != '1') && ($show_price2 != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_PRICE_ERROR2);
        }
     if ( ($show_tax2 != '1') && ($show_tax2 != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_TAX_ERROR2);
        }
        
	if ($error == false) {
	                
	// admin
	xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES ('0', '2', 'Admin', 1, 'admin_status.gif', '0.00', '1', '0.00', '1', '1', '1')");
	xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES ('0', '1', 'Admin', 1, 'admin_status.gif', '0.00', '1', '0.00', '1', '1', '1')");
	
	// status Guest
	xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (1, 2, 'Gast', 1, 'guest_status.gif', '".$status_discount."', '".$status_ot_discount_flag."', '".$status_ot_discount."', '".$graduated_price."', '".$show_price."', '".$show_tax."')");
	xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (1, 1, 'Guest', 1, 'guest_status.gif', '".$status_discount."', '".$status_ot_discount_flag."', '".$status_ot_discount."', '".$graduated_price."', '".$show_price."', '".$show_tax."')");
	
	// status New customer
	xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (2, 2, 'Neuer Kunde', 1, 'customer_status.gif', '".$status_discount2."', '".$status_ot_discount_flag2."', '".$status_ot_discount2."', '".$graduated_price2."', '".$show_price2."', '".$show_tax2."')");
	xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (2, 1, 'New Customer', 1, 'customer_status.gif', '".$status_discount2."', '".$status_ot_discount_flag2."', '".$status_ot_discount2."', '".$graduated_price2."', '".$show_price2."', '".$show_tax2."')");
	
	// status Merchant
	xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (3, 2, 'H&auml;ndler', 1, 'merchant_status.gif', '0.00', '0', '0.00', '1', 1, 0)");
	xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (3, 1, 'Merchant', 1, 'merchant_status.gif', '0.00', '0', '0.00', '1', 1, 0)");
	
	// create Group prices (Admin wont get own status!)
	
	xtc_db_query("create table personal_offers_by_customers_status_1 (price_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,products_id int NOT NULL,quantity int, personal_offer decimal(15,4)) ");
	xtc_db_query("create table personal_offers_by_customers_status_2 (price_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,products_id int NOT NULL,quantity int, personal_offer decimal(15,4)) ");
	xtc_db_query("create table personal_offers_by_customers_status_0 (price_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,products_id int NOT NULL,quantity int, personal_offer decimal(15,4)) ");
	xtc_db_query("create table personal_offers_by_customers_status_3 (price_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,products_id int NOT NULL,quantity int, personal_offer decimal(15,4)) ");
	xtc_redirect(xtc_href_link('xtc_installer/install_finished.php', '', 'NONSSL'));
	}                       
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>xtc:Modified Installer - STEP 7 / Success</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require('includes/form_check.js.php'); ?>
<style type="text/css">
body { background: #eee; font-family: Arial, sans-serif; font-size: 12px;}
table,td,div { font-family: Arial, sans-serif; font-size: 12px;}
h1 { font-size: 18px; margin: 0; padding: 0; margin-bottom: 10px; }
<!--
.messageBox {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 1;
}
.messageStackError, .messageStackWarning { font-family: Verdana, Arial, sans-serif; font-weight: bold; font-size: 10px; background-color: #; }
-->
</style>
</head>

<body>
<table width="800" style="border:30px solid #fff;" bgcolor="#f3f3f3" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td height="95" colspan="2" ><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td><img src="images/logo.gif" alt="" /></td>
        </tr>
      </table>
  </tr>
  <tr> 
        <td align="center" valign="top"> 
      <br />
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td> <img src="images/step7.gif" width="705" height="180" border="0"><br />
            <br />
            <br />
            <div style="border:1px solid #ccc; background:#fff; padding:10px;"><?php echo TEXT_WELCOME_STEP7; ?></div></td>
        </tr>
      </table> 
      <br />
            <table width="95%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td>
             <form name="install" action="install_step7.php" method="post" onSubmit="return check_form(install_step6);">
              <?php echo $input_lang; ?>
              <input name="action" type="hidden" value="process">
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td><strong>
                    <h1><?php echo TITLE_GUEST_CONFIG; ?> </h1></strong>
                    <strong><?php echo TEXT_REQU_INFORMATION; ?></strong></td>
                                  </tr>
              </table>
                          
                          <div style="border:1px solid #ccc; background:#fff; padding:10px;">
              <table width="100%" border="0">
                <tr> 
                  <td><p><strong><?php echo TEXT_STATUS_DISCOUNT; ?></strong><br />
                      <?php echo xtc_draw_input_field_installer('STATUS_DISCOUNT','0.00'); ?><br />
                      <?php echo TEXT_STATUS_DISCOUNT_LONG; ?></p>
                    <p><strong><?php echo TEXT_STATUS_OT_DISCOUNT_FLAG; ?></strong><br />
                      <?php echo  TEXT_ZONE_YES; ?> <?php echo xtc_draw_radio_field_installer('STATUS_OT_DISCOUNT_FLAG', '1'); ?><?php echo  TEXT_ZONE_NO; ?>
                      <?php echo xtc_draw_radio_field_installer('STATUS_OT_DISCOUNT_FLAG', '0', 'true'); ?><br />
                      <?php echo TEXT_STATUS_OT_DISCOUNT_FLAG_LONG; ?></p>
                    <p><strong><?php echo TEXT_STATUS_OT_DISCOUNT; ?></strong><br />
                      <?php echo xtc_draw_input_field_installer('STATUS_OT_DISCOUNT','0.00'); ?><br />
                      <?php echo TEXT_STATUS_OT_DISCOUNT_LONG; ?></p>
                    <p><strong><?php echo TEXT_STATUS_GRADUATED_PRICE; ?></strong><br />
                      <?php echo  TEXT_ZONE_YES; ?> <?php echo xtc_draw_radio_field_installer('STATUS_GRADUATED_PRICE', '1'); ?>
                      <?php echo  TEXT_ZONE_NO; ?> 
                      <?php echo xtc_draw_radio_field_installer('STATUS_GRADUATED_PRICE', '0', 'true'); ?><br />
                      <?php echo TEXT_STATUS_GRADUATED_PRICE_LONG; ?></p>
                    <p><br />
                      <strong><?php echo TEXT_STATUS_SHOW_PRICE; ?></strong><br />
                      <?php echo  TEXT_ZONE_YES; ?> <?php echo xtc_draw_radio_field_installer('STATUS_SHOW_PRICE', '1', 'true'); ?>
                      <?php echo  TEXT_ZONE_NO; ?> 
                      <?php echo xtc_draw_radio_field_installer('STATUS_SHOW_PRICE', '0'); ?><br />
                      <?php echo TEXT_STATUS_SHOW_PRICE_LONG; ?></p>
                    <p><strong><?php echo TEXT_STATUS_SHOW_TAX; ?></strong><br />
                      <?php echo  TEXT_ZONE_YES; ?> <?php echo xtc_draw_radio_field_installer('STATUS_SHOW_TAX', '1', 'true'); ?>
                      <?php echo  TEXT_ZONE_NO; ?> 
                      <?php echo xtc_draw_radio_field_installer('STATUS_SHOW_TAX', '0'); ?><br />
                      <?php echo TEXT_STATUS_SHOW_TAX_LONG; ?></p></td>     
                </tr>
              </table>
              </div><br />
                          <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr> 
                <td>
                  <h1><?php echo TITLE_CUSTOMERS_CONFIG; ?> </h1>
                 </td>
                
              </tr>
            </table>
          <div style="border:1px solid #ccc; background:#fff; padding:10px;">
              <table width="100%" border="0">
                <tr> 
                  <td><p><strong><?php echo TEXT_STATUS_DISCOUNT; ?></strong><br />
                      <?php echo xtc_draw_input_field_installer('STATUS_DISCOUNT2','0.00'); ?><br />
                      <?php echo TEXT_STATUS_DISCOUNT_LONG; ?></p>
                    <p><strong><?php echo TEXT_STATUS_OT_DISCOUNT_FLAG; ?></strong><br />
                      <?php echo  TEXT_ZONE_YES; ?> <?php echo xtc_draw_radio_field_installer('STATUS_OT_DISCOUNT_FLAG2', '1'); ?>
                      <?php echo  TEXT_ZONE_NO; ?> 
                      <?php echo xtc_draw_radio_field_installer('STATUS_OT_DISCOUNT_FLAG2', '0', 'true'); ?><br />
                      <?php echo TEXT_STATUS_OT_DISCOUNT_FLAG_LONG; ?></p>
                    <p><strong><?php echo TEXT_STATUS_OT_DISCOUNT; ?></strong><br />
                      <?php echo xtc_draw_input_field_installer('STATUS_OT_DISCOUNT2','0.00'); ?><br />
                      <?php echo TEXT_STATUS_OT_DISCOUNT_LONG; ?></p>
                    <p><strong><?php echo TEXT_STATUS_GRADUATED_PRICE; ?></strong><br />
                      <?php echo  TEXT_ZONE_YES; ?> <?php echo xtc_draw_radio_field_installer('STATUS_GRADUATED_PRICE2', '1', 'true'); ?>
                      <?php echo  TEXT_ZONE_NO; ?> 
                      <?php echo xtc_draw_radio_field_installer('STATUS_GRADUATED_PRICE2', '0'); ?><br />
                      <?php echo TEXT_STATUS_GRADUATED_PRICE_LONG; ?></p>
                    <p><br />
                      <strong><?php echo TEXT_STATUS_SHOW_PRICE; ?></strong><br />
                      <?php echo  TEXT_ZONE_YES; ?> <?php echo xtc_draw_radio_field_installer('STATUS_SHOW_PRICE2', '1', 'true'); ?>
                      <?php echo  TEXT_ZONE_NO; ?> 
                      <?php echo xtc_draw_radio_field_installer('STATUS_SHOW_PRICE2', '0'); ?><br />
                      <?php echo TEXT_STATUS_SHOW_PRICE_LONG; ?></p>
                    <p><strong><?php echo TEXT_STATUS_SHOW_TAX; ?></strong><br />
                      <?php echo  TEXT_ZONE_YES; ?> <?php echo xtc_draw_radio_field_installer('STATUS_SHOW_TAX2', '1', 'true'); ?>
                      <?php echo  TEXT_ZONE_NO; ?> 
                      <?php echo xtc_draw_radio_field_installer('STATUS_SHOW_TAX2', '0'); ?><br />
                      <?php echo TEXT_STATUS_SHOW_TAX_LONG; ?></p></td>
                    
                </tr>
              </table>
              </div>
              <br />
             
                <input name="image" type="image" src="buttons/<?php echo $lang;?>/button_continue.gif" align="right">
              <br /><br />
            </form></td>
        </tr>
      </table> 
      
    </td>
  </tr>
</table>
<br />
<div align="center" style="font-family:Arial, sans-serif; font-size:11px;"><?php echo TEXT_FOOTER; ?></div>
</body>
</html>
