<?php
/* --------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce, www.oscommerce.com
   (c) 2003      nextcommerce; www.nextcommerce.org
   (c) 2006      xt:Commerce; www.xt-commerce.com

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  if ($messageStack->size > 0) {
    echo $messageStack->output();
  }

// BOF - web28 - 2010-08-19 - Admin language switch
  if (!isset($lng) || !is_object($lng)) {
    include(DIR_WS_CLASSES . 'language.php');
    $lng = new language;
  }

  $languages_string = '';
  $count_lng=0;
  reset($lng->catalog_languages);
  while (list($key, $value) = each($lng->catalog_languages)) {
    if ( $value['status'] != 0 ){
      $count_lng++;
      $languages_string .= '&nbsp;<a href="' . xtc_href_link(basename($_SERVER["PHP_SELF"]), 'language=' . $key.'&'.xtc_get_all_get_params(array('language', 'currency')), 'NONSSL') . '">' . xtc_image('../lang/' .  $value['directory'] .'/admin/images/' . $value['image'], $value['name']) . '</a>';
    }
  }
  if ($count_lng < 2) $languages_string = '';
// EOF - web28 - 2010-08-19 - Admin language switch

// BOF - Tomcraft - 2009-11-02 - JavaScript WEICHE  - Admin Umschaltauswahl
  if (USE_ADMIN_TOP_MENU != 'false') {
?>
   <script src="includes/liststyle_menu/topmenu.js" type="text/javascript"></script>
   <script language="javascript">
    <!--
	document.write('<link href="includes/liststyle_menu/liststyle_top.css" rel="stylesheet" type="text/css" />');
	//-->
	</script>
<?php
  } else echo '<link href="includes/liststyle_menu/liststyle_left.css" rel="stylesheet" type="text/css" />';
 // EOF - Tomcraft - 2009-11-02 - JavaScript WEICHE  - Admin Umschaltauswahl

// BOF - hendrik - 2010-08-29 - xajax in backend
if( XAJAX_BACKEND_SUPPORT=='true' ) {
	// Include XAJAX JS Library
	require ('xajax.common.php');                            // imdxajax
	if ($imdxajax) {
		$imdxajax->printJavascript('includes/');
	}
}
// EOF - hendrik - 2010-08-29 - xajax in backend
?>
<!-- BOF - Tomcraft - 2009-11-02 - JavaScript WEICHE //-->
<noscript>
	<link href="includes/liststyle_menu/liststyle_left.css" rel="stylesheet" type="text/css" />
</noscript>
<!-- EOF - Tomcraft - 2009-11-02 - JavaScript WEICHE //-->

<!-- BOF - web28 - 2010-04-10 - added ADMIN SEARCH BAR//-->
<!--div id="top1"></div-->
<div id="top1"><?php include(DIR_WS_INCLUDES . "admin_search_bar.php");?></div>
<!-- EOF - web28 - 2010-04-10 - added ADMIN SEARCH BAR//-->

<table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 5px;">
  <tr>
    <td><?php echo xtc_image(DIR_WS_IMAGES . 'logo.gif', 'xt:Commerce').'<br>&nbsp;&nbsp;&nbsp;'.$languages_string ; ?></td>
    <td valign="bottom" align="left" width="100%"><table border="0" cellspacing="0" cellpadding="2">
      <tr>
<!-- BOF - web28 - 2010-06-20 - added xtc_href_link to fastmenu//-->
        <td class="fastmenu" align="center"><a href="<?php echo xtc_href_link('orders.php', '', 'NONSSL') ; ?>"><img src="images/icons/fastnav/icon_orders.jpg" alt="<?php echo (BOX_ORDERS) ; ?>" width="40" height="40" border="0"></a><br>
		<?php echo (BOX_ORDERS) ; ?>
		</td>
         <!--td class="fastmenu" align="center"><a href="orders_status.php"><img src="images/icons/fastnav/icon_order_status.jpg" alt="<?php echo (BOX_ORDERS_STATUS) ; ?>" width="40" height="40" border="0"></a><br>
		<?php //echo (BOX_ORDERS_STATUS) ; ?>
		</td>
         <td class="fastmenu" align="center"><a href="shipping_status.php"><img src="images/icons/fastnav/icon_shipping_status.jpg" alt="<?php echo (BOX_SHIPPING_STATUS) ; ?>" width="40" height="40" border="0"></a><br>
		<?php //echo (BOX_SHIPPING_STATUS) ; ?>
		</td-->
         <td class="fastmenu" align="center"><a href="<?php echo xtc_href_link('content_manager.php', '', 'NONSSL') ; ?>"><img src="images/icons/fastnav/icon_content.jpg" alt="<?php echo (BOX_CONTENT) ; ?>" width="40" height="40" border="0"></a><br>
		<?php echo (BOX_CONTENT) ; ?>
		</td>
         <td class="fastmenu" align="center"><a href="<?php echo xtc_href_link('backup.php', '', 'NONSSL') ; ?>"><img src="images/icons/fastnav/icon_backup.jpg" alt="<?php echo (BOX_BACKUP) ; ?>" width="40" height="40" border="0"></a><br>
		<?php echo (BOX_BACKUP) ; ?>
		</td>
         <td class="fastmenu" align="center"><a href="<?php echo xtc_href_link('customers.php', '', 'NONSSL') ; ?>"><img src="images/icons/fastnav/icon_customers.jpg" alt="<?php echo (BOX_CUSTOMERS) ; ?>" width="40" height="40" border="0"></a><br>
		<?php echo (BOX_CUSTOMERS) ; ?>
		</td>
         <td class="fastmenu" align="center"><a href="<?php echo xtc_href_link('categories.php', '', 'NONSSL') ; ?>"><img src="images/icons/fastnav/icon_categories.jpg" alt="<?php echo (BOX_CATEGORIES) ; ?>" width="40" height="40" border="0"></a><br>
		<?php echo (BOX_CATEGORIES) ; ?>
		</td>
         <td class="fastmenu" align="center"><a href="<?php echo xtc_href_link('../index.php', '', 'NONSSL') ; ?>"><img src="images/icons/fastnav/icon_shop.jpg" width="40" height="40" border="0"></a><br>
		Shop
		</td>
        <td class="fastmenu" align="center"><a href="<?php echo xtc_href_link('../logoff.php', '', 'NONSSL') ; ?>"><img src="images/icons/fastnav/icon_logout.jpg" width="40" height="40" border="0"></a><br>
		Logout
		</td>
        <td class="fastmenu" align="center"><a href="<?php echo xtc_href_link('credits.php', '', 'NONSSL') ; ?>"><img src="images/icons/fastnav/icon_credits.jpg" width="40" height="40" border="0"></a><br>
		Credits
		</td>
<!-- EOF - web28 - 2010-06-20 - added xtc_href_link to fastmenu //-->

<!-- BOF - hendrik - 2010-08-29 - xajax in backend //-->
    <?php if( XAJAX_BACKEND_SUPPORT_TEST=='true' ) {  ?>
    <td class="fastmenu" align="center">
    <!-- ---- beginn xajax_support_test------------------------ -->
    <a href="#" onClick="xajax_xajax_support_test_get_servertime( new Date().toLocaleString() );">xajax_support_test</a>
    <!-- ---- end xajax_support_test------------------------ -->
  </td>
  <?php } ?>
<!-- EOF - hendrik - 2010-08-29 - xajax in backend //-->
      </tr>
    </table>
    </td>
  </tr>
</table>
</div>
<div id="top2"></div>

<?php
if (USE_ADMIN_TOP_MENU != 'false') {
// BOF - vr/web28 - 2009-12-13 - escape some characters
// BOF - Hetfield  2009-12-16 - rename $content in $menucontent because $content already exist
?>
<script language="javascript">
	<!--
    document.write('<?php ob_start(); require(DIR_WS_INCLUDES . "column_left.php"); $menucontent = ob_get_clean(); echo addslashes($menucontent);?>');
	//-->
</script>
<?php
// EOF - Hetfield  2009-12-16 - rename $content in $menucontent because $content already exist
// EOF - vr/web28 - 2009-12-13 - escape some characters
}
?>