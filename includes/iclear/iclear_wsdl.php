<?php
// $Id: iclear_wsdl.php,v 1.1 2007/03/18 19:51:15 dis Exp $

/*
  iclear payment system - because secure is simply secure
  http://www.iclear.de

  Copyright (c) 2001 - 2006 iclear

  Released under the GNU General Public License

************************************************************************
  Copyright (C) 2005 - 2006 BSE, David Brandt

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

// some installation come with php5 SOAP extension
include(DIR_FS_CATALOG . 'includes/iclear/nusoap.php');
require(DIR_FS_CATALOG . 'includes/iclear/iclear_error.php');

define ('ICLEAR_URI_BASE', 'http://www.iclear.de/');
define ('ICLEAR_URI_ORDER', ICLEAR_URI_BASE . 'ICOrderServices.wsdl');
define ('ICLEAR_URI_USER', ICLEAR_URI_BASE . 'ICUserServices.wsdl');

/**
 * The iclearWSDL provides the basic services to access the iclear WSDL payment interface
 * It expects values in preformatted manner.
 * For specification refer to the iclear WSDL / DMI documentation
 *
 */
class iclearWSDL extends iclearError {
    var $uriUser = ICLEAR_URI_USER;
    var $uriOrder = ICLEAR_URI_ORDER;
    var $currency = 'EUR'; // default currency is euro
    var $language = 'DE'; // default language is german
    var $basket;
    var $basketID;
    var $sessionID = '';
    var $requestID = '';
    var $wsdlResult = false;
    var $wsdlOrder = false;

  /**
   * constructor
   * expects at least the type of the WSDL client, which should be created (user/order)
   * in case of order a shopID must be provided
   * is in case of user a name and a password is given, a login will be performed
   *
   * @param [user|order] $type
   * @param int $shopID
   * @param string $sessionID
   * @param string $user
   * @param string $pass
   * @return iclearWSDL
   */
  function iclearWSDL($type, $shopID = '', $sessionID = '', $user = '', $pass = '') {
    $this->sessionID = $sessionID;

    $rc = false;
    if($this->client = $this->initWsdlClient($type)) {
      $this->type = $type;
      $this->shopID = $shopID;
      if($this->proxy = $this->client->getProxy()) {
        $rc = true;
        if($user) {
          $rc = $this->connect($user, $pass);
        }
      }
    } else {
      $this->addError('iclearWSDL: Unable 2 get proxy!');
    }

    return $rc;
  }

  function initWsdlClient($type) {
    if($type) {
// check if we're working with nusoap or the PHP extension and setup options
      switch($type) {
        case 'user':
          $this->client = new nusoapclient($this->uriUser, 'wsdl');
          break;

        case 'order':
          $this->client = new nusoapclient($this->uriOrder, 'wsdl');
          break;

        default:
          $this->client = false;
      }
    } else {
      $this->client = false;
    }
    return $this->client;
  }

  /**
   * Check if a connection error occured
   *
   * @return string errorMsg
   */
  function isWsdlError() {
    $rc = '';
    if(is_object($this->proxy) && ($rc = $this->proxy->getError())) {
      $this->addError('nusoap proxy: ' . $rc);
    } elseif(is_object($this->proxy) && ($rc = $this->client->getError())) {
      $this->addError('nusoap client: ' . $rc);
    }
    return $rc;
  }

  /**
   * connect a user type session to the iclear interface an retrieve requestID
   *
   * @param string $user
   * @param string $pass
   */
  function connectWsdl($user, $pass) {
    $res = $this->proxy->login($user, $pass, $this->sessionID);
    if(!$this->isWsdlError()) {
      $this->requestID = $res['requestID'];
    }
    !$this->getErrorCount();
  }

  /**
   * setter 4 the currency. defaults to EUR
   *
   * @param string $currency
   * @return string
   */
  function setWsdlCurrency ($currency = 'EUR') {
    return $this->currency = $currency;
  }

  /**
   * setter 4 the language. defaults 2 DE
   *
   * @param string $language
   * @return string
   */
  function setWsdlLanguage ($language = 'DE') {
    return $this->language = $language;
  }

  /**
   * set the sessionID which is used during wsdl requests
   *
   * @param string $sessionID
   */
  function setWsdlSessionID ($sessionID = '') {
    if($sessionID) {
      $this->sessionID = $sessionID;
    }
  }

