<?php

/* -----------------------------------------------------------------------------------------
   $Id: shop_content.php 148 2007-01-24 09:08:14Z mzanier $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(conditions.php,v 1.21 2003/02/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (shop_content.php,v 1.1 2003/08/19); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

require ('includes/application_top.php');
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

// include needed functions
require_once (DIR_FS_INC.'xtc_validate_email.inc.php');

$content_body = ''; //DokuMan - set undefined variable
$group_check = ''; //DokuMan - set undefined variable
if (GROUP_CHECK == 'true') {
	$group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
}

$shop_content_query = xtc_db_query("SELECT
                     content_id,
                     content_title,
                     content_heading,
                     content_text,
                     content_file
                     FROM ".TABLE_CONTENT_MANAGER."
                     WHERE content_group='".(int) $_GET['coID']."' ".$group_check."
                     AND languages_id='".(int) $_SESSION['languages_id']."'");
$shop_content_data = xtc_db_fetch_array($shop_content_query);

// BOF - DokuMan - 2009-05-29 - added shopstat bugfix
//-- SHOPSTAT --//
//$breadcrumb->add($shop_content_data['content_title'], xtc_href_link(FILENAME_CONTENT.'?coID='.(int) $_GET['coID']));
$breadcrumb->add($shop_content_data['content_title'], xtc_href_link(FILENAME_CONTENT,'coID='.(int) $_GET['coID']));
//-- SHOPSTAT --//
// EOF - DokuMan - 2009-05-29 - added shopstat bugfix

if ($_GET['coID'] != 7) {
	require (DIR_WS_INCLUDES.'header.php');
}
if ($_GET['coID'] == 7 && $_GET['action'] == 'success') {
	require (DIR_WS_INCLUDES.'header.php');
}

$smarty->assign('CONTENT_HEADING', $shop_content_data['content_heading']);

if ($_GET['coID'] == 7) {    

	$error = false;
	if (isset ($_GET['action']) && ($_GET['action'] == 'send')) {
  // BOF - Dokuman - 2009-09-04: convert uppercase Captchas to lowercase, to be more flexible on user input
  // BOF - Tomcraft - 2009-09-16 - test empty message_body
		//if (xtc_validate_email(trim($_POST['email'])) && (strtoupper($_POST['vvcode']) == $_SESSION['vvcode']) && $_SESSION['vvcode']!='') {
		if (xtc_validate_email(trim($_POST['email'])) && (strtoupper($_POST['vvcode']) == $_SESSION['vvcode']) && $_SESSION['vvcode']!='' && trim($_POST['message_body'])!='') {
  // EOF - Tomcraft - 2009-09-16 - test empty message_body
  // EOF - Dokuman - 2009-09-04: convert uppercase Captchas to lowercase, to be more flexible on user input
            // BOF - Tomcraft - 2009-11-05 - Advanced contact form (new email layout)			
			
			// Datum und Uhrzeit
			$datum= date("d.m.Y");
			$uhrzeit= date("H:i");
			
			// BOF - Tomcraft - 2009-11-05 - Advanced contact form (support multilanguage)
			require (DIR_WS_LANGUAGES.$_SESSION['language'].'/contact_us.php');
			// EOF - Tomcraft - 2009-11-05 - Advanced contact form (support multilanguage)
			
			// BOF - Tomcraft - 2009-11-05 - Advanced contact form (additional fields)
			$additional_fields = '';			
			if (isset($_POST['company']))  $additional_fields =  EMAIL_COMPANY. $_POST['company'] . "\n" ;
			if (isset($_POST['street']))   $additional_fields .= EMAIL_STREET . $_POST['street'] . "\n" ;
			if (isset($_POST['postcode'])) $additional_fields .= EMAIL_POSTCODE . $_POST['postcode'] . "\n" ;
			if (isset($_POST['city']))     $additional_fields .= EMAIL_CITY . $_POST['city'] . "\n" ;
			if (isset($_POST['phone']))    $additional_fields .= EMAIL_PHONE . $_POST['phone'] . "\n" ;
			if (isset($_POST['fax']))      $additional_fields .= EMAIL_FAX . $_POST['fax'] . "\n" ;
			// EOF - Tomcraft - 2009-11-05 - Advanced contact form (additional fields)

			//xtc_php_mail($_POST['email'], $_POST['name'], CONTACT_US_EMAIL_ADDRESS, CONTACT_US_NAME, CONTACT_US_FORWARDING_STRING, $_POST['email'], $_POST['name'], '', '', CONTACT_US_EMAIL_SUBJECT, nl2br($_POST['message_body']), $_POST['message_body']);
			// BOF - Tomcraft - 2009-11-05 - Advanced contact form
			
			// BOF - Tomcraft - 2009-11-05 - Advanced contact form (check for USE_CONTACT_EMAIL_ADDRESS)
			$use_contact_email_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'USE_CONTACT_EMAIL_ADDRESS'");
			$use_contact_email = xtc_db_fetch_array($use_contact_email_query);
			if ($use_contact_email['configuration_value'] == 'true') {
			    $email = trim(CONTACT_US_EMAIL_ADDRESS);
				$name = CONTACT_US_NAME;
				$notify =  EMAIL_NOTIFY . "\n\n";				
			} else {
				$email = trim($_POST['email']);
				$name = $_POST['name'];
				$notify =  '';                			
			}
			// EOF - Tomcraft - 2009-11-05 - Advanced contact form (check for USE_CONTACT_EMAIL_ADDRESS)
			
			$email_layout = sprintf(EMAIL_SENT_BY, CONTACT_US_NAME, CONTACT_US_EMAIL_ADDRESS, $datum , $uhrzeit) . "\n" .
							"--------------------------------------------------------------" . "\n" . $notify .
							"Name: ". $_POST['name'] . "\n" .
							"Email: ". trim($_POST['email']) . "\n" .
							// BOF - Tomcraft - 2009-11-05 - Advanced contact form (additional fields)
							$additional_fields .
							// EOF - Tomcraft - 2009-11-05 - Advanced contact form (additional fields)
							"\nNachricht:\n ". $_POST['message_body'] . "\n";

			xtc_php_mail($email, 
						 $name, 
						 CONTACT_US_EMAIL_ADDRESS, 
						 CONTACT_US_NAME, 
						 CONTACT_US_FORWARDING_STRING, 
						 $email, 
						 $name, 				 						 
						 '', 
						 '', 
						 CONTACT_US_EMAIL_SUBJECT, 
						 nl2br($email_layout), 
						 $email_layout
						 );
			// EOF - Tomcraft - 2009-11-05 - Advanced contact form
            // EOF - Tomcraft - 2009-11-05 - Advanced contact form (new email layout)
			if (!isset ($mail_error)) {
				xtc_redirect(xtc_href_link(FILENAME_CONTENT, 'action=success&coID='.(int) $_GET['coID']));
			} else {
				$smarty->assign('error_message', $mail_error);

			}
		} else {
			// error report hier einbauen
			// BOF - Tomcraft - 2009-09-16 - new error message
			// $smarty->assign('error_message', ERROR_MAIL);
			$err_msg = '';
			if (!xtc_validate_email(trim($_POST['email']))) $err_msg .= ERROR_EMAIL;
			if (strtoupper($_POST['vvcode']) != $_SESSION['vvcode']) $err_msg .= ERROR_VVCODE;
			if (trim($_POST['message_body']) == '') $err_msg .= ERROR_MSG_BODY;
			
			$smarty->assign('error_message', ERROR_MAIL . $err_msg);
			// EOF - Tomcraft - 2009-09-16 - new error message
			$error = true;
		}

	}

	$smarty->assign('CONTACT_HEADING', $shop_content_data['content_heading']);
	if (isset ($_GET['action']) && ($_GET['action'] == 'success')) {
		$smarty->assign('success', '1');
		$smarty->assign('BUTTON_CONTINUE', '<a href="'.xtc_href_link(FILENAME_DEFAULT).'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>');

	} else {
		if ($shop_content_data['content_file'] != '') {
			ob_start();
			if (strpos($shop_content_data['content_file'], '.txt'))
				echo '<pre>';
			include (DIR_FS_CATALOG.'media/content/'.$shop_content_data['content_file']);
			if (strpos($shop_content_data['content_file'], '.txt'))
				echo '</pre>';
		$contact_content = ob_get_contents();
		ob_end_clean();
		} else {
			$contact_content = $shop_content_data['content_text'];
		}
		require (DIR_WS_INCLUDES.'header.php');
		
		// BOF - Tomcraft - 2009-11-05 - Advanced contact form (fix override by error request)
		if (isset ($_SESSION['customer_id']) && !$error) {
		// EOF - Tomcraft - 2009-11-05 - Advanced contact form (fix override by error request)
			$customers_name = $_SESSION['customer_first_name'].' '.$_SESSION['customer_last_name'];
			// BOF - Dokuman - 2009-09-04: preallocate email address on contact form
			//$email_address = $_SESSION['customer_email_address'];
			$c_query = xtc_db_query("SELECT * FROM ".TABLE_CUSTOMERS." WHERE customers_id='".$_SESSION['customer_id']."'"); 
			$c_data = xtc_db_fetch_array($c_query); 
			$email_address = stripslashes($c_data['customers_email_address']);
			// EOF - Dokuman - 2009-09-04: preallocate email address on contact form
			// BOF - Tomcraft - 2009-11-05 - Advanced contact form (additional fields)
			$phone = stripslashes($c_data['customers_telephone']);
			$company = stripslashes($c_data['entry_company']);
			$street = stripslashes($c_data['entry_street_address']);
			$postcode= stripslashes($c_data['entry_postcode']);
			$city = stripslashes($c_data['entry_city']);
			$fax= stripslashes($c_data['customers_fax']);
			// EOF - Tomcraft - 2009-11-05 - Advanced contact form (additional fields)
		}

		// BOF - Tomcraft - 2009-11-05 - Advanced contact form (product question)
		$products_info = '';
		if (trim($_GET['products_name'] != '')) {$products_info= trim($_GET['products_name']);}
		if (trim($_GET['products_model'] != '')) {$products_info= trim($products_info . ' - ' . trim($_GET['products_model']));}
		if ($products_info != '') {$products_info = trim($_GET['question'])."\n" . $products_info . "\n"; }
		if (!$error) $message_body = $products_info . "\n";
		// EOF - Tomcraft - 2009-11-05 - Advanced contact form (product question)
		
		$smarty->assign('CONTACT_CONTENT', $contact_content);
		//BOF - Dokuman - 2009-12-23 - send contact form information with SSL
		//$smarty->assign('FORM_ACTION', xtc_draw_form('contact_us', xtc_href_link(FILENAME_CONTENT, 'action=send&coID='.(int) $_GET['coID'])));
		$smarty->assign('FORM_ACTION', xtc_draw_form('contact_us', xtc_href_link(FILENAME_CONTENT, 'action=send&coID='.(int) $_GET['coID'], 'SSL')));
		//EOF - Dokuman - 2009-12-23 - send contact form information with SSL
		$smarty->assign('VVIMG', '<img src="'.xtc_href_link(FILENAME_DISPLAY_VVCODES).'" alt="Captcha" />');
		$smarty->assign('INPUT_CODE', xtc_draw_input_field('vvcode', '', 'size="8" maxlength="6"', 'text', false));
		$smarty->assign('INPUT_NAME', xtc_draw_input_field('name', ($error ? $_POST['name'] : $customers_name), 'size="30"'));
		$smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', ($error ? $_POST['email'] : $email_address), 'size="30"'));
		// BOF - Tomcraft - 2009-11-05 - Advanced contact form (additional fields)
		$smarty->assign('INPUT_PHONE', xtc_draw_input_field('phone', ($error ? $_POST['phone'] : $phone), 'size="30"'));
		$smarty->assign('INPUT_COMPANY', xtc_draw_input_field('company', ($error ? $_POST['company'] : $company), 'size="30"'));
		$smarty->assign('INPUT_STREET', xtc_draw_input_field('street', ($error ? $_POST['street'] : $street), 'size="30"'));
		$smarty->assign('INPUT_POSTCODE', xtc_draw_input_field('postcode', ($error ? $_POST['postcode'] : $postcode), 'size="30"'));
		$smarty->assign('INPUT_CITY', xtc_draw_input_field('city', ($error ? $_POST['city'] : $city), 'size="30"'));
		$smarty->assign('INPUT_FAX', xtc_draw_input_field('fax', ($error ? $_POST['fax'] : $fax), 'size="30"'));			
		// EOF - Tomcraft - 2009-11-05 - Advanced contact form (additional fields)
		// BOF - Tomcraft - 2009-09-29 - fixed word-wrap in contact-form
		//$smarty->assign('INPUT_TEXT', xtc_draw_textarea_field('message_body', 'soft', 50, 15, ($error ? xtc_db_input($_POST['message_body']) : $first_name)));
		// BOF - Tomcraft - 2010-02-18 - Fixed width of textarea in FireFox under Linux.
		//$smarty->assign('INPUT_TEXT', xtc_draw_textarea_field('message_body', 'soft', 50, 15, ($error ? $_POST['message_body'] : $message_body)));
		$smarty->assign('INPUT_TEXT', xtc_draw_textarea_field('message_body', 'soft', 45, 15, ($error ? $_POST['message_body'] : $message_body)));
		// EOF - Tomcraft - 2010-02-18 - Fixed width of textarea in FireFox under Linux.
		// EOF - Tomcraft - 2009-09-29 - fixed word-wrap in contact-form
		$smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_send.gif', IMAGE_BUTTON_SEND));
		$smarty->assign('FORM_END', '</form>');
	}

	$smarty->assign('language', $_SESSION['language']);

	$smarty->caching = 0;
	$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/contact_us.html');

} else {

	if ($shop_content_data['content_file'] != '') {

		ob_start();

		if (strpos($shop_content_data['content_file'], '.txt'))
			echo '<pre>';
		include (DIR_FS_CATALOG.'media/content/'.$shop_content_data['content_file']);
		if (strpos($shop_content_data['content_file'], '.txt'))
			echo '</pre>';
		$smarty->assign('file', ob_get_contents());
		ob_end_clean();

	} else {
		$content_body = $shop_content_data['content_text'];
	}
	$smarty->assign('CONTENT_BODY', $content_body);

	$smarty->assign('BUTTON_CONTINUE', '<a href="javascript:history.back(1)">'.xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK).'</a>');
	$smarty->assign('language', $_SESSION['language']);

	// set cache ID
	 if (!CacheCheck()) {
		$smarty->caching = 0;
		$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/content.html');
	} else {
		$smarty->caching = 1;
		$smarty->cache_lifetime = CACHE_LIFETIME;
		$smarty->cache_modified_check = CACHE_CHECK;
		$cache_id = $_SESSION['language'].$shop_content_data['content_id'];
		$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/content.html', $cache_id);
	}

}

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM))
	$smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
?>
