<?php
/* -----------------------------------------------------------------------------------------
   $Id: image_processing_new_step2.php 950 2005-05-14 16:45:21Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (invoice.php,v 1.6 2003/08/24); www.nextcommerce.org

   --------------------------------------------------------------
   Contribution
   image_processing_new_step2 (mit leeren Verzeichnissen step-by-step Variante D) by INSEH 2008-03-26

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

define('MODULE_NEW_STEP2_IMAGE_PROCESS_TEXT_DESCRIPTION', 'Es werden nur die noch fehlenden Bilder in den Verzeichnissen<br /><br /> 

/images/product_images/popup_images/<br />

/images/product_images/info_images/<br />

/images/product_images/thumbnail_images/<br /><br /> neu erstellt.<br /><br />



Hierzu verarbeitet das Script nur eine begrenzte Anzahl von 5 Bildern 

und ruft sich danach selbst wieder auf.');
define('MODULE_NEW_STEP2_IMAGE_PROCESS_TEXT_TITLE', 'XT-Imageprocessing-New - Variante D');
define('MODULE_NEW_STEP2_IMAGE_PROCESS_STATUS_DESC','Modulstatus');
define('MODULE_NEW_STEP2_IMAGE_PROCESS_STATUS_TITLE','Status');
define('IMAGE_EXPORT','Dr&uuml;cken Sie Ok um die Stapelverarbeitung zu starten, dieser Vorgang kann einige Zeit dauern, auf keinen Fall unterbrechen!.');
define('IMAGE_EXPORT_TYPE','<hr noshade><strong>Stapelverarbeitung:</strong>');



  class image_processing_new_step2 {
    var $code, $title, $description, $enabled;


    function image_processing_new_step2() {
      global $order;

      $this->code = 'image_processing_new_step2';
      $this->title = MODULE_NEW_STEP2_IMAGE_PROCESS_TEXT_TITLE;
      $this->description = MODULE_NEW_STEP2_IMAGE_PROCESS_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_NEW_STEP2_IMAGE_PROCESS_SORT_ORDER;
      $this->enabled = ((MODULE_NEW_STEP2_IMAGE_PROCESS_STATUS == 'True') ? true : false);

    }

// <neu> Zeile 68 $n=5 hier Bilderanzahl pro Durchgang
function process($file) {
    include ('includes/classes/'.FILENAME_IMAGEMANIPULATOR); 
    @xtc_set_time_limit(0);
    $files=array();
    if ($dir= opendir(DIR_FS_CATALOG_ORIGINAL_IMAGES)){
        while ($file = readdir($dir)) {
            if ((is_file(DIR_FS_CATALOG_ORIGINAL_IMAGES.$file)) &&
                ($file !="index.html") && 
                ($file !="Thumbs.db") 
            ) {
                if ((!is_file(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$file)) || 
                    (!is_file(DIR_FS_CATALOG_INFO_IMAGES.$file)) || 
                    (!is_file(DIR_FS_CATALOG_POPUP_IMAGES.$file))
                ) {
                    $files[]=array('id' => $file, 'text' =>$file);
                }
            }
        }
        closedir($dir);
    }
    for ($i=0;$n=5,$i<$n;$i++) {
        $products_image_name = $files[$i]['text'];
        if (!is_file(DIR_FS_CATALOG_THUMBNAIL_IMAGES.$files[$i]['text'])) { require(DIR_WS_INCLUDES . 'product_thumbnail_images.php'); }
        if (!is_file(DIR_FS_CATALOG_INFO_IMAGES.$files[$i]['text'])) { require(DIR_WS_INCLUDES . 'product_info_images.php'); }
        if (!is_file(DIR_FS_CATALOG_POPUP_IMAGES.$files[$i]['text'])) {    require(DIR_WS_INCLUDES . 'product_popup_images.php'); }
    }
    xtc_redirect(xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&action=save&module=image_processing_new_step2')); 
// </neu>
    }

    function display() {


    return array('text' =>
                            IMAGE_EXPORT_TYPE.'<br />'.
                            IMAGE_EXPORT.'<br />'.
                            '<br />' . xtc_button(BUTTON_REVIEW_APPROVE) . '&nbsp;' .
                            xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=image_processing_new_step2')));


    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_NEW_STEP2_IMAGE_PROCESS_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_NEW_STEP2_IMAGE_PROCESS_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
}

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_NEW_STEP2_IMAGE_PROCESS_STATUS');
    }

  }
?>