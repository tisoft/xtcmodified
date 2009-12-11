<?php
/* --------------------------------------------------------------
   $Id: header.php 1025 2005-07-14 11:57:54Z gwinger $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(header.php,v 1.19 2002/04/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (header.php,v 1.17 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  if ($messageStack->size > 0) {
    echo $messageStack->output();
  } 

// BOF - Tomcraft - 2009-11-02 - Admin language switch
  if (!isset($lng) && !is_object($lng)) {
    include(DIR_WS_CLASSES . 'language.php');
    $lng = new language;
  }

  $languages_string = '';
  $count_lng='';
  reset($lng->catalog_languages);
  while (list($key, $value) = each($lng->catalog_languages)) {
  $count_lng++;
 	  $languages_string .= '&nbsp;<a href="' . xtc_href_link(basename($_SERVER["PHP_SELF"]), 'language=' . $key.'&'.xtc_get_all_get_params(array('language', 'currency')), 'NONSSL') . '">' . xtc_image('../lang/' .  $value['directory'] .'/admin/images/' . $value['image'], $value['name']) . '</a>';
  }
  //if ($count_lng > 1 ) echo $languages_string;
// EOF - Tomcraft - 2009-11-02 - Admin language switch
  
// BOF - Tomcraft - 2009-11-02 - JavaScript WEICHE  - Admin Umschaltauswahl
  $admin_top_menu_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'USE_ADMIN_TOP_MENU'");
  $admin_top_menu = xtc_db_fetch_array($admin_top_menu_query);
  if ($admin_top_menu['configuration_value'] != 'false') {
  ?>
   <script language="javascript">
	<!--
	document.write('<script src="includes/liststyle_menu/topmenu.js" type="text/javascript"></script>');
	document.write('<link href="includes/liststyle_menu/liststyle_top.css" rel="stylesheet" type="text/css" />');
	//-->
	</script>	 
  <?php	 
  } else echo '<link href="includes/liststyle_menu/liststyle_left.css" rel="stylesheet" type="text/css" />';
 // EOF - Tomcraft - 2009-11-02 - JavaScript WEICHE  - Admin Umschaltauswahl
?>

<!-- BOF - Tomcraft - 2009-11-02 - JavaScript WEICHE //-->
<noscript> 
	<link href="includes/liststyle_menu/liststyle_left.css" rel="stylesheet" type="text/css" />
</noscript>
<!-- EOF - Tomcraft - 2009-11-02 - JavaScript WEICHE //-->

<div id="top1"></div>

<table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 5px;">  
  <tr>
    <td><?php echo xtc_image(DIR_WS_IMAGES . 'logo.gif', 'xt:Commerce').'<br>&nbsp;&nbsp;&nbsp;'.$languages_string ; ?></td>
    <td valign="bottom" align="left" width="100%"><table border="0" cellspacing="0" cellpadding="2">      
      <tr>
        <td class="fastmenu" align="center"><a href="orders.php"><img src="images/icons/fastnav/icon_orders.jpg" alt="<?php echo (BOX_ORDERS) ; ?>" width="40" height="40" border="0"></a><br>
		<?php echo (BOX_ORDERS) ; ?>
		</td>
         <!--td class="fastmenu" align="center"><a href="orders_status.php"><img src="images/icons/fastnav/icon_order_status.jpg" alt="<?php echo (BOX_ORDERS_STATUS) ; ?>" width="40" height="40" border="0"></a><br>
		<?php //echo (BOX_ORDERS_STATUS) ; ?>
		</td>
         <td class="fastmenu" align="center"><a href="shipping_status.php"><img src="images/icons/fastnav/icon_shipping_status.jpg" alt="<?php echo (BOX_SHIPPING_STATUS) ; ?>" width="40" height="40" border="0"></a><br>
		<?php //echo (BOX_SHIPPING_STATUS) ; ?>
		</td-->
         <td class="fastmenu" align="center"><a href="content_manager.php"><img src="images/icons/fastnav/icon_content.jpg" alt="<?php echo (BOX_CONTENT) ; ?>" width="40" height="40" border="0"></a><br>
		<?php echo (BOX_CONTENT) ; ?>
		</td>
         <td class="fastmenu" align="center"><a href="backup.php"><img src="images/icons/fastnav/icon_backup.jpg" alt="<?php echo (BOX_BACKUP) ; ?>" width="40" height="40" border="0"></a><br>
		<?php echo (BOX_BACKUP) ; ?>
		</td>
         <td class="fastmenu" align="center"><a href="customers.php"><img src="images/icons/fastnav/icon_customers.jpg" alt="<?php echo (BOX_CUSTOMERS) ; ?>" width="40" height="40" border="0"></a><br>
		<?php echo (BOX_CUSTOMERS) ; ?>
		</td>
         <td class="fastmenu" align="center"><a href="categories.php"><img src="images/icons/fastnav/icon_categories.jpg" alt="<?php echo (BOX_CATEGORIES) ; ?>" width="40" height="40" border="0"></a><br>
		<?php echo (BOX_CATEGORIES) ; ?>
		</td>
         <td class="fastmenu" align="center"><a href="../index.php" target="_blank"><img src="images/icons/fastnav/icon_shop.jpg" width="40" height="40" border="0"></a><br>
		Shop
		</td>
        <td class="fastmenu" align="center"><a href="../logoff.php"><img src="images/icons/fastnav/icon_logout.jpg" width="40" height="40" border="0"></a><br>
		Logout
		</td>
        <td class="fastmenu" align="center"><a href="credits.php"><img src="images/icons/fastnav/icon_credits.jpg" width="40" height="40" border="0"></a><br>
		Credits
		</td>      
    </table>
    </td>
  </tr>
</table>
</div> 

<div id="top2"></div>

<?php
// BOF - vr - 2009-12-11 - JavaScript WEICHE  - Admin Umschaltauswahl
if ($admin_top_menu['configuration_value'] != 'false') 
  include(DIR_WS_INCLUDES . 'column_left.php');
// EOF - vr - 2009-12-11 - JavaScript WEICHE  - Admin Umschaltauswahl

?>