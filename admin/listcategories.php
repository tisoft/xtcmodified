<?php
   /* -----------------------------------------------------------------------------------------
   $Id: listcategories.php 1313 2005-10-18 15:49:15Z mz $

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
   (c) 2002-2003 osCommerce (validcategories.php,v 0.01 2002/08/17); www.oscommerce.com
   (c) 2003 XT-Commerce (listcategories.php 1313 2005-10-18); www.xt-commerce.com

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


?>
<html>
<head>
<title><?php echo TEXT_VALID_CATEGORIES_LIST;?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body>
<table width="550" border="0" cellspacing="1">
<tr>
<td class="pageHeading" colspan="2">
<?php echo TEXT_VALID_CATEGORIES_LIST;?>
</td>
</tr>
<?php
   $coupon_get=xtc_db_query("select restrict_to_categories from " . TABLE_COUPONS . " where coupon_id='".$_GET['cid']."'");
   $get_result=xtc_db_fetch_array($coupon_get);
   
   echo "<tr>
		  <th class=\"dataTableHeadingContent\">".TEXT_VALID_CATEGORIES_ID."</th>
		  <th class=\"dataTableHeadingContent\">".TEXT_VALID_CATEGORIES_NAME."</th>
		</tr>";
		
   $cat_ids = explode(",", $get_result['restrict_to_categories']); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3
   
   for ($i = 0; $i < count($cat_ids); $i++) {
     
	 $result = xtc_db_query("SELECT * FROM ".TABLE_CATEGORIES." c, 
										   ".TABLE_CATEGORIES_DESCRIPTION." cd 
									 WHERE c.categories_id = cd.categories_id 
									   and cd.language_id = '" . $_SESSION['languages_id'] . "' 
									   and c.categories_id='" . $cat_ids[$i] . "'");
     
	 if ($row = xtc_db_fetch_array($result)) {
       echo "<tr><td class=\"dataTableHeadingContent\">".$row["categories_id"]."</td>\n";
       echo "<td class=\"dataTableHeadingContent\">".$row["categories_name"]."</td>\n";
       echo "</tr>\n";
     } 
   }   
?>
</table>
<br />
<table width="550" border="0" cellspacing="1">
<tr>
<td align=middle><input type="button" value="<?php echo BUTTON_CLOSE_WINDOW;?>" onclick="window.close()"></td>
</tr></table>
</body>
</html>