<?php
/* --------------------------------------------------------------
  $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(coupon_admin.php); www.oscommerce.com
   (c) 2006 XT-Commerce (coupon_admin.php 1084 2005-07-23)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'class.phpmailer.php');
  require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

  // initiate template engine for mail
  $smarty = new Smarty;

  if ($_GET['selected_box']) {
    $_GET['action']='';
    $_GET['old_action']='';
  }

  if (($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address']) && (!$_POST['back_x'])) {
    switch ($_POST['customers_email_address']) {
    case '***':
      $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS);
      $mail_sent_to = TEXT_ALL_CUSTOMERS;
      break;
    case '**D':
      $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
      $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
      break;
    default:
      $customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
      $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($customers_email_address) . "'");
      $mail_sent_to = $_POST['customers_email_address'];
      break;
    }
    $coupon_query = xtc_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon_result = xtc_db_fetch_array($coupon_query);
    $coupon_name_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
    $coupon_name = xtc_db_fetch_array($coupon_name_query);

    $from = xtc_db_prepare_input($_POST['from']);
    $subject = xtc_db_prepare_input($_POST['subject']);
    while ($mail = xtc_db_fetch_array($mail_query)) {

      // assign language to template for caching
      $smarty->assign('language', $_SESSION['language']);
      $smarty->caching = false;

      // set dirs manual
      $smarty->template_dir=DIR_FS_CATALOG.'templates';
      $smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
      $smarty->config_dir=DIR_FS_CATALOG.'lang';

      //BOF - GTB - 2010-08-03 - Security Fix - Base
	  $smarty->assign('tpl_path',DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
	  //$smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
      //EOF - GTB - 2010-08-03 - Security Fix - Base
      $smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

      $smarty->assign('MESSAGE', $_POST['message']);
      $smarty->assign('COUPON_ID', $coupon_result['coupon_code']);
      $smarty->assign('WEBSITE', HTTP_SERVER  . DIR_WS_CATALOG);


      $html_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$_SESSION['language'].'/send_coupon.html');
      $txt_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$_SESSION['language'].'/send_coupon.txt');


      xtc_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME, $mail['customers_email_address'] , $mail['customers_firstname'] . ' ' . $mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $subject, $html_mail , $txt_mail);

    }

    xtc_redirect(xtc_href_link(FILENAME_COUPON_ADMIN, 'mail_sent_to=' . urlencode($mail_sent_to)));
  }

  if ( ($_GET['action'] == 'preview_email') && (!$_POST['customers_email_address']) ) {
    $_GET['action'] = 'email';
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }

  if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'notice');
  }

  switch ($_GET['action']) {
    case 'confirmdelete':
      $delete_query=xtc_db_query("update " . TABLE_COUPONS . " set coupon_active = 'N' where coupon_id='".$_GET['cid']."'");
      break;
	//BOF - web28 - 2010-07-23 - new coupon actions
	case 'noconfirmdelete':
		// WM Koupon wird aus der DB gel�scht
		$delete_query = xtc_db_query("delete from ".TABLE_COUPONS." where coupon_id = '".$_GET['cID']."'");
		$delete_query = xtc_db_query("delete from ".TABLE_COUPONS_DESCRIPTION." where coupon_id = '".$_GET['cID']."'");
	break;
	//EOF - web28 - 2010-07-23 - new coupon actions
    case 'update':
      // get all _POST and validate
      $_POST['coupon_code'] = trim($_POST['coupon_code']);
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $_POST['coupon_name'][$language_id] = trim($_POST['coupon_name'][$language_id]);
          $_POST['coupon_desc'][$language_id] = trim($_POST['coupon_desc'][$language_id]);
        }
      $_POST['coupon_amount'] = trim($_POST['coupon_amount']);
      $update_errors = 0;
      if (!$_POST['coupon_name']) {
        $update_errors = 1;
        $messageStack->add(ERROR_NO_COUPON_NAME, 'error');
      }
      if ((!$_POST['coupon_amount']) && (!$_POST['coupon_free_ship'])) {
        $update_errors = 1;
        $messageStack->add(ERROR_NO_COUPON_AMOUNT, 'error');
      }
      if (!$_POST['coupon_code']) {
        $coupon_code = create_coupon_code();
      }
      if ($_POST['coupon_code']) $coupon_code = $_POST['coupon_code'];
      $query1 = xtc_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_code = '" . xtc_db_prepare_input($coupon_code) . "'");
      if (xtc_db_num_rows($query1) && $_POST['coupon_code'] && $_GET['oldaction'] != 'voucheredit')  {
        $update_errors = 1;
        $messageStack->add(ERROR_COUPON_EXISTS, 'error');
      }
      if ($update_errors != 0) {
        $_GET['action'] = 'new';
      } else {
        $_GET['action'] = 'update_preview';
      }
      break;
    case 'update_confirm':
      if ( ($_POST['back_x']) || ($_POST['back_y']) ) {
        $_GET['action'] = 'new';
      } else {
        $coupon_type = "F";
        if (substr($_POST['coupon_amount'], -1) == '%') $coupon_type='P';
        if ($_POST['coupon_free_ship']) $coupon_type = 'S';
        $sql_data_array = array('coupon_code' => xtc_db_prepare_input($_POST['coupon_code']),
                                'coupon_amount' => xtc_db_prepare_input($_POST['coupon_amount']),
                                'coupon_type' => xtc_db_prepare_input($coupon_type),
                                'uses_per_coupon' => xtc_db_prepare_input($_POST['coupon_uses_coupon']),
                                'uses_per_user' => xtc_db_prepare_input($_POST['coupon_uses_user']),
                                'coupon_minimum_order' => xtc_db_prepare_input($_POST['coupon_min_order']),
                                'restrict_to_products' => xtc_db_prepare_input($_POST['coupon_products']),
                                'restrict_to_categories' => xtc_db_prepare_input($_POST['coupon_categories']),
                                'coupon_start_date' => $_POST['coupon_startdate'],
                                'coupon_expire_date' => $_POST['coupon_finishdate'],
                                'date_created' => 'now()',
                                'date_modified' => 'now()');
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $sql_data_marray[$i] = array('coupon_name' => xtc_db_prepare_input($_POST['coupon_name'][$language_id]),
                                 'coupon_description' => xtc_db_prepare_input($_POST['coupon_desc'][$language_id])
                                 );
        }
//        $query = xtc_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_code = '" . xtc_db_prepare_input($_POST['coupon_code']) . "'");
//        if (!xtc_db_num_rows($query)) {
        if ($_GET['oldaction']=='voucheredit') {
          xtc_db_perform(TABLE_COUPONS, $sql_data_array, 'update', "coupon_id='" . $_GET['cid']."'");
          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
			//BOF - web28 - 2010-07-11 - BUGFIX no entry stored for previous deactivated languages
			$coupon_query = xtc_db_query("select * from ".TABLE_COUPONS_DESCRIPTION." where language_id = '".$language_id."' and coupon_id = '".xtc_db_input($coupon_id)."'");
			if (xtc_db_num_rows($coupon_query) == 0) xtc_db_perform(TABLE_COUPONS_DESCRIPTION, array ('coupon_id' => xtc_db_input($coupon_id), 'language_id' => $language_id));
			//EOF - web28 - 2010-07-11 - BUGFIX no entry stored for previous deactivated languages
            $update = xtc_db_query("update " . TABLE_COUPONS_DESCRIPTION . " set coupon_name = '" . xtc_db_prepare_input($_POST['coupon_name'][$language_id]) . "', coupon_description = '" . xtc_db_prepare_input($_POST['coupon_desc'][$language_id]) . "' where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $language_id . "'");
//            tep_db_perform(TABLE_COUPONS_DESCRIPTION, $sql_data_marray[$i], 'update', "coupon_id='" . $_GET['cid']."'");
          }
        } else {
          $query = xtc_db_perform(TABLE_COUPONS, $sql_data_array);
          $insert_id = xtc_db_insert_id($query);

          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
            $sql_data_marray[$i]['coupon_id'] = $insert_id;
            $sql_data_marray[$i]['language_id'] = $language_id;
            xtc_db_perform(TABLE_COUPONS_DESCRIPTION, $sql_data_marray[$i]);
          }
//        }
      }
    }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script type="text/javascript" src="includes/general.js"></script>
<?php // BOF - DokuMan/Web28 - 2010-09-20 - Replace SPIFFY CAL by JqueryUI
/*
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script type="text/javascript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script type="text/javascript">
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
</script>
*/
?>
<link type="text/css" href="includes/javascript/jquery-ui.custom.css" rel="stylesheet" />
<script type="text/javascript" src="includes/javascript/jquery.min.js"></script>
<script type="text/javascript" src="includes/javascript/ui/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="includes/javascript/ui/jquery.ui.datepicker.min.js"></script>
<script type="text/javascript" src="includes/javascript/ui/jquery.ui.datepicker-<?php echo strtolower($_SESSION['language_code']); ?>.js"></script>
 <script type="text/javascript">
  $(function() {
  /* set Datepicker for coupon_startdate (1) and coupon_finishdate (2) */  
	  $('#hasDatepicker1').datepicker(		
		$.datepicker.regional['<?php echo strtolower($_SESSION['language_code']); ?>'],
		{dateFormat:'yy-mm-dd', changeMonth: true,	changeYear: true}
	  );
	  $('#hasDatepicker2').datepicker(
		$.datepicker.regional['<?php echo strtolower($_SESSION['language_code']); ?>'],
		{dateFormat:'yy-mm-dd', changeMonth: true,	changeYear: true}	
	  );	  
	});
