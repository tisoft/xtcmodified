<?php

/*------------------------------------------------------------------------------
   $Id: password_double_opt.php,v 1.0 

   XTC-NEWSLETTER_RECIPIENTS RC1 - Contribution for XT-Commerce http://www.xt-commerce.com
   by Matthias Hinsche http://www.gamesempire.de

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce www.oscommerce.com 
   (c) 2003  nextcommerce www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

require ('includes/application_top.php');

// create smarty elements
$smarty = new Smarty;

// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

// include needed functions
require_once (DIR_FS_INC.'xtc_render_vvcode.inc.php');
require_once (DIR_FS_INC.'xtc_random_charcode.inc.php');
require_once (DIR_FS_INC.'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC.'xtc_validate_password.inc.php');
require_once (DIR_FS_INC.'xtc_rand.inc.php');
$case = double_opt;
$info_message = TEXT_PASSWORD_FORGOTTEN;
if (isset ($_GET['action']) && ($_GET['action'] == 'first_opt_in')) {

	$check_customer_query = xtc_db_query("select customers_email_address, customers_id from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($_POST['email'])."'");
	$check_customer = xtc_db_fetch_array($check_customer_query);

	$vlcode = xtc_random_charcode(32);
	$link = xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=verified&customers_id='.$check_customer['customers_id'].'&key='.$vlcode, 'NONSSL');

	// assign language to template for caching
	$smarty->assign('language', $_SESSION['language']);
	$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
	$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

	// assign vars
	$smarty->assign('EMAIL', $check_customer['customers_email_address']);
	$smarty->assign('LINK', $link);
	// dont allow cache
	$smarty->caching = false;

	// create mails
	$html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/password_verification_mail.html');
	$txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/password_verification_mail.txt');

	if ($_POST['vvcode'] == $_SESSION['vvcode']) {
		if (!xtc_db_num_rows($check_customer_query)) {
			$case = wrong_mail;
			$info_message = TEXT_EMAIL_ERROR;
		} else {
			$case = first_opt_in;
			xtc_db_query("update ".TABLE_CUSTOMERS." set password_request_key = '".$vlcode."' where customers_id = '".$check_customer['customers_id']."'");
			xtc_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $check_customer['customers_email_address'], '', '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', TEXT_EMAIL_PASSWORD_FORGOTTEN, $html_mail, $txt_mail);

		}
	} else {
		$case = code_error;
		$info_message = TEXT_CODE_ERROR;
	}
}

// Verification
if (isset ($_GET['action']) && ($_GET['action'] == 'verified')) {
	$check_customer_query = xtc_db_query("select customers_id, customers_email_address, password_request_key from ".TABLE_CUSTOMERS." where customers_id = '".(int)$_GET['customers_id']."' and password_request_key = '".xtc_db_input($_GET['key'])."'");
	$check_customer = xtc_db_fetch_array($check_customer_query);
	if (!xtc_db_num_rows($check_customer_query) || $_GET['key']=="") {

		$case = no_account;
		$info_message = TEXT_NO_ACCOUNT;
	} else {

		$newpass = xtc_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
		$crypted_password = xtc_encrypt_password($newpass);

		xtc_db_query("update ".TABLE_CUSTOMERS." set customers_password = '".$crypted_password."' where customers_email_address = '".$check_customer['customers_email_address']."'");
		xtc_db_query("update ".TABLE_CUSTOMERS." set password_request_key = '' where customers_id = '".$check_customer['customers_id']."'");
		// assign language to template for caching
		$smarty->assign('language', $_SESSION['language']);
		$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
		$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

		// assign vars
		$smarty->assign('EMAIL', $check_customer['customers_email_address']);
		$smarty->assign('NEW_PASSWORD', $newpass);
		// dont allow cache
		$smarty->caching = false;
		// create mails
		$html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/new_password_mail.html');
		$txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/new_password_mail.txt');

		xtc_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $check_customer['customers_email_address'], '', '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', TEXT_EMAIL_PASSWORD_NEW_PASSWORD, $html_mail, $txt_mail);
		if (!isset ($mail_error)) {
			xtc_redirect(xtc_href_link(FILENAME_LOGIN, 'info_message='.urlencode(TEXT_PASSWORD_SENT), 'SSL', true, false));
		}
	}
}

$breadcrumb->add(NAVBAR_TITLE_PASSWORD_DOUBLE_OPT, xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'NONSSL'));

require (DIR_WS_INCLUDES.'header.php');

switch ($case) {
	case first_opt_in :
		$smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$smarty->assign('info_message', $info_message);
		$smarty->assign('info_message', TEXT_LINK_MAIL_SENDED);
		$smarty->assign('language', $_SESSION['language']);
		$smarty->caching = 0;
		$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/password_messages.html');

		break;
	case second_opt_in :
		$smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$smarty->assign('info_message', $info_message);
		//    $smarty->assign('info_message', TEXT_PASSWORD_MAIL_SENDED);
		$smarty->assign('language', $_SESSION['language']);
		$smarty->caching = 0;
		$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/password_messages.html');
		break;
	case code_error :
		
		//BUGFIX 24.04.2009 - JUNG/GESTALTEN.com
		//$smarty->assign('VVIMG', '<img src="'.FILENAME_DISPLAY_VVCODES.'">');
		$smarty->assign('VVIMG', '<img src="'.xtc_href_link(FILENAME_DISPLAY_VVCODES).'">');
		$smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$smarty->assign('info_message', $info_message);
		$smarty->assign('message', TEXT_PASSWORD_FORGOTTEN);
		$smarty->assign('SHOP_NAME', STORE_NAME);
		$smarty->assign('FORM_ACTION', xtc_draw_form('sign', xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=first_opt_in', 'NONSSL')));
		$smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', xtc_db_input($_POST['email'])));
		$smarty->assign('INPUT_CODE', xtc_draw_input_field('vvcode', '', 'size="6" maxlenght="6"', false, '', false));
		$smarty->assign('BUTTON_SEND', xtc_image_submit('button_send.gif', IMAGE_BUTTON_LOGIN));
		$smarty->assign('language', $_SESSION['language']);
		$smarty->caching = 0;
		$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/password_double_opt_in.html');

		break;
	case wrong_mail :

		//BUGFIX 24.04.2009 - JUNG/GESTALTEN.com
		//$smarty->assign('VVIMG', '<img src="'.FILENAME_DISPLAY_VVCODES.'">');
		$smarty->assign('VVIMG', '<img src="'.xtc_href_link(FILENAME_DISPLAY_VVCODES).'">');
		$smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$smarty->assign('info_message', $info_message);
		$smarty->assign('message', TEXT_PASSWORD_FORGOTTEN);
		$smarty->assign('SHOP_NAME', STORE_NAME);
		$smarty->assign('FORM_ACTION', xtc_draw_form('sign', xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=first_opt_in', 'NONSSL')));
		$smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', xtc_db_input($_POST['email'])));
		$smarty->assign('INPUT_CODE', xtc_draw_input_field('vvcode', '', 'size="6" maxlenght="6"', false, '', false));
		$smarty->assign('BUTTON_SEND', xtc_image_submit('button_send.gif', IMAGE_BUTTON_LOGIN));
		$smarty->assign('language', $_SESSION['language']);
		$smarty->caching = 0;
		$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/password_double_opt_in.html');

		break;
	case no_account :
		$smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		$smarty->assign('info_message', $info_message);
		$smarty->assign('language', $_SESSION['language']);
		$smarty->caching = 0;
		$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/password_messages.html');

		break;
	case double_opt :

		//BUGFIX 24.04.2009 - JUNG/GESTALTEN.com
		//$smarty->assign('VVIMG', '<img src="'.FILENAME_DISPLAY_VVCODES.'">');
		$smarty->assign('VVIMG', '<img src="'.xtc_href_link(FILENAME_DISPLAY_VVCODES).'">');
		$smarty->assign('text_heading', HEADING_PASSWORD_FORGOTTEN);
		//    $smarty->assign('info_message', $info_message);
		$smarty->assign('message', TEXT_PASSWORD_FORGOTTEN);
		$smarty->assign('SHOP_NAME', STORE_NAME);
		$smarty->assign('FORM_ACTION', xtc_draw_form('sign', xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=first_opt_in', 'NONSSL')));
		$smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', xtc_db_input($_POST['email'])));
		$smarty->assign('INPUT_CODE', xtc_draw_input_field('vvcode', '', 'size="6" maxlenght="6"', false, '', false));
		$smarty->assign('BUTTON_SEND', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_LOGIN));
		$smarty->assign('FORM_END', '</form>');
		$smarty->assign('language', $_SESSION['language']);
		$smarty->caching = 0;
		$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/password_double_opt_in.html');

		break;
}

$smarty->assign('main_content', $main_content);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
if (!defined(RM))
	$smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
?>