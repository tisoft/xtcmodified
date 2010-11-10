<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_show_category.inc.php,v 1.5 2008/04/11 15:30:00 Hetfield $
   
   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.23 2002/11/12); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_show_category.inc.php,v 1.4 2003/08/13); www.nextcommerce.org 
   (c) 2003  XT-Commerce (xtc_show_category.inc.php 1262 2005-09-30); www.xt-commerce.com
   (c) 2010  web28 (xtc_show_category.inc.php, v 2.0 2010/11/11); www.rpa-com.de

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/   

function xtc_show_category($counter, $oldlevel=1) {
	global $foo, $categories_string, $id, $cPath;	
	
	//BOF +++ UL LI Verschachtelung  mit Quelltext Tab Einz¸gen +++
		$newlevel = $foo[$counter]['level']+1;
		$ul = $tab_ul = $tab_li = '';	
		for ($i = 1; $i <= $newlevel; $i++) {
			$tab_ul .= "\t";
		}
		$tab_li = $tab_ul;
		
		if ($newlevel > $oldlevel) { //neue Unterebene
			$ul = "\n" . $tab_ul. '<ul>'. "\n";
			$categories_string = rtrim($categories_string, "\n"); //Zeilenumbruch entfernen
			$categories_string = substr($categories_string, 0, strlen($categories_string) -5);  //letztes  </li>  entfernen	
		} 
		elseif ($newlevel < $oldlevel) { //zur¸ck zur hˆheren Ebene
			$ul = close_ul_tags($newlevel,$oldlevel);			
		}
		$oldlevel = $newlevel;
	//EOF +++ UL LI Verschachtelung  mit Quelltext Tab Einz¸gen +++
	
	//BOF +++ Kategorien markieren +++
		$category_path = explode('_',$cPath); //Kategoriepfad in Array einlesen
		
		//Elternkategorie markieren
		$cat_active_parent = '';
		$in_path = in_array($counter, $category_path); //Testen, ob aktuelle Kategorie ID im Kategoriepfad enthalten ist
		if ($in_path) $cat_active_parent = " activeparent".($foo[$counter]['level']+1); 
		
		//Aktive Kategorie markieren
		$cat_active = '';
		$this_category = array_pop($category_path); //Letzter Eintrag im Array ist die aktuelle Kategorie
		if ($this_category == $counter) $cat_active = " active".($foo[$counter]['level']+1);
	//EOF +++ Kategorien markieren +++
	
	
	//BOF +++ Kategorie Linkerstellung +++	
		$cPath_new=xtc_category_link($counter,$foo[$counter]['name']);
		
		if (trim($categories_string == '')) $categories_string = "\n"; //Zeilenschaltung Codedarstellung	
		
		$categories_string .= $ul; //UL LI Versschachtelung
		$categories_string .= $tab_li; //Tabulator Codedarstellung
		$categories_string .= '<li class="level'.($foo[$counter]['level']+1).$cat_active.$cat_active_parent.'">';
		$categories_string .= '<a href="'.xtc_href_link(FILENAME_DEFAULT, $cPath_new).'" title="'. $foo[$counter]['name'] . '">';
		$categories_string .= $foo[$counter]['name'];
		
		//Anzeige Anzahl der Produkte in Kategorie, f¸r bessere Performance im Admin deaktivieren
		if (SHOW_COUNTS == 'true') {
			$products_in_category = xtc_count_products_in_category($counter);
			if ($products_in_category > 0) {
				$categories_string .= '&nbsp;(' . $products_in_category . ')';
			}
		}	

		$categories_string .= '</a></li>';
		$categories_string .= "\n"; //Zeilenschaltung Codedarstellung	
	//EOF  +++ Kategorie Linkerstellung +++

	//N‰chste Kategorie
	if ($foo[$counter]['next_id']) {
		xtc_show_category($foo[$counter]['next_id'], $oldlevel);
	} else {	
		if ($oldlevel > 1) $categories_string .= close_ul_tags(1,$oldlevel);
		return;
	}
}

//Alle offenen UL LI Tags schlieﬂen
function close_ul_tags($newlevel, $oldlevel) {
	$count = 1;
	$ul = '';
	while($count <= $oldlevel - $newlevel) { //f¸r jede Ebene die UL LI Tags schlieﬂen
		$tab_end = '';
		for ($i = 1; $i <= $oldlevel - $count; $i++) {
			$tab_end .= "\t";
		}			
		$ul .=  $tab_end . "\t". '</ul>'. "\n". $tab_end . '</li>'. "\n";			
		$count++;
	}
	return $ul;
}
?>