</script>
<?php /* EOF - DokuMan/Web28 - 2010-09-20 - Replace SPIFFY CAL by JqueryUI */ ?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php // BOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
//<div id="spiffycalendar" class="text"></div>
// EOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI ?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
<?php
  switch ($_GET['action']) {
  case 'voucherreport':
?>
      <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
			 <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
				<td class="dataTableHeadingContent" align="left"><?php echo COUPON_ID; ?></td> <?php // web28 - 2010-07-23 - new table design ?>
                <td class="dataTableHeadingContent" align="left"><?php echo CUSTOMER_ID; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php echo CUSTOMER_NAME; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php echo IP_ADDRESS; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php echo REDEEM_DATE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $cc_query_raw = "select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $_GET['cid'] . "'";
    $cc_split = new splitPageResults($_GET['page'], '20', $cc_query_raw, $cc_query_numrows);
    $cc_query = xtc_db_query($cc_query_raw);
    while ($cc_list = xtc_db_fetch_array($cc_query)) {
      $rows++;
      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      if (((!$_GET['uid']) || (@$_GET['uid'] == $cc_list['unique_id'])) && (!$cInfo)) {
        $cInfo = new objectInfo($cc_list);
      }
      if ( (is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cInfo->coupon_id . '&action=voucherreport&uid=' . $cinfo->unique_id) . '\'">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cc_list['coupon_id'] . '&action=voucherreport&uid=' . $cc_list['unique_id']) . '\'">' . "\n";
      }
$customer_query = xtc_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $cc_list['customer_id'] . "'");
$customer = xtc_db_fetch_array($customer_query);

?>
                <td class="dataTableContent" align="left">&nbsp;<?php echo $_GET['cid']; ?></td><?php // web28 - 2010-07-23 - new table design ?>
				<td class="dataTableContent" align="left">&nbsp;<?php echo $cc_list['customer_id']; ?></td>
                <td class="dataTableContent" align="left">&nbsp;<?php echo $customer['customers_firstname'] . ' ' . $customer['customers_lastname']; ?></td>
                <td class="dataTableContent" align="left">&nbsp;<?php echo $cc_list['redeem_ip']; ?></td>
                <td class="dataTableContent" align="left">&nbsp;<?php echo xtc_date_short($cc_list['redeem_date']); ?></td>
<!-- BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
<!--
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xtc_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cid=' . $cc_list['coupon_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
-->
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ICON_ARROW_RIGHT); } else { echo '<a href="' . xtc_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cid=' . $cc_list['coupon_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
<!-- EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
              </tr>
<?php
    }
