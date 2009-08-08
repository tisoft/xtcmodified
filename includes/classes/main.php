<?php
/* -----------------------------------------------------------------------------------------
   $Id: main.php 1286 2005-10-07 10:10:18Z mz $ 

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2005 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(Coding Standards); www.oscommerce.com 

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
 
class main {
 	
 	function main () {
 		$this->SHIPPING = array();
 		 		
 		// prefetch shipping status
		$status_query=xtDBquery("SELECT shipping_status_name,
                                    shipping_status_image,
                                    shipping_status_id
                             FROM ".TABLE_SHIPPING_STATUS."
                             WHERE language_id = '".(int)$_SESSION['languages_id']."'");
         
     while ($status_data=xtc_db_fetch_array($status_query,true)) {
         	$this->SHIPPING[$status_data['shipping_status_id']]=array('name'=>$status_data['shipping_status_name'],'image'=>$status_data['shipping_status_image']);
     }
         
 	}
 	
 	function getShippingStatusName($id) {
 		return $this->SHIPPING[$id]['name'];
 	}
 	
 	function getShippingStatusImage($id) {
 		if ($this->SHIPPING[$id]['image']) {
 		return 'admin/images/icons/'.$this->SHIPPING[$id]['image'];
 		} else {
 			return;
 		}
 	}
 	
 	function getShippingLink() {
 	  //BOF - DokuMan - 2009-08-09 - fixed wrong quotationmark position and fixed wrong question mark on KeepThis=true
 	  //return ' '.SHIPPING_EXCL.' <a target="blank" href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS.'?KeepThis=true&TB_iframe=true&height=400&width=600"').' title="Information" class="thickbox">'.SHIPPING_COSTS.'</a>';
	return ' '.SHIPPING_EXCL.' <a target="_blank" href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.SHIPPING_INFOS.'&KeepThis=true&TB_iframe=true&height=400&width=600').'" title="Information" class="thickbox">'.SHIPPING_COSTS.'</a>';
	  //EOF - DokuMan - 2009-05-31 - fixed wrong quotationmark position and fixed wrong question mark on KeepThis=true
	}

	function getTaxNotice() {

		// no prices
		if ($_SESSION['customers_status']['customers_status_show_price'] == 0)
			return;

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
			return TAX_INFO_INCL_GLOBAL;
		}
		// excl tax + tax at checkout
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			return TAX_INFO_ADD_GLOBAL;
		}
		// excl tax
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
			return TAX_INFO_EXCL_GLOBAL;
		}
		
		return;
	}
	
	function getTaxInfo($tax_rate) {
		
        // price incl tax
				if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
					$tax_info = sprintf(TAX_INFO_INCL, $tax_rate.' %');
				}
				// excl tax + tax at checkout
				if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
					$tax_info = sprintf(TAX_INFO_ADD, $tax_rate.' %');
				}
				// excl tax
				if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
					$tax_info = sprintf(TAX_INFO_EXCL, $tax_rate.' %');
				}
		return $tax_info;
	}
	
	function getShippingNotice() {
		if (SHOW_SHIPPING == 'true') {
			return ' '.SHIPPING_EXCL.'<a href="'.xtc_href_link(FILENAME_CONTENT, 'coID='.SHIPPING_INFOS).'">'.SHIPPING_COSTS.'</a>';
		}
		return;
	}
	
   function getContentLink($coID,$text,$ssl='') {
 	  //BOF - Hetfield - 2009-07-29 - SSL for Content-Links per getContentLink and fixed wrong question mark on KeepThis=true	  
		if ($ssl != 'SSL' ) { $ssl = 'NONSSL'; }
		return '<a target="_blank" href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.$coID.'&KeepThis=true&TB_iframe=true&height=400&width=600"', $ssl).' title="Information" class="thickbox"><font color="#ff0000">'.$text.'</font></a>';
		//EOF - Hetfield - 2009-07-29 - SSL for Content-Links per getContentLink and fixed wrong question mark on KeepThis=true	  
	 }

}
?>