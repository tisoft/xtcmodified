<?php
/* -----------------------------------------------------------------------------------------
   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2005 XT-Commerce
   (c) idealo 2009, provided as is, no warranty
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com
   (c) 2003	 nextcommerce (invoice.php,v 1.6 2003/08/24); www.nextcommerce.org

   Extended by
   - Jens-Uwe Rumstich (Idealo Internet GmbH, http://www.idealo.de)
   - Andreas Geisler (Idealo Internet GmbH, http://www.idealo.de)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

// module display config
define('MODULE_IDEALO_TEXT_DESCRIPTION', 'Export - Idealo (Semikolon getrennt)');
define('MODULE_IDEALO_TEXT_TITLE', 'Idealo - CSV (ab PHP 5.x!)');
define('MODULE_IDEALO_FILE_TITLE' , '<hr noshade>Dateiname');
define('MODULE_IDEALO_FILE_DESC' , 'Geben Sie einen Dateinamen ein, falls die Exportadatei am Server gespeichert werden soll.<br>(Verzeichnis export/)');
define('FIELDSEPARATOR', '<b>Spaltentrenner</b>');
define('FIELDSEPARATOR_HINT', 'Beispiel:<br>;&nbsp;&nbsp;&nbsp;(Semikolon)<br>,&nbsp;&nbsp;&nbsp;(Komma)<br>\t&nbsp;&nbsp;(Tab)<br>...<br>Wird das Feld leer gelassen, wird Tab als Trenner genutzt.');
define('QUOTING','<b>Quoting</b>');
define('QUOTING_HINT','Beispiel:<br>"&nbsp;&nbsp;&nbsp;(Anf&uuml;hrungszeichen)<br>\'&nbsp;&nbsp;&nbsp;(Hochkomma)<br>#&nbsp;&nbsp;(Raute)<br>... <br>Wird das Feld leer gelassen, wird nicht gequotet.');
define('LANGUAGE', '<b>Export f&uuml;r</b>');
define('LANGUAGE_HINT', 'Beispiel:<br>DE (Deutschland)<br>AT (&Ouml;sterreich)<br>...<br>Es sollten(!) die Sprachen genutzt werden, die auch bei den Versandkosten etc. korrekt hinterlegt sind.<br>Wird das Feld leer gelassen, wird \'DE\' benutzt.');
define('MODULE_IDEALO_STATUS_DESC','Modulstatus');
define('MODULE_IDEALO_STATUS_TITLE','Status');
define('MODULE_IDEALO_CURRENCY_TITLE','W&auml;hrung');
define('MODULE_IDEALO_CURRENCY_DESC','Welche W&auml;hrung soll exportiert werden?');
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
define('DISPLAY_PRICE_WITH_TAX','true');

// check admin file config
// is a specific separator set?
if( isset($_POST['separator_input']) && $_POST['separator_input'] != '' ) {
	$separator = $_POST['separator_input'];
} else {
	// if nothing is entered by the admin: $separator gets \t as default
	$separator = "\t";
}

// is a specific quoting character set?
if( isset($_POST['quoting_input']) && $_POST['quoting_input'] != '' ) {
	$quoting = stripcslashes($_POST['quoting_input']);
} else {
	// if nothing is entered by the admin: $quoting is disabled
	$quoting = "";
}

// is a specific language set?
if( isset($_POST['language_input']) && $_POST['language_input'] != '' ) {
	$country_sc = stripslashes($_POST['language_input']);
} else {
	// if nothing is entered by the admin: $quoting is disabled
	$country_sc = "DE";
}
// file config
define('SEPARATOR',  $separator);  		// character that separates the data
define('QUOTECHAR',  $quoting);    		// character to quote the data
define('COUNTRY_SC', $country_sc);   		// country the shipping costs are for
define('DISPLAYINACTIVEMODULES', true); // display modules that are not active but in the payment array
										// advantage: structure of the file hardly changes  

require_once(DIR_FS_CATALOG.DIR_WS_CLASSES . 'xtcPrice.php');

//	if( $_GET['module'] == 'idealo' ) {
//		 print phpversion();
//	}

  class idealo {

    // these attributes have to be public, as module_export.php uses them directly ...
    public $code;
    public $title;
    public $description;
    public $enabled;

	// all payment (and its status) that should be displayed in the csv
	// if a payment is 'false', the column in the csv stays empty
	// the key needs to be the same as it is used in the db for the entry in `configuration_key` in the table `configuration`
	private $payment = array('MONEYORDER'   => array('active' => false,
													 'title' => 'Vorkasse'),
							 'COD' 			=> array('active' => false,
													 'title' => 'Nachnahme'),
							 'INVOICE' 		=> array('active' => false,
													 'title' => 'Rechnung'),
							 'CC' 			=> array('active' => false,
													 'title' => 'Kreditkarte'),
							 'BANKTRANSFER' => array('active' => false,
													 'title' => 'Lastschrift'),
							 'PAYPAL' 		=> array('active' => false,
													 'title' => 'PayPal'),
							 'MONEYBOOKERS' => array('active' => false,
													 'title' => 'Moneybookers'),
							 'UOS_GIROPAY'  => array('active' => false,
											 		 'title' => 'Giropay')
							);

	// types of shipping cost and 2-3 properties
	// this is neccessary to get the correct values for "cash on delivery"
	private $paymentTable = false;        // table sc
	private $paymentTableMode = 'weight'; // default mode for table sc

	private $paymentItem  = false;        // sc per item
	private $paymentFlat  = false;        // flat rate sc

	private $freeShipping = false;        // no sc
	private $freeShippingValue;           // calculates when shipping is free

	// table shipping
	private $paymentTableValues = array();

	// default shipping cost (does NOT count when modul "table shipping cost" is active)
	private $standardShippingCost = 0.00;

    public function __construct() {
      // $this->code = 'idealo';
      $this->code = 'idealo';
      $this->title = MODULE_IDEALO_TEXT_TITLE;
      $this->description = MODULE_IDEALO_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_IDEALO_SORT_ORDER;
      $this->enabled = ((MODULE_IDEALO_STATUS == 'True') ? true : false);
      $this->CAT=array();
      $this->PARENT=array();
      $this->productsPrice = 0;

      // check which payment method (cod, cash etc. ...) is active
      $this->checkActivePayment();

      // check which payment option (default, per item, table) is active
      $this->checkStandardShippingCostsOption();
    }

	/**
	 * Checks which payment method (pm) is active
	 * If a pm is not active, it wont appear in the csv
	 *
	 * A pm is only active when the entry 'MODULE_PAYMENT_{paymentmethod}_STATUS' in the table `configuration` exists
	 * and the `configuration_value` is 'true'
	 */
	private function checkActivePayment() {
		// run through every payment method
		foreach($this->payment as $singlePayment => $status) {
			// is the pm active?
			$checkPayment = xtc_db_query("SELECT COUNT(*) AS `found`
										  FROM `configuration`
										  WHERE `configuration_key` LIKE 'MODULE_PAYMENT_{$singlePayment}_STATUS'
										  AND `configuration_value` LIKE 'True';");

			$result = xtc_db_fetch_array($checkPayment);
			// if the result is > 0, the pm is active
			if($result['found'] > 0) {
				$this->payment[$singlePayment]['active'] = true;
			}
		}
	}

	/**
	 * Method returns the shipping cost for a specific payment method
	 *
	 * @param string $payment
	 * @param double|null $price
	 * @param double|null $offerWeight
	 *
	 * @return double|'' shipping costs else an empty string
	 */
	private function getShippingCosts($payment, $price = null, $offerWeight = null) {
		$shippingCost = '';

		// is the is payment active?
		if( $this->payment[$payment]['active'] === true ) {

			// is free delivery active and price equal or higher than the limit?
			if(($this->freeShipping) === true && ($price >= $this->freeShippingValue)) {
				$shippingCost = 0.00;
			}
			// is at least one shipping option active?
			elseif(($this->paymentTable === true) || ($this->paymentItem === true) || ($this->paymentFlat === true) ) {

				// first of all we get the standard shipping costs (default sc, per item or table)

				// are the table shipping costs active? Check which table payment option is active
				if($this->paymentTable === true) {

					// run through the table values and check which weight / price matches the offer
					switch($this->paymentTableMode) {
						case 'weight':
							$offerCompareValue = $offerWeight;
						break;
						case 'price':
							$offerCompareValue = $price;
						break;
					}

					if(is_array($this->paymentTableValues) && $offerCompareValue != null) {

						foreach($this->paymentTableValues as $tableModeValue => $tablePrice) {
							// stop the loop if sth. matched
							if($offerCompareValue <= $tableModeValue) {
								$shippingCost = $tablePrice;
								break;
							}
						}

						// If no weight / price was matched accordingly, the last entry in the array is taken
						if($shippingCost == '') {
							end($this->paymentTableValues); // Zeiger an letzte Stelle bewegen
							$shippingCost = current($this->paymentTableValues); // Wert ausgeben auf den der Zeiger aktuell zeigt
							reset($this->paymentTableValues); // Setze Zeiger wieder in Ausgangsposition
						}

					} else {
						// if the table sc values are not correct or the weight / price is null => nothing shall appear in the csv
						$shippingCost = '';
					}
				} else {
					$shippingCost = $this->standardShippingCost;
				}
			}

			// cod needs additional calculation
			// the additional cod_fee (if active) depends on the shipping option that is active as the fee can differ
			if($payment == 'COD') {
				 // check if extra fee for "Cash on Delivery" is active

				 // 1. get the db data
				$getCodExtraFeeStatus = xtc_db_query("SELECT `configuration_value` AS `cod_fee_status`
													  FROM `configuration`
													  WHERE `configuration_key` LIKE 'MODULE_ORDER_TOTAL_COD_FEE_STATUS';");

				$result = array();
				$result = xtc_db_fetch_array($getCodExtraFeeStatus);

				// 2. is the fee status active?
				if(isset($result['cod_fee_status']) && $result['cod_fee_status'] == 'true') {
					$modul = '';
					// which shipping option is active?
					if(($this->freeShipping) === true && ($price >= $this->freeShippingValue)) {
						$modul = 'MODULE_ORDER_TOTAL_FREEAMOUNT_FREE';
					} elseif($this->paymentTable === true) {
						$modul = 'MODULE_ORDER_TOTAL_COD_FEE_TABLE';
					} elseif($this->paymentItem === true) {
						$modul = 'MODULE_ORDER_TOTAL_COD_FEE_ITEM';
					} elseif($this->paymentFlat === true) {
						$modul = 'MODULE_ORDER_TOTAL_COD_FEE_FLAT';
					}

					$getCodCost = xtc_db_query("SELECT `configuration_value` AS `cod_cost`
												FROM `configuration`
												WHERE `configuration_key` LIKE '{$modul}';");

					unset($result);
					$result = array();
					$result = xtc_db_fetch_array($getCodCost);

					// Are there any costs?
					if(isset($result['cod_cost']) && $result['cod_cost'] != '') {
						// get the value for the country
						preg_match_all('/' . COUNTRY_SC . ':([^,]+)?/', $result['cod_cost'], $match);

						// $match[1][0] contains the result in the form of (e.g.) 7.00 or 7
						// to make sure that mistakes like 7.00:9.99 (correct would be 7,00:9.99) are also handled, we check for the colon
						if(preg_match('/:/', $match[1][0])) {
							$tmpArr = explode(':', $match[1][0]);
							$codCost = $tmpArr[0];
						} else {
							$codCost = $match[1][0];
						}

						// de we ge a useful value?
						if(isset($codCost) && $codCost != NULL && is_numeric($codCost)) {
							$shippingCost += $codCost;
						}
					}
				}

			}

			// calculate taxes
	        if (DISPLAY_PRICE_WITH_TAX == 'true') {
	            $tax = xtc_get_tax_rate_export(MODULE_SHIPPING_FLAT_TAX_CLASS, STORE_COUNTRY, MODULE_SHIPPING_FLAT_ZONE);
	            $shippingCost = xtc_add_tax($shippingCost, $tax);
	        }

			// format and round numbers
			$shippingCost = number_format($shippingCost, 2, '.', '');
		}


		return $shippingCost;
	}

	/**
	 * Method checks which standard shipping option is active.
	 *  - is the freeShipping active, $this->freeShipping = true
	 *  - is table sc option active, $this->paymentTable = true
	 *  - is table sc option NOT active, but sc per item, $this->paymentItem = true
	 *  - are neither table sc NOR sc per item, BUT the default sc active, $this->paymentFlat = true
	 *
	 * This is important for cash on delivery as there are different fee options possible.
	 */

	private function checkStandardShippingCostsOption() {
		// free shipping?
		if($this->checkShippingCostOption('FREEAMOUNT') > 0  ) {
			$this->freeShipping = true;

			// catch the limit for free shipping
			$getFreeamountValue = xtc_db_query("SELECT `configuration_value` AS `freeShippingValue`
												FROM `configuration`
											 	WHERE `configuration_key` LIKE 'MODULE_SHIPPING_FREEAMOUNT_AMOUNT';");

			$result = xtc_db_fetch_array($getFreeamountValue);

			// if the value of the free shipping value is not set, its 0.00 ( = always free)
			if(isset($result['freeShippingValue']) && is_numeric($result['freeShippingValue'])) {
				$this->freeShippingValue = $result['freeShippingValue'];
			} else {
				$this->freeShippingValue = 0.00;
			}
		}

		if($this->checkShippingCostOption('TABLE') > 0) {
			// table shipping cost
			$this->paymentTable = true;

			// set the values for table sc to get the correct sc for every offer
			$this->setPaymentTableValues();

		} elseif($this->checkShippingCostOption('ITEM') > 0) {
			// sc per item
			$this->paymentItem = true;

			// set the standard shipping costs
			$this->setStandardShippingCosts();
		} elseif($this->checkShippingCostOption('FLAT') > 0) {
			// flat sc
			$this->paymentFlat = true;

			// set the standard shipping costs
			$this->setStandardShippingCosts();
		}
	}

	/**
	 * Method sets the standard shipping costs (NOT the one for "table sc")
	 * The standard sc can consist of the "flat sc" OR the "sc per item"
	 * as the offer listing in the csv refers to ONE offer
	 */

	private function setStandardShippingCosts() {
		$shippingModul = '';

		if($this->paymentItem === true) {
			$shippingModul = 'MODULE_SHIPPING_ITEM_COST';
		} else {
			$shippingModul = 'MODULE_SHIPPING_FLAT_COST';
		}

		$getStandardShippingCosts = xtc_db_query("SELECT `configuration_value` AS `standard_sc`
												  FROM `configuration`
										 		  WHERE `configuration_key` LIKE '{$shippingModul}';");

		$result = xtc_db_fetch_array($getStandardShippingCosts);

		// if $result['standard_sc'] is not set, $this->standardShippingCost stays empty (to be on the safe side)
		if(isset($result['standard_sc'])) {
			$this->standardShippingCost = $result['standard_sc'];
		} else {
			$this->standardShippingCost = '';
		}
	}

	/**
	 * Method checks if a specific shipping costs option is activated
	 *
	 * @param string $option
	 *
	 * @return integer 0 when nothing is found, otherwise a number bigger than 0
	 */
	private function checkShippingCostOption($option) {
		// transform to uppercase
		$option = strtoupper($option);
		$checkOption = xtc_db_query("SELECT COUNT(*) AS `found`
										 FROM `configuration`
										 WHERE `configuration_key` LIKE 'MODULE_SHIPPING_{$option}_STATUS'
										 AND `configuration_value` LIKE 'True';");

		$result = xtc_db_fetch_array($checkOption);

		// if $result['found'] is not set, 0 (option is not activated) will be returned
		return ( isset($result['found']) ) ? $result['found'] : 0;
	}

	/**
	 * Method sets the "table shipping costs" values
	 */
	private function setPaymentTableValues() {
		$explodedValues = array();

		// take the data from the db
		$getValues = xtc_db_query("SELECT `configuration_value` AS `table_values`
								   FROM `configuration`
								   WHERE `configuration_key` LIKE 'MODULE_SHIPPING_TABLE_COST';");

		$result = xtc_db_fetch_array($getValues);

		// the result shouldnt be empty
		// otherwise $this->paymentTableValues stays empty
		// example string: 25:8.50,50:5.50,10000:0.00

		if( isset($result['table_values']) && $result['table_values'] != '') {
			// split die Value at the comma
			$explodedValues = explode(',', $result['table_values']);

			// run through the values and split again at the colon
			// the key is the weight / price and the value is the sc
			foreach($explodedValues as $values) {
				$tmpAr = array();
				$tmpAr = explode(":", $values);

				// are there only numbers?
				if( is_numeric($tmpAr[0]) && is_numeric($tmpAr[1]) ) {
					$this->paymentTableValues[$tmpAr[0]] = $tmpAr[1];
				}
				unset($tmpAr);
			}
		}

		// check what param is used for "table sc": weight or price
		$getPaymentTableMode = xtc_db_query("SELECT `configuration_value` AS `table_mode`
								   			 FROM `configuration`
								   			 WHERE `configuration_key` LIKE 'MODULE_SHIPPING_TABLE_MODE';");
		$result = xtc_db_fetch_array($getPaymentTableMode);
		if(isset($result['table_mode']) && $result['table_mode'] != '') {
			$this->paymentTableMode = $result['table_mode'];
		}
	}

	/**
	 * Methode creates the content of the csv
	 *
	 * @param string $file
	 */
    public function process($file) {
		$schema = '';
        @xtc_set_time_limit(0);
        $xtPrice = new xtcPrice($_POST['currencies'],$_POST['status']);

        $schema .= QUOTECHAR . 'artikelId' . QUOTECHAR . SEPARATOR .
				   QUOTECHAR . 'hersteller' . QUOTECHAR . SEPARATOR .
        		   QUOTECHAR . 'bezeichnung' . QUOTECHAR . SEPARATOR .
        		   QUOTECHAR . 'kategorie' . QUOTECHAR . SEPARATOR .
        		   QUOTECHAR . 'beschreibung_kurz' . QUOTECHAR . SEPARATOR .
        		   QUOTECHAR . 'beschreibung_lang' . QUOTECHAR . SEPARATOR .
        		   QUOTECHAR . 'bild' . QUOTECHAR . SEPARATOR .
        		   QUOTECHAR . 'deeplink' . QUOTECHAR . SEPARATOR .
        		   QUOTECHAR . 'preis' . QUOTECHAR . SEPARATOR .
        		   QUOTECHAR . 'ean' . QUOTECHAR . SEPARATOR .
        		   QUOTECHAR . 'lieferzeit' . QUOTECHAR . SEPARATOR;

		// run through the payment method titles to display them in the header
		foreach($this->payment as $payment => $options) {
			// display only the payment methods that are active (if this is desired)
			if($options['active'] === true || DISPLAYINACTIVEMODULES === true) {
				$schema .= QUOTECHAR . $options['title'] . QUOTECHAR . SEPARATOR;
			}
		}

        $schema .= "\n";

        $export_query =xtc_db_query("SELECT
                             p.products_id,
                             pd.products_name,
                             pd.products_description,pd.products_short_description,
                             p.products_model,p.products_ean,
                             p.products_image,
                             p.products_price,
                             p.products_status,
                             p.products_date_available,
                             p.products_shippingtime,
                             p.products_discount_allowed,
                             pd.products_meta_keywords,
                             p.products_tax_class_id,
                             p.products_date_added,
                             p.products_weight,
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
            $this->productsPrice = $products_price;

            // get product categorie
            $categorie_query=xtc_db_query("SELECT
                                            categories_id
                                            FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                            WHERE products_id='".$products['products_id']."'");
             while ($categorie_data=xtc_db_fetch_array($categorie_query)) {
                    $categories=$categorie_data['categories_id'];
             }


            // remove trash

            // characters that should be replaced
			$spaceToReplace = array("<br>", "<br />", "\n", "\r", "\t", "\v", chr(13)); // replace by space
			$commaToReplace = array("'");  												// replace by comma
			$quoteToReplace = array("&quot,", "&qout,");								// replace by quote ( " )

			// replace characters and cut to the appropriate length
            $products_description = strip_tags($products['products_description']);
			$products_description = str_replace($spaceToReplace," ",$products_description);
			$products_description = str_replace($commaToReplace,", ",$products_description);
			$products_description = str_replace($quoteToReplace," \"",$products_description);

			$products_description = substr($products_description, 0, 65536);

            $products_short_description = strip_tags($products['products_short_description']);
			$products_short_description = str_replace($spaceToReplace," ",$products_short_description);
			$products_short_description = str_replace($commaToReplace,", ",$products_short_description);
			$products_short_description = str_replace($quoteToReplace," \"",$products_short_description);

            $products_short_description = substr($products_short_description, 0, 255);

			$cat = $this->buildCAT($categories);


	if ($products['products_image'] != ''){
	    $image = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_ORIGINAL_IMAGES .$products['products_image'];
	}else{
	    $image = '';
	}
            //create content
            $schema .= QUOTECHAR . $products['products_id'] . QUOTECHAR . SEPARATOR .
                       QUOTECHAR . $products['manufacturers_name']. QUOTECHAR . SEPARATOR .
                       QUOTECHAR . $products['products_name'] . QUOTECHAR . SEPARATOR .
                       QUOTECHAR . substr($cat,0,strlen($cat)-2) . QUOTECHAR. SEPARATOR .
                       QUOTECHAR . $products_short_description . QUOTECHAR . SEPARATOR .
                       QUOTECHAR . $products_description . QUOTECHAR . SEPARATOR .
                       QUOTECHAR . $image . QUOTECHAR . SEPARATOR .
                       QUOTECHAR . HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'product_info.php?'.$_POST['campaign'].xtc_product_link($products['products_id'], $products['products_name']) . QUOTECHAR . SEPARATOR .
                       QUOTECHAR . number_format($products_price,2,'.','') . QUOTECHAR . SEPARATOR .
                       QUOTECHAR . $products['products_ean'] . QUOTECHAR . SEPARATOR .
                       QUOTECHAR . xtc_get_shipping_status_name($products['products_shippingtime']) . QUOTECHAR . SEPARATOR;

					   // run through the payment methods to display the fee
				       foreach($this->payment as $singlePayment => $options) {
					   		// display only the payment fee that is active (if this is desired)
				        	if($options['active'] === true || DISPLAYINACTIVEMODULES === true) {
								$schema .= QUOTECHAR . $this->getShippingCosts($singlePayment, $products_price, $products['products_weight']) . QUOTECHAR . SEPARATOR;
				        	}
				        }

			$schema .= "\n";
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

   /**
    * Methods creates the Categorie for a categorieId
    *
    * @param int $catID
    * @return string Category
    */
   private function buildCAT($catID) {
		if (isset($this->CAT[$catID])) {
		 return  $this->CAT[$catID];
		} else {
		   $cat=array();
		   $tmpID=$catID;

		   while ($this->getParent($catID)!=0 || $catID!=0) {
		        $cat_select=xtc_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id='".$catID."' and language_id='".$_SESSION['languages_id']."'");
		  	    $cat_data=xtc_db_fetch_array($cat_select);
		    	$catID=$this->getParent($catID);
		    	$cat[]=$cat_data['categories_name'];
		   }

		   $catStr='';
		   for ($i=count($cat);$i>0;$i--) {
		      $catStr.=$cat[$i-1].' > ';
		   }
		   $this->CAT[$tmpID]=$catStr;

		  return $this->CAT[$tmpID];
		}
    }

	/**
	 * Method returns the parentId of a categoryId
	 *
	 * @param int $catID
	 * @return int parent id of the category
	 */
   private function getParent($catID) {
      if (isset($this->PARENT[$catID])) {
       return $this->PARENT[$catID];
      } else {
       $parent_query=xtc_db_query("SELECT parent_id FROM ".TABLE_CATEGORIES." WHERE categories_id='".$catID."'");
       $parent_data=xtc_db_fetch_array($parent_query);
       $this->PARENT[$catID]=$parent_data['parent_id'];
       return  $parent_data['parent_id'];
      }
    }

	/**
	 * Method prepares the text that is displayed at the detailed options on module_export.php
	 */
    public function display() {

	    $customers_statuses_array = xtc_get_customers_statuses();

	    // build Currency Select
	    $curr='';
	    $currencies=xtc_db_query("SELECT code FROM ".TABLE_CURRENCIES);
	    while ($currencies_data=xtc_db_fetch_array($currencies)) {
	     $curr.=xtc_draw_radio_field('currencies', $currencies_data['code'],true).$currencies_data['code'].'<br>';
	    }

	    $campaign_array = array(array('id' => '', 'text' => TEXT_NONE));
		$campaign_query = xtc_db_query("select campaigns_name, campaigns_refID from ".TABLE_CAMPAIGNS." order by campaigns_id");
		while ($campaign = xtc_db_fetch_array($campaign_query)) {
		$campaign_array[] = array ('id' => 'refID='.$campaign['campaigns_refID'].'&', 'text' => $campaign['campaigns_name'],);
		}

	    return array('text' =>
	    						'<br>' . FIELDSEPARATOR . '<br>' .
	    						FIELDSEPARATOR_HINT . '<br>' .
	    						xtc_draw_small_input_field('separator_input', ';') . '<br><br>' .
	    						QUOTING . '<br>' .
	    						QUOTING_HINT . '<br>' .
	    						xtc_draw_small_input_field('quoting_input', '"') . '<br><br>' .
	    						LANGUAGE . '<br>' .
	    						LANGUAGE_HINT . '<br>' .
	    						xtc_draw_small_input_field('language_input', 'DE') . '<br>' .
	    						EXPORT_STATUS_TYPE.'<br>'.
	                          	EXPORT_STATUS.'<br>'.
	                          	xtc_draw_pull_down_menu('status',$customers_statuses_array, '1').'<br>'.
	                            CURRENCY.'<br>'.
	                            CURRENCY_DESC.'<br>'.
	                            $curr.
	                            CAMPAIGNS.'<br>'.
	                            CAMPAIGNS_DESC.'<br>'.
	                          	xtc_draw_pull_down_menu('campaign',$campaign_array).'<br>'.
	                            EXPORT_TYPE.'<br>'.
	                            EXPORT.'<br>'.
	                          	xtc_draw_radio_field('export', 'no',false).EXPORT_NO.'<br>'.
	                            xtc_draw_radio_field('export', 'yes',true).EXPORT_YES.'<br>'.
	                            '<br>' . xtc_button(BUTTON_EXPORT) .
	                            xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=idealo')));


    }

    public function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_IDEALO_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

	/**
	 * Method installs a module in module_export.php
	 */
    public function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_IDEALO_FILE', 'idealo.csv',  '6', '1', '', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_IDEALO_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
    }

	/**
	 * Method removes a module
	 */
    public function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    public function keys() {
      return array('MODULE_IDEALO_STATUS','MODULE_IDEALO_FILE');
    }
  }
?>