?>



             </table></td>
<?php
    $heading = array();
    $contents = array();
      $coupon_description_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $coupon_desc = xtc_db_fetch_array($coupon_description_query);
      $count_customers = xtc_db_query("select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $_GET['cid'] . "' and customer_id = '" . $cInfo->customer_id . "'");

      $heading[] = array('text' => '<b>[' . $_GET['cid'] . ']' . COUPON_NAME . ' ' . $coupon_desc['coupon_name'] . '</b>');
      $contents[] = array('text' => '<b>' . TEXT_REDEMPTIONS . '</b>');
      $contents[] = array('text' => TEXT_REDEMPTIONS_TOTAL . '=' . xtc_db_num_rows($cc_query));
      $contents[] = array('text' => TEXT_REDEMPTIONS_CUSTOMER . '=' . xtc_db_num_rows($count_customers));
	  // WM - und wie kommt man zur�ck? zur�ck Button fehlt
      $contents[] = array('text' => '<a class="button" onclick="this.blur();" href="' . xtc_href_link(FILENAME_COUPON_ADMIN) . '">' . BUTTON_BACK . '</a>');

?>
    <td width="25%" valign="top">
<?php
      $box = new box;
      echo $box->infoBox($heading, $contents);
      echo '            </td>' . "\n";

    break;
  case 'preview_email':
    $coupon_query = xtc_db_query("select coupon_code from " .TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon_result = xtc_db_fetch_array($coupon_query);
    $coupon_name_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
    $coupon_name = xtc_db_fetch_array($coupon_name_query);
    switch ($_POST['customers_email_address']) {
    case '***':
      $mail_sent_to = TEXT_ALL_CUSTOMERS;
      break;
    case '**D':
      $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
      break;
    default:
      $mail_sent_to = $_POST['customers_email_address'];
      break;
    }
?>
      <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
          <tr><?php echo xtc_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=send_email_to_user&cid=' . $_GET['cid']); ?>
            <td><table border="0" width="100%" cellpadding="0" cellspacing="2">
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_CUSTOMER; ?></b><br /><?php echo $mail_sent_to; ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_COUPON; ?></b><br /><?php echo $coupon_name['coupon_name']; ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_FROM; ?></b><br /><?php echo htmlspecialchars(stripslashes($_POST['from'])); ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_SUBJECT; ?></b><br /><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_MESSAGE; ?></b><br /><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td>
<?php
/* Re-Post all POST'ed variables */
    reset($_POST);
    while (list($key, $value) = each($_POST)) {
      if (!is_array($_POST[$key])) {
        echo xtc_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
    }
?>
                <table border="0" width="100%" cellpadding="0" cellspacing="2">
                  <tr>
                    <td><?php ?>&nbsp;</td>
                    <td align="right"><?php echo '<a class="button" onclick="this.blur();" href="' . xtc_href_link(FILENAME_COUPON_ADMIN) . '">' . BUTTON_CANCEL . '</a> <input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </form></tr>
<?php
    break;
  case 'email':
    $coupon_query = xtc_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon_result = xtc_db_fetch_array($coupon_query);
    $coupon_name_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
    $coupon_name = xtc_db_fetch_array($coupon_name_query);
?>
      <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>

          <tr><?php echo xtc_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=preview_email&cid='. $_GET['cid']); ?>
            <td><table class="main" border="0" cellpadding="0" cellspacing="2"><?php // web28 - 2010-07-23 - new table design ?>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
    $customers = array();
    $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
    $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
    $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
    $mail_query = xtc_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
    while($customers_values = xtc_db_fetch_array($mail_query)) {
      $customers[] = array('id' => $customers_values['customers_email_address'],
                           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    }
?>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_COUPON; ?>&nbsp;&nbsp;</td>
                <td><?php echo $coupon_name['coupon_name']; ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_CUSTOMER; ?>&nbsp;&nbsp;</td>
                <td><?php echo xtc_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_FROM; ?>&nbsp;&nbsp;</td>
                <td><?php echo xtc_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
/*
              <tr>
                <td class="main"><?php echo TEXT_RESTRICT; ?>&nbsp;&nbsp;</td>
                <td><?php echo xtc_draw_checkbox_field('customers_restrict', $customers_restrict);?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
*/
?>
              <tr>
                <td class="main"><?php echo TEXT_SUBJECT; ?>&nbsp;&nbsp;</td>
                <td><?php echo xtc_draw_input_field('subject'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?>&nbsp;&nbsp;</td>
                <td><?php echo xtc_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><?php echo '<input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>'; ?></td>
              </tr>
            </table></td>
          </form></tr>

      </tr>
      </td>
<?php
    break;

//##########################

  case 'update_preview':
?>
      <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
      <td>
<?php echo xtc_draw_form('coupon', 'coupon_admin.php', 'action=update_confirm&oldaction=' . $_GET['oldaction'] . '&cid=' . $_GET['cid']); ?>
 <?php // BOF - web28 - 2010-07-23 - new table design ?>
	  <table class="main" border="0" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse">

<?php
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
?>
      <tr>
        <td class="td_left"><?php echo COUPON_NAME . '&nbsp;&nbsp;' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?></td>
        <td class="td_right"><?php echo $_POST['coupon_name'][$language_id]; ?>&nbsp;</td>
      </tr>
<?php
}

        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
?>
      <tr>
        <td class="td_left"><?php echo COUPON_DESC . '&nbsp;&nbsp;' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?></td>
        <td class="td_right"><?php echo $_POST['coupon_desc'][$language_id]; ?>&nbsp;</td>
      </tr>
<?php
}
?>
      <tr>
		<td class="td_left"><?php echo COUPON_AMOUNT; ?></td>
        <td class="td_right"><?php echo $_POST['coupon_amount']; ?>&nbsp;</td>
      </tr>

      <tr>
		<td class="td_left"><?php echo COUPON_MIN_ORDER; ?></td>
        <td class="td_right"><?php echo $_POST['coupon_min_order']; ?>&nbsp;</td>
      </tr>

      <tr>
        <td class="td_left"><?php echo COUPON_FREE_SHIP; ?></td>
<?php
    if ($_POST['coupon_free_ship']) {
?>
        <td class="td_right"><?php echo TEXT_FREE_SHIPPING; ?></td>
<?php
    } else {
?>
        <td class="td_right"><?php echo TEXT_NO_FREE_SHIPPING; ?></td>
<?php
    }
?>
      </tr>
      <tr>
        <td class="td_left"><?php echo COUPON_CODE; ?></td>
<?php
    if ($_POST['coupon_code']) {
      $c_code = $_POST['coupon_code'];
    } else {
      $c_code = $coupon_code;
    }
?>
        <td class="td_right"><?php echo $coupon_code; ?>&nbsp;</td>
      </tr>

      <tr>
        <td class="td_left"><?php echo COUPON_USES_COUPON; ?></td>
        <td class="td_right"><?php echo $_POST['coupon_uses_coupon']; ?>&nbsp;</td>
      </tr>

      <tr>
        <td class="td_left"><?php echo COUPON_USES_USER; ?></td>
        <td class="td_right"><?php echo $_POST['coupon_uses_user']; ?>&nbsp;</td>
      </tr>

       <tr>
        <td class="td_left"><?php echo COUPON_PRODUCTS; ?></td>
        <td class="td_right"><?php echo $_POST['coupon_products']; ?>&nbsp;</td>
      </tr>


      <tr>
        <td class="td_left"><?php echo COUPON_CATEGORIES; ?></td>
        <td class="td_right"><?php echo $_POST['coupon_categories']; ?>&nbsp;</td>

      </tr>
      <tr>
        <td class="td_left"><?php echo COUPON_STARTDATE; ?></td>
<?php
// BOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
    //$start_date = date(DATE_FORMAT, mktime(0, 0, 0, $_POST['coupon_startdate_month'],$_POST['coupon_startdate_day'] ,$_POST['coupon_startdate_year'] ));
    $start_date = xtc_date_short($_POST['coupon_startdate']);
// EOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
?>
        <td class="td_right"><?php echo $start_date; ?>&nbsp;</td>
      </tr>

      <tr>
        <td class="td_left"><?php echo COUPON_FINISHDATE; ?></td>
<?php
// BOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
    //$finish_date = date(DATE_FORMAT, mktime(0, 0, 0, $_POST['coupon_finishdate_month'],$_POST['coupon_finishdate_day'] ,$_POST['coupon_finishdate_year'] ));
    $finish_date = xtc_date_short($_POST['coupon_finishdate']);
// EOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
?>
        <td class="td_right"><?php echo $finish_date; ?>&nbsp;</td>
      </tr>
<?php
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          echo xtc_draw_hidden_field('coupon_name[' . $languages[$i]['id'] . ']', $_POST['coupon_name'][$language_id]);
          echo xtc_draw_hidden_field('coupon_desc[' . $languages[$i]['id'] . ']', $_POST['coupon_desc'][$language_id]);
       }
    echo xtc_draw_hidden_field('coupon_amount', $_POST['coupon_amount']);
    echo xtc_draw_hidden_field('coupon_min_order', $_POST['coupon_min_order']);
    echo xtc_draw_hidden_field('coupon_free_ship', $_POST['coupon_free_ship']);
    echo xtc_draw_hidden_field('coupon_code', $c_code);
    echo xtc_draw_hidden_field('coupon_uses_coupon', $_POST['coupon_uses_coupon']);
    echo xtc_draw_hidden_field('coupon_uses_user', $_POST['coupon_uses_user']);
    echo xtc_draw_hidden_field('coupon_products', $_POST['coupon_products']);
    echo xtc_draw_hidden_field('coupon_categories', $_POST['coupon_categories']);
    // BOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
    //echo xtc_draw_hidden_field('coupon_startdate', date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_startdate_month'],$_POST['coupon_startdate_day'] ,$_POST['coupon_startdate_year'] )));
    //echo xtc_draw_hidden_field('coupon_finishdate', date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_finishdate_month'],$_POST['coupon_finishdate_day'] ,$_POST['coupon_finishdate_year'] )));
    echo xtc_draw_hidden_field('coupon_startdate', $_POST['coupon_startdate']);
    echo xtc_draw_hidden_field('coupon_finishdate', $_POST['coupon_finishdate']);
    // EOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
?>
     <tr>
        <td align="left"><?php echo '<input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_CONFIRM . '"/>'; ?></td>
        <td align="left"><?php echo '<input type="submit" name="back" class="button" onclick="this.blur();" value="' . BUTTON_BACK . '"/>'; ?></td>
      </td>
      </tr>
      </td></table>
<?php // EOF - web28 - 2010-07-23 - new table design ?>
	  </form>
      </tr>
      </table></td>
<?php

    break;

//##########################

  case 'voucheredit':
    $languages = xtc_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $language_id = $languages[$i]['id'];
      $coupon_query = xtc_db_query("select coupon_name,coupon_description from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" .  $_GET['cid'] . "' and language_id = '" . $language_id . "'");
      $coupon = xtc_db_fetch_array($coupon_query);
      $coupon_name[$language_id] = $coupon['coupon_name'];
      $coupon_desc[$language_id] = $coupon['coupon_description'];
    }
    $coupon_query = xtc_db_query("select coupon_code, coupon_amount, coupon_type, coupon_minimum_order, coupon_start_date, coupon_expire_date, uses_per_coupon, uses_per_user, restrict_to_products, restrict_to_categories from " . TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon = xtc_db_fetch_array($coupon_query);
    $coupon_amount = $coupon['coupon_amount'];
    if ($coupon['coupon_type']=='P') {
      $coupon_amount .= '%';
    }
    if ($coupon['coupon_type']=='S') {
      $coupon_free_ship .= true;
    }
    $coupon_min_order = $coupon['coupon_minimum_order'];
    $coupon_code = $coupon['coupon_code'];
    $coupon_uses_coupon = $coupon['uses_per_coupon'];
    $coupon_uses_user = $coupon['uses_per_user'];
    $coupon_products = $coupon['restrict_to_products'];
    $coupon_categories = $coupon['restrict_to_categories'];
  case 'new':
// set some defaults
    if (!$coupon_uses_user) $coupon_uses_user=1;
?>
      <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
      <td>
<?php
    echo xtc_draw_form('coupon', 'coupon_admin.php', 'action=update&oldaction='.$_GET['action'] . '&cid=' . $_GET['cid'],'post', 'enctype="multipart/form-data"');
?>
<?php // BOF - web28 - 2010-07-23 - new table design?>
      <table class="main" border="0" width="100%" cellspacing="0" cellpadding="6" style="border-collapse:collapse">
<?php
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
?>
      <tr>
        <td class="td_left"><?php if ($i==0) echo COUPON_NAME; ?></td>
        <td class="td_middle"><?php echo xtc_draw_input_field('coupon_name[' . $languages[$i]['id'] . ']', $coupon_name[$language_id], 'style="width: 150px"') . '&nbsp;' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?></td>
        <td class="td_right"><?php if ($i==0) echo COUPON_NAME_HELP; ?></td>
      </tr>
<?php
}

        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
?>

      <tr>
        <td class="td_left"><?php if ($i==0) echo COUPON_DESC; ?></td>
        <td class="td_middle"><nobr><?php echo xtc_draw_textarea_field('coupon_desc[' . $languages[$i]['id'] . ']','physical','24','3', $coupon_desc[$language_id], 'style="width: 250px"') . '&nbsp;' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?><nobr></td>
        <td class="td_right"><?php if ($i==0) echo COUPON_DESC_HELP; ?></td>
      </tr>
<?php
}
?>
      <tr>
        <td class="td_left"><?php echo COUPON_AMOUNT; ?></td>
        <td class="td_middle"><?php echo xtc_draw_input_field('coupon_amount', $coupon_amount, 'style="width: 150px"'); ?></td>
        <td class="td_right"><?php echo COUPON_AMOUNT_HELP; ?></td>
      </tr>
      <tr>
        <td class="td_left"><?php echo COUPON_MIN_ORDER; ?></td>
        <td class="td_middle"><?php echo xtc_draw_input_field('coupon_min_order', $coupon_min_order, 'style="width: 150px"'); ?></td>
        <td class="td_right"><?php echo COUPON_MIN_ORDER_HELP; ?></td>
      </tr>
      <tr>
        <td class="td_left"><?php echo COUPON_FREE_SHIP; ?></td>
        <td class="td_middle"><?php echo xtc_draw_checkbox_field('coupon_free_ship', $coupon_free_ship); ?></td>
        <td class="td_right"><?php echo COUPON_FREE_SHIP_HELP; ?></td>
      </tr>
      <tr>
        <td class="td_left"><?php echo COUPON_CODE; ?></td>
        <td class="td_middle"><?php echo xtc_draw_input_field('coupon_code', $coupon_code, 'style="width: 150px"'); ?></td>
        <td class="td_right"><?php echo COUPON_CODE_HELP; ?></td>
      </tr>
      <tr>
        <td class="td_left"><?php echo COUPON_USES_COUPON; ?></td>
        <td class="td_middle"><?php echo xtc_draw_input_field('coupon_uses_coupon', $coupon_uses_coupon, 'style="width: 150px"'); ?></td>
        <td class="td_right"><?php echo COUPON_USES_COUPON_HELP; ?></td>
      </tr>
      <tr>
        <td class="td_left"><?php echo COUPON_USES_USER; ?></td>
        <td class="td_middle"><?php echo xtc_draw_input_field('coupon_uses_user', $coupon_uses_user, 'style="width: 150px"'); ?></td>
        <td class="td_right"><?php echo COUPON_USES_USER_HELP; ?></td>
      </tr>
       <tr>
        <td class="td_left"><?php echo COUPON_PRODUCTS; ?></td>
        <td class="td_middle"><?php echo xtc_draw_input_field('coupon_products', $coupon_products, 'style="width: 150px"'); ?> <A HREF="validproducts.php" TARGET="_blank" ONCLICK="window.open('validproducts.php', 'Valid_Products', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600'); return false">View</A></td>
        <td class="td_right"><?php echo COUPON_PRODUCTS_HELP; ?></td>
      </tr>
      <tr>
        <td class="td_left"><?php echo COUPON_CATEGORIES; ?></td>
        <td class="td_middle"><?php echo xtc_draw_input_field('coupon_categories', $coupon_categories, 'style="width: 150px"'); ?> <A HREF="validcategories.php" TARGET="_blank" ONCLICK="window.open('validcategories.php', 'Valid_Categories', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600'); return false">View</A></td>
        <td class="td_right"><?php echo COUPON_CATEGORIES_HELP; ?></td>
      </tr>
      <tr>
