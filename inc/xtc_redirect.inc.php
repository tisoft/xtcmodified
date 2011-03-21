<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   ----------------------------------------------------------------------------------------- 
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_redirect.inc.php,v 1.5 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - www.xt-commerce.com
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  // include needed functions
  
  require_once(DIR_FS_INC . 'xtc_exit.inc.php');
  
  //BOF - web28 - 2010-07-19 - New SSL  parameter
  //function xtc_redirect($url) {
  function xtc_redirect($url, $ssl='') {
  //EOF - web28 - 2010-07-19 - New SSL  parameter
	//BOF - web28 - 2010-07-19 - FIX switch to NONSSL & New SSL  handling  defined by $request_type
    //if ( (ENABLE_SSL == true) && (getenv('HTTPS') == 'on' || getenv('HTTPS') == '1') ) { // We are loading an SSL page	
	global $request_type;	
    if ( (ENABLE_SSL == true) && ($request_type == 'SSL') && ($ssl != 'NONSSL') ) { // We are loading an SSL page
	//EOF - web28 - 2010-07-19 - FIX switch to NONSSL & New SSL  handling  defined by $request_type		
		if (substr($url, 0, strlen(HTTP_SERVER)) == HTTP_SERVER) { // NONSSL url
		    $url = HTTPS_SERVER . substr($url, strlen(HTTP_SERVER)); // Change it to SSL
		}
    }
    
    // BOF - GTB - 2011-03-21 - write referer to Session
    $_SESSION['REFERER'] = basename(parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH));
    // EOF - GTB - 2011-03-21 - write referer to Session
    
    // BOF - Hetfield - 2009-08-11 - replaced deprecated function eregi_replace with preg_replace to be ready for PHP >= 5.3
    header('Location: ' . preg_replace("/[\r\n]+(.*)$/i", "", html_entity_decode($url)));
    // EOF - Hetfield - 2009-08-11 - replaced deprecated function eregi_replace with preg_replace to be ready for PHP >= 5.3

    xtc_exit();
    
  }
?>