<?php
/*
	This is the back-end PHP file for the osCommerce AJAX Search Suggest
	
	You may use this code in your own projects as long as this 
	copyright is left	in place.  All code is provided AS-IS.
	This code is distributed in the hope that it will be useful,
 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
	
	The complete tutorial on how this works can be found at:
	http://www.dynamicajax.com/fr/AJAX_Suggest_Tutorial-271_290_312.html
	
	For more AJAX code and tutorials visit http://www.DynamicAJAX.com
	For more osCommerce related tutorials and code examples visit http://www.osCommerce-SSL.com
	
	Copyright 2006 Ryan Smith / 345 Technical / 345 Group.	
	
	Auf XT-Commerce portiert von TechWay (Steffen Decker) mit Unterstützung von Purecut (aus dem ecombase.de Forum)
	Copyright 2006 @ TechWay, Steffen Decker
*/
	include('includes/application_top.php');

	//fsk18 lock
	if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
		$fsk_lock = " AND p.products_fsk18 != '1' ";
	} else {
		unset ($fsk_lock);
	}

	//group check
	if (GROUP_CHECK == 'true') {
		$group_check = " AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
	} else {
		unset ($group_check);
	}
	
	//wenn suchtext größer 3 Zeichen, dann suche das wort mittendrinn, sonst nur am anfang
	if (strlen($_GET['search'])>3) {
		// wenn nur products_name angezeigt werden soll
		$sql = "SELECT pd.products_name FROM ".TABLE_PRODUCTS." AS p LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." AS
                pd ON (p.products_id = pd.products_id) WHERE p.products_status = '1'
                AND products_name like('%" . xtc_db_input($_GET['search']) . "%')
                AND pd.language_id = '".(int) $_SESSION['languages_id']."' AND p.products_fsk18 != '1'        
                LIMIT 15";
		// wenn der categories_name auch angezeigt werden soll
		/*
		$sql = "SELECT pd.products_name, cd.categories_name
				FROM ((".TABLE_PRODUCTS." AS p LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." AS pd ON (p.products_id = pd.products_id))
				INNER JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." AS ptc ON (p.products_id = ptc.products_id)) 
				INNER JOIN ".TABLE_CATEGORIES_DESCRIPTION." AS cd ON (ptc.categories_id = cd.categories_id)
				WHERE p.products_status = '1'
				AND cd.language_id = '".(int) $_SESSION['languages_id']."'
				AND products_name like('%" . xtc_db_input($_GET['search']) . "%')
                AND pd.language_id = '".(int) $_SESSION['languages_id']."'".$fsk_lock.$group_check.
                " ORDER BY pd.products_name LIMIT 15";
		*/
	}
	else {
		// wenn nur products_name angezeigt werden soll
		$sql = "SELECT pd.products_name FROM ".TABLE_PRODUCTS." AS p LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." AS
                pd ON (p.products_id = pd.products_id) WHERE p.products_status = '1'
                AND products_name like('" . xtc_db_input($_GET['search']) . "%')
                AND pd.language_id = '".(int) $_SESSION['languages_id']."' AND p.products_fsk18 != '1'        
                LIMIT 15";
		// wenn der categories_name auch angezeigt werden soll
		/*
		$sql = "SELECT pd.products_name, cd.categories_name
				FROM ((".TABLE_PRODUCTS." AS p LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." AS pd ON (p.products_id = pd.products_id))
				INNER JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." AS ptc ON (p.products_id = ptc.products_id)) 
				INNER JOIN ".TABLE_CATEGORIES_DESCRIPTION." AS cd ON (ptc.categories_id = cd.categories_id)
				WHERE p.products_status = '1'
				AND cd.language_id = '".(int) $_SESSION['languages_id']."'
				AND products_name like('" . xtc_db_input($_GET['search']) . "%')
                AND pd.language_id = '".(int) $_SESSION['languages_id']."'".$fsk_lock.$group_check.
                " ORDER BY pd.products_name LIMIT 15";
		*/
	}
	if (strlen($_GET['search'])>0) {
		$product_query = xtc_db_query($sql);
		while($product_array = xtc_db_fetch_array($product_query)) {
			//Umlaute konvertieren
			$product_array['products_name'] = htmlentities($product_array['products_name']);
			$product_array['categories_name'] = htmlentities($product_array['categories_name']);			
			//Ausgabe MIT Kategorienamen!
			//echo '<div style="float:left;">'.$product_array['products_name'] . '</div><div style="text-align:right;">&nbsp;' . $product_array['categories_name'] . "</div>\n";
			//Ausgabe OHNE Kategorienamen!
			echo $product_array['products_name'] . "&nbsp;\n";
		}
	}
?>