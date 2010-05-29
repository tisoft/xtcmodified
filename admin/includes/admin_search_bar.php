
<!-- #### Admin Search Bar (ASB) #### -->
<?php
$page = $_SERVER['PHP_SELF'];
if (strpos($page, 'customers.php') !== false) {
	$search_cus= $_GET['search'];
	$search_email= $_GET['search_email'];
}
if (strpos($page, 'orders.php') !== false) $search_ord= $_GET['oID'];
if (strpos($page, 'categories.php') !== false) $search_cat= $_GET['search'];
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
<!-- #### Ende #### -->