<?php
    if (!$_POST['coupon_startdate']) {
      // BOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
      //$coupon_startdate = explode("-", date('Y-m-d')); // Hetfield - 2009-08-18 - replaced depricated function split with explode to be ready for PHP >= 5.3
      $coupon_startdate = date('Y-m-d');
      // EOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
    } else {
      // BOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
      //$coupon_startdate = explode("-", $_POST['coupon_startdate']); // Hetfield - 2009-08-18 - replaced depricated function split with explode to be ready for PHP >= 5.3
      $coupon_startdate = $_POST['coupon_startdate'];
      // EOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
    }

    if (!$_POST['coupon_finishdate']) {
      // BOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
      //$coupon_finishdate = explode("-", date('Y-m-d')); // Hetfield - 2009-08-18 - replaced depricated function split with explode to be ready for PHP >= 5.3
      $coupon_finishdate = date('Y-m-d');
      $coupon_finishdate[3] = $coupon_finishdate[3] + 1; //set actual year +1
      // EOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
    } else {
      // BOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
      //$coupon_finishdate = explode("-", $_POST['coupon_finishdate']); // Hetfield - 2009-08-18 - replaced depricated function split with explode to be ready for PHP >= 5.3
      $coupon_finishdate = $_POST['coupon_finishdate'];
      // EOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
    }
