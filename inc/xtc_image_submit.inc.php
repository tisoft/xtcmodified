<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com
   (c) 2003	nextcommerce (xtc_image_submit.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2006 XT-Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// The HTML form submit button wrapper function
// Outputs a button in the selected language
  function xtc_image_submit($image, $alt = '', $parameters = '') {
    //BOF - GTB - 2010-08-03 - Security Fix - Base
    $image_submit = '<input type="image" src="' . xtc_parse_input_field_data(DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE.'/buttons/' . $_SESSION['language'] . '/'. $image, array('"' => '&quot;')) . '" alt="' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . '"';
    //$image_submit = '<input type="image" src="' . xtc_parse_input_field_data('templates/'.CURRENT_TEMPLATE.'/buttons/' . $_SESSION['language'] . '/'. $image, array('"' => '&quot;')) . '" alt="' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . '"';
    //EOF - GTB - 2010-08-03 - Security Fix - Base
    if (xtc_not_null($alt)) $image_submit .= ' title="' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . '"';

    if (xtc_not_null($parameters)) $image_submit .= ' ' . $parameters;

    $image_submit .= ' />';

    return $image_submit;
  }
?>