<?php
/* -----------------------------------------------------------------------------------------
   $Id: googlebase.php 1000 2009-08-11 18:10:30Z Hetfield $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (invoice.php,v 1.6 2003/08/24); www.nextcommerce.org
   (c) 2005  (froogle.php, v 1188 2005/08/28); matthias - www.xt-commerce.com
   
   -------------------------------------------------------------------------------------------------------------------------
   Erweiterung der googlebase.php (c)2009 by Hetfield - http://www.MerZ-IT-SerVice.de um folgende Funktionen:
   - Gewichts- oder preisabhängige Vesandkosten mit Berücksichtigung der Versandkostenfrei-Grenze
   - Beachtung des Mindermengenzuschlags
   - Zustand 'neu' fest hinterlegt
   - Anzeige Zahlungsarten
   - Anzeige Gewicht
   - Anzeige EAN
   - Auswahl der verschiedenen suchmaschinenfreundlichen URL für den Exportlink (Original/keine, Shopstat oder DirectURL)
   - Umlautproblematik und str_replace-Wahnsinn beseitigt
   -------------------------------------------------------------------------------------------------------------------------

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

define('MODULE_GOOGLEBASE_TEXT_DESCRIPTION', 'Export - Google Base (Tab getrennt)');
define('MODULE_GOOGLEBASE_TEXT_TITLE', 'Google Base - TXT');
define('MODULE_GOOGLEBASE_FILE_TITLE' , '<hr noshade>Dateiname');
define('MODULE_GOOGLEBASE_FILE_DESC' , 'Geben Sie einen Dateinamen ein, falls die Exportadatei am Server gespeichert werden soll.<br />(Verzeichnis export/)');
define('MODULE_GOOGLEBASE_STATUS_DESC','Modulstatus');
define('MODULE_GOOGLEBASE_STATUS_TITLE','Status');
define('MODULE_GOOGLEBASE_CURRENCY_TITLE','W&auml;hrung');
define('MODULE_GOOGLEBASE_CURRENCY_DESC','Welche W&auml;hrung soll exportiert werden?');
define('MODULE_GOOGLEBASE_SHIPPING_COST_TITLE','<hr noshade><b>Versandkosten</b>');
define('MODULE_GOOGLEBASE_SHIPPING_COST_DESC','Die Versandkosten basieren auf dem Artikelpreis oder dem Artikelgewicht. Beispiel: 25:4.90,50:9.90,etc.. Bis 25 werden 4.90 verrechnet, dar&uuml;ber bis 50 werden 9.90 verrechnet, etc.');
define('MODULE_GOOGLEBASE_SHIPPING_ART_TITLE','<hr noshade><b>Versandkosten-Methode</b>');
define('MODULE_GOOGLEBASE_SHIPPING_ART_DESC','Die Versandkosten basieren auf dem Artikelpreis oder dem Artikelgewicht.');
define('MODULE_GOOGLEBASE_SUMAURL_TITLE','<hr noshade><b>Suchmaschinenfreundliche URL</b>');
define('MODULE_GOOGLEBASE_SUMAURL_DESC','W&auml;hlen Sie aus, ob und welche Erweiterung Sie f&uuml;r suchmaschinenfreundliche URL in Ihrem Shop nutzen');
define('EXPORT_YES','Nur Herunterladen');
define('EXPORT_NO','Am Server Speichern');
define('CURRENCY','<hr noshade><b>W&auml;hrung:</b>');
define('CURRENCY_DESC','W&auml;hrung in der Exportdatei');
define('EXPORT','Bitte den Sicherungsprozess AUF KEINEN FALL unterbrechen. Dieser kann einige Minuten in Anspruch nehmen.');
define('EXPORT_TYPE','<hr noshade><b>Speicherart:</b>');
define('EXPORT_STATUS_TYPE','<hr noshade><b>Kundengruppe:</b>');
define('EXPORT_STATUS','Bitte w&auml;hlen Sie die Kundengruppe, die Basis f&uuml;r den Exportierten Preis bildet. (Falls Sie keine Kundengruppenpreise haben, w&auml;hlen Sie <i>Gast</i>):</b>');
define('CAMPAIGNS','<hr noshade><b>Kampagnen:</b>');
define('CAMPAIGNS_DESC','Mit Kampagne zur Nachverfolgung verbinden.');
define('DATE_FORMAT_EXPORT', '%d.%m.%Y');  // this is used for strftime()
// include needed functions


  class googlebase {
    var $code, $title, $description, $enabled;

    function googlebase() {
      global $order;

      $this->code = 'googlebase';
      $this->title = MODULE_GOOGLEBASE_TEXT_TITLE;
      $this->description = MODULE_GOOGLEBASE_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_GOOGLEBASE_SORT_ORDER;
      $this->enabled = ((MODULE_GOOGLEBASE_STATUS == 'True') ? true : false);
      $this->CAT=array();
      $this->PARENT=array();

    }
	
    function process($file) {

        @xtc_set_time_limit(0);
        require(DIR_FS_CATALOG.DIR_WS_CLASSES . 'xtcPrice.php');
        $xtPrice = new xtcPrice($_POST['currencies'],$_POST['status']);
		
		if ($_POST['sumaurl'] == 'directurl') {
			require_once(DIR_FS_CATALOG.'inc/bluegate_seo.inc.php');
			$bluegateSeo = new BluegateSeo();
		}

        $schema = 'beschreibung'."\t".'id'."\t".'link'."\t".'preis'."\t".'währung '."\t".'titel'."\t".'zustand'."\t".'bild_url'."\t".'ean'."\t".'gewicht'."\t".'marke'."\t".'versand'."\t".'zahlungsmethode'."\n";
        
		if ($_POST['shippingcosts'] != MODULE_GOOGLEBASE_SHIPPING_COST) {
			xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($_POST['shippingcosts']) . "' where configuration_key = 'MODULE_GOOGLEBASE_SHIPPING_COST'");
		}
		$zahlungsmethode = '';
		if (defined('MODULE_PAYMENT_INSTALLED') && xtc_not_null(MODULE_PAYMENT_INSTALLED)) {
			$customers_status_query = xtc_db_query("SELECT customers_status_payment_unallowed FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . (int)$_POST['status'] . "' AND language_id = '" . (int)$_SESSION['languages_id'] . "'");
			$customers_status_value = xtc_db_fetch_array($customers_status_query);
			$installedpayments = explode(';', MODULE_PAYMENT_INSTALLED);
			$unallowed_payment_modules = explode(',', $customers_status_value['customers_status_payment_unallowed']);
			for ($i = 0, $n = sizeof($installedpayments); $i < $n; $i++) {
				$installedpayments[$i] = str_replace('.php','',$installedpayments[$i]);
				if (!in_array($installedpayments[$i], $unallowed_payment_modules)) {						
					@include(DIR_FS_CATALOG.'lang/'.$_SESSION['language'].'/modules/payment/'.$installedpayments[$i].'.php');
					$zahlungsmethode .= strip_tags(constant(strtoupper('MODULE_PAYMENT_'.$installedpayments[$i].'_TEXT_TITLE')));
					if (($n-$i) >= 2) {	$zahlungsmethode .= ','; }
				}
			}
		}	
		
		$export_query =xtc_db_query("SELECT
                             p.products_id,
                             pd.products_name,
                             pd.products_description,
                             p.products_model,
							 p.products_ean,
                             p.products_image,
                             p.products_price,
                             p.products_status,
                             p.products_date_available,
                             p.products_shippingtime,
							 p.products_weight,
                             p.products_discount_allowed,
                             pd.products_meta_keywords,
                             p.products_tax_class_id,
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
            $products_price = $xtPrice->xtcGetPrice($products['products_id'], $format=false, 1, $products['products_tax_class_id'], '');
			$categorie_query=xtc_db_query("SELECT
                                            categories_id
                                            FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                            WHERE products_id='".$products['products_id']."'");
             while ($categorie_data=xtc_db_fetch_array($categorie_query)) {
                    $categories=$categorie_data['categories_id'];
             }
			
            // remove trash
            $products_description = strip_tags($products['products_description']);         
            $products_description = html_entity_decode($products_description);
			$products_description = str_replace(";",", ",$products_description);
			$products_description = str_replace("'",", ",$products_description);
            $products_description = str_replace("\n"," ",$products_description);
            $products_description = str_replace("\r"," ",$products_description);
            $products_description = str_replace("\t"," ",$products_description);
            $products_description = str_replace("\v"," ",$products_description);
            $products_description = str_replace(chr(13)," ",$products_description);            
            $products_description = substr($products_description, 0, 65536);
			$cat = $this->buildCAT($categories);			
			
			if ($products['products_image'] != ''){
				$image = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_ORIGINAL_IMAGES .$products['products_image'];
			} else {
				$image = '';
			}
			if ($products['products_weight'] != '0.00'){
				$weight = number_format($products['products_weight'],2,'.','');
			} else {
				$weight = '';
			}
			$versand = '0.00';
			if ($products_price < MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER && MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS == 'true') {
				$customers_tax_query = xtc_db_query("SELECT customers_status_show_price_tax, customers_status_add_tax_ot FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . (int)$_POST['status'] . "' AND language_id = '" . (int)$_SESSION['languages_id'] . "'");
				$customers_tax_value = xtc_db_fetch_array($customers_tax_query);
				$tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS);
				if ($customers_tax_value['customers_status_show_price_tax'] == 1) {
					$low_order_fee = xtc_add_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax);
				}
				if ($customers_tax_value['customers_status_show_price_tax'] == 0 && $customers_tax_value['customers_status_add_tax_ot'] == 1) {
					$low_order_fee = MODULE_ORDER_TOTAL_LOWORDERFEE_FEE;
				}
				if ($customers_tax_value['customers_status_show_price_tax'] == 0 && $customers_tax_value['customers_status_add_tax_ot'] != 1) {
					$low_order_fee = MODULE_ORDER_TOTAL_LOWORDERFEE_FEE;
				}
				$versand = $versand + $low_order_fee;
			}
            if ($products_price > MODULE_SHIPPING_FREEAMOUNT_AMOUNT && MODULE_SHIPPING_FREEAMOUNT_STATUS == 'True') {
				$versand = $versand;
			} else if ($products_price > MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER && MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true') {
				$versand = $versand;
			} else {
				$shipping = -1;
				$shippinglist = split("[:,]" , $_POST['shippingcosts']);
				for ($i=0; $i<sizeof($shippinglist); $i+=2) {
					if ($_POST['shippingart'] == 'weight') {
				  		if ($products['products_weight'] <= $shippinglist[$i]) {
							$shipping = $shippinglist[$i+1];
							break;
				  		}
					} else if ($_POST['shippingart'] == 'price') {
						if ($products_price <= $shippinglist[$i]) {
							$shipping = $shippinglist[$i+1];
							break;
				  		}
					}
				}	
				if ($shipping == -1) {
				  $shipping_cost = 0;
				} else {
				  $shipping_cost = $shipping;
				}
				$versand = $versand + $shipping_cost;
				$versand = number_format($versand,2,'.','');			
			}
			if ($_POST['sumaurl'] == 'shopstat') {
				$cat = strip_tags($this->buildCAT($categories));
				require_once(DIR_FS_INC . 'xtc_href_link_from_admin.inc.php');
				$productURL = xtc_href_link_from_admin('product_info.php', xtc_product_link($products['products_id'], $products['products_name']));
				(preg_match("/\?/",$productURL)) ? $link .= '&' : $productURL .= '?';
				$productURL .= 'referer='.$this->code;
				(!empty($_POST['campaign']))
					? $productURL .= '?'.$_POST['campaign']
					: false;
				$productURL .= '&language='.$this->language;
			} else if ($_POST['sumaurl'] == 'directurl') {
				$productURL = $bluegateSeo->getProductLink(xtc_product_link($products['products_id'], $products['products_name']),$connection,$_SESSION['languages_id']);
				if ($_POST['campaign']<>'') {
					$productURL.='?'.$_POST['campaign'];
				}
			} else if ($_POST['sumaurl'] == 'original') {
				$productURL = HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'product_info.php?'.$_POST['campaign'].xtc_product_link($products['products_id'], $products['products_name']);
			}	
			
            //create content
            $schema .=  $products_description."\t".
						$products['products_id']."\t".
                        $productURL . "\t" .
                        number_format($products_price,2,'.','')."\t".
						$_POST['currencies']."\t".
						$products['products_name']."\t".
						"neu\t".
                        $image."\t" .
						$products['products_ean']."\t".
						$weight."\t".
                        $products['manufacturers_name']."\t".
						":::".$versand."\t" .
						$zahlungsmethode."\n";
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
            //header('Content-type: application/x-octet-stream');
			header('Content-type: application/x-octet-stream; charset=ISO-8859-1');
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
							'<b>'.MODULE_GOOGLEBASE_SHIPPING_COST_TITLE.'</b><br />'.
							MODULE_GOOGLEBASE_SHIPPING_COST_DESC.'<br />'.
							xtc_draw_input_field('shippingcosts',MODULE_GOOGLEBASE_SHIPPING_COST).'<br />'.
							'<b>'.MODULE_GOOGLEBASE_SHIPPING_ART_TITLE.'</b><br />'.
							MODULE_GOOGLEBASE_SHIPPING_ART_DESC.'<br />'.
                            xtc_draw_radio_field('shippingart', 'weight',true).'Versandksten nach Gewicht<br />'.
                            xtc_draw_radio_field('shippingart', 'price',false).'Versandkosten nach Preis<br />'.
							'<b>'.MODULE_GOOGLEBASE_SUMAURL_TITLE.'</b><br />'.
							MODULE_GOOGLEBASE_SUMAURL_DESC.'<br />'.
                            xtc_draw_radio_field('sumaurl', 'original',true).'Originale bzw. keine<br />'.
                            xtc_draw_radio_field('sumaurl', 'shopstat',false).'Shopstat<br />'.
							xtc_draw_radio_field('sumaurl', 'directurl',false).'DirectURL<br />'.
							CAMPAIGNS.'<br />'.
                            CAMPAIGNS_DESC.'<br />'.
                          	xtc_draw_pull_down_menu('campaign',$campaign_array).'<br />'.                               
                            EXPORT_TYPE.'<br />'.
                            EXPORT.'<br />'.
                          	xtc_draw_radio_field('export', 'no',false).EXPORT_NO.'<br />'.
                            xtc_draw_radio_field('export', 'yes',true).EXPORT_YES.'<br />'.
                            '<br />' . xtc_button(BUTTON_EXPORT) .
                            xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=googlebase')));


    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_GOOGLEBASE_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_GOOGLEBASE_FILE', 'googlebase.txt', '6', '1', '', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_GOOGLEBASE_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
	  xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_GOOGLEBASE_SHIPPING_COST', '25:6.90,50:9.90,10000:0.00', '6', '1', '', '', now())");
	}

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	  xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_GOOGLEBASE_SHIPPING_COST'");
    }

    function keys() {
      return array('MODULE_GOOGLEBASE_STATUS','MODULE_GOOGLEBASE_FILE');
    }

  }
?>