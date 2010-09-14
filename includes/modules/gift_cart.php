<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(shopping_cart.php,v 1.32 2003/02/11); www.oscommerce.com
   (c) 2003     nextcommerce (shopping_cart.php,v 1.21 2003/08/17); www.nextcommerce.org
   (c) 2006 XT-Commerce (gift_cart.php 842 2005-03-24 14)
   
   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:


   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

$gift_smarty = new Smarty;
//BOF - GTB - 2010-08-03 - Security Fix - Base
$gift_smarty->assign('tpl_path',DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
//$gift_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
//EOF - GTB - 2010-08-03 - Security Fix - Base
if (ACTIVATE_GIFT_SYSTEM == 'true') {
	$gift_smarty->assign('ACTIVATE_GIFT', 'true');
}

if (isset ($_SESSION['customer_id'])) {
	$gv_query = xtc_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id = '".$_SESSION['customer_id']."'");
	$gv_result = xtc_db_fetch_array($gv_query);
	if ($gv_result['amount'] > 0) {
		$gift_smarty->assign('GV_AMOUNT', $xtPrice->xtcFormat($gv_result['amount'], true, 0, true));
		$gift_smarty->assign('GV_SEND_TO_FRIEND_LINK', xtc_href_link(FILENAME_GV_SEND));
	} else {
		$gift_smarty->assign('GV_AMOUNT', 0);
	}
}
if (isset ($_SESSION['gv_id'])) {
	$gv_query = xtc_db_query("select coupon_amount from ".TABLE_COUPONS." where coupon_id = '".$_SESSION['gv_id']."'");
	$coupon = xtc_db_fetch_array($gv_query);
	$gift_smarty->assign('COUPON_AMOUNT2', $xtPrice->xtcFormat($coupon['coupon_amount'], true, 0, true));
}
if (isset ($_SESSION['cc_id'])) {
	//$gift_smarty->assign('COUPON_HELP_LINK', '<a style="cursor:pointer" onclick="javascript:window.open(\''.xtc_href_link(FILENAME_POPUP_COUPON_HELP, 'cID='.$_SESSION['cc_id']).'\', \'popup\', \'toolbar=0,scrollbars=yes, width=350, height=350\')">');
	
	//BOF - web28 - FIX LOST cID
	//$gift_smarty->assign('COUPON_HELP_LINK', '<a target="_blank" class="thickbox" href="'.xtc_href_link(FILENAME_POPUP_COUPON_HELP.'?KeepThis=true&TB_iframe=true&height=400&width=600').'">Information</a>');
	$gift_smarty->assign('COUPON_HELP_LINK', '<a target="_blank" class="thickbox" href="'.xtc_href_link(FILENAME_POPUP_COUPON_HELP.'?cID='.$_SESSION['cc_id'].'&KeepThis=true&TB_iframe=true&height=400&width=600').'">Information</a>');
	//EOF - web28 - FIX LOST cID
}
if (isset ($_SESSION['customer_id'])) {
	$gift_smarty->assign('C_FLAG', 'true');
}
//BOF - web28 - 2010-09-04 - FIX SSL Link FILENAME_CREATE_ACCOUNT
//$gift_smarty->assign('LINK_ACCOUNT', xtc_href_link(FILENAME_CREATE_ACCOUNT));
$gift_smarty->assign('LINK_ACCOUNT', xtc_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'));
//EOF - web28 - 2010-09-04 - FIX SSL Link FILENAME_CREATE_ACCOUNT
$gift_smarty->assign('FORM_ACTION', xtc_draw_form('gift_coupon', xtc_href_link(FILENAME_SHOPPING_CART, 'action=check_gift', 'SSL')));
$gift_smarty->assign('INPUT_CODE', xtc_draw_input_field('gv_redeem_code'));
$gift_smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_redeem.gif', IMAGE_REDEEM_GIFT));
$gift_smarty->assign('language', $_SESSION['language']);
$gift_smarty->assign('FORM_END', '</form>');
$gift_smarty->caching = 0;

$smarty->assign('MODULE_gift_cart', $gift_smarty->fetch(CURRENT_TEMPLATE.'/module/gift_cart.html'));
?>