<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_show_category.inc.php,v 1.5 2008/04/11 15:30:00 Hetfield $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.23 2002/11/12); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_show_category.inc.php,v 1.4 2003/08/13); www.nextcommerce.org 
   (c) 2008 Hetfield (xtc_show_category.inc.php,v 1.5 2008/04/11); www.merz-it-service.de

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function xtc_show_category($counter,$oldlevel='1',$last_next_id = '',$ul_open = false) {
    global $foo, $categories_string, $id;
		
	$category_path = explode('_',$GLOBALS['cPath']); 
	$in_path = in_array($foo[$counter]['path'], $category_path);
	$this_category = array_pop($category_path);
	$einzug = '';
	
	for ($a=0; $a<$foo[$counter]['level']; $a++) {
		$einzug .= "\t";
	}
 	$categories_string .= '';
 	if ( ($id) && (in_array($counter, $id)) ) {
      	$submenu = " childs";
    } else {
		$submenu = "";
	}
	if ($in_path) { 
		$active = " activeparent";
		$active2 = " class=\"actparentlink\"";
	} else {
		$active = "";
		$active2 = "";
	}
	if ($this_category == $counter) {
		$current = " active";
		$current2 = " class=\"actlink\"";
	} else {
		$current = "";
		$current2 = "";
	}
	if ($oldlevel > $foo[$counter]['level'] and $last_next_id != '') {
		$categories_string .= "</li>"."\n\t".$einzug."</ul>"."\n\t".$einzug."</li>\n".$einzug;
	} else if ($ul_open == true) {
		$categories_string .= "\n".$einzug;
	} else if ($last_next_id != '') {
		$categories_string .= "</li>\n".$einzug;
	}
	if ($einzug != '') {
		$einzug2 = $einzug;
	} else {
		$einzug2 = "\t";
	}
    $categories_string .= $einzug2.'<li class="level'.($foo[$counter]['level']+1).$active.$current.$submenu.'">';
	$categories_string .= "<a".$current2.$active2." href=\"".xtc_href_link(FILENAME_DEFAULT, xtc_category_link($counter,$foo[$counter]['name']))."\" title=\"".$foo[$counter]['name']."\">";
    $categories_string .= $foo[$counter]['name']."</a>";    
	if (SHOW_COUNTS == 'true') {
      	$products_in_category = xtc_count_products_in_category($counter);
      	if ($products_in_category > 0) {
        	$categories_string .= "&nbsp;<em>(" . $products_in_category . ")</em>";
      	}
    }	
	if ($counter == get_parent($foo[$counter]['next_id'])) {
		$categories_string .= "\n\t".$einzug."<ul>";
		$ul_open = true;
	} else {
		$ul_open = false;
	}
	$categories_string .= "";
    if ($foo[$counter]['next_id']) {
      	xtc_show_category($foo[$counter]['next_id'],$foo[$counter]['level'],$foo[$counter]['next_id'],$ul_open);
    }
}
function get_parent($categories_id) {
    $parent_query = "SELECT parent_id FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . $categories_id . "'";
    $parent_query  = xtDBquery($parent_query);
    $parent = xtc_db_fetch_array($parent_query);
	return $parent['parent_id'];
}
?>