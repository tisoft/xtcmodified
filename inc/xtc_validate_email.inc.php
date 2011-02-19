<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(validations.php,v 1.11 2003/02/11); www.oscommerce.com
   (c) 2003 nextcommerce (xtc_validate_email.inc.php,v 1.5 2003/08/14); www.nextcommerce.org
   (c) 2003 XT-Commerce (xtc_validate_email.inc.php 899 2005-04-29)
   (c) 2010 osCommerce (validations.php)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  ////////////////////////////////////////////////////////////////////////////////////////////////
  //
  // Function    : xtc_validate_email
  //
  // Arguments   : email   email address to be checked
  //
  // Return      : true  - valid email address
  //               false - invalid email address
  //
  // Description : function for validating email address that conforms to RFC 822 specs
  //
  //              This function will first attempt to validate the Email address using the filter
  //              extension for performance. If this extension is not available it will
  //              fall back to a regex based validator which doesn't validate all RFC822
  //              addresses but catches 99.9% of them. The regex is based on the code found at
  //              http://www.regular-expressions.info/email.html
  //
  //              Optional validation for validating the domain name is also valid is supplied
  //              and can be enabled using the administration tool.
  //
  // Sample Valid Addresses:
  //
  //    first.last@host.com
  //    firstlast@host.to
  //    first-last@host.com
  //    first_last@host.com
  //
  // Invalid Addresses:
  //
  //    first last@host.com
  //    first@last@host.com
  //
  ////////////////////////////////////////////////////////////////////////////////////////////////

  function xtc_validate_email($email) {

    //BOF - DokuMan - 2011-02-19 - SQL nullbyte injection fix 16.02.2011
    if (strpos($email,"\0") !== false) {
      return false;
    }
    //EOF - DokuMan - 2011-02-19 - SQL nullbyte injection fix 16.02.2011

    $email = trim($email);
    if (strlen($email) > 255) {
      $valid_address = false;
    } elseif (function_exists('filter_var') && defined('FILTER_VALIDATE_EMAIL')) {

     $valid_address = (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    } else {
      if ( substr_count( $email, '@' ) > 1 ) {
        $valid_address = false;
      }

      if ( preg_match("/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i", $email)) {
        $valid_address = true;
      } else {
        $valid_address = false;
      }
    }

    if ($valid_address && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
      $domain = explode('@', $email);
      if (!checkdnsrr($domain[1], "MX") && !checkdnsrr($domain[1], "A")) {
        $valid_address = false;
      }
    }
    return $valid_address;
  }
?>