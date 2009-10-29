<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_show_category.inc.php 1262 2005-09-30 10:00:32Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.23 2002/11/12); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_show_category.inc.php,v 1.4 2003/08/13); www.nextcommerce.org 

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function xtc_show_category($counter) {
	global $foo, $categories_string, $id;

    // image for first level
    //$img_1='<img src="templates/'.CURRENT_TEMPLATE.'/img/icon_arrow.jpg" alt="" />&nbsp;';

	if ($foo[$counter]['level']=='') {
		if (strlen($categories_string)=='0') {
			$categories_string .='';
		} else {
			//$categories_string .= '<li class="submenuspacer"></li>';
			$categories_string .= '';
			$categories_string .='';
		}
		if (trim($foo[$counter]['name']) != '' ) $categories_string .= '<li class="level1"><a href="';
	} else {
		if (trim($foo[$counter]['name']) != '' ) $categories_string .= '<li class="level'.($foo[$counter]['level']+1).'"><a  href="';
	}

	$cPath_new=xtc_category_link($counter,$foo[$counter]['name']);
    
	if (trim($foo[$counter]['name']) != '' ) {
		$categories_string .= xtc_href_link(FILENAME_DEFAULT, $cPath_new);
		$categories_string .= '">';
    }
	
	/* Anzeigen der Kategoriebezeichnung
	if ($foo[$counter]['level']=='1') {
		$categories_string .= '';
	}*/
	
	$categories_string .= $foo[$counter]['name'];

	if ( ($id) && (in_array($counter, $id)) ) {
			//$categories_string .= '</b>';
	}

	if ($foo[$counter]['level']=='') {
		if (trim($foo[$counter]['name']) != '' ) $categories_string .= '</a></li>';
	} else {
		if (trim($foo[$counter]['name']) != '' ) $categories_string .= '</a></li>';
		if ($foo[$counter]['level']=='1') {
			$categories_string .='';
		}
	}

	if (SHOW_COUNTS == 'true') {
		$products_in_category = xtc_count_products_in_category($counter);
		if ($products_in_category > 0) {
			if (trim($foo[$counter]['name']) != '' ) $categories_string .= '(' . $products_in_category . ')';
		}
	}

	if ($foo[$counter]['next_id']) {
		xtc_show_category($foo[$counter]['next_id']);
	} else {
		$categories_string .= '';
	}
}
?>
