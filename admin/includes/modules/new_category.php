<?php
/* --------------------------------------------------------------
   $Id: new_category.php 799 2005-02-23 18:08:06Z novalis $

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
    if (isset($_GET['cID']) && (!$_POST) ) {
      $category_query = xtc_db_query("select * from " .
                                      TABLE_CATEGORIES . " c, " .
                                      TABLE_CATEGORIES_DESCRIPTION . " cd
                                      where c.categories_id = cd.categories_id
                                      and c.categories_id = '" . $_GET['cID'] . "'");

      $category = xtc_db_fetch_array($category_query);

      $cInfo = new objectInfo($category);
    } elseif (xtc_not_null($_POST)) {
      $cInfo = new objectInfo($_POST);
      $categories_name = $_POST['categories_name'];
      $categories_heading_title = $_POST['categories_heading_title'];
      $categories_description = $_POST['categories_description'];
      $categories_meta_title = $_POST['categories_meta_title'];
      $categories_meta_description = $_POST['categories_meta_description'];
      $categories_meta_keywords = $_POST['categories_meta_keywords'];
    } else {
      $cInfo = new objectInfo(array());
    }

    $languages = xtc_get_languages();

    $text_new_or_edit = ($_GET['action']=='new_category_ACD') ? TEXT_INFO_HEADING_NEW_CATEGORY : TEXT_INFO_HEADING_EDIT_CATEGORY;
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sprintf($text_new_or_edit, xtc_output_generated_category_path($current_category_id)); ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <?php
          $form_action = isset($_GET['cID']) ? 'update_category' : 'insert_category';
    echo xtc_draw_form('new_category', FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID'] . '&action='.$form_action, 'post', 'enctype="multipart/form-data"'); ?>

        <td>
<!-- BOF - Tomcraft - 2009-11-02 - Block1 //-->
<div style="width: 860px; padding:5px;">
<!-- BOF - Hetfield - 2009-12-17 - removing HTML-Error //-->
<table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="f3f3f3" style="width: 100%; border: 1px solid; border-color: #aaaaaa; padding: 5px;">
<!-- EOF - Hetfield - 2009-12-17 - removing HTML-Error //-->
<tr>
  <td class="main" width="204" valign="top"><?php echo TEXT_EDIT_CATEGORIES_IMAGE; ?></td>
  <td class="main"><?php echo xtc_draw_file_field('categories_image') . '<br />' . xtc_draw_separator('pixel_trans.gif', '24', '15') . xtc_draw_hidden_field('categories_previous_image', $cInfo->categories_image); ?>
<?php
if ($cInfo->categories_image) {
  ?>
  <br />
  <img src="<?php echo DIR_WS_CATALOG.'images/categories/'.$cInfo->categories_image; ?>" style="max-width:200px; max-height:200px">
  <br /><?php echo '&nbsp;' .$cInfo->categories_image;
  echo xtc_draw_selection_field('del_cat_pic', 'checkbox', 'yes').TEXT_DELETE;

} ?>
  </td>
</tr>
<tr>
  <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
</tr>
<tr>
  <td class="main"><?php echo TEXT_EDIT_STATUS; ?>:</td>
  <td class="main"><?php echo xtc_draw_selection_field('status', 'checkbox', '1',$cInfo->categories_status==1 ? true : false); ?></td>
</tr>
<?php
// BOF - Tomcraft - 2009-09-12 - add option to sort by date and products model
/*
$order_array='';
$order_array=array(array('id' => 'p.products_price','text'=>TXT_PRICES),
                   array('id' => 'pd.products_name','text'=>TXT_NAME),
                   array('id' => 'p.products_ordered','text'=>TXT_ORDERED),
                   array('id' => 'p.products_sort','text'=>TXT_SORT),
                   array('id' => 'p.products_weight','text'=>TXT_WEIGHT),
                   array('id' => 'p.products_quantity','text'=>TXT_QTY));
$default_value='pd.products_name';
*/
$order_array='';
$order_array=array(array('id' => 'p.products_price','text'=>TXT_PRICES),
                   array('id' => 'pd.products_name','text'=>TXT_NAME),
                   array('id' => 'p.products_date_added','text'=>TXT_DATE),
                   array('id' => 'p.products_model','text'=>TXT_MODEL),
                   array('id' => 'p.products_ordered','text'=>TXT_ORDERED),
                   array('id' => 'p.products_sort','text'=>TXT_SORT),
                   array('id' => 'p.products_weight','text'=>TXT_WEIGHT),
                   array('id' => 'p.products_quantity','text'=>TXT_QTY));
