<?php
/* --------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   --------------------------------------------------------------
   based on:
   Admin Search Bar (ASB)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

$page = basename($_SERVER['SCRIPT_FILENAME']);
$search_cus = '';
$search_email = '';
$search_ord = '';
$search_cat = '';
if (strpos($page, 'customers.php') !== false) {
	//$search_cus= $_GET['search'];
	$search_cus = isset($_GET['search']) ? $_GET['search'] : ''; //DokuMan - 2010-09-08 - set undefined index
	//$search_email= $_GET['search_email'];
	$search_email = isset($_GET['search_email']) ? $_GET['search_email'] : ''; //DokuMan - 2010-09-08 - set undefined index
}
if (strpos($page, 'orders.php') !== false) {
  //$search_ord= $_GET['oID'];
  $search_ord = isset($_GET['oID']) ? $_GET['oID'] : ''; //DokuMan - 2010-09-08 - set undefined index
}
if (strpos($page, 'categories.php') !== false){
  //$search_cat= $_GET['search'];
  $search_cat = isset($_GET['search']) ? $_GET['search'] : ''; //DokuMan - 2010-09-08 - set undefined index
}
?>
<link href="includes/searchbar_menu/searchbar_menu.css" rel="stylesheet" type="text/css" />
  <div class="searchbar">
  <ul id="topmenu_search">
  <li><form action="<?php echo xtc_href_link('customers.php'); ?>" method="get"><?php echo ASB_QUICK_SEARCH_CUSTOMER; ?><input name="search" type="text" value="<?php echo $search_cus;?>" size="15" />
  <input name="asb" type="hidden" value="asb" />
  </form></li>
<li><form action="<?php echo xtc_href_link('customers.php'); ?>" method="get"><?php echo ASB_QUICK_SEARCH_EMAIL; ?><input name="search_email" type="text" value="<?php echo $search_email;?>" size="15" />
  </form></li>
  <li><form action="<?php echo xtc_href_link('orders.php'); ?>" method="get"><?php echo ASB_QUICK_SEARCH_ORDER_ID; ?><input name="oID" type="text" value="<?php echo $search_ord;?>" size="7" />
  <input type="hidden" name="action" value="search" /></form></li>
  <li><form action="<?php echo xtc_href_link('categories.php'); ?>" method="get"><?php echo ASB_QUICK_SEARCH_ARTICLE; ?><input name="search" type="text" value="<?php echo $search_cat;?>" size="15" />
  </form></li>
  </ul>
  </div>