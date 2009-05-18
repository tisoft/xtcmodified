<?php
// $Id: wsdl_iclear_order.php,v 1.12 2006/10/26 16:20:46 dis Exp $
/*
  iclear WSDL order service

  iclear payment system - because secure is simply secure
  http://www.iclear.de

  Copyright (c) 2001 - 2006 iclear

  Released under the GNU General Public License

************************************************************************
  Copyright (C) 2005 - 2007 BSE, David Brandt

                    All rights reserved.

  This program is free software licensed under the GNU General Public License (GPL).

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
  USA

*************************************************************************/

// setup osc environment
  require('includes/configure.php');
  if (strlen(DB_SERVER) < 1) {
    if (is_dir('install')) {
      header('Location: install/index.php');
    }
  }
  define('PROJECT_VERSION', 'osCommerce 2.2-MS2');
  $request_type = (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

  if (!isset($PHP_SELF)) $PHP_SELF = $HTTP_SERVER_VARS['PHP_SELF'];
  if ($request_type == 'NONSSL') {
    define('DIR_WS_CATALOG', DIR_WS_HTTP_CATALOG);
  } else {
    define('DIR_WS_CATALOG', DIR_WS_HTTPS_CATALOG);
  }

  // include the list of project filenames
  require (DIR_WS_INCLUDES.'filenames.php');

  // include the list of project database tables
  require (DIR_WS_INCLUDES.'database_tables.php');

  // SQL caching dir
  define('SQL_CACHEDIR', DIR_FS_CATALOG.'cache/');

// Database
  require_once (DIR_FS_INC.'xtc_db_connect.inc.php');
  require_once (DIR_FS_INC.'xtc_db_close.inc.php');
  require_once (DIR_FS_INC.'xtc_db_error.inc.php');
  require_once (DIR_FS_INC.'xtc_db_perform.inc.php');
  require_once (DIR_FS_INC.'xtc_db_query.inc.php');
  require_once (DIR_FS_INC.'xtc_db_queryCached.inc.php');
  require_once (DIR_FS_INC.'xtc_db_fetch_array.inc.php');
  require_once (DIR_FS_INC.'xtc_db_num_rows.inc.php');
  require_once (DIR_FS_INC.'xtc_db_data_seek.inc.php');
  require_once (DIR_FS_INC.'xtc_db_insert_id.inc.php');
  require_once (DIR_FS_INC.'xtc_db_free_result.inc.php');
  require_once (DIR_FS_INC.'xtc_db_fetch_fields.inc.php');
  require_once (DIR_FS_INC.'xtc_db_output.inc.php');
  require_once (DIR_FS_INC.'xtc_db_input.inc.php');
  require_once (DIR_FS_INC.'xtc_db_prepare_input.inc.php');
  require_once (DIR_FS_INC.'xtc_get_top_level_domain.inc.php');

// make a connection to the database... now
  xtc_db_connect() or die('Unable to connect to database server!');

  $configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from '.TABLE_CONFIGURATION);
  while ($configuration = xtc_db_fetch_array($configuration_query)) {
  	define($configuration['cfgKey'], $configuration['cfgValue']);
  }

// loading osc internal stuff

require('includes/iclear/iclear_catalog.php');

// WSDL stuff goes here
$server = new soap_server();
$server->soap_defencoding = 'UTF-8';
$server->configureWSDL('SH_ICOrderServices', 'urn:SH_ICOrderServices');
$server->wsdl->schemaTargetNameSpace = 'urn:SH_ICOrderServices';

// define incoming address element
$server->wsdl->addComplexType(
																'Address',
																'complexType',
																'array',
																'all',
																'',
																array(
																	'addrAnrede'	=> array('name'	=> 'addrAnrede', 'type' => 'xsd:string'),
																	'addrFirstname'	=> array('name'	=> 'addrFirstname', 'type' => 'xsd:string'),
																	'addrLastname'	=> array('name'	=> 'addrLastname', 'type' => 'xsd:string'),
																	'addrOrgname'	=> array('name'	=> 'addrOrgname', 'type' => 'xsd:string'),
																	'addrStrasse'	=> array('name'	=> 'addrStrasse', 'type' => 'xsd:string'),
																	'addrHausNr'	=> array('name'	=> 'addrHausNr', 'type' => 'xsd:string'),
																	'addrPLZ'	=> array('name'	=> 'addrPLZ', 'type' => 'xsd:string'),
																	'addrOrt'	=> array('name'	=> 'addrOrt', 'type' => 'xsd:string'),
																	'addrLand'	=> array('name'	=> 'addrLand', 'type' => 'xsd:string')
																)
															);

// define incoming basketItem element
$server->wsdl->addComplexType(
																'BasketItem',
																'complexType',
																'struct',
																'all',
																'',
																array(
																	'itemNr'	=> array('name'	=> 'itemNr', 'type' => 'xsd:string'),
																	'title'	=> array('name'	=> 'title', 'type' => 'xsd:string'),
																	'numOfArtikel'	=> array('name'	=> 'numOfArtikel', 'type' => 'xsd:long'),
																	'priceN'	=> array('name'	=> 'priceN', 'type' => 'xsd:string'),
																	'priceB'	=> array('name'	=> 'priceB', 'type' => 'xsd:string'),
																	'ustSatz'	=> array('name'	=> 'ustSatz', 'type' => 'xsd:string'),
																	'Status'	=> array('name'	=> 'Status', 'type' => 'xsd:string')
																)
															);

// define incoming basketItemList element
$server->wsdl->addComplexType(
																'BasketItemList',
																'complexType',
																'struct',
																'all',
																'',
																array(
																	'BasketItem'	=> array('name'	=> 'BasketItem', 'type' => 'tns:BasketItem')
																)
															);


$server->register(
									'acceptOrder',
									array(
										'sessionID' => 'xsd:string',
										'basketID' => 'xsd:string',
										'currency' => 'xsd:string',
										'orderStatus' => 'xsd:long',
										'orderStatusMessage' => 'xsd:string',
										'deliveryAddress' => 'tns:Address',
										'requestID' => 'xsd:string',
										'BasketItemList' => 'tns:BasketItemList'
									),
									array(
										'requestID' => 'xsd:string',
										'status' => 'xsd:long',
										'statusMessage' => 'xsd:string',
										'shopURL' => 'xsd:string'
									),
									'urn:SH_ICOrderServices',
									'urn:SH_ICOrderServices#acceptOrder',
									'rpc',
									'encoded',
									'Iclear shop side order web service'
								);

/**
 * WSDL acceptOrder function
 *
 * @param string $sessionID
 * @param string $basketID
 * @param string $currency
 * @param int $orderStatus
 * @param string $orderStatusMessage
 * @param int $requestID
 * @param array $deliveryAddress
 * @param array $basketItemList
 * @return array (SOAP)
 */
function acceptOrder($sessionID, $basketID, $currency, $orderStatus, $orderStatusMessage, $requestID, $deliveryAddress, $basketItemList) {
	$iclearWsdl = new iclearCatalog();

// calculate the gros value of the incoming basket 2 compare it with stored order
  $basketValue = 0;

  if(is_array($basketItemList['BasketItem']) && sizeof($basketItemList['BasketItem'])) {
    foreach($basketItemList['BasketItem'] AS $id => $rec) {
      $basketValue += $rec['priceB'];
    }
  }

// first check data integrity and if this basket is known
  if(!$sessionID) {
    $iclearWsdl->addError('acceptOrder: no sessionID found!');
    $orderStatusResponse = 101;
  }elseif(!$basketID) {
    $iclearWsdl->addError('acceptOrder: no basketID found!');
    $orderStatusResponse = 102;
  } elseif(!$basketValue) {
    $iclearWsdl->addError('acceptOrder: no value of basket is zero!');
    $orderStatusResponse = 103;
  } elseif(!$iclearWsdl->loadWsdlOrder($sessionID, $basketID)) {
    $iclearWsdl->addError('acceptOrder: order specified by basketID / sessionID not found!');
    $orderStatusResponse = 400;
  } elseif((int)$iclearWsdl->wsdlOrder['status'] == 2) {
// order is already cancelled
    $iclearWsdl->addError('acceptOrder: order cancelled @ ' . $iclearWsdl->wsdlOrder['accepted'] . '! Status: ' . $iclearWsdl->wsdlOrder['status_message']);
    $orderStatusResponse = 501;
  }elseif($iclearWsdl->wsdlOrder['accepted'] != '0000-00-00 00:00:00') {
     $iclearWsdl->addError('acceptOrder: order already accepted @ ' . $iclearWsdl->wsdlOrder['accepted'] . '!');
     $orderStatusResponse = 401;
  } elseif($orderStatus == 2) {
    $iclearWsdl->wsdlOrder['status'] = $orderStatus;
    $orderStatusResponse = $iclearWsdl->cancelOrder();
  } elseif(($iclearWsdl->wsdlOrder['basket_value'] - $basketValue) > 10) {
// basket values differ
     $iclearWsdl->addError('acceptOrder: basket value and stored order value differ more than 0.10 EUR!');
     $orderStatusResponse = 402;
  } elseif ((int)$iclearWsdl->wsdlOrder['orders_id'] && (int)$iclearWsdl->wsdlOrder['status'] == 1) {
// this is a lagging order, which is should be released now
      if(!(int) $iclearWsdl->wsdlOrder['orders_id']) {
        $orderStatusResponse = 600;
        $iclearWsdl->addError('accepptOrder: OSC order id not assigned!');
      } elseif($orderStatus == 1 && $iclearWsdl->checkLaggingOrder()) {
// in this case the error is supplied by the class
        $orderStatusResponse = 601;
      } else {
				switch($orderStatus) {
					case 0:
		        $iclearWsdl->acceptLaggingOrder($orderStatus);
		        $orderStatusResponse = 0;
						break;

					default:
						$iclearWsdl->addError('Unknown iclear state (' . $orderStatus . ') for lagging order detected! Basket dropped!');
						$orderStatusResponse = 999;
				}
      }
    } else {
// looks good update or cancel order...
      $orderStatusResponse = 0;
      switch($orderStatus) {
       	case '0': # everything in sane
          $iclearWsdl->acceptOrder($orderStatus);
          break;
// it's a waitstate (HWK) accepted is 0000-00-00 00:00:00
     	case '1':
      	  $orderStatusResponse = $iclearWsdl->acceptLaggingOrder($orderStatus);
      	        		  break;

      	default: # it's a unknown case!
      	  $iclearWsdl->addError('Unknown iclear state detected! Basket dropped!');
      	  $orderStatusResponse = 999;
     }
   }

// prepare return URI
   $returnURL = (ENABLE_SSL ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG;
   if($iclearWsdl->isError()) {
      $returnURL .= FILENAME_CHECKOUT_CONFIRMATION;
     $orderStatusMessageResponse = $iclearWsdl->getErrorString();
   } else {
     $returnURL .=  FILENAME_CHECKOUT_PROCESS;
     $orderStatusMessageResponse = 'OK';
   }
   $returnURL .= '?XTCsid=' . $sessionID;

   $wsdlOut = array(
    								'sessionID' 	=> $sessionID,
    								'requestID' => $requestID,
    								'status' 		=> $orderStatusResponse,
    								'statusMessage' => $orderStatusMessageResponse,
    								'shopURL' => $returnURL
     							  );
	 return $wsdlOut;
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
// removing bogus namespace definitions - nusoap doesn't like it!
$HTTP_RAW_POST_DATA  = preg_replace('/xmlns=""/', '', $HTTP_RAW_POST_DATA);
$server->service($HTTP_RAW_POST_DATA);
?>