  /**
   * load the wsdl basket into object
   * WARNING: the basket must be in the correct format or sendOrder / sendOrderS2S request will fail
   * sets automatically the basketID
   *
   * @param array $basket
   * @return string basketID
   */
  function setWsdlBasket(&$basket) {
    $this->basketID = 0;
    if (!$basket) {
      $this->addError('setWsdlOrder: Basket not present!');
    } elseif (!sizeof($basket)) {
      $this->addError('setWsdlOrder: Basket empty!');
    } else {
// here the basket processing goes
      $this->basketID = md5(microtime() . $this->sessionID);
    }
    return $this->basketID;
  }

  /**
   * Get billing address of a (loged in!) user from iclear
   *
   * @return array
   */
  function getWsdlBillingAddress() {
    if(!$this->requestID) {
      $this->addError('getWsdlBillingAddress: No requestID found!');
    } else {
      $res = $this->proxy->getWsdlAddressList($this->requestID, $this->sessionID);
      if(!$this->isError()) {
        $address = $res['resultElements']['Address'][0];
      }
    }
    return $address;
  }

  /**
   * Get delivery address of a (loged in!) user from iclear
   *
   * @return array
   */
  function getWsdlDeliveryAddress() {
    if(!$this->requestID) {
      $this->addError('getWsdlDeliveryAddress: No requestID found!');
    } else {
      $res = $this->proxy->getWsdlAddressList($this->requestID, $this->sessionID);
      if(!$this->isError()) {
        $address = $res['resultElements']['Address'][1];
      }
    }
    return $address;
  }

  /**
   * Get user addressID from iclear
   *
   * @param [billing|delivery] $type
   * @return int
   */
  function getWsdlAddressID($type) {
    $addressID = 0;

    switch($type) {
      case 'billing':
        if($address = $this->getWsdlBillingAddress()) {
          $addressID = $address['addrID'];
        }
        break;

      case 'delivery':
        if($address = $this->getWsdlDeliveryAddress()) {
          $addressID = $address['addrID'];
        }
        break;
    }

    return $addressID;
  }

  /**
   * send a order without user interaction
   * for this feature a special agreement with iclear must be present
   * and the shop is responsible 4 the authentification / authentication and the data
   * integrity of the transported information
   * Note: The user must be loged in first!
   *
   * @param int $userAddrID
   * @param string $currency
   * @param array $basket
   * @return boolean
   */
  function sendWsdlOrderS2S ($userAddrID, $currency, $basket) {
    $rc = false;
    if(!$this->shopID) {
      $this->addError('sendOrderS2S: No ShopID found!');
    } elseif (!$this->requestID) {
      $this->addError('sendOrderS2S: No requestID found!');
    } else {
      $res = $this->proxy->sendOrderS2S($this->requestID, $userAddrID, $this->shopID, md5(time()), $currence, $basket, $this->sessionID);
      if(!$this->isError()) {
        if((int)$res['status']) {
          $this->addError('sendOrderS2S: Error from iclear: ' . $res['statusMessage']);
        } else {
          $rc = true;
        }
      }
    }
    return $rc;
  }

  /**
   * send basket 2 iclear wsdl interface and collects result
   *
   * @return array wsdlResult
   */
  function sendWsdlOrder () {
    $this->wsdlResult = false;
    if(!$this->shopID) {
      $this->addError('sendOrder: No ShopID found!');
    } elseif (!$this->basket) {
      $this->addError('sendOrder: Basket not present!');
    } elseif (!sizeof($this->basket)) {
      $this->addError('sendOrder: Basket empty!');
    } else {
      $res = $this->proxy->sendOrder($this->shopID, $this->basketID, $this->currency, $this->basket, $this->sessionID, $this->language);
      if(!$this->isWsdlError()) {
        if(isset($res['status'])) {
          if(!(int)$res['status'] || (int)$res['status'] == 1) {
            $this->requestID = $res['requestID'];
  // here order ok
            $this->wsdlResult =& $res;
          } else {
            $this->addError('sendOrder: Error from iclear: ' . $res['statusMessage']);
          }
        }
      } else {
        $this->addError('SendWsdlOrder: Interface error - ' . $res['detail']['string']);

      }
    }
    return $this->wsdlResult;
  } //sendOrder()

  /**
   * abstract method which is used to store the intermediate basket and orders in
   * the iclear table.
   * Must be provided by the inheriting class!
   * See: iclearCatalog
   *
   */
  function storeWsdlOrder() {
    die("Abstract method storeWsdlOrder() of class iclearWSDL called!");
  }

  /**
   * abstract method which is used to load the intermeidiate basket and orders from
   * the iclear table.
   * Must be provided by the inheriting class!
   * See: iclearCatalog
   *
   */
  function loadWsdlOrder() {
    die("Abstract method loadWsdlOrder() of class iclearWSDL called!");
  }

}// class



?>