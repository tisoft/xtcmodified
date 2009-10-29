<?php

/* -----------------------------------------------------------------------------------------
   $Id: product_reviews_write.php 1101 2005-07-24 14:51:13Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_reviews_write.php,v 1.51 2003/02/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (product_reviews_write.php,v 1.13 2003/08/1); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

if ($_SESSION['customers_status']['customers_status_write_reviews'] == 0) {
             xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if (isset ($_GET['action']) && $_GET['action'] == 'process') {
	if (is_object($product) && $product->isProduct()) { // We got to the process but it is an illegal product, don't write

		$customer = xtc_db_query("select customers_firstname,
                                     customers_lastname
                              from ".TABLE_CUSTOMERS."
                              where customers_id = '".(int) $_SESSION['customer_id']."'");
		$customer_values = xtc_db_fetch_array($customer);
		$date_now = date('Ymd');
		if ($customer_values['customers_lastname'] == '')
			$customer_values['customers_lastname'] = TEXT_GUEST;
		xtc_db_query("insert into ".TABLE_REVIEWS." (products_id, customers_id, customers_name, reviews_rating, date_added) values ('".$product->data['products_id']."', '".(int) $_SESSION['customer_id']."', '".addslashes($customer_values['customers_firstname']).' '.addslashes($customer_values['customers_lastname'])."', '".addslashes($_POST['rating'])."', now())");
		$insert_id = xtc_db_insert_id();
		xtc_db_query("insert into ".TABLE_REVIEWS_DESCRIPTION." (reviews_id, languages_id, reviews_text) values ('".$insert_id."', '".(int) $_SESSION['languages_id']."', '".addslashes($_POST['review'])."')");
	}

	xtc_redirect(xtc_href_link(FILENAME_PRODUCT_REVIEWS, $_POST['get_params']));
}

// lets retrieve all $HTTP_GET_VARS keys and values..
$get_params = xtc_get_all_get_params();
$get_params_back = xtc_get_all_get_params(array ('reviews_id')); // for back button
$get_params = substr($get_params, 0, -1); //remove trailing &
if (xtc_not_null($get_params_back)) {
	$get_params_back = substr($get_params_back, 0, -1); //remove trailing &
} else {
	$get_params_back = $get_params;
}

$breadcrumb->add(NAVBAR_TITLE_REVIEWS_WRITE, xtc_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));

$customer_info_query = xtc_db_query("select customers_firstname,
                                            customers_lastname
                                     from ".TABLE_CUSTOMERS."
                                     where customers_id = '".(int) $_SESSION['customer_id']."'");
$customer_info = xtc_db_fetch_array($customer_info_query);

require (DIR_WS_INCLUDES.'header.php');

if (!$product->isProduct()) {
	$smarty->assign('error', ERROR_INVALID_PRODUCT);
} else {
	$name = $customer_info['customers_firstname'].' '.$customer_info['customers_lastname'];
	if ($name == ' ')
		$customer_info['customers_lastname'] = TEXT_GUEST;
	$smarty->assign('PRODUCTS_NAME', $product->data['products_name']);
	$smarty->assign('AUTHOR', $customer_info['customers_firstname'].' '.$customer_info['customers_lastname']);
	$smarty->assign('INPUT_TEXT', xtc_draw_textarea_field('review', 'soft', 60, 15, '', '', false));
	$smarty->assign('INPUT_RATING', xtc_draw_radio_field('rating', '1').' '.xtc_draw_radio_field('rating', '2').' '.xtc_draw_radio_field('rating', '3').' '.xtc_draw_radio_field('rating', '4').' '.xtc_draw_radio_field('rating', '5'));
	$smarty->assign('FORM_ACTION', xtc_draw_form('product_reviews_write', xtc_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'action=process&'.xtc_product_link($product->data['products_id'],$product->data['products_name'])), 'post', 'onSubmit="return checkForm();"'));
	$smarty->assign('BUTTON_BACK', '<a href="javascript:history.back(1)">'.xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK).'</a>');
	$smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE).xtc_draw_hidden_field('get_params', $get_params));
	$smarty->assign('FORM_END', '</form>');
}
$smarty->assign('language', $_SESSION['language']);

$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/product_reviews_write.html');

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM))
	$smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
?>