?>
        <td class="td_left"><?php echo COUPON_STARTDATE; ?></td>
        <td class="td_middle">
        <?php
          // BOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
          //echo xtc_draw_date_selector('coupon_startdate', mktime(0,0,0, $coupon_startdate[1], $coupon_startdate[2], $coupon_startdate[0], 0));
          echo xtc_draw_input_field('coupon_startdate', $coupon_startdate ,'id="hasDatepicker1"');
          // EOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
        ?>
        </td>
        <td class="td_right"><?php echo COUPON_STARTDATE_HELP; ?></td>
      </tr>
      <tr>
        <td class="td_left"><?php echo COUPON_FINISHDATE; ?></td>
        <td class="td_middle">
        <?php
          // BOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
          //echo xtc_draw_date_selector('coupon_finishdate', mktime(0,0,0, $coupon_finishdate[1], $coupon_finishdate[2], $coupon_finishdate[0], 0));
          echo xtc_draw_input_field('coupon_finishdate', $coupon_finishdate ,'id="hasDatepicker2"');
          // EOF - DokuMan - 2010-09-03 - Replace SPIFFY CAL by JqueryUI
        ?>
		</td>
        <td class="td_right"><?php echo COUPON_FINISHDATE_HELP; ?></td>
      </tr>
      <tr>
        <td align="left"><?php echo '<input type="submit" class="button" onclick="this.blur();" value="' . BUTTON_PREVIEW . '"/>'; ?></td>
        <td align="left"><?php echo '&nbsp;&nbsp;<a class="button" onclick="this.blur();" href="' . xtc_href_link('coupon_admin.php', ''); ?>"><?php echo BUTTON_CANCEL; ?></a></td>
		<td align="left">&nbsp;</td>
	  </tr>
      </td></table>
