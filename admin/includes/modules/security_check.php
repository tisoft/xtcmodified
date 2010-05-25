<?php

/* --------------------------------------------------------------
   $Id: security_check.php 1221 2005-09-20 16:44:09Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (security_check.php,v 1.2 2003/08/23); www.nextcommerce.org
   
   Released under the GNU General Public License
   --------------------------------------------------------------*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

$file_warning = '';

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'includes/configure.php')), '444')) {
	$file_warning .= '<br />'.DIR_FS_CATALOG.'includes/configure.php';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'includes/configure.org.php')), '444')) {
	$file_warning .= '<br />'.DIR_FS_CATALOG.'includes/configure.org.php';
}

if (!strpos(decoct(fileperms(DIR_FS_ADMIN.'includes/configure.php')), '444')) {
	$file_warning .= '<br />'.DIR_FS_ADMIN.'includes/configure.php';
}

if (!strpos(decoct(fileperms(DIR_FS_ADMIN.'includes/configure.org.php')), '444')) {
	$file_warning .= '<br />'.DIR_FS_ADMIN.'includes/configure.org.php';
}

if (!strpos(decoct(fileperms(DIR_FS_ADMIN.'rss/')), '777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'admin/rss/')), '755')) {
	$folder_warning .= '<br />'.DIR_FS_ADMIN.'rss/';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'templates_c/')), '777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'templates_c/')), '755')) {
	$folder_warning .= '<br />'.DIR_FS_CATALOG.'templates_c/';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'cache/')), '777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'cache/')), '755')) {
	$folder_warning .= '<br />'.DIR_FS_CATALOG.'cache/';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'media/')), '777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'media/')), '755')) {
	$folder_warning .= '<br />'.DIR_FS_CATALOG.'media/';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'media/content/')), '777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'media/content/')), '755')) {
	$folder_warning .= '<br />'.DIR_FS_CATALOG.'media/content/';
}

// BOF - Tomcraft - 2010-05-25 - Fixed display of error messages from security_check.php in admin area when file and folder-permissions are set correctly
//if ($file_warning != '' or $folder_warning != '') {
	$payment_query = xtc_db_query("SELECT *
				FROM ".TABLE_CONFIGURATION."
				WHERE configuration_key = 'MODULE_PAYMENT_INSTALLED'");
	while ($payment_data = xtc_db_fetch_array($payment_query)) {
		$installed_payment = $payment_data['configuration_value'];

	}
	$shipping_query = xtc_db_query("SELECT *
				FROM ".TABLE_CONFIGURATION."
				WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
	while ($shipping_data = xtc_db_fetch_array($shipping_query)) {
		$installed_shipping = $shipping_data['configuration_value'];

	}
if ($file_warning != '' or $folder_warning != '' or $installed_payment == '' or $installed_payment == '') {
// EOF - Tomcraft - 2010-05-25 - Fixed display of error messages from security_check.php in admin area when file and folder-permissions are set correctly
?>


<table style="border: 1px solid; border-color: #ff0000;" bgcolor="FDAC00" border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>
<div class="main"> 
        <table width="100%" border="0">
          <tr>
<!-- BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
<!--
            <td width="1"><?php echo xtc_image(DIR_WS_ICONS.'big_warning.gif'); ?></td>
-->
						<td width="1"><?php echo xtc_image(DIR_WS_ICONS.'big_warning.gif', ICON_BIG_WARNING); ?></td>
<!-- EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
            <td class="main">
              <?php

// BOF - Tomcraft - 2010-05-25 - Fixed display of error messages from security_check.php in admin area when file and folder-permissions are set correctly
/*
	if ($file_warning != '') {

		echo TEXT_FILE_WARNING;

		echo '<strong>'.$file_warning.'</strong><br />';
	}

	if ($folder_warning != '') {

		echo TEXT_FOLDER_WARNING;

		echo '<strong>'.$folder_warning.'</strong>';
	}

	$payment_query = xtc_db_query("SELECT *
				FROM ".TABLE_CONFIGURATION."
				WHERE configuration_key = 'MODULE_PAYMENT_INSTALLED'");
	while ($payment_data = xtc_db_fetch_array($payment_query)) {
		$installed_payment = $payment_data['configuration_value'];

	}
	if ($installed_payment == '') {
		echo '<br />'.TEXT_PAYMENT_ERROR;
	}
	$shipping_query = xtc_db_query("SELECT *
				FROM ".TABLE_CONFIGURATION."
				WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
	while ($shipping_data = xtc_db_fetch_array($shipping_query)) {
		$installed_shipping = $shipping_data['configuration_value'];

	}
	if ($installed_shipping == '') {
		echo '<br />'.TEXT_SHIPPING_ERROR;
	}
*/
	if ($file_warning != '') {

		echo TEXT_FILE_WARNING;

		echo '<strong>'.$file_warning.'</strong><br />';
	}

	if ($folder_warning != '') {

		echo TEXT_FOLDER_WARNING;

		echo '<strong>'.$folder_warning.'</strong><br />';
	}

	if ($installed_payment == '') {
		echo TEXT_PAYMENT_ERROR.'<br />';
	}

	if ($installed_shipping == '') {
		echo TEXT_SHIPPING_ERROR;
	}
// EOF - Tomcraft - 2010-05-25 - Fixed display of error messages from security_check.php in admin area when file and folder-permissions are set correctly
?>
            </td>
          </tr>
        </table>
      </div>
</td>
</tr>
</table>
<?php

}
?>

