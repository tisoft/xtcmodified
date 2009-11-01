<?php


// -----------------------------------------------------------------------------------------
//	gunnart_productRedirect.inc.php
// -----------------------------------------------------------------------------------------
//	Request-Adresse mit "aktuellem"-Artikel-Link vergleichen und ggf. weiterleiten
// -----------------------------------------------------------------------------------------
//	Gunnar Tillmann / http://www.gunnart.de
//	im Oktober/Dezember 2008
// 	
//	N�here Infos: http://www.gunnart.de?p=379
//	
//	v 0.13
// -----------------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------------
	function ProductRedirectionLink($ProdID=false) {
		
		if($ProdID) {
			
			if($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
				$fsk_lock = "AND p.products_fsk18!=1 ";
			}
			if(GROUP_CHECK == 'true') {
				$group_check = "AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
			}
			$dbQuery = xtDBquery("
				SELECT 	p.products_id, 
						pd.products_name
				FROM 	".TABLE_PRODUCTS_DESCRIPTION." pd,
						".TABLE_PRODUCTS." p
				WHERE 	pd.products_id = '".intval($ProdID)."'
				AND 	pd.products_id = p.products_id
				".$fsk_lock."
				".$group_check."
				AND		p.products_status = '1'
				AND 	pd.language_id = '".(int)$_SESSION['languages_id']."' 
			");
			$dbQuery = xtc_db_fetch_array($dbQuery,true);
			
			if(!empty($dbQuery['products_id'])) {
				return xtc_href_link(FILENAME_PRODUCT_INFO,xtc_product_link(intval($ProdID),$dbQuery['products_name']));
			}
		}
		return false;
	}
// -----------------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------------
//	Auf den "gew�nschten Artikel-Link umleiten
// -----------------------------------------------------------------------------------------
	function productRedirect() {
		// Wenn wir auf ner Produkt-Info-Seite sind
		if(basename($_SERVER['SCRIPT_NAME']) == FILENAME_PRODUCT_INFO) {
		
			global $actual_products_id;
			
			// Link zum Weiterleiten (MIT Session-ID)
			//$RedirectionLink = xtc_href_link(FILENAME_PRODUCT_INFO,xtc_product_link($actual_products_id));
			$RedirectionLink = ProductRedirectionLink($actual_products_id);
			
			// Wenn es den Artikel gibt
			if($RedirectionLink) { 
				
				// Gew�nschter Link (OHNE http/https-Zeug, Session-ID und weitere $_GET-Parameter)
				$ProductLink = str_replace(array(HTTP_SERVER,HTTPS_SERVER),'',preg_replace("/([^\?]*)(\?.*)/","$1",$RedirectionLink));
				
				// Angefragte Adresse (OHNE Session-ID und weitere $_GET-Parameter)
				$CurrentLink = preg_replace("/([^\?]*)(\?.*)/","$1",$_SERVER['REQUEST_URI']);
				
				// 301er-Weiterleitung mit Unterscheidung SSL / kein SSL
				if(strpos($ProductLink, $CurrentLink) === false) {
					if ( (ENABLE_SSL == true) && (getenv('HTTPS') == 'on' || getenv('HTTPS') == '1') ) { // Bei aktivem SSL 
						if (substr($RedirectionLink, 0, strlen(HTTP_SERVER)) == HTTP_SERVER) { 
							$RedirectionLink = HTTPS_SERVER . substr($RedirectionLink, strlen(HTTP_SERVER)); 
						}
					}
					header('HTTP/1.1 301 Moved Permanently' );
					header('Location: '.eregi_replace("[\r\n]+(.*)$","",$RedirectionLink));            
				}
			
			// Wenn es den Artikel nicht gibt
			} else {
				
				// 404er-Weiterleitung
				$DefaultLink = xtc_href_link(FILENAME_DEFAULT); // <-- Hier Fehlerseite festlegen ...
				if ( (ENABLE_SSL == true) && (getenv('HTTPS') == 'on' || getenv('HTTPS') == '1') ) { // Bei aktivem SSL 
					if (substr($DefaultLink, 0, strlen(HTTP_SERVER)) == HTTP_SERVER) { 
						$DefaultLink = HTTPS_SERVER . substr($DefaultLink, strlen(HTTP_SERVER)); 
					}
				}
				header('HTTP/1.1 404 Not Found' );
				header('Location: '.eregi_replace("[\r\n]+(.*)$","",$DefaultLink));            
			}
		}
	}
// -----------------------------------------------------------------------------------------
	productRedirect(); // <-- ... und los!
// -----------------------------------------------------------------------------------------


?>