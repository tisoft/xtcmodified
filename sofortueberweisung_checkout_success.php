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

  include( 'includes/application_top.php');
   // create smarty elements
  $smarty = new Smarty;
  // include boxes
  require(DIR_FS_CATALOG .'templates/'.CURRENT_TEMPLATE. '/source/boxes.php');
  // include needed functions
  require_once(DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_image_button.inc.php');

  // if the customer is not logged on, redirect them to the shopping cart page
  if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
  }

  if (isset($_GET['action']) && ($_GET['action'] == 'update')) {
    $notify_string = 'action=notify&';
    $notify = $_POST['notify'];
    if (!is_array($notify)) $notify = array($notify);
    for ($i=0, $n=sizeof($notify); $i<$n; $i++) {
      $notify_string .= 'notify[]=' . $notify[$i] . '&';
    }
    if (strlen($notify_string) > 0) $notify_string = substr($notify_string, 0, -1);
    if ($_SESSION['account_type']!=1) {
    xtc_redirect(xtc_href_link(FILENAME_DEFAULT, $notify_string));
    } else {
    xtc_redirect(xtc_href_link(FILENAME_LOGOFF, $notify_string));
    }

  }


 $breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_SUCCESS);
  $breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_SUCCESS);

  $global_query = xtc_db_query("select global_product_notifications from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . (int)$_SESSION['customer_id'] . "'");
  $global = xtc_db_fetch_array($global_query);

    $orders_query = xtc_db_query("select orders_id from " . TABLE_ORDERS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "' order by date_purchased desc limit 1");
    $orders = xtc_db_fetch_array($orders_query);

  if ($global['global_product_notifications'] != '1') {

    $products_array = array();
    $products_query = xtc_db_query("select products_id, products_name from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$orders['orders_id'] . "' order by products_name");
    while ($products = xtc_db_fetch_array($products_query)) {
      $products_array[] = array('id' => $products['products_id'],
                                'text' => $products['products_name']);
    }
  }

 require(DIR_WS_INCLUDES . 'header.php');


 $smarty->assign('FORM_ACTION',xtc_draw_form('order', xtc_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update', 'SSL')));
 $smarty->assign('BUTTON_CONTINUE',xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
 $smarty->assign('BUTTON_PRINT','<img src="'.'templates/'.CURRENT_TEMPLATE.'/buttons/' . $_SESSION['language'].'/button_print.gif" style="cursor:hand" onclick="window.open(\''. xtc_href_link(FILENAME_PRINT_ORDER,'oID='.$orders['orders_id']).'\', \'popup\', \'toolbar=0, width=640, height=600\')" />');
 $smarty->assign('FORM_END','</form>');
 // GV Code Start
 $gv_query=xtc_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id='".$_SESSION['customer_id']."'");
    if ($gv_result=xtc_db_fetch_array($gv_query)) {
       if ($gv_result['amount'] > 0) {
            $smarty->assign('GV_SEND_LINK', xtc_href_link(FILENAME_GV_SEND));
            }
       }
 // GV Code End
 // Google Conversion tracking
 if (GOOGLE_CONVERSION == 'true') {

    $smarty->assign('google_tracking','true');
    $smarty->assign('tracking_code','
<noscript>
<a href="http://services.google.com/sitestats/'.GOOGLE_LANG.'.html" onclick="window.open(this.href); return false;">
<img height=27 width=135 border=0 src="http://www.googleadservices.com/pagead/conversion/'.GOOGLE_CONVERSION_ID.'/?hl='.GOOGLE_LANG.'" />
</a>
</noscript>
    ');

 }

 if (DOWNLOAD_ENABLED == 'true') include(DIR_WS_MODULES . 'downloads.php');

 // Sofortüberweisung
  require(DIR_WS_CLASSES . 'payment.php');
  $last_order_id = $orders['orders_id'];
  /*
  require(DIR_WS_CLASSES . 'order.php');
  $order = new order($last_order_id ,&$xtPrice);
  echo "<pre>";
  Print_R($order);
  */
  $payment = ($_GET['sofortueberweisung_payment'] == 'sofortueberweisung' ? 'sofortueberweisung' : 'sofortueberweisungvorkasse');

  $payment_module = new payment($payment);
  $smarty->assign('FORM_ACTION_SOFORTUEBERWEISUNG',xtc_draw_form('sofortueberweisung', 'https://www.sofort-ueberweisung.de/payment.php'));
  $smarty->assign('FORM_HIDDEN_FIELD_SOFORTUEBERWEISUNG', $$payment->checkout_success($last_order_id));
  $smarty->assign('SOFORTUEBERWEISUNG_INFO', '<a href="#" onclick="window.open(\'https://www.sofort-ueberweisung.de/paynetag/anbieter/download/informationen.html\', \'Popup\',\'toolbar=yes,status=no,menubar=no,scrollbars=yes,width=690,height=500\'); return false;"><img src="templates/'.CURRENT_TEMPLATE.'/buttons/' . $_SESSION['language'].'/sofortueberweisung_info.gif" alt="Sofortüberweisung></a>');
  $smarty->assign('BUTTON_SOFORTUEBERWEISUNG_CONTINUE',xtc_image_submit('sofortueberweisung_paynow.gif', IMAGE_BUTTON_CONTINUE));
  $smarty->assign('sofortueberweisung_method', $payment);

  $smarty->assign('language', $_SESSION['language']);
  $smarty->assign('PAYMENT_BLOCK',$payment_block);
  $smarty->caching = 0;
  $main_content=$smarty->fetch(CURRENT_TEMPLATE . '/module/sofortueberweisung_checkout_success.html');

  $smarty->assign('language', $_SESSION['language']);
  $smarty->assign('main_content',$main_content);
  $smarty->caching = 0;
  if (!defined(RM)) $smarty->load_filter('output', 'note');
  $smarty->display(CURRENT_TEMPLATE . '/index.html');
?>