<?php // EOF - web28 - 2010-07-23 - new table design?>
	  </form>
      </tr>

      </table></td>
<?php
    break;
  default:
?>
    <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="main"><?php echo xtc_draw_form('status', FILENAME_COUPON_ADMIN, '', 'get'); ?>
<?php
    $status_array[] = array('id' => 'Y', 'text' => TEXT_COUPON_ACTIVE);
    $status_array[] = array('id' => 'N', 'text' => TEXT_COUPON_INACTIVE);
    $status_array[] = array('id' => '*', 'text' => TEXT_COUPON_ALL);

    if ($_GET['status']) {
      $status = xtc_db_prepare_input($_GET['status']);
    } else {
      $status = 'Y';
    }
    echo HEADING_TITLE_STATUS . ' ' . xtc_draw_pull_down_menu('status', $status_array, $status, 'onChange="this.form.submit();"');
?>
              </form>
           </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><a class="button" onclick="this.blur();" href="<?php echo xtc_href_link('coupon_admin.php', 'action=new'); ?>"><?php echo BUTTON_INSERT; ?></a><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
<?php // BOF - web28 - 2010-07-23 - new table design?>
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="left" width="25"><?php echo COUPON_ID; ?></td>
				<td class="dataTableHeadingContent" align="left"><?php echo COUPON_NAME; ?></td>
				<td class="dataTableHeadingContent" align="left" width="110"><?php echo COUPON_AMOUNT; ?></td>
				<td class="dataTableHeadingContent" align="left" width="110"><?php echo TEXT_COUPON_MINORDER; ?></td>
				<td class="dataTableHeadingContent" align="left" width="80"><?php echo COUPON_CODE; ?></td>
				<td class="dataTableHeadingContent" align="center" width="70"><?php echo TEXT_COUPON_STATUS; ?></td>
				<td class="dataTableHeadingContent" align="center" width="70"><?php echo TEXT_COUPON_DELETE; ?></td>
				<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
			  </tr>
