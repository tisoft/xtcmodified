<?php
// $Id: iclear_catalog.php,v 1.3 2007/03/18 20:01:02 dis Exp $

/*
  iclear payment system - because secure is simply secure
  http://www.iclear.de

  Copyright (c) 2001 - 2007 iclear

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

  include(DIR_FS_CATALOG . 'includes/iclear/iclear_wsdl.php');
  include(DIR_FS_CATALOG . 'includes/iclear/iclear_config.php');
// XTC hack
  if(!function_exists('xtc_round')) {
    require (DIR_FS_INC . 'xtc_round.inc.php');
  }


  /**
   * The iclearCatlog class acts as a wrapper which transforms the XTC internal data format
   * to that of the underlying iclearWsdl class
   *
   */
  class iclearCatalog extends iclearWSDL {
    var $order = false;
    var $basketValue = 0;
    /**
     * constructor
     * if shopID is given a WSDL proxy is initiated
     *
     * @param int $shopID
     * @return iclearCatalog
     */
    function iclearCatalog($shopID = 0) {
      $rc = false;
// no shopID indicates, that we're may be in WSDL mode -> asynchronous call
      if(!$shopID) {
        $rc = true;
      } else {
// initialize the WSDL client
        $rc = $this->iclearWSDL('order', $shopID);
      }
      return $rc;
    }

    /**
     * wrapper 2 set ISO compliant wsdl interface language
     *
     * @param int $languageID
     */
    function _setLanguage($languageID) {
      $lang = 'DE'; // default
      if($languageID) {
        switch($languageID) {
          case 1:
            $lang = 'US';
            break;
          case 3:
            $lang = 'ES';
        }
      }
      return $this->setWsdlLanguage($lang);
    }

    /**
     * formats osc basket content 2 iclear basketItem format and setup wsdl basket
     *
     * @return array basketItemList
     */
    function _processOrder() {
      global $shipping_modules;
      $this->basket = false;
      if (!is_object($this->order)) {
        $this->addError('_processOrder: No order loaded!');
      } else {
        $basket = array();
        $order =& $this->order;
        for ($i=0; $i<sizeof($order->products); $i++) {
          $finalPrice = xtc_round($order->products[$i]['price'] * 100, 0);
          $item['itemNr'] = $order->products[$i]['model'];
          $item['title'] = utf8_encode($order->products[$i]['name']);
// adding the attributes to the title
          if(is_array($order->products[$i]['attributes'])) {
            foreach($order->products[$i]['attributes'] AS $rec) {
              $item['title'] .= utf8_encode(' | ' . $rec['option'] . ': ' . $rec['value']);
            }
          }
          $item['numOfArtikel'] = $order->products[$i]['qty'];
//          $item['priceN'] = (int) xtc_round($finalPrice / (($order->products[$i]['tax']/100) + 1), 0);
          $item['priceN'] = (int) xtc_round($finalPrice / (($order->products[$i]['tax']/100) + 1), 0);
          $item['priceB'] = (int) $finalPrice;
          $item['ustSatz'] = xtc_round($order->products[$i]['tax'], 2);
          $this->basketValue += $item['priceB'];
          array_push($basket, $item);
        }

        if($order->info['shipping_method']) {
// get tax rate
// get shipping costs
          $modules = $shipping_modules->quote();
          $taxRate = $modules[0]['tax'];
          $item['priceN'] = $_SESSION['shipping']['cost'] * 100;
          if(preg_match('/freeamount/', $order->info['shipping_class'])) {
            $item['priceB'] = $item['priceN'];
            $taxRate = '0.0';
          } else {
            $item['priceB'] = xtc_round(xtc_add_tax($_SESSION['shipping']['cost'], $taxRate) * 100, 0);
          }
          $item['itemNr'] = 0; // in osc exists no zero itemID
          $item['title'] = 'Versandkosten';
          $item['numOfArtikel'] = 1;
          $item['ustSatz'] = $taxRate;
          if($item['ustSatz'] == null) {
            $this->addError('Process basket: Tax class for shipping type "' . $modules[0]['module']. '" is not set!');
          }

          array_push($basket, $item);
          $this->basketValue += $item['priceB'];
        }

// check if low orderfee is given
        if(isset($GLOBALS['ot_loworderfee']) && $GLOBALS['ot_loworderfee']->enabled && sizeof($GLOBALS['ot_loworderfee']->output)) {
          $sql = 'SELECT tax_rate FROM ' . TABLE_TAX_RATES . ' tax, ' . TABLE_CONFIGURATION . ' config WHERE config.configuration_key="MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS" AND tax.tax_class_id = config.configuration_value';

          $taxQry = xtc_db_query($sql);
          if($taxQry) {
            $taxRes = xtc_db_fetch_array($taxQry);
            $taxRate = xtc_round($taxRes['tax_rate'], 1);
          } else {
            $taxRate = '0.0';
          }

          $sql = 'SELECT configuration_value AS loworderfee FROM ' . TABLE_CONFIGURATION . ' WHERE configuration_key="MODULE_ORDER_TOTAL_LOWORDERFEE_FEE"';
          $feeQry = xtc_db_query($sql);
          if($feeQry) {
            $feeRes = xtc_db_fetch_array($feeQry);
            $item['priceN'] = (int) xtc_round($feeRes['loworderfee'] * 100, 0);
            $item['priceB'] = (int) xtc_round($item['priceN'] + $item['priceN'] * 0.01 * $taxRate, 0);
          } else {
            $item['priceN'] = $item['priceB'] = 0;
          }
          $item['itemNr'] = -1; // in osc exists no negative itemNumbers
          $item['title'] = 'Mindermengenzuschlag';
          $item['numOfArtikel'] = 1;
          $item['ustSatz'] = $taxRate;
          array_push($basket, $item);
          $this->basketValue += $item['priceB'];
        }

// check 4 coupons
        $class = 'ot_gv';
        if(isset($GLOBALS['ot_gv']) && $GLOBALS[$class]->enabled) {
          $gvNr = -2;
					for ($x = 0, $y = sizeof($GLOBALS[$class]->output); $x < $y; $x++) {
					  $item['priceN'] = $item['priceB'] = (int) xtc_round(-100 * $GLOBALS[$class]->output[$x]['value'], 0);
            $item['itemNr'] = $gvNr; // in osc exists no negative itemNumbers
            $item['title'] = 'Gutschein ' . ( $x + 1);
            $item['numOfArtikel'] = 1;
            $item['ustSatz'] = '0.0';
            array_push($basket, $item);
            $this->basketValue += $item['priceB'];
					}

        }

        $this->basket =& $basket;
      }
      return $this->basket;
    } //_processOrder

    /**
     * perpare wsdl basket and send order 2 iclear wsdl interface
     *
     * @param object $order
     * @param int_type $languageID
     * @return array wsdlResult
     */
    function sendOrder(&$order, $languageID) {
      $iclearWsdlResult =& $_SESSION['iclearWsdlResult'];
      if(!empty($iclearWsdlResult)) {
// delete exiting order from iclear table
        $this->deleteWsdlOrder($iclearWsdlResult['sessionID'], $iclearWsdlResult['basketID']);
        $iclearWsdlResult = null;
      }
      if(!$this->shopID) {
        $this->addError('sendOrder: No shop id found!');
      } elseif (!is_object($order)) {
        $this->addError('sendOrder: Order is no object or not present!');
      } else {
        $this->order =& $order;
        $this->iclearWSDL('order', $this->shopID, $this->sessionID);
        if($basket = $this->_processOrder()) {
          $this->_setLanguage($languageID);
          $this->setWsdlCurrency($order->info['currency']);
          if(!$this->setWsdlBasket($basket)) {
            $this->addError('sendOrder: Error while setting WSDL basket!');
          }elseif(!$this->sendWsdlOrder()) {
// error case...
            $this->addError('sendOrder: Error while sending WSDL order!');
          } else {
            $iclearWsdlResult = $this->wsdlResult;
            if(!xtc_session_is_registered('iclearWsdlResult')) {
              xtc_session_register('iclearWsdlResult');
            }
            $this->storeWsdlOrder();
// order sended, check result;
          }
        }
        return $this->wsdlResult;
      }
    } //sendOrder()

/****************************************************************************
  wsdl stuff: all modifications requre only session_id / basket_id
****************************************************************************/

    /**
     * store wsdl order in internal iclear table
     *
     */
    function storeWsdlOrder() {
      $iclearWsdlResult =& $_SESSION['iclearWsdlResult'];
      if(!$this->wsdlResult){
        $this->addError('storeWsdlOrder: No WSDL result present!');
      } else {
        $qry = 'INSERT INTO ' . TABLE_ICLEAR_ORDERS . '(basket_id, request_id, session_id, status, status_message, basket_value) VALUES ("' . $this->wsdlResult['basketID'] .'", "'. $this->wsdlResult['requestID'] .'", "'. $this->sessionID .'", "'. $this->wsdlResult['status'] .'", "'. $this->wsdlResult['statusMessage'] . '", "'. $this->basketValue . '")';
        if(!xtc_db_query($qry)) {
          $iclearWsdlResult = false;
        }
      }
    } //storeWsdlOrder


    /**
     * load wsdl order from internal iclear table
     *
     * @param string $sessionID
     * @param string $basketID
     * @return array wsdlOrder
     */
    function loadWsdlOrder($sessionID, $basketID) {
      $this->wsdlOrder = false;
      $sql = 'SELECT wsdl_id, orders_id, basket_id, request_id, session_id, status, status_message, accepted, basket_value FROM ' . TABLE_ICLEAR_ORDERS . ' WHERE session_id = "' . $sessionID . '" AND basket_id = "' . $basketID . '"';
      if($qry = xtc_db_query($sql)) {
        $this->wsdlOrder = xtc_db_fetch_array($qry);
      }
      return $this->wsdlOrder;
    }

    /**
     * delete wsdl order from TABLE_ORDERS_ICLEAR. this can happen without
     * the presence of an OSC order (in TABLE_ORDERS)
     *
     * @param string $sessionID
     * @param string $basketID
     */
    function deleteWsdlOrder($sessionID, $basketID) {
      $sql = 'DELETE FROM ' . TABLE_ICLEAR_ORDERS . ' WHERE session_id = "' . $sessionID . '" AND basket_id = "' . $basketID . '" LIMIT 1';
      xtc_db_query($sql);
    }

    /**
     * cancel wsdl order from TABLE_ORDERS_ICLEAR. this can happen without
     * the presence of an osc order (in TABLE_ORDERS)
     *
     * @param string $sessionID
     * @param string $basketID
     * @param string $statusMessage
     */
    function cancelWsdlOrder($sessionID, $basketID, $statusMessage = '') {
      $sql = 'UPDATE ' . TABLE_ICLEAR_ORDERS . ' SET status = 2, status_message = "' . $statusMessage . '" WHERE session_id = "' . $sessionID . '" AND basket_id = "' . $basketID . '" LIMIT 1';
      xtc_db_query($sql);
    }

/****************************************************************************
  order stuff: all modifications requre an already stored order (TABLE_ORDERS)
****************************************************************************/

    /**
     * compares the incoming (asynchronous) basket with the order in OSC
     * in case of error they're added to the internal error stack
     *
     * @return boolean
     */
    function checkLaggingOrder() {
      $rc = 0;
// here the proof is going: check if the incoming basket gots the same content as the order in OSC
      	$order_query = xtc_db_query("select products_id, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$iclearWsdl->wsdlOrder['orders_id'] . "'");
      if($order_query) {
      	while ($order = xtc_db_fetch_array($order_query)) {
//todo: compare wsdl basket with OSC order
      	}
      } else {
        $statusMessage = 'OSC order not found! Probably deleted! iclear order will be deleted!';
        $this->addError($statusMessage);
        $this->deleteWsdlOrder($this->wsdlOrder['session_id'], $this->wsdlOrder['basket_id']);
        $rc = 401;

      }
      return $rc;
    }

  /**
   * change the state of an (asynchronous) lagging order to open and restore
   * the delivery and billing address in TABLE_ORDERS
   *
   * @param unknown_type $orderStatus
   */
  function acceptLaggingOrder(&$orderStatus) {
    if($orderStatus) {
// create lagging order
  		xtc_db_query('UPDATE ' . TABLE_ICLEAR_ORDERS . ' SET status = "' . $orderStatus . '" WHERE wsdl_id = "' . $this->wsdlOrder['wsdl_id'] . '" LIMIT 1');

    } else {
// activate lagging order
// retrieve original billing and delivery address and reset it in OSC
      $address = $this->loadLaggingAddress($this->wsdlOrder['orders_id']);
      $billing = $address['billing'];
      $delivery = $address['delivery'];
  		$qry = 'UPDATE ' . TABLE_ORDERS . ' SET delivery_street_address = "' . $delivery['street_address'] . '", delivery_suburb = "' . $delivery['suburb'] . '",  delivery_city = "' . $delivery['city'] . '", delivery_postcode = "' . $delivery['postcode'] . '", delivery_state = "' . $delivery['state'] . '", billing_street_address = "' . $billing['street_address'] . '", billing_suburb = "' . $billing['suburb'] . '",  billing_city = "' . $billing['city'] . '", billing_postcode = "' . $billing['postcode'] . '", billing_state = "' . $billing['state'] . '", orders_status = 1 WHERE orders_id = "' . $this->wsdlOrder['orders_id'] . '"';


  		xtc_db_query($qry);
  		xtc_db_query('INSERT INTO ' . TABLE_ORDERS_STATUS_HISTORY . ' (orders_id, orders_status_id, date_added, customer_notified, comments) VALUES (' . $this->wsdlOrder['orders_id'] . ', 1, "' . date('Y-m-d H:i') . '", 0, "Async. accepted basket. ID: ' . $this->wsdlOrder['basket_id'] . '")');
  		$this->acceptOrder($orderStatus);
    }
      return 0;
  }

  /**
  * stores the delivery and billing address of a lagging order in iclear table; needs loaded wsdl order
  *
  * @param array $address
  * @return boolean
  */
    function storeLaggingAddress($address) {
      $sql = "UPDATE " . TABLE_ICLEAR_ORDERS . " SET delivery='" . serialize($address['delivery']) . "', billing='" . serialize($address['billing']) . "' WHERE session_id = '" . $this->wsdlOrder['session_id'] . "' AND basket_id='" . $this->wsdlOrder['basket_id'] . "' LIMIT 1";
      return xtc_db_query($sql);
    }

    /**
     * load the delivery and billing address for a lagging order in iclear table; needs orders_id
     *
     * @param int $orders_id
     * @return array
     */
    function loadLaggingAddress(&$orders_id) {
      $address = false;
      $sql = 'SELECT delivery, billing FROM ' . TABLE_ICLEAR_ORDERS . ' WHERE orders_id="' . $orders_id . '"';

      if($qry = xtc_db_query($sql)) {
        $address = xtc_db_fetch_array($qry);
        $address['delivery'] = unserialize($address['delivery']);
        $address['billing'] = unserialize($address['billing']);
      }
      return $address;
    }

    /**
     * cancel accepted order; restores original product stocks in
     * TABLE_PRODUCTS set status of wsdl_order (TABLE_ORDERS_ICLEAR) 2 cancelled
     *
     */
    function cancelOrder() {
      if(!$this->wsdlOrder['orders_id']) {
        $this->addError('No WSDL order loaded!');
        return 601;
      } elseif($order_id = (int)$this->wsdlOrder['orders_id']) {
				$order_query = xtc_db_query("select products_id, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
				while ($order = xtc_db_fetch_array($order_query)) {
					xtc_db_query("update " . TABLE_PRODUCTS . " set products_quantity = products_quantity + " . $order['products_quantity'] . ", products_ordered = products_ordered - " . $order['products_quantity'] . " where products_id = '" . (int)$order['products_id'] . "'");
				}

				xtc_db_query("delete from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
				xtc_db_query("delete from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
				xtc_db_query("delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$order_id . "'");
				xtc_db_query("delete from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . (int)$order_id . "'");
				xtc_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "'");
			} elseif((int)$iclearWsdl->wsdlOrder['status'] == 1) {
        xtc_db_query('DELETE FROM ' . TABLE_ORDERS_ICLEAR . ' WHERE wsdl_id = "' . $this->wsdlOrder['wsdl_id'] . '" LIMIT 1');
        return 0;
      }else {
// the order is stored in iclear table, but not in osc
        $this->addError('Status of iclear order is now cancelled!');
        $this->acceptOrder($this->wsdlOrder['status']);
        return 0;
			}
    }

    /**
     * update iclear WSDL table with given status and actual date
     * assumes, that a WSDL order is loaded (used in case of cancel)
     *
     * @param unknown_type $orderStatus
     */
    function acceptOrder(&$orderStatus) {
  		xtc_db_query('UPDATE ' . TABLE_ICLEAR_ORDERS . ' SET accepted = "' . date('Y-m-d H:i:s') . '", status = "' . $orderStatus . '" WHERE wsdl_id = "' . $this->wsdlOrder['wsdl_id'] . '" LIMIT 1');
    }

  }


?>
