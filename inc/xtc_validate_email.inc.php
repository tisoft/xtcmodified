<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_validate_email.inc.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(validations.php,v 1.11 2003/02/11); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_validate_email.inc.php,v 1.5 2003/08/14); www.nextcommerce.org

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
  //               This function is converted from a JavaScript written by
  //               Sandeep V. Tamhankar (stamhankar@hotmail.com). The original JavaScript
  //               is available at http://javascript.internet.com
  //
  // Sample Valid Addresses:
  //
  //    first.last@host.com
  //    firstlast@host.to
  //    "first last"@host.com
  //    "first@last"@host.com
  //    first-last@host.com
  //    first.last@[123.123.123.123]
  //
  // Invalid Addresses:
  //
  //    first last@host.com
  //
  //
  ////////////////////////////////////////////////////////////////////////////////////////////////

  function xtc_validate_email($email) {
    $valid_address = true;

    $mail_pat = '^(.+)@(.+)$';
    $valid_chars = "[^] \(\)<>@,;:\.\\\"\[]";
    $atom = "$valid_chars+";
    $quoted_user='(\"[^\"]*\")';
    $word = "($atom|$quoted_user)";
    $user_pat = "^$word(\.$word)*$";
    $ip_domain_pat='^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$';
    $domain_pat = "^$atom(\.$atom)*$";

    if (preg_match('/'.$mail_pat.'/i', $email, $components)) { // Hetfield - 2009-08-19 - replaced depricated function eregi with preg_match to be ready for PHP >= 5.3
      $user = $components[1];
      $domain = $components[2];
      // validate user
      if (preg_match('/'.$user_pat.'/i', $user)) { // Hetfield - 2009-08-19 - replaced depricated function eregi with preg_match to be ready for PHP >= 5.3
        // validate domain
        if (preg_match('/'.$ip_domain_pat.'/i', $domain, $ip_components)) { // Hetfield - 2009-08-19 - replaced depricated function eregi with preg_match to be ready for PHP >= 5.3
          // this is an IP address
      	  for ($i=1;$i<=4;$i++) {
      	    if ($ip_components[$i] > 255) {
      	      $valid_address = false;
      	      break;
      	    }
          }
        } else {
          // Domain is a name, not an IP
          if (preg_match('/'.$domain_pat.'/i', $domain)) { // Hetfield - 2009-08-19 - replaced depricated function eregi with preg_match to be ready for PHP >= 5.3
            /* domain name seems valid, but now make sure that it ends in a valid TLD or ccTLD
               and that there's a hostname preceding the domain or country. */
            $domain_components = explode(".", $domain);
            // Make sure there's a host name preceding the domain.
            if (sizeof($domain_components) < 2) {
              $valid_address = false;
            } else {
              $top_level_domain = strtolower($domain_components[sizeof($domain_components)-1]);
              // Allow all 2-letter TLDs (ccTLDs)
              if (preg_match('/^[a-z][a-z]$/i', $top_level_domain) != 1) { // Hetfield - 2009-08-19 - replaced depricated function eregi with preg_match to be ready for PHP >= 5.3
                $tld_pattern = '';
                // Get authorized TLDs from text file
                $tlds = file(DIR_FS_INC.'tld.txt');
                while (list(,$line) = each($tlds)) {
                  // Get rid of comments
                  $words = explode('#', $line);
                  $tld = trim($words[0]);
                  // TLDs should be 3 letters or more
                  if (preg_match('/^[a-z]{3,}$/i', $tld) == 1) { // Hetfield - 2009-08-19 - replaced depricated function eregi with preg_match to be ready for PHP >= 5.3
                    $tld_pattern .= '^' . $tld . '$|';
                  }
                }
                // Remove last '|'
                $tld_pattern = substr($tld_pattern, 0, -1);
                if (preg_match('/'.$tld_pattern.'/i', $top_level_domain) == 0) { // Hetfield - 2009-08-19 - replaced depricated function eregi with preg_match to be ready for PHP >= 5.3
                    $valid_address = false;
                }
              }
            }
          } else {
      	    $valid_address = false;
      	  }
      	}
      } else {
        $valid_address = false;
      }
    } else {
      $valid_address = false;
    }
    if ($valid_address && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
      if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
        $valid_address = false;
      }
    }
    return $valid_address;
  }

?>