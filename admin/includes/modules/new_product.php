<?php

/* --------------------------------------------------------------
   $Id: new_product.php 897 2005-04-28 21:36:55Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.140 2003/03/24); www.oscommerce.com
   (c) 2003  nextcommerce (categories.php,v 1.37 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

if (($_GET['pID']) && (!$_POST)) {
	$product_query = xtc_db_query("select *, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available 
	                               from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
                                  where p.products_id = '".(int) $_GET['pID']."'
                                  and p.products_id = pd.products_id
                                  and pd.language_id = '".$_SESSION['languages_id']."'");

	$product = xtc_db_fetch_array($product_query);
	$pInfo = new objectInfo($product);

}
elseif ($_POST) {
	$pInfo = new objectInfo($_POST);
	$products_name = $_POST['products_name'];
	$products_description = $_POST['products_description'];
	$products_short_description = $_POST['products_short_description'];
	$products_keywords = $_POST['products_keywords'];
	$products_meta_title = $_POST['products_meta_title'];
	$products_meta_description = $_POST['products_meta_description'];
	$products_meta_keywords = $_POST['products_meta_keywords'];
	$products_url = $_POST['products_url'];
	$pInfo->products_startpage = $_POST['products_startpage'];
   $products_startpage_sort = $_POST['products_startpage_sort'];
} else {
	$pInfo = new objectInfo(array ());
}

$manufacturers_array = array (array ('id' => '', 'text' => TEXT_NONE));
$manufacturers_query = xtc_db_query("select manufacturers_id, manufacturers_name from ".TABLE_MANUFACTURERS." order by manufacturers_name");
while ($manufacturers = xtc_db_fetch_array($manufacturers_query)) {
	$manufacturers_array[] = array ('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
}

$vpe_array = array (array ('id' => '', 'text' => TEXT_NONE));
$vpe_query = xtc_db_query("select products_vpe_id, products_vpe_name from ".TABLE_PRODUCTS_VPE." WHERE language_id='".$_SESSION['languages_id']."' order by products_vpe_name");
while ($vpe = xtc_db_fetch_array($vpe_query)) {
	$vpe_array[] = array ('id' => $vpe['products_vpe_id'], 'text' => $vpe['products_vpe_name']);
}

$tax_class_array = array (array ('id' => '0', 'text' => TEXT_NONE));
$tax_class_query = xtc_db_query("select tax_class_id, tax_class_title from ".TABLE_TAX_CLASS." order by tax_class_title");
while ($tax_class = xtc_db_fetch_array($tax_class_query)) {
	$tax_class_array[] = array ('id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']);
}
$shipping_statuses = array ();
$shipping_statuses = xtc_get_shipping_status();
$languages = xtc_get_languages();

switch ($pInfo->products_status) {
	case '0' :
		$status = false;
		//$out_status = true;
		break;
	case '1' :
	default :
		$status = true;
		//$out_status = false;
}
$product_status_array = array(array('id'=>0,'text'=>TEXT_PRODUCT_NOT_AVAILABLE),array('id'=>1,'text'=>TEXT_PRODUCT_AVAILABLE));

//if ($pInfo->products_startpage == '1') { $startpage_checked = true; } else { $startpage_checked = false; }

?>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script type="text/javascript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script type="text/javascript">
// BOF - Tomcraft - 2009-11-06 - Replaced the blue Button with calendar icon
  //var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",2);
// BOF - Tomcraft - 2009-11-06 - Replaced the blue Button with calendar icon
</script>

<tr><td>
<?php $form_action = ($_GET['pID']) ? 'update_product' : 'insert_product'; ?>
<?php $fsk18_array=array(array('id'=>0,'text'=>NO),array('id'=>1,'text'=>YES)); ?>
<?php echo xtc_draw_form('new_product', FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID'] . '&action='.$form_action, 'post', 'enctype="multipart/form-data"'); ?>
<span class="pageHeading"><?php echo sprintf(TEXT_NEW_PRODUCT, xtc_output_generated_category_path($current_category_id)); ?></span><br />

<!-- BOF - Tomcraft - 2009-11-02 - Block1 //-->
<div style="width: 860px; padding:5px;">

<table bgcolor="f3f3f3" style="width: 100%; border: 1px solid; border-color: #aaaaaa; padding:5px;">
  <tr>
    <td><table "width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="58%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="3">
            <tr>
              <td width="260"><span class="main"><?php echo TEXT_PRODUCTS_STATUS; ?></span></td>
              <td><span class="main"><?php echo xtc_draw_pull_down_menu('products_status', $product_status_array, $status, 'style="width: 135px"'); ?></span></td>
            </tr>
            <tr>
<!-- BOF - Tomcraft - 2009-11-06 - Use variable TEXT_PRODUCTS_DATE_FORMAT //-->
<!--
              <td><span class="main"><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?><small>(JJJJ-MM-TT)</small></span></td>
//-->
              <td><span class="main"><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?> <small><?php echo TEXT_PRODUCTS_DATE_FORMAT; ?></small></span></td>
<!-- EOF - Tomcraft - 2009-11-06 - Use variable TEXT_PRODUCTS_DATE_FORMAT //-->
              <td><span class="main">
                <script type="text/javascript">dateAvailable.writeControl(); dateAvailable.dateFormat="yyyy-MM-dd";</script>
<!-- BOF - Tomcraft - 2009-11-06 - Modified Section for use without Javascript //-->
		<noscript>
                <?php echo  xtc_draw_input_field('products_date_available', $pInfo->products_date_available ,'style="width: 135px"'); ?>
                </noscript>
<!-- EOF - Tomcraft - 2009-11-06 - Modified Section for use without Javascript //-->
              </span></td>
            </tr>
            <tr>
              <td><span class="main"><?php echo TEXT_PRODUCTS_STARTPAGE; ?></span></td>
              <td><span class="main"><?php echo xtc_draw_selection_field('products_startpage', 'checkbox', '1',$pInfo->products_startpage==1 ? true : false); ?></span></td>
            </tr>
            <tr>
              <td><span class="main"><?php echo TEXT_PRODUCTS_STARTPAGE_SORT; ?></span></td>
              <td><span class="main"><?php echo  xtc_draw_input_field('products_startpage_sort', $pInfo->products_startpage_sort ,'style="width: 135px"'); ?></span></td>
            </tr>
            <tr>
              <td><span class="main"><?php echo TEXT_PRODUCTS_SORT; ?></span></td>
              <td><span class="main"><?php echo  xtc_draw_input_field('products_sort', $pInfo->products_sort,'style="width: 135px"'); ?></span></td>
            </tr>
            <tr>
              <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><span class="main"><?php echo TEXT_PRODUCTS_VPE_VISIBLE.xtc_draw_selection_field('products_vpe_status', 'checkbox', '1',$pInfo->products_vpe_status==1 ? true : false);?></span></td>
                    <td align="right"><span class="main"><?php echo TEXT_PRODUCTS_VPE_VALUE; ?></span></td>
                  </tr>
                </table></td>
              <td><span class="main"><?php echo xtc_draw_input_field('products_vpe_value', $pInfo->products_vpe_value,'style="width: 135px"'); ?></span></td>
            </tr>
            <tr>
              <td><span class="main"><?php echo TEXT_PRODUCTS_VPE ?></span></td>
              <td><span class="main"><?php echo xtc_draw_pull_down_menu('products_vpe', $vpe_array, $pInfo->products_vpe='' ?  DEFAULT_PRODUCTS_VPE_ID : $pInfo->products_vpe, 'style="width: 135px"'); ?></span></td>
            </tr>
            <tr>
              <td><span class="main"><?php echo TEXT_FSK18; ?></span></td>
              <td><span class="main"><?php echo xtc_draw_pull_down_menu('fsk18', $fsk18_array, $pInfo->products_fsk18, 'style="width: 135px"'); ?></span></td>
            </tr>
          </table>
        </td>
        <td width="4%"><?php echo xtc_draw_separator('pixel_trans.gif', '24', '15'); ?></td>
        <td width="38%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="3">
            <tr>
              <td><span class="main"><?php echo TEXT_PRODUCTS_QUANTITY; ?></span></td>
              <td><span class="main"><?php echo xtc_draw_input_field('products_quantity', $pInfo->products_quantity, 'style="width: 135px"'); ?></span></td>
            </tr>
            <tr>
              <td><span class="main"><?php echo TEXT_PRODUCTS_MODEL; ?></span></td>
              <td><span class="main"><?php echo  xtc_draw_input_field('products_model', $pInfo->products_model, 'style="width: 135px"'); ?></span></td>
            </tr>
            <tr>
              <td><span class="main"><?php echo TEXT_PRODUCTS_EAN; ?></span></td>
              <td><span class="main"><?php echo  xtc_draw_input_field('products_ean', $pInfo->products_ean, 'style="width: 135px"'); ?></span></td>
            </tr>
            <tr>
              <td><span class="main"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></span></td>
              <td><span class="main"><?php echo xtc_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id, 'style="width: 135px"'); ?></span></td>
            </tr>
            <tr>
              <td><span class="main"><?php echo TEXT_PRODUCTS_WEIGHT; ?></span></td>
              <td><span class="main"><?php echo xtc_draw_input_field('products_weight', $pInfo->products_weight, 'style="width: 135px"'); ?>&nbsp;<?php echo TEXT_PRODUCTS_WEIGHT_INFO; ?></span></td>
            </tr>
            <?php if (ACTIVATE_SHIPPING_STATUS=='true') { ?>
            <tr>
              <td><span class="main"><?php echo BOX_SHIPPING_STATUS.':'; ?></span></td>
              <td><span class="main"><?php echo xtc_draw_pull_down_menu('shipping_status', $shipping_statuses, $pInfo->products_shippingtime, 'style="width: 135px"'); ?></span></td>
            </tr>
            <?php } ?>
            <tr>
              <td><span class="main">&nbsp;</span></td>
              <td><span class="main">&nbsp;</span></td>
            </tr>
            <tr>
              <td><span class="main">&nbsp;</span></td>
              <td><span class="main">&nbsp;</span></td>
            </tr>
          </table>
        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>	<table width="500" border="0" cellpadding="3" cellspacing="0">
      <tr>
        <td width="260"><span class="main">&nbsp;</span></td>
        <td><span class="main">&nbsp;</span></td>
      </tr>
<?php
$files = array ();
if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/')) {
	while (($file = readdir($dir)) !== false) {
// BOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
		//if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'.$file) and ($file != "index.html")) {
		if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'.$file) and (substr($file, -5) == ".html") and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
// EOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
			$files[] = array ('id' => $file, 'text' => $file);
		} //if
	} // while
	closedir($dir);
}
$default_array = array ();
// set default value in dropdown!
if ($content['content_file'] == '') {
	$default_array[] = array ('id' => 'default', 'text' => TEXT_SELECT);
	$default_value = $pInfo->product_template;
	$files = array_merge($default_array, $files);
} else {
	$default_array[] = array ('id' => 'default', 'text' => TEXT_NO_FILE);
	$default_value = $pInfo->product_template;
	$files = array_merge($default_array, $files);
}
?>
      <tr>
        <td><span class="main"><?php echo TEXT_CHOOSE_INFO_TEMPLATE; ?>:</span></td>
        <td><span class="main"><?php echo xtc_draw_pull_down_menu('info_template', $files, $default_value, 'style="width: 220px"'); ?></span></td>
      </tr>
	  
	  <?php
$files = array ();
if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/')) {
	while (($file = readdir($dir)) !== false) {
// BOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
		//if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/'.$file) and ($file != "index.html")) {
		if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/'.$file) and (substr($file, -5) == ".html") and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
// EOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
			$files[] = array ('id' => $file, 'text' => $file);
		} //if
	} // while
	closedir($dir);
}
// set default value in dropdown!
$default_array = array ();
if ($content['content_file'] == '') {
	$default_array[] = array ('id' => 'default', 'text' => TEXT_SELECT);
	$default_value = $pInfo->options_template;
	$files = array_merge($default_array, $files);
} else {
	$default_array[] = array ('id' => 'default', 'text' => TEXT_NO_FILE);
	$default_value = $pInfo->options_template;
	$files = array_merge($default_array, $files);
}
?>
      <tr>
        <td><span class="main"><?php echo TEXT_CHOOSE_OPTIONS_TEMPLATE; ?>:</span></td>
        <td><span class="main"><?php echo xtc_draw_pull_down_menu('options_template', $files, $default_value, 'style="width: 220px"'); ?></span></td>
      </tr>
	  <tr>
        <td><span class="main">&nbsp;</span></td>
        <td><span class="main">&nbsp;</span></td>
      </tr>
	  
    </table></td>
  </tr>
</table>

  <!-- BOF - Tomcraft - 2009-11-06 - Included specials //-->
 <?php
 if (file_exists("includes/modules/categories_specials.php")) { 
	require_once("includes/modules/categories_specials.php");
	showSpecialsBox();
 }	
 ?>
  <!-- EOF - Tomcraft - 2009-11-06 - Included specials //-->

  <!-- BOF - Tomcraft - 2009-11-02 - TOP SAVE AND CANCEL BUTTON //-->
  <?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
  <!-- BOF - Tomcraft - 2009-11-06 - Included specials //-->
	  <?php if (file_exists("includes/modules/categories_specials.php")) { ?>
      <td class="main" align="left"><div id="butSpecial">&nbsp;</div></td>
	  <script language="JavaScript" type="text/JavaScript">
	    document.getElementById('butSpecial').innerHTML= '<a href="JavaScript:showSpecial()" class="button">Sonderangebot &raquo;</a>';
	  </script>
	  <?php } ?>
  <!-- EOF - Tomcraft - 2009-11-06 - Included specials //-->
      <td class="main" align="right">      	
      	<input type="submit" class="button" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')">
      	&nbsp;&nbsp;
      	<?php echo '<a class="button" href="' . xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID']) . '">' . BUTTON_CANCEL . '</a>'; ?>
  	 </td>
    </tr>
  </table>
  <!-- EOF - Tomcraft - 2009-11-02 - TOP SAVE AND CANCEL BUTTON //-->
</div> 
<!-- EOF - Tomcraft - 2009-11-02 - Block1 //--> 

<!-- BOF - Tomcraft - 2009-11-02 - Block2 //--> 
<div style="width: 860px; padding:5px;">  
  <?php // BOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
  
  <link rel="stylesheet" type="text/css" href="includes/lang_tabs_menu/lang_tabs_menu.css">
  <script type="text/javascript" src="includes/lang_tabs_menu/lang_tabs_menu.js"></script>
  <?php  
  $langtabs = '<div class="tablangmenu"><ul>';
  $csstabstyle = 'border: 1px solid #aaaaaa; padding: 5px; width: 850px; margin-top: -1px; margin-bottom: 10px; float: left;background: #F3F3F3;';
  $csstab = '<style type="text/css">' .  '#tab_lang_0' . '{display: block;' . $csstabstyle . '}';
  $csstab_nojs = '<style type="text/css">';
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $tabtmp = "\'tab_lang_$i\'," ; 
// BOF - Tomcraft - 2009-11-17 - changed path to show language-flag
//	$langtabs.= '<li onclick="showTab('. $tabtmp. $n.')" style="cursor: pointer;" id="tabselect_' . $i .'">' .xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/'. $languages[$i]['image'], $languages[$i]['name']) . ' ' . $languages[$i]['name'].  '</li>';
	$langtabs.= '<li onclick="showTab('. $tabtmp. $n.')" style="cursor: pointer;" id="tabselect_' . $i .'">' .xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/admin/images/'. $languages[$i]['image'], $languages[$i]['name']) . ' ' . $languages[$i]['name'].  '</li>';
// EOF - Tomcraft - 2009-11-17 - changed path to show language-flag
    if($i > 0) $csstab .= '#tab_lang_' . $i .'{display: none;' . $csstabstyle . '}';
    $csstab_nojs .= '#tab_lang_' . $i .'{display: block;' . $csstabstyle . '}';	
  }
  $csstab .= '</style>';
  $csstab_nojs .= '</style>';  
  $langtabs.= '</ul></div>';  
  //echo $csstab;
  //echo $langtabs;  
  ?>   
  <?php if (USE_ADMIN_LANG_TABS != 'false') { ?>  
  <script type="text/javascript">
    
	document.write('<?php echo ($csstab);?>');
    document.write('<?php echo ($langtabs);?>');
    //alert ("TEST");	
   
  </script>
  <?php } else echo ($csstab_nojs);?>
  <noscript>
    <?php echo ($csstab_nojs);?>
  </noscript>  
  <?php // EOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
  
  <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
  
  <?php // BOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
  <?php echo ('<div id="tab_lang_' . $i . '">');?>
  <?php // EOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
  
  <table width="100%" border="0">
  <tr>
  <td bgcolor="000000" height="10"></td>
  </tr>
  <tr>
<!-- BOF - Tomcraft - 2009-11-17 - changed path to show language-flag //-->
<!--
    <td bgcolor="#FFCC33" valign="top" class="main"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/'. $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<STRONG><?php echo TEXT_PRODUCTS_NAME; ?>&nbsp;</STRONG><?php echo xtc_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : xtc_get_products_name($pInfo->products_id, $languages[$i]['id'])),'size=60'); ?></td>
//-->
    <td bgcolor="#FFCC33" valign="top" class="main"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/admin/images/'. $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<STRONG><?php echo TEXT_PRODUCTS_NAME; ?>&nbsp;</STRONG><?php echo xtc_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : xtc_get_products_name($pInfo->products_id, $languages[$i]['id'])),'size=60'); ?></td>
<!-- EOF - Tomcraft - 2009-11-17 - changed path to show language-flag //-->
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_URL . '&nbsp;<small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; ?><?php echo xtc_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : xtc_get_products_url($pInfo->products_id, $languages[$i]['id'])),'size=60'); ?></td>
  </tr>
</table>

<!-- input boxes desc, meta etc -->
<table width="100%" border="0">
  <tr>
    <td class="main">
<!-- BOF - Tomcraft - 2009-11-17 - changed path to show language-flag //-->
<!--
        <STRONG><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/'. $languages[$i]['image'], $languages[$i]['name']) . ' ' . TEXT_PRODUCTS_DESCRIPTION; ?></STRONG><br />
//-->
        <STRONG><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/admin/images/'. $languages[$i]['image'], $languages[$i]['name']) . ' ' . TEXT_PRODUCTS_DESCRIPTION; ?></STRONG><br />
<!-- EOF - Tomcraft - 2009-11-17 - changed path to show language-flag //-->
        <?php echo xtc_draw_textarea_field('products_description_' . $languages[$i]['id'], 'soft', '103', '30', (($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : xtc_get_products_description($pInfo->products_id, $languages[$i]['id']))); ?>
    </td>
  </tr>
  <tr>
    <td class="main" valign="top">
    
    <table>
    <tr>
<!-- BOF - Tomcraft - 2009-11-17 - changed path to show language-flag //-->
<!--
     <td width="60%" valign="top" class="main">        <strong><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/'. $languages[$i]['image'], $languages[$i]['name']) . ' ' . TEXT_PRODUCTS_SHORT_DESCRIPTION; ?></strong><br />
//-->
     <td width="60%" valign="top" class="main">        <strong><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/admin/images/'. $languages[$i]['image'], $languages[$i]['name']) . ' ' . TEXT_PRODUCTS_SHORT_DESCRIPTION; ?></strong><br />
<!-- EOF - Tomcraft - 2009-11-17 - changed path to show language-flag //-->
       <?php echo xtc_draw_textarea_field('products_short_description_' . $languages[$i]['id'], 'soft', '103', '20', (($products_short_description[$languages[$i]['id']]) ? stripslashes($products_short_description[$languages[$i]['id']]) : xtc_get_products_short_description($pInfo->products_id, $languages[$i]['id']))); ?> </td>
     <td class="main" valign="top" style="padding: 15px;">
        <?php echo TEXT_PRODUCTS_KEYWORDS; ?><br />
        <?php echo xtc_draw_input_field('products_keywords[' . $languages[$i]['id'] . ']',(($products_keywords[$languages[$i]['id']]) ? stripslashes($products_keywords[$languages[$i]['id']]) : xtc_get_products_keywords($pInfo->products_id, $languages[$i]['id'])), 'size=25 maxlenght=255'); ?><br />     
        <?php echo TEXT_META_TITLE; ?><br />
        <?php echo xtc_draw_input_field('products_meta_title[' . $languages[$i]['id'] . ']',(($products_meta_title[$languages[$i]['id']]) ? stripslashes($products_meta_title[$languages[$i]['id']]) : xtc_get_products_meta_title($pInfo->products_id, $languages[$i]['id'])), 'size=25 maxlenght=50'); ?><br />
        <?php echo TEXT_META_DESCRIPTION; ?><br />
        <?php echo xtc_draw_input_field('products_meta_description[' . $languages[$i]['id'] . ']',(($products_meta_description[$languages[$i]['id']]) ? stripslashes($products_meta_description[$languages[$i]['id']]) : xtc_get_products_meta_description($pInfo->products_id, $languages[$i]['id'])), 'size=25 maxlenght=50'); ?><br />
        <?php echo TEXT_META_KEYWORDS; ?><br />
        <?php echo xtc_draw_input_field('products_meta_keywords[' . $languages[$i]['id'] . ']', (($products_meta_keywords[$languages[$i]['id']]) ? stripslashes($products_meta_keywords[$languages[$i]['id']]) : xtc_get_products_meta_keywords($pInfo->products_id, $languages[$i]['id'])), 'size=25 maxlenght=50'); ?> 
     </td>
    </tr>
    </table>
   
   </td>
  </tr>
</table>

<?php // BOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
<?php echo ('</div>');?>
<?php // EOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>

<?php } ?>
</div>
<!-- EOF - Tomcraft - 2009-11-02 - Block2 //-->

<!-- BOF - Tomcraft - 2009-11-11 - STYLEFIX FOR GOOGLE CHROME-->
<div style=clear:both;></div>
<!-- EOF - Tomcraft - 2009-11-11 - STYLEFIX FOR GOOGLE CHROME-->

<?php // BOF - Tomcraft - 2009-11-02 - NEW WIDTH ?>
<div style="width: 860px; padding:5px;">
<?php // EOF - Tomcraft - 2009-11-02 - NEW WIDTH ?>

<table width="100%"><tr><td style="border-bottom: thin dashed Gray;">&nbsp;</td></tr></table>

<!-- BOF - Tomcraft - 2009-11-02 - Product images //-->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td><span class="main" style="padding-left: 10px; line-height:20px;"><?php echo HEADING_PRODUCT_IMAGES; ?></span></td></tr>
<tr><td>
<table width="100%" border="0" bgcolor="f3f3f3" style="border: 1px solid #aaaaaa; padding:5px;">
<?php
include (DIR_WS_MODULES.'products_images.php');
?>
</table>
</td></tr>
</table>
<!-- EOF - Tomcraft - 2009-11-02 - Product images //-->

<!-- BOF - Tomcraft - 2009-11-02 - Customers group block //-->
<?php
if (GROUP_CHECK == 'true') {
	$customers_statuses_array = xtc_get_customers_statuses();
	$customers_statuses_array = array_merge(array (array ('id' => 'all', 'text' => TXT_ALL)), $customers_statuses_array);
?>
<table width="100%"><tr><td style="border-bottom: thin dashed Gray;">&nbsp;</td></tr></table>
<?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?>
<table width="100%" border="0" bgcolor="f3f3f3" style="border: 1px solid #aaaaaa; padding:5px;">
<tr>
<td style="border-top: 0px solid; border-color: #ff0000;" valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
<td style="border: 1px solid #ff0000;"  bgcolor="#FFCC33" class="main">
<?php
	for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i ++) {
		$code = '$id=$pInfo->group_permission_'.$customers_statuses_array[$i]['id'].';';
		eval ($code);
		
		if ($id==1) {

			$checked = 'checked ';
			
		} else {
			$checked = '';
		}
		echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.$customers_statuses_array[$i]['text'].'<br />';
	}
?>
</td>
</tr>
</table>
<?php
}
?>
<!-- EOF - Tomcraft - 2009-11-02 - Customers group block //-->

<!-- BOF - Tomcraft - 2009-11-02 - Price options //-->
<table width="100%" border="0">
        <tr>
          <td><?php include(DIR_WS_MODULES.'group_prices.php'); ?></td>
        </tr>
        <tr>
          <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
</table>
<!-- EOF - Tomcraft - 2009-11-02 - Price options //-->

<!-- BOF - Tomcraft - 2009-11-02 - Save //-->
<table width="100%" border="0">
    <tr>
     <td class="main" align="right">
<?php
echo xtc_draw_hidden_field('products_date_added', (($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));
echo xtc_draw_hidden_field('products_id', $pInfo->products_id);
?>
      	<input type="submit" class="button" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')">
      	&nbsp;&nbsp;
      	<?php echo '<a class="button" href="' . xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID']) . '">' . BUTTON_CANCEL . '</a>'; ?>
  	 </td>
    </tr>
</table>
<!-- EOF - Tomcraft - 2009-11-02 - Save //-->
</form>
	
<?php // BOF - Tomcraft - 2009-11-02 - NEW WIDTH ?>
</div>
<?php // EOF - Tomcraft - 2009-11-02 - NEW WIDTH ?>