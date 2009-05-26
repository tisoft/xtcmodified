<?php
/* -----------------------------------------------------------------------------------------

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (invoice.php,v 1.6 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
define('MODULE_PREISSUCHMASCHINE_TEXT_DESCRIPTION', '<hr noshade="noshade"><br /><center><a href="http://www.preissuchmaschine.de/"><img src="http://bilder.preissuchmaschine.de/other/PSMLogoMid1.jpg" width="100" height="46" border="0" alt="Preissuchmaschine - Ihr Preisvergleich"></a></center><br /><br />
<strong>Export</strong><br />PreisSuchmaschine.de<br /><br />
<strong>Trennzeichen</strong><br />getrennt durch | (PIPE)<br /><br />
<strong>Format</strong><br />- ProduktID<br />- Gewicht<br />- EAN<br />- Hersteller<br />- ProduktBezeichnung<br />- ArtikelNr. (ggf. auch Hersteller-ArtikelNr.)<br />- Preis<br />- Produktbeschreibung (kurz)<br />- Produktbeschreibung (lang)<br />- Lieferzeit<br />- Produktlink<br />- FotoLink<br />- Kategoriename<br /><br />
<strong>Modulversion</strong><br />PreisSuchmaschine.de - <i>August 2008 - 1.2 - 29.08.2008</i><br /><br />
<strong>Fragen</strong><br />Metashopper Europe GmbH<br />Rambachstrasse 1<br />20459 Hamburg<br /><br />Tel: 040 319 796-30<br />Fax: 040 319 796-39<br />E-Mail:<a href="mailto:post@metashopper.de?SUBJECT=Fragen zum XT:Commerce-Modul 1.1"><u>post@metashopper.de</u></a>');
define('MODULE_PREISSUCHMASCHINE_TEXT_TITLE', 'Preissuchmaschine.de - CSV');
define('MODULE_PREISSUCHMASCHINE_FILE_TITLE' , '<hr noshade>Dateiname');
define('MODULE_PREISSUCHMASCHINE_FILE_DESC' , 'Geben Sie einen Dateinamen ein, falls die Exportadatei am Server gespeichert werden soll.<br />(Verzeichnis export/)');
define('MODULE_PREISSUCHMASCHINE_STATUS_DESC','Modulstatus');
define('MODULE_PREISSUCHMASCHINE_STATUS_TITLE','Status');
define('MODULE_PREISSUCHMASCHINE_CURRENCY_TITLE','W&auml;hrung');
define('MODULE_PREISSUCHMASCHINE_CURRENCY_DESC','Welche W&auml;hrung soll exportiert werden?');
define('EXPORT_YES','Nur Herunterladen');
define('EXPORT_NO','Am Server Speichern');
define('CURRENCY','<hr noshade><strong>W&auml;hrung:</strong>');
define('CURRENCY_DESC','W&auml;hrung in der Exportdatei');
define('EXPORT','Bitte diesen Prozess AUF <strong>KEINEN</strong> FALL unterbrechen. Dieser kann einige Minuten in Anspruch nehmen.');
define('EXPORT_TYPE','<hr noshade><strong>Speicherart:</strong>');
define('EXPORT_STATUS_TYPE','<hr noshade><strong>Kundengruppe:</strong>');
define('EXPORT_STATUS','Bitte w&auml;hlen Sie die Kundengruppe, die Basis f&uuml;r den Exportierten Preis bildet. (Falls Sie keine Kundengruppenpreise haben, w&auml;hlen Sie <i>Gast</i>):</strong>');
define('CAMPAIGNS','<hr noshade><strong>Kampagnen:</strong>');
define('CAMPAIGNS_DESC','Mit Kampagne verbinden.<br />(Nachverfolgung/Tracken/Klickz&auml;hlen) .');
// include needed functions


  class preissuchmaschine {
    var $code, $title, $description, $enabled;


    function preissuchmaschine() {
      global $order;

      $this->code = 'preissuchmaschine';
      $this->title = MODULE_PREISSUCHMASCHINE_TEXT_TITLE;
      $this->description = MODULE_PREISSUCHMASCHINE_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PREISSUCHMASCHINE_SORT_ORDER;
      $this->CAT=array();
      $this->PARENT=array();
      $this->enabled = ((MODULE_PREISSUCHMASCHINE_STATUS == 'True') ? true : false);

    }


    function process($file) {

        @xtc_set_time_limit(0);
        require(DIR_FS_CATALOG.DIR_WS_CLASSES . 'xtcPrice.php');
        $xtPrice = new xtcPrice($_POST['currencies'],$_POST['status']);

        $schema = 'ProduktID|Gewicht|EAN|Hersteller|ProduktBezeichnung|ArtikelNroderHerstellerArtikelNr|Preis|ProduktLangBeschreibung|ProduktKurzBeschreibung|Lieferzeit|Produktlink|FotoLink|Kategoriename' . "\n";
        $export_query =xtc_db_query("SELECT
                             p.products_id,
                             pd.products_name,
                             pd.products_description,
                             pd.products_short_description,
                             p.products_weight,
                             p.products_ean,
                             p.products_model,
                             p.products_shippingtime,
                             p.products_image,
                             p.products_price,
                             p.products_status,
                             p.products_discount_allowed,
                             p.products_tax_class_id,
                             IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
                             p.products_date_added,
                             m.manufacturers_name
                         FROM
                             " . TABLE_PRODUCTS . " p LEFT JOIN
                             " . TABLE_MANUFACTURERS . " m
                           ON p.manufacturers_id = m.manufacturers_id LEFT JOIN
                             " . TABLE_PRODUCTS_DESCRIPTION . " pd
                           ON p.products_id = pd.products_id AND
                            pd.language_id = '".$_SESSION['languages_id']."' LEFT JOIN
                             " . TABLE_SPECIALS . " s
                           ON p.products_id = s.products_id
                         WHERE
                           p.products_status = 1
                         ORDER BY
                            p.products_date_added DESC,
                            pd.products_name");


        while ($products = xtc_db_fetch_array($export_query)) {


            $products_price = $xtPrice->xtcGetPrice($products['products_id'],
                                        $format=false,
                                        1,
                                        $products['products_tax_class_id'],
                                        '');

	    // get product categorie
            $categorie_query=xtc_db_query("SELECT
                                            categories_id
                                            FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                            WHERE products_id='".$products['products_id']."'");
             while ($categorie_data=xtc_db_fetch_array($categorie_query)) {
                    $categories=$categorie_data['categories_id'];
             }

            // remove trash in $products_description
            $products_description = strip_tags($products['products_description']);
            $products_description = str_replace(";",", ",$products_description);
            $products_description = str_replace("'",", ",$products_description);
            $products_description = str_replace("\n"," ",$products_description);
            $products_description = str_replace("\r"," ",$products_description);
            $products_description = str_replace("\t"," ",$products_description);
            $products_description = str_replace("\v"," ",$products_description);
            $products_description = str_replace("&quot,"," \"",$products_description);
            $products_description = str_replace("&qout,"," \"",$products_description);
            $products_description = str_replace("|",",",$products_description);
            $products_description = substr($products_description, 0, 253);

            // remove trash in $products_short_description
            $products_short_description = strip_tags($products['products_short_description']);
            $products_short_description = str_replace(";",", ",$products_short_description);
            $products_short_description = str_replace("'",", ",$products_short_description);
            $products_short_description = str_replace("\n"," ",$products_short_description);
            $products_short_description = str_replace("\r"," ",$products_short_description);
            $products_short_description = str_replace("\t"," ",$products_short_description);
            $products_short_description = str_replace("\v"," ",$products_short_description);
            $products_short_description = str_replace("&quot,"," \"",$products_short_description);
            $products_short_description = str_replace("&qout,"," \"",$products_short_description);
            $products_short_description = str_replace("|",",",$products_short_description);
            $products_short_description = substr($products_short_description, 0, 253);
           
            $cat = $this->buildCAT($categories);

            // creates pathes of images, if images are integrated
            if ($products['products_image'] != '')
              {
	        $image_if_available = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_ORIGINAL_IMAGES .$products['products_image'];
	      }
              else
              {
	        $image_if_available = '';
	      }

            //create content
            $schema .= $products['products_id'] .'|'. 
                       $products['products_weight'] .'|' . 
                       $products['products_ean'] .'|' . 
                       $products['manufacturers_name'] .'|'. 
                       $products['products_name'] .'|' .
                       $products['products_model'] . '|' .
                       number_format($products_price,2,',','.'). '|' .
                       $products_description .'|'.
                       $products_short_description .'|'.
                       xtc_get_shipping_status_name($products['products_shippingtime']). '|' .
                       HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'product_info.php?'.$_POST['campaign'].xtc_product_link($products['products_id'], $products['products_name']) . '|' .
                       $image_if_available . '|' .
                       substr($cat,0,strlen($cat)-2) . 
                       "\n";

        
        }
        // create File
          $fp = fopen(DIR_FS_DOCUMENT_ROOT.'export/' . $file, "w+");
          fputs($fp, $schema);
          fclose($fp);


      switch ($_POST['export']) {
        case 'yes':
            // send File to Browser
            $extension = substr($file, -3);
            $fp = fopen(DIR_FS_DOCUMENT_ROOT.'export/' . $file,"rb");
            $buffer = fread($fp, filesize(DIR_FS_DOCUMENT_ROOT.'export/' . $file));
            fclose($fp);
            header('Content-type: application/x-octet-stream');
            header('Content-disposition: attachment; filename=' . $file);
            echo $buffer;
            exit;

        break;
        }

    }

    function buildCAT($catID)
    {

        if (isset($this->CAT[$catID]))
        {
         return  $this->CAT[$catID];
        } else {
           $cat=array();
           $tmpID=$catID;

               while ($this->getParent($catID)!=0 || $catID!=0)
               {
                    $cat_select=xtc_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id='".$catID."' and language_id='".$_SESSION['languages_id']."'");
                    $cat_data=xtc_db_fetch_array($cat_select);
                    $catID=$this->getParent($catID);
                    $cat[]=$cat_data['categories_name'];

               }
               $catStr='';
               for ($i=count($cat);$i>0;$i--)
               {
                  $catStr.=$cat[$i-1].' > ';
               }
               $this->CAT[$tmpID]=$catStr;
        return $this->CAT[$tmpID];
        }
    }

     
    function getParent($catID)
    {
      if (isset($this->PARENT[$catID]))
      {
       return $this->PARENT[$catID];
      } else {
       $parent_query=xtc_db_query("SELECT parent_id FROM ".TABLE_CATEGORIES." WHERE categories_id='".$catID."'");
       $parent_data=xtc_db_fetch_array($parent_query);
       $this->PARENT[$catID]=$parent_data['parent_id'];
       return  $parent_data['parent_id'];
      }
    }


    function display() {

    $customers_statuses_array = xtc_get_customers_statuses();

    // build Currency Select
    $curr='';
    $currencies=xtc_db_query("SELECT code FROM ".TABLE_CURRENCIES);
    while ($currencies_data=xtc_db_fetch_array($currencies)) {
     $curr.=xtc_draw_radio_field('currencies', $currencies_data['code'],true).$currencies_data['code'].'<br />';
    }

    $campaign_array = array(array('id' => '', 'text' => TEXT_NONE));
	$campaign_query = xtc_db_query("select campaigns_name, campaigns_refID from ".TABLE_CAMPAIGNS." order by campaigns_id");
	while ($campaign = xtc_db_fetch_array($campaign_query)) {
	$campaign_array[] = array ('id' => 'refID='.$campaign['campaigns_refID'].'&', 'text' => $campaign['campaigns_name'],);
	}

    return array('text' =>  EXPORT_STATUS_TYPE.'<br />'.
                          	EXPORT_STATUS.'<br />'.
                          	xtc_draw_pull_down_menu('status',$customers_statuses_array, '1').'<br />'.
                            CURRENCY.'<br />'.
                            CURRENCY_DESC.'<br />'.
                            $curr.
                            CAMPAIGNS.'<br />'.
                            CAMPAIGNS_DESC.'<br />'.
                          	xtc_draw_pull_down_menu('campaign',$campaign_array).'<br />'.                             
                            EXPORT_TYPE.'<br />'.
                            EXPORT.'<br />'.
                          	 xtc_draw_radio_field('export', 'no',false).EXPORT_NO.'<br />'.
                            xtc_draw_radio_field('export', 'yes',true).EXPORT_YES.'<br /><br />' . xtc_button(BUTTON_EXPORT) .
                            xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=preissuchmaschine')));


    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PREISSUCHMASCHINE_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PREISSUCHMASCHINE_FILE', 'preissuchmaschine.csv',  '6', '1', '', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PREISSUCHMASCHINE_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
}

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PREISSUCHMASCHINE_STATUS','MODULE_PREISSUCHMASCHINE_FILE');
    }

  }
?>
