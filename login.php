<?php
/* -----------------------------------------------------------------------------------------
   $Id$   

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(login.php,v 1.79 2003/05/19); www.oscommerce.com 
   (c) 2003 nextcommerce (login.php,v 1.13 2003/08/17); www.nextcommerce.org
   (c) 2006 XT-Commerce (login.php 1143 2005-08-11)

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   guest account idea by Ingo T. <xIngox@web.de>
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

if (isset ($_SESSION['customer_id'])) {
	xtc_redirect(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

// include needed functions
require_once (DIR_FS_INC.'xtc_validate_password.inc.php');
require_once (DIR_FS_INC.'xtc_array_to_string.inc.php');
require_once (DIR_FS_INC.'xtc_write_user_info.inc.php');
// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
if ($session_started == false) {
	xtc_redirect(xtc_href_link(FILENAME_COOKIE_USAGE));
}
$info_message = false; //DokuMan - 2010-02-28 - set undefined variable

if (isset ($_GET['action']) && ($_GET['action'] == 'process')) {
	$email_address = xtc_db_prepare_input($_POST['email_address']);
	$password = xtc_db_prepare_input($_POST['password']);

	// Check if email exists
	$check_customer_query = xtc_db_query("select 
                                            customers_id,
                                            customers_vat_id,
                                            customers_firstname,
                                            customers_lastname,
                                            customers_gender,
                                            customers_password,
                                            customers_email_address,
                                            customers_default_address_id
                                            from ".TABLE_CUSTOMERS."
                                            where customers_email_address = '".xtc_db_input($email_address)."' 
                                            and account_type = 0");
	if (xtc_db_num_rows($check_customer_query) == 0) {
		$_GET['login'] = 'fail';
		$info_message = TEXT_NO_EMAIL_ADDRESS_FOUND;
	} else {
		$check_customer = xtc_db_fetch_array($check_customer_query);
		// Check that password is good
		if (!xtc_validate_password($password, $check_customer['customers_password'])) {
			$_GET['login'] = 'fail';
			$info_message = TEXT_LOGIN_ERROR;
		} else {
			if (SESSION_RECREATE == 'True') {
				xtc_session_recreate();
			}

			$check_country_query = xtc_db_query("select entry_country_id, entry_zone_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $check_customer['customers_id']."' and address_book_id = '".$check_customer['customers_default_address_id']."'");
			$check_country = xtc_db_fetch_array($check_country_query);

			$_SESSION['customer_gender'] = $check_customer['customers_gender'];
			$_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
			$_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
			$_SESSION['customer_id'] = $check_customer['customers_id'];
			$_SESSION['customer_vat_id'] = $check_customer['customers_vat_id'];
			$_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
			$_SESSION['customer_country_id'] = $check_country['entry_country_id'];
			$_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];

			$date_now = date('Ymd');

			xtc_db_query("update ".TABLE_CUSTOMERS_INFO." SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");
			xtc_write_user_info((int) $_SESSION['customer_id']);
			// restore cart contents
			$_SESSION['cart']->restore_contents();
			
			if (isset($econda) && is_object($econda)) $econda->_loginUser();

			if ($_SESSION['cart']->count_contents() > 0) {
				//BOF - web28 - 2010-09-20 redirect NONSSL
				//xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
				xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART), 'NONSSL');
				//EOF - web28 - 2010-09-20 redirect NONSSL
			} else {
			    //BOF - web28 - 2010-08-11 redirect NONSSL
				//xtc_redirect(xtc_href_link(FILENAME_DEFAULT));
				xtc_redirect(xtc_href_link(FILENAME_DEFAULT),'NONSSL');
				//EOF - web28 - 2010-08-11 redirect NONSSL
			}

		}
	}
}

$breadcrumb->add(NAVBAR_TITLE_LOGIN, xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
require (DIR_WS_INCLUDES.'header.php');

if (isset($_GET['info_message'])) $info_message = $_GET['info_message'];
$smarty->assign('info_message', $info_message);
$smarty->assign('account_option', ACCOUNT_OPTIONS);
$smarty->assign('BUTTON_NEW_ACCOUNT', '<a href="'.xtc_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL').'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>');
$smarty->assign('BUTTON_LOGIN', xtc_image_submit('button_login.gif', IMAGE_BUTTON_LOGIN));
$smarty->assign('BUTTON_GUEST', '<a href="'.xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL').'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>');
$smarty->assign('FORM_ACTION', xtc_draw_form('login', xtc_href_link(FILENAME_LOGIN, 'action=process', 'SSL')));
$smarty->assign('INPUT_MAIL', xtc_draw_input_field('email_address'));
$smarty->assign('INPUT_PASSWORD', xtc_draw_password_field('password'));
$smarty->assign('LINK_LOST_PASSWORD', xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL'));
$smarty->assign('FORM_END', '</form>');
$smarty->assign('language', $_SESSION['language']);
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/login.html');
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined('RM'))
	$smarty->loadfilter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
?>