$default_value='pd.products_name';
// EOF - Tomcraft - 2009-09-12 - add option to sort by date and products model

$order_array_desc='';
$order_array_desc =array(array('id' => 'ASC','text'=>TEXT_SORT_ASC),
                        array('id' => 'DESC','text'=>TEXT_SORT_DESC));

?>
<tr>
  <td class="main"><?php echo TEXT_EDIT_PRODUCT_SORT_ORDER; ?>:</td>
  <td class="main"><?php echo xtc_draw_pull_down_menu('products_sorting',$order_array,$cInfo->products_sorting, 'style="width: 130px"'); ?>&nbsp;<?php echo xtc_draw_pull_down_menu('products_sorting2',$order_array_desc,$cInfo->products_sorting2); ?></td>
</tr>
  <td class="main"><?php echo TEXT_EDIT_SORT_ORDER; ?></td>
  <td class="main"><?php echo xtc_draw_input_field('sort_order', $cInfo->sort_order, 'style="width: 130px"'); ?></td>
</tr>
<tr>
  <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
</tr>
<?php
$files=array();
if ($dir= opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_listing/')){
  while  (($file = readdir($dir)) !==false) {
        if (is_file( DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_listing/'.$file) and ($file !="index.html")){
        $files[]=array(
                        'id' => $file,
                        'text' => $file);
        }//if
     } // while
     closedir($dir);
}
$default_array=array();
 // set default value in dropdown!
if (empty($content['content_file'])) {
  $default_array[]=array('id' => 'default','text' => TEXT_SELECT);
  $default_value=$cInfo->listing_template;
  $files=array_merge($default_array,$files);
} else {
  $default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
  $default_value=$cInfo->listing_template;
  $files=array_merge($default_array,$files);
}
?>
<tr>
  <td><span class="main"><?php echo TEXT_CHOOSE_INFO_TEMPLATE_LISTING; ?>:</span></td>
  <td><span class="main"><?php echo xtc_draw_pull_down_menu('listing_template',$files,$default_value, 'style="width: 200px"');?></span></td>
</tr>

<?php
$files=array();
if ($dir= opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/')){
  while  (($file = readdir($dir)) !==false) {
        if (is_file( DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/'.$file) and ($file !="index.html")){
        $files[]=array(
                        'id' => $file,
                        'text' => $file);
        }//if
    } // while
    closedir($dir);
}
$default_array=array();
// set default value in dropdown!
if (empty($content['content_file'])) {
  $default_array[]=array('id' => 'default','text' => TEXT_SELECT);
  $default_value=$cInfo->categories_template;
  $files=array_merge($default_array,$files);
} else {
  $default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
  $default_value=$cInfo->categories_template;
  $files=array_merge($default_array,$files);
}
?>
<tr>
  <td><span class="main"><?php echo TEXT_CHOOSE_INFO_TEMPLATE_CATEGORIE; ?>:</span></td>
  <td><span class="main"><?php echo xtc_draw_pull_down_menu('categories_template',$files,$default_value, 'style="width: 200px"');?></span></td>
</tr>

</table>
</div>
<!-- EOF - Tomcraft - 2009-11-02 - Block1 //-->

<!-- BOF - Tomcraft - 2009-11-02 - Customers group block //-->
<div style="width: 860px; padding:5px;">
<!-- BOF - Tomcraft - 2009-12-18 - removing HTML-Error //-->
<table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="f3f3f3" style="width: 100%; border: 1px solid; border-color: #aaaaaa; padding: 5px;">
<!-- EOF - Tomcraft - 2009-12-18 - removing HTML-Error //-->
<?php
if (GROUP_CHECK=='true') {
  $customers_statuses_array = xtc_get_customers_statuses();
  $customers_statuses_array=array_merge(array(array('id'=>'all','text'=>TXT_ALL)),$customers_statuses_array);
?>

<tr>
  <td style="border-top: 0px solid;  border-color: #ff0000;" width="204" valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
  <td style="border: 1px solid; border-color: #ff0000;"  bgcolor="#FFCC33" class="main">
<?php

for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {

  if ($category['group_permission_'.$customers_statuses_array[$i]['id']] == 1) {
    $checked='checked ';
  } else {
  $checked='';
  }
  echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.$customers_statuses_array[$i]['text'].'<br />';
}
?>
  </td>
</tr>
<?php
}
?>
</table>
</div>
<!-- EOF - Tomcraft - 2009-11-02 - Customers group block //-->


<?php // BOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>

  <link rel="stylesheet" type="text/css" href="includes/lang_tabs_menu/lang_tabs_menu.css">
  <script type="text/javascript" src="includes/lang_tabs_menu/lang_tabs_menu.js"></script>

  <?php
  $langtabs = '<div class="tablangmenu"><ul>';
  $csstabstyle = 'border: 1px solid #aaaaaa; padding: 5px; width: 848px; margin-top: -1px; margin-bottom: 10px; float: left; background: #f3f3f3;';
  $csstab = '<style type="text/css">' .  '#tab_lang_0' . '{display: block;' . $csstabstyle . '}';
  $csstab_nojs = '<style type="text/css">';
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $tabtmp = "\'tab_lang_$i\'," ;
    $langtabs.= '<li onclick="showTab('. $tabtmp. $n.')" style="cursor: pointer;" id="tabselect_' . $i .'">' .xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'], $languages[$i]['name']) . ' ' . $languages[$i]['name'].  '</li>';
    if($i > 0) $csstab .= '#tab_lang_' . $i .'{display: none;' . $csstabstyle . '}';
    $csstab_nojs .= '#tab_lang_' . $i .'{display: block;' . $csstabstyle . '}';
  }
  $csstab .= '</style>';
  $csstab_nojs .= '</style>';
  $langtabs.= '</ul></div>';
  //echo $csstab;
  //echo $langtabs;
  ?>

  <?php // EOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>

<!-- BOF - Tomcraft - 2009-11-02 - Block2 //-->
<!--
<div style="width: 860px; padding:5px;">
<table width="100%" border="0" cellpadding="3" cellspacing="0" bgcolor="f3f3f3" style="border: 1px solid; border-color: #aaaaaa; padding:5px;">
<?php    for ($i=0; $i<sizeof($languages); $i++) { ?>
          <tr>
            <td class="main" width="180"><?php if ($i == 0) {echo TEXT_EDIT_CATEGORIES_NAME;} else echo '&nbsp;' ?></td>
            <td class="main"><?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_name($cInfo->categories_id, $languages[$i]['id'])), 'style="width: 200px"'); ?></td>
          </tr>