<?php
    if ($_GET['page'] > 1) $rows = $_GET['page'] * 20 - 20;
    if ($status != '*') {
      $cc_query_raw = "select coupon_id, coupon_code, coupon_amount, coupon_type, coupon_start_date,coupon_expire_date,uses_per_user,uses_per_coupon,restrict_to_products, restrict_to_categories, coupon_active, date_created,date_modified from " . TABLE_COUPONS ." where coupon_active='" . xtc_db_input($status) . "' and coupon_type != 'G'";
    } else {
      $cc_query_raw = "select coupon_id, coupon_code, coupon_amount, coupon_type, coupon_start_date,coupon_expire_date,uses_per_user,uses_per_coupon,restrict_to_products, restrict_to_categories, coupon_active, date_created,date_modified from " . TABLE_COUPONS . " where coupon_type != 'G'";
    }
    $cc_split = new splitPageResults($_GET['page'], '20', $cc_query_raw, $cc_query_numrows);
    $cc_query = xtc_db_query($cc_query_raw);
    while ($cc_list = xtc_db_fetch_array($cc_query)) {
      $rows++;
      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      if (((!$_GET['cid']) || (@$_GET['cid'] == $cc_list['coupon_id'])) && (!$cInfo)) {
        $cInfo = new objectInfo($cc_list);
      }
      if ( (is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action')) . 'cid=' . $cInfo->coupon_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action')) . 'cid=' . $cc_list['coupon_id']) . '\'">' . "\n";
      }
      $coupon_description_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cc_list['coupon_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $coupon_desc = xtc_db_fetch_array($coupon_description_query);
?>
		<td class="dataTableContent" align="left">&nbsp;<?php echo $cc_list['coupon_id']; ?></td>
		<td class="dataTableContent" align="left">&nbsp;<?php echo $coupon_desc['coupon_name']; ?></td>
		<td class="dataTableContent" align="left" style="padding-left: 5px">
<?php
      if ($cc_list['coupon_type'] == 'P') {
        echo $cc_list['coupon_amount'] . '%';
      } elseif ($cc_list['coupon_type'] == 'S') {
        echo TEXT_FREE_SHIPPING;
      } else {
        echo $currencies->format($cc_list['coupon_amount']);
      }
?>
            &nbsp;</td>
				<td class="dataTableContent" align="left">&nbsp;<?php echo $currencies->format($cc_list['coupon_minimum_order']); ?></td>
                <td class="dataTableContent" align="left">&nbsp;<?php echo $cc_list['coupon_code']; ?></td>
<!-- BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
<!--
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xtc_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cid=' . $cc_list['coupon_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
-->
				<td class="dataTableContent" align="center"><?php if ($cc_list['coupon_active'] == 'N') { echo xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10); } else { echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10); } ?></td>
                <td class="dataTableContent" align="center">&nbsp;<?php if ($cc_list['coupon_active'] == 'N') { echo '<a href="' . xtc_href_link('coupon_admin.php',  '&action=noconfirmdelete' . '&cID=' . $cc_list['coupon_id']) . '">' . xtc_image(DIR_WS_ICONS . 'delete.gif', BUTTON_DELETE_NO_CONFIRM) . '</a>'; } ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ICON_ARROW_RIGHT); } else { echo '<a href="' . xtc_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cid=' . $cc_list['coupon_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
<!-- EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
              </tr>
<?php
    }
