<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com
   (c) 2003      nextcommerce (product_info.php,v 1.46 2003/08/25); www.nextcommerce.org
   (c) 2006 xt:Commerce (product_info.php 1317 2005-10-21); www.xt-commerce.de

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist
   New Attribute Manager v4b                            Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Cross-Sell (X-Sell) Admin 1                          Autor: Joshua Dechant (dreamscape)
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

//include needed functions
require_once (DIR_FS_INC.'xtc_check_categories_status.inc.php');
require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');
require_once (DIR_FS_INC.'get_cross_sell_name.inc.php');
// BOF  - DokuMan - 2010-08-13 - show expiry date of special products
require_once (DIR_FS_INC.'xtc_date_short.inc.php');  // Für Anzeige Sonderangebotsende (c) by Hetfield
// EOF  - DokuMan - 2010-08-13 - show expiry date of special products

$info_smarty = new Smarty;
//BOF - GTB - 2010-08-03 - Security Fix - Base
$info_smarty->assign('tpl_path',DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/');
//$info_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
//EOF - GTB - 2010-08-03 - Security Fix - Base
$group_check = '';

// BOF - Tomcraft - 2009-11-28 - Included xs:booster
// xs:booster start (v1.041)
$xsb_tx = array();
if(@is_array($_SESSION['xtb0']['tx'])) {
	foreach($_SESSION['xtb0']['tx'] as $tx) {
		if($tx['products_id']==$product->data['products_id']) {
			$xsb_tx = $tx;
			break;
		}
	}
}
// xs:booster end
// EOF - Tomcraft - 2009-11-28 - Included xs:booster

if (!is_object($product) || !$product->isProduct()) { // product not found in database
	$error = TEXT_PRODUCT_NOT_FOUND;
	include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);

} else {
	if (ACTIVATE_NAVIGATOR == 'true')
		include (DIR_WS_MODULES.'product_navigator.php');

	xtc_db_query("update ".TABLE_PRODUCTS_DESCRIPTION." set products_viewed = products_viewed+1 where products_id = '".$product->data['products_id']."' and language_id = '".$_SESSION['languages_id']."'");

// BOF - Tomcraft - 2011-02-24 - Get manufacturer name etc. for the product page
	$manufacturer_query = xtc_db_query("select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url from " . TABLE_MANUFACTURERS . " m left join " . TABLE_MANUFACTURERS_INFO . " mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$_SESSION['languages_id'] . "'), " . TABLE_PRODUCTS . " p where p.products_id = '" . $product->data['products_id'] . "' and p.manufacturers_id = m.manufacturers_id");
	if (xtc_db_num_rows($manufacturer_query)) {
		$manufacturer = xtc_db_fetch_array($manufacturer_query);
		$manufacturers_image='';
		if (xtc_not_null($manufacturer['manufacturers_image'])) $manufacturers_image=DIR_WS_IMAGES . $manufacturer['manufacturers_image'];
		$info_smarty->assign('MANUFACTURER_IMAGE',$manufacturers_image);
		$info_smarty->assign('MANUFACTURER',$manufacturer['manufacturers_name']);
		$info_smarty->assign('MANUFACTURER_LINK',xtc_href_link(FILENAME_DEFAULT, xtc_manufacturer_link($manufacturer['manufacturers_id'],$manufacturer['manufacturers_name'])));
	}
// EOF - Tomcraft - 2011-02-24 - Get manufacturer name etc. for the product page

		$products_price = $xtPrice->xtcGetPrice($product->data['products_id'], $format = true, 1, $product->data['products_tax_class_id'], $product->data['products_price'], 1);

		// check if customer is allowed to add to cart
		if ($_SESSION['customers_status']['customers_status_show_price'] != '0') {
			// fsk18
			if ($_SESSION['customers_status']['customers_fsk18'] == '1') {
				if ($product->data['products_fsk18'] == '0') {
          // BOF - Tomcraft - 2009-11-28 - Included xs:booster
          //$info_smarty->assign('ADD_QTY', xtc_draw_input_field('products_qty', '1', 'size="3"').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
          //BOF - DokuMan - 2010-09-18 - suppress undefined index message
          //if(@$xsb_tx['XTB_ALLOW_USER_CHQTY']=='true'||$xsb_tx['products_id']!=$product->data['products_id'])
          if(@$xsb_tx['XTB_ALLOW_USER_CHQTY']=='true'|| @$xsb_tx['products_id']!=$product->data['products_id'])
          //EOF - DokuMan - 2010-09-18 - suppress undefined index message
          $info_smarty->assign('ADD_QTY', xtc_draw_input_field('products_qty', '1', 'size="3"').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
        else
          $info_smarty->assign('ADD_QTY', xtc_draw_hidden_field('products_qty', '1').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
          // EOF - Tomcraft - 2009-11-28 - Included xs:booster
          $info_smarty->assign('ADD_CART_BUTTON', xtc_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART));
				}
			} else {
        // BOF - Tomcraft - 2009-11-28 - Included xs:booster
				//$info_smarty->assign('ADD_QTY', xtc_draw_input_field('products_qty', '1', 'size="3"').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
        //BOF - DokuMan - 2010-09-18 - suppress undefined index message
        //if(@$xsb_tx['XTB_ALLOW_USER_CHQTY']=='true'||$xsb_tx['products_id']!=$product->data['products_id'])
        if(@$xsb_tx['XTB_ALLOW_USER_CHQTY']=='true'|| @$xsb_tx['products_id']!=$product->data['products_id'])
        //EOF - DokuMan - 2010-09-18 - suppress undefined index message
				$info_smarty->assign('ADD_QTY', xtc_draw_input_field('products_qty', '1', 'size="3"').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
			else
				$info_smarty->assign('ADD_QTY', xtc_draw_hidden_field('products_qty', '1').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
        // EOF - Tomcraft - 2009-11-28 - Included xs:booster
				$info_smarty->assign('ADD_CART_BUTTON', xtc_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART));
			}
		}

    // BOF  - DokuMan - 2010-08-13 - show expiry date of special products
    // Anzeige des Ablaufdatums für Sonderangebote -- Anfang -- (c) by Hetfield
    $special_expires_date_query = "SELECT expires_date FROM ".TABLE_SPECIALS." WHERE products_id = '".$product->data['products_id']."'";
    $special_expires_date_query = xtDBquery($special_expires_date_query);
    $sDate = xtc_db_fetch_array($special_expires_date_query, true);
    if ($sDate['expires_date'] != '0000-00-00 00:00:00') {
       $info_smarty->assign('PRODUCTS_EXPIRES', xtc_date_short($sDate['expires_date']));
    }
    // Anzeige des Ablaufdatums für Sonderangebote -- Ende -- (c) by Hetfield
    // EOF  - DokuMan - 2010-08-13 - show expiry date of special products

		if ($product->data['products_fsk18'] == '1') {
			$info_smarty->assign('PRODUCTS_FSK18', 'true');
		}
		if (ACTIVATE_SHIPPING_STATUS == 'true') {
			$info_smarty->assign('SHIPPING_NAME', $main->getShippingStatusName($product->data['products_shippingtime']));
			$info_smarty->assign('SHIPPING_IMAGE', $main->getShippingStatusImage($product->data['products_shippingtime']));
		}
		$info_smarty->assign('FORM_ACTION', xtc_draw_form('cart_quantity', xtc_href_link(FILENAME_PRODUCT_INFO, xtc_get_all_get_params(array ('action')).'action=add_product')));
		$info_smarty->assign('FORM_END', '</form>');
		$info_smarty->assign('PRODUCTS_PRICE', $products_price['formated']);
		if ($product->data['products_vpe_status'] == 1 && $product->data['products_vpe_value'] != 0.0 && $products_price['plain'] > 0)
			$info_smarty->assign('PRODUCTS_VPE', $xtPrice->xtcFormat($products_price['plain'] * (1 / $product->data['products_vpe_value']), true).TXT_PER.xtc_get_vpe_name($product->data['products_vpe']));
		$info_smarty->assign('PRODUCTS_ID', $product->data['products_id']);
		$info_smarty->assign('PRODUCTS_NAME', $product->data['products_name']);
		//BOF - DokuMan - 2010-08-24 - set Undefined index: 0
		//if ($_SESSION['customers_status']['customers_status_show_price'] != 0) {
		if ($_SESSION['customers_status']['customers_status_show_price'] != 0 && isset($xtPrice->TAX[$product->data['products_tax_class_id']])) {
		//EOF - DokuMan - 2010-08-24 - set Undefined index: 0

			// price incl tax
			$tax_rate = $xtPrice->TAX[$product->data['products_tax_class_id']];
			$tax_info = $main->getTaxInfo($tax_rate);
			$info_smarty->assign('PRODUCTS_TAX_INFO', $tax_info);
			$info_smarty->assign('PRODUCTS_SHIPPING_LINK',$main->getShippingLink());
		}
		$info_smarty->assign('PRODUCTS_MODEL', $product->data['products_model']);
		$info_smarty->assign('PRODUCTS_EAN', $product->data['products_ean']);
		$info_smarty->assign('PRODUCTS_QUANTITY', $product->data['products_quantity']);
		$info_smarty->assign('PRODUCTS_WEIGHT', $product->data['products_weight']);
		$info_smarty->assign('PRODUCTS_STATUS', $product->data['products_status']);
		$info_smarty->assign('PRODUCTS_ORDERED', $product->data['products_ordered']);
    //BOF - Tomcraft - 2010-04-03 - unified popups with scrollbars and make them resizable
		//$info_smarty->assign('PRODUCTS_PRINT', '<img src="templates/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/print.gif"  style="cursor:pointer" onclick="javascript:window.open(\''.xtc_href_link(FILENAME_PRINT_PRODUCT_INFO, 'products_id='.$product->data['products_id']).'\', \'popup\', \'toolbar=0, width=640, height=600\')" alt="" />');
		//BOF - GTB - 2010-08-03 - Security Fix - Base
    //BOF - DokuMan - 2010-09-18 - used xtc_image_button for correct image dimensions
		//$info_smarty->assign('PRODUCTS_PRINT', '<img src="'.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/print.gif"  style="cursor:pointer" onclick="javascript:window.open(\''.xtc_href_link(FILENAME_PRINT_PRODUCT_INFO, 'products_id='.$product->data['products_id']).'\', \'popup\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width=640, height=600\')" alt="" />');
		$info_smarty->assign('PRODUCTS_PRINT', xtc_image_button('print.gif', $product->data['products_name'], 'style="cursor:pointer" onclick="javascript:window.open(\''.xtc_href_link(FILENAME_PRINT_PRODUCT_INFO, 'products_id='.$product->data['products_id']).'\', \'popup\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width=640, height=600\')"'));
    //EOF - DokuMan - 2010-09-18 - used xtc_image_button for correct image dimensions
		//$info_smarty->assign('PRODUCTS_PRINT', '<img src="templates/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/print.gif"  style="cursor:pointer" onclick="javascript:window.open(\''.xtc_href_link(FILENAME_PRINT_PRODUCT_INFO, 'products_id='.$product->data['products_id']).'\', \'popup\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width=640, height=600\')" alt="" />');
		//BOF - GTB - 2010-08-03 - Security Fix - Base
    //EOF - Tomcraft - 2010-04-03 - unified popups with scrollbars and make them resizable
		$info_smarty->assign('PRODUCTS_DESCRIPTION', stripslashes($product->data['products_description']));
    // BOF - Tomcraft - 2011-02-24 - PRODUCTS_SHORT_DESCRIPTION for the product page		
    $info_smarty->assign('PRODUCTS_SHORT_DESCRIPTION', stripslashes($product->data['products_short_description']));
    // EOF - Tomcraft - 2011-02-24 - PRODUCTS_SHORT_DESCRIPTION for the product page
    // BOF - Tomcraft - 2009-11-28 - Included xs:booster
		if(isset($xsb_tx['XTB_REDIRECT_USER_TO'])&&$xsb_tx['products_id']==$product->data['products_id'])
			$info_smarty->assign('XTB_REDIRECT_USER_TO', $xsb_tx['XTB_REDIRECT_USER_TO']);
    // EOF - Tomcraft - 2009-11-28 - Included xs:booster
		$image = '';
    // BOF - Tomcraft - 2009-10-30 - use allready defined function from product.php
    /*
		if ($product->data['products_image'] != '')
			$image = DIR_WS_INFO_IMAGES.$product->data['products_image'];
    */
		$image = $product->productImage($product->data['products_image'], 'info');
    // EOF - Tomcraft - 2009-10-30 - use allready defined function from product.php

		$info_smarty->assign('PRODUCTS_IMAGE', $image);

//-- SEO ShopStat
/*
		//mo_images - by Novalis@eXanto.de
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
			$connector = '/';
		}else{
			$connector = '&';
		}
*/
    $connector = '&amp;';
//-- SEO ShopStat

		$info_smarty->assign('PRODUCTS_POPUP_LINK', 'javascript:popupWindow(\''.xtc_href_link(FILENAME_POPUP_IMAGE, 'pID='.$product->data['products_id'].$connector.'imgID=0').'\')');
		$mo_images = xtc_get_products_mo_images($product->data['products_id']);
		if ($mo_images != false) {
      // BOF - Tomcraft - 2009-09-12 - build more_images array
      /*
			foreach ($mo_images as $img) {
				$mo_img = DIR_WS_INFO_IMAGES.$img['image_name'];
				$info_smarty->assign('PRODUCTS_IMAGE_'.$img['image_nr'], $mo_img);
				$info_smarty->assign('PRODUCTS_POPUP_LINK_'.$img['image_nr'], 'javascript:popupWindow(\''.xtc_href_link(FILENAME_POPUP_IMAGE, 'pID='.$product->data['products_id'].$connector.'imgID='.$img['image_nr']).'\')');
			}
      */
			$more_images_data = array();
			foreach ($mo_images as $img) {
				$more_images_data[] = array ( // BOF - web28 - 2010-11-27 - use allready defined function from product.php
											  //'PRODUCTS_IMAGE' => DIR_WS_INFO_IMAGES.$img['image_name'],
											  'PRODUCTS_IMAGE' => $product->productImage($img['image_name'], 'info'),           
											  // BOF - web28 - 2010-11-27 - use allready defined function from product.php
											  'PRODUCTS_POPUP_LINK' => 'javascript:popupWindow(\''.xtc_href_link(FILENAME_POPUP_IMAGE, 'pID='.$product->data['products_id'].$connector.'imgID='.$img['image_nr']).'\')'
											  );
				// BOF - Tomcraft - 2009-09-12 - needed for non modified templates
				// BOF - web28 - 2010-11-27 - use allready defined function from product.php
				//$mo_img = DIR_WS_INFO_IMAGES.$img['image_name'];
				$mo_img = $product->productImage($img['image_name'], 'info');				
				//EOF - web28 - 2010-11-27 - use allready defined function from product.php
				$info_smarty->assign('PRODUCTS_IMAGE_'.$img['image_nr'], $mo_img);
				$info_smarty->assign('PRODUCTS_POPUP_LINK_'.$img['image_nr'], 'javascript:popupWindow(\''.xtc_href_link(FILENAME_POPUP_IMAGE, 'pID='.$product->data['products_id'].$connector.'imgID='.$img['image_nr']).'\')');
				// EOF - Tomcraft - 2009-09-12 - needed for non modified templates
			}
			$info_smarty->assign('more_images', $more_images_data);
    // EOF - Tomcraft - 2009-09-12 - build more_images array
		}
		//mo_images EOF
		$discount = 0.00;
		if ($_SESSION['customers_status']['customers_status_public'] == 1 && $_SESSION['customers_status']['customers_status_discount'] != '0.00') {
			$discount = $_SESSION['customers_status']['customers_status_discount'];
			if ($product->data['products_discount_allowed'] < $_SESSION['customers_status']['customers_status_discount'])
				$discount = $product->data['products_discount_allowed'];
			if ($discount != '0.00')
				$info_smarty->assign('PRODUCTS_DISCOUNT', $discount.'%');
		}

		include (DIR_WS_MODULES.'product_attributes.php');
		include (DIR_WS_MODULES.'product_reviews.php');

		if (xtc_not_null($product->data['products_url']))
			$info_smarty->assign('PRODUCTS_URL', sprintf(TEXT_MORE_INFORMATION, xtc_href_link(FILENAME_REDIRECT, 'action=product&id='.$product->data['products_id'], 'NONSSL', true, false)));

		if ($product->data['products_date_available'] > date('Y-m-d H:i:s')) {
		// Tomcraft - 2009-10-08 - do not correct the name 'PRODUCTS_DATE_AVIABLE' to remain compatible to standard templates
			$info_smarty->assign('PRODUCTS_DATE_AVIABLE', sprintf(TEXT_DATE_AVAILABLE, xtc_date_long($product->data['products_date_available'])));

		} else {
			if ($product->data['products_date_added'] != '0000-00-00 00:00:00')
				$info_smarty->assign('PRODUCTS_ADDED', sprintf(TEXT_DATE_ADDED, xtc_date_long($product->data['products_date_added'])));
		}

		if ($_SESSION['customers_status']['customers_status_graduated_prices'] == 1)
			include (DIR_WS_MODULES.FILENAME_GRADUATED_PRICE);

		include (DIR_WS_MODULES.FILENAME_PRODUCTS_MEDIA);
		include (DIR_WS_MODULES.FILENAME_ALSO_PURCHASED_PRODUCTS);
		include (DIR_WS_MODULES.FILENAME_CROSS_SELLING);

	if ($product->data['product_template'] == '' or $product->data['product_template'] == 'default') {
		$files = array ();
		if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/')) {
			while ($file = readdir($dir)) {
        // BOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
				//if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
				if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'.$file) and (substr($file, -5) == ".html") and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
        // EOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
        // BOF - web28 - 2010-07-12 - sort templates array
					//$files[] = array ('id' => $file, 'text' => $file);
					$files[] = $file;
				} //if
			} // while
			closedir($dir);
		}
		sort($files);
		//$product->data['product_template'] = $files[0]['id'];
		$product->data['product_template'] = $files[0];
      //EOF - web28 - 2010-07-12 - sort templates array
	}

  //BOF - DokuMan - 2010-08-24 - set undefined index
  //$i = count($_SESSION['tracking']['products_history']);
  $i = isset($_SESSION['tracking']['products_history']) ? count($_SESSION['tracking']['products_history']) : 0;
  //EOF - DokuMan - 2010-08-24 - set undefined index
	if ($i > 6) {
		array_shift($_SESSION['tracking']['products_history']);
		$_SESSION['tracking']['products_history'][6] = $product->data['products_id'];
		$_SESSION['tracking']['products_history'] = array_unique($_SESSION['tracking']['products_history']);
	} else {
		$_SESSION['tracking']['products_history'][$i] = $product->data['products_id'];
		$_SESSION['tracking']['products_history'] = array_unique($_SESSION['tracking']['products_history']);
	}

	$info_smarty->assign('language', $_SESSION['language']);
	// set cache ID
	 if (!CacheCheck()) {
		$info_smarty->caching = 0;
		$product_info = $info_smarty->fetch(CURRENT_TEMPLATE.'/module/product_info/'.$product->data['product_template']);
	} else {
		$info_smarty->caching = 1;
		$info_smarty->cache_lifetime = CACHE_LIFETIME;
		$info_smarty->cache_modified_check = CACHE_CHECK;
		$cache_id = $product->data['products_id'].$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
		$product_info = $info_smarty->fetch(CURRENT_TEMPLATE.'/module/product_info/'.$product->data['product_template'], $cache_id);
	}
}
$smarty->assign('main_content', $product_info);
?>