<?php } ?>
          <tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
<?php    for ($i=0; $i<sizeof($languages); $i++) { ?>
          <tr>
            <td class="main"><?php if ($i == 0) {echo TEXT_EDIT_CATEGORIES_HEADING_TITLE;} else echo '&nbsp;' ?></td>
            <td class="main"><?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('categories_heading_title[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_heading_title($cInfo->categories_id, $languages[$i]['id'])), 'style="width: 200px"'); ?></td>
          </tr>
<?php } ?>
</table>
</div>
<br />
//-->
<!-- EOF - Tomcraft - 2009-11-02 - Block2 //-->


<table border="0" cellspacing="0" cellpadding="5">
<?php // BOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
<tr>
  <td colspan="2">
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

<?php    for ($i=0; $i<sizeof($languages); $i++) { ?>
          <?php // BOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
          <?php echo ('<div id="tab_lang_' . $i . '">');?>
      <table>
      <?php // EOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
      <tr>
            <td class="main" width="184"><?php echo TEXT_EDIT_CATEGORIES_NAME; ?></td>
            <td class="main"><?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', (isset($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_name($cInfo->categories_id, $languages[$i]['id'])), 'style="width: 200px"'); ?></td>
          </tr>
      <tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '2'); ?></td></tr>
      <tr>
            <td class="main"><?php echo TEXT_EDIT_CATEGORIES_HEADING_TITLE; ?></td>
            <td class="main"><?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('categories_heading_title[' . $languages[$i]['id'] . ']', (isset($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_heading_title($cInfo->categories_id, $languages[$i]['id'])), 'style="width: 200px"'); ?></td>
          </tr>
      <tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
          <tr>
            <td class="main" valign="top"><?php  echo TEXT_EDIT_CATEGORIES_DESCRIPTION; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&nbsp;</td>
                <td class="main"><?php echo xtc_draw_textarea_field('categories_description[' . $languages[$i]['id'] . ']', 'soft', '70', '25', (isset($categories_description[$languages[$i]['id']]) ? stripslashes($categories_description[$languages[$i]['id']]) : xtc_get_categories_description($cInfo->categories_id, $languages[$i]['id'])), 'style="width: 600px"'); ?></td>
              </tr>
            </table></td>
          </tr>
      <tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
          <tr>
            <td class="main" valign="top"><?php  echo TEXT_META_TITLE; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&nbsp;</td>
                <td class="main"><?php echo xtc_draw_input_field('categories_meta_title[' . $languages[$i]['id'] . ']',(isset($categories_meta_title[$languages[$i]['id']]) ? stripslashes($categories_meta_title[$languages[$i]['id']]) : xtc_get_categories_meta_title($cInfo->categories_id, $languages[$i]['id'])), 'style="width: 600px"'); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
           <tr>
            <td class="main" valign="top"><?php  echo TEXT_META_DESCRIPTION; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&nbsp;</td>
                <td class="main"><?php echo xtc_draw_input_field('categories_meta_description[' . $languages[$i]['id'] . ']', (isset($categories_meta_description[$languages[$i]['id']]) ? stripslashes($categories_meta_description[$languages[$i]['id']]) : xtc_get_categories_meta_description($cInfo->categories_id, $languages[$i]['id'])),'style="width:600px"'); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
           <tr>
            <td class="main" valign="top"><?php  echo TEXT_META_KEYWORDS; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&nbsp;</td>
                <td class="main"><?php echo xtc_draw_input_field('categories_meta_keywords[' . $languages[$i]['id'] . ']',(isset($categories_meta_keywords[$languages[$i]['id']]) ? stripslashes($categories_meta_keywords[$languages[$i]['id']]) : xtc_get_categories_meta_keywords($cInfo->categories_id, $languages[$i]['id'])),'style="width: 600px"'); ?></td>
              </tr>
            </table></td>
          </tr>
      <?php // BOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
      </table>
      <?php echo ('</div>');?>
      <?php // EOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
<?php } ?>
<?php // BOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>
</td></tr>
<?php // EOF - Tomcraft - 2009-11-02 - LANGUAGE TABS ?>

        </table></td>
      </tr>
      <tr>
        <td class="main">
       <table><tr><td align="right" width="850">
          <?php echo xtc_draw_hidden_field('categories_date_added', (($cInfo->date_added) ? $cInfo->date_added : date('Y-m-d'))) . xtc_draw_hidden_field('parent_id', $cInfo->parent_id); ?>
          <?php echo xtc_draw_hidden_field('categories_id', $cInfo->categories_id); ?>
          <INPUT type="submit" class="button" name="update_category" value="<?php echo BUTTON_SAVE; ?>" style="cursor:pointer" onclick="return confirm('<?php echo SAVE_ENTRY; ?>')">&nbsp;&nbsp;<a class="button" onclick="this.blur()" href="<?php echo xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID']); ?>"><?php echo BUTTON_CANCEL ; ?></a>
    </td></tr></table>
    </td>
      </form>
      </tr>