?>
          <tr>
            <td colspan="8"><table border="0" width="100%" cellspacing="0" cellpadding="2">
            <?php if (is_object($cc_split)) { ?>
              <tr>
                <td class="smallText">&nbsp;<?php echo $cc_split->display_count($cc_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUPONS); ?>&nbsp;</td>
                <td align="right" class="smallText">&nbsp;<?php echo $cc_split->display_links($cc_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>&nbsp;</td>
              </tr>
            <?php } ?>
              <tr>
                <td align="right" colspan="2" class="smallText"><?php echo '<a class="button" onclick="this.blur();" href="' . xtc_href_link('coupon_admin.php', 'page=' . $_GET['page'] . '&cID=' . $cInfo->coupon_id . '&action=new') . '">' . BUTTON_INSERT . '</a>'; ?></td>
              </tr>
            </table>
<?php // EOF - web28 - 2010-07-23 - new table design?>
			</td>
          </tr>
        </table></td>

<?php

    $heading = array();
    $contents = array();

    switch ($_GET['action']) {
    case 'release':
      break;
    case 'voucherreport':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_COUPON_REPORT . '</b>');
      $contents[] = array('text' => TEXT_NEW_INTRO);
      break;
    case 'neww':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_COUPON . '</b>');
      $contents[] = array('text' => TEXT_NEW_INTRO);
      $contents[] = array('text' => '<br />' . COUPON_NAME . '<br />' . xtc_draw_input_field('name'));
      $contents[] = array('text' => '<br />' . COUPON_AMOUNT . '<br />' . xtc_draw_input_field('voucher_amount'));
      $contents[] = array('text' => '<br />' . COUPON_CODE . '<br />' . xtc_draw_input_field('voucher_code'));
      $contents[] = array('text' => '<br />' . COUPON_USES_COUPON . '<br />' . xtc_draw_input_field('voucher_number_of'));
      break;
    default:
      $heading[] = array('text'=>'['.$cInfo->coupon_id.']  '.$cInfo->coupon_code);


      $amount = $cInfo->coupon_amount;
      if ($cInfo->coupon_type == 'P') {
        $amount .= '%';
	  // BOF - web28 - 2010-07-22 - FIX coupon_amount
	  } elseif ($cInfo->coupon_type == 'S') {
        $amount = TEXT_FREE_SHIPPING;
	  // EOF - web28 - 2010-07-22 - FIX coupon_amount
      } else {
        $amount = $currencies->format($amount);
      }

      if ($_GET['action'] == 'voucherdelete') {
        $contents[] = array('text'=> TEXT_CONFIRM_DELETE . '</br></br>' .
                '<a class="button" onclick="this.blur();" href="'.xtc_href_link('coupon_admin.php','action=confirmdelete&cid='.$_GET['cid'],'NONSSL').'">'.BUTTON_CONFIRM.'</a>' .
                '<a class="button" onclick="this.blur();" href="'.xtc_href_link('coupon_admin.php','cid='.$cInfo->coupon_id,'NONSSL').'">'.BUTTON_CANCEL.'</a>'
                );
      } else {
        $prod_details = NONE;
        if ($cInfo->restrict_to_products) {
          $prod_details = '<a href="listproducts.php?cid=' . $cInfo->coupon_id . '" target="_blank" onclick="window.open(\'listproducts.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false"><strong>' . TEXT_VIEW_SHORT .'</strong></a>';
        }
        $cat_details = NONE;
        if ($cInfo->restrict_to_categories) {
          $cat_details = '<a href="listcategories.php?cid=' . $cInfo->coupon_id . '" target="_blank" onclick="window.open(\'listcategories.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false"><strong>' . TEXT_VIEW_SHORT .'</strong></a>';
        }
        $coupon_name_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cInfo->coupon_id . "' and language_id = '" . $_SESSION['languages_id'] . "'");
        $coupon_name = xtc_db_fetch_array($coupon_name_query);
									  // BOF - web28 - 2010-07-23 - new table design / Abfrage ob Coupon aktiv ist
                                      $coupon_active_query = xtc_db_query("select coupon_active from " . TABLE_COUPONS . " where coupon_id = '" . $cInfo->coupon_id . "'");
                                      $coupon_active = xtc_db_fetch_array($coupon_active_query);

                                      $contents[] = array('text'=>COUPON_NAME . ':&nbsp;' . $coupon_name['coupon_name'] . '<br />' .
                                      COUPON_AMOUNT . ':&nbsp;<strong><font color="red">' . $amount . '</font></strong><br /><br />' .

                                      COUPON_STARTDATE . ':&nbsp;' . xtc_date_short($cInfo->coupon_start_date) . '<br />' .
                                      COUPON_FINISHDATE . ':&nbsp;' . xtc_date_short($cInfo->coupon_expire_date) . '<br /><br />' .
                                      COUPON_USES_COUPON . ':&nbsp;<strong>' . $cInfo->uses_per_coupon . '</strong><br />' .
                                      COUPON_USES_USER . ':&nbsp;<strong>' . $cInfo->uses_per_user . '</strong><br /><br />' .
                                      COUPON_PRODUCTS . ':&nbsp;' . $prod_details . '<br />' .
                                      COUPON_CATEGORIES . ':&nbsp;' . $cat_details . '<br /><br />' .
                                      DATE_CREATED . ':&nbsp;' . xtc_date_short($cInfo->date_created) . '<br />' .
                                      DATE_MODIFIED . ':&nbsp;' . xtc_date_short($cInfo->date_modified) . '<br /><br />');

                                      // WM Buttons ausgeblendet, wenn kein Coupon vorhanden oder der Coupon inaktiv ist. Dann sind die
                                      // Buttons unn�tz oder verwirren nur
                                      //if ($coupon_desc['coupon_name'] != '') {
                                      //  if ($coupon_active['coupon_active'] != 'N') {
                                      $contents[] = array('text'=>'<a class="button" onclick="this.blur();" href="'.xtc_href_link('coupon_admin.php','action=email&cid='.$cInfo->coupon_id,'NONSSL').'">'.BUTTON_EMAIL.'</a>' .
                                      '<a class="button" onclick="this.blur();" href="'.xtc_href_link('coupon_admin.php','action=voucheredit&cid='.$cInfo->coupon_id,'NONSSL').'">'.BUTTON_EDIT.'</a>' .
                                      '<a class="button" onclick="this.blur();" href="'.xtc_href_link('coupon_admin.php','action=voucherdelete&cid='.$cInfo->coupon_id,'NONSSL').'">'.BUTTON_STATUS_OFF.'</a>' .
                                      '<a class="button" onclick="this.blur();" href="'.xtc_href_link('coupon_admin.php','action=voucherreport&cid='.$cInfo->coupon_id,'NONSSL').'">'.BUTTON_REPORT.'</a>');
                                      //}
                                     //}
									 // EOF - web28 - 2010-07-23 - new table design / Abfrage ob Coupon aktiv ist

                                    }
                                    break;
      }
?>
    <td width="25%" valign="top">
<?php
      $box = new box;
      echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
    }
?>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>