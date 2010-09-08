<?php
/* --------------------------------------------------------------
   $Id$   

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.26 2002/08/06); www.oscommerce.com 
   (c) 2003	 nextcommerce (html_output.php,v 1.7 2003/08/18); www.nextcommerce.org
   (c) 2006	 xt-commerce (html_output.php 1125 2005-07-28)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
  ////
  // The HTML href link wrapper function
  function xtc_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
    if ($page == '') {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><strong>Error!</strong></font><br /><br /><strong>Unable to determine the page link!<br /><br />Function used:<br /><br />xtc_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</strong>');
    }
    if ($connection == 'NONSSL') {
      $link = HTTP_SERVER . DIR_WS_ADMIN;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL == 'true') {
        $link = HTTPS_SERVER . DIR_WS_ADMIN;
      } else {
        $link = HTTP_SERVER . DIR_WS_ADMIN;
      }
    } else {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><strong>Error!</strong></font><br /><br /><strong>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL<br /><br />Function used:<br /><br />xtc_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</strong>');
    }
    if ($parameters == '') {
      $link = $link . $page . '?' . SID;
    } else {
      $link = $link . $page . '?' . $parameters . '&' . SID;
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

    return $link;
  }

  function xtc_catalog_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
    if ($connection == 'NONSSL') {
      $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL_CATALOG == 'true') {
        $link = HTTPS_CATALOG_SERVER . DIR_WS_CATALOG;
      } else {
        $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
      }
    } else {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><strong>Error!</strong></font><br /><br /><strong>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL<br /><br />Function used:<br /><br />xtc_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</strong>');
    }
    if ($parameters == '') {
      $link .= $page;
    } else {
      $link .= $page . '?' . $parameters;
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

    return $link;
  }

  ////
  // The HTML image wrapper function
  function xtc_image($src, $alt = '', $width = '', $height = '', $params = '') {
    $image = '<img src="' . $src . '" border="0" alt="' . $alt . '"';
    if ($alt) {
      $image .= ' title=" ' . $alt . ' "';
    }
    if ($width) {
      $image .= ' width="' . $width . '"';
    }
    if ($height) {
      $image .= ' height="' . $height . '"';
    }
    if ($params) {
      $image .= ' ' . $params;
    }
    $image .= '>';

    return $image;
  }

  ////
  // Draw a 1 pixel black line
  function xtc_black_line() {
    return xtc_image(DIR_WS_IMAGES . 'pixel_black.gif', '', '100%', '1');
  }

  ////
  // Output a separator either through whitespace, or with an image
  function xtc_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
    return xtc_image(DIR_WS_IMAGES . $image, '', $width, $height);
  }

  ////
  // javascript to dynamically update the states/provinces list when the country is changed
  // TABLES: zones
  function xtc_js_zone_list($country, $form, $field) {
    $countries_query = xtc_db_query("select distinct zone_country_id from " . TABLE_ZONES . " order by zone_country_id");
    $num_country = 1;
    $output_string = '';
    while ($countries = xtc_db_fetch_array($countries_query)) {
      if ($num_country == 1) {
        $output_string .= '  if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      } else {
        $output_string .= '  } else if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      }

      $states_query = xtc_db_query("select zone_name, zone_id from " . TABLE_ZONES . " where zone_country_id = '" . $countries['zone_country_id'] . "' order by zone_name");

      $num_state = 1;
      while ($states = xtc_db_fetch_array($states_query)) {
        if ($num_state == '1') $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . PLEASE_SELECT . '", "");' . "\n";
        $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $states['zone_name'] . '", "' . $states['zone_id'] . '");' . "\n";
        $num_state++;
      }
      $num_country++;
    }
    $output_string .= '  } else {' . "\n" .
                      '    ' . $form . '.' . $field . '.options[0] = new Option("' . TYPE_BELOW . '", "");' . "\n" .
                      '  }' . "\n";

    return $output_string;
  }

  ////
  // Output a form
  function xtc_draw_form($name, $action, $parameters = '', $method = 'post', $params = '') {
    $form = '<form name="' . $name . '" action="';
    if ($parameters) {
      $form .= xtc_href_link($action, $parameters);
    } else {
      $form .= xtc_href_link($action);
    }
    $form .= '" method="' . $method . '"';
    if ($params) {
      $form .= ' ' . $params;
    }
    $form .= '>';

    return $form;
  }

  ////
  // Output a form input field
  function xtc_draw_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . $type . '" name="' . $name . '"';
    if ( ($GLOBALS[$name]) && ($reinsert_value) ) {
      $field .= ' value="' . htmlspecialchars(trim($GLOBALS[$name])) . '"';
    } elseif ($value != '') {
      $field .= ' value="' . htmlspecialchars(trim($value)) . '"';
    }
    if ($parameters != '') {
      $field .= ' ' . $parameters;
    }
    $field .= '>';

    if ($required) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }
  // Output a form small input field
  function xtc_draw_small_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . $type . '" size="3" name="' . $name . '"';
    if ( ($GLOBALS[$name]) && ($reinsert_value) ) {
      $field .= ' value="' . htmlspecialchars(trim($GLOBALS[$name])) . '"';
    } elseif ($value != '') {
      $field .= ' value="' . htmlspecialchars(trim($value)) . '"';
    }
    if ($parameters != '') {
      $field .= ' ' . $parameters;
    }
    $field .= '>';

    if ($required) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

  ////
  // Output a form password field
  function xtc_draw_password_field($name, $value = '', $required = false) {
    $field = xtc_draw_input_field($name, $value, 'maxlength="40"', $required, 'password', false);

    return $field;
  }

  ////
  // Output a form filefield
  function xtc_draw_file_field($name, $required = false) {
    $field = xtc_draw_input_field($name, '', '', $required, 'file');

    return $field;
  }

  ////
  // Output a selection field - alias function for xtc_draw_checkbox_field() and xtc_draw_radio_field()
  function xtc_draw_selection_field($name, $type, $value = '', $checked = false, $compare = '') {
    $selection = '<input type="' . $type . '" name="' . $name . '"';
    if ($value != '') {
      $selection .= ' value="' . $value . '"';
    }
    if ( ($checked == true) || ($GLOBALS[$name] == 'on') || ($value && ($GLOBALS[$name] == $value)) || ($value && ($value == $compare)) ) {
      $selection .= ' CHECKED';
    }
    $selection .= '>';

    return $selection;
  }

  ////
  // Output a form checkbox field
  function xtc_draw_checkbox_field($name, $value = '', $checked = false, $compare = '') {
    return xtc_draw_selection_field($name, 'checkbox', $value, $checked, $compare);
  }

  ////
  // Output a form radio field
  function xtc_draw_radio_field($name, $value = '', $checked = false, $compare = '') {
    return xtc_draw_selection_field($name, 'radio', $value, $checked, $compare);
  }

  ////
  // Output a form textarea field
  function xtc_draw_textarea_field($name, $wrap, $width, $height, $text = '', $params = '', $reinsert_value = true) {
    $field = '<textarea id="'.$name.'" name="' . $name . '" wrap="' . $wrap . '" cols="' . $width . '" rows="' . $height . '"';
    if ($params) $field .= ' ' . $params;
    $field .= '>';
    if ( ($GLOBALS[$name]) && ($reinsert_value) ) {
      $field .= $GLOBALS[$name];
    } elseif ($text != '') {
      $field .= $text;
    }
    $field .= '</textarea>';

    return $field;
  }

  ////
  // Output a form hidden field
  function xtc_draw_hidden_field($name, $value = '') {
    $field = '<input type="hidden" name="' . $name . '" value="';
    if ($value != '') {
      $field .= trim($value);
    } else {
      $field .= trim($GLOBALS[$name]);
    }
    $field .= '">';

    return $field;
  }

  ////
  // Output a form pull down menu
  function xtc_draw_pull_down_menu($name, $values, $default = '', $params = '', $required = false) {
    $field = '<select name="' . $name . '"';
    if ($params) $field .= ' ' . $params;
    $field .= '>';
 	if(is_array($values))
   	{
         foreach ($values as $key=>$val) {
             $field .= '<option value="' .$val['id'] . '"';
             //BOF - DokuMan - 2010-09-08 - set undefined index            
             if ( ((strlen($val['id']) > 0) && isset($GLOBALS[$name]) && ($GLOBALS[$name] == $val['id'])) || ($default == $val['id']) ) {
             //if ( ((strlen($val['id']) > 0) && ($GLOBALS[$name] == $val['id'])) || ($default == $val['id']) ) {
             //EOF - DokuMan - 2010-09-08 - set undefined index
               $field .= ' SELECTED';    
             }    
             $field .= '>' . $val['text'] . '</option>';
         }
   	} 
    $field .= '</select>';

    if ($required) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

  // output 2 Soring arrows
  function xtc_sorting($page,$sort) {

      switch ($page) {
          case FILENAME_CUSTOMERS:

          $nav='<br /><a href="'.xtc_href_link(FILENAME_CUSTOMERS,'sorting='.$sort.'&'.xtc_get_all_get_params(array('action','sorting'))).'">';
          $nav.=xtc_image(DIR_WS_ICONS . 'sort_down.gif', '', '20' ,'20').'</a>';
          $nav.='<a href="'.xtc_href_link(FILENAME_CUSTOMERS,'sorting='.$sort.'-desc&'.xtc_get_all_get_params(array('action','sorting'))).'">';
          $nav.= xtc_image(DIR_WS_ICONS . 'sort_up.gif', '', '20' ,'20').'</a>';

          break;
          
          case FILENAME_CATEGORIES:

          $nav='<br /><div><a href="'.xtc_href_link(FILENAME_CATEGORIES,'sorting='.$sort.'&'.xtc_get_all_get_params(array('action','sorting'))).'">';
          $nav.=xtc_image(DIR_WS_ICONS . 'sort_down.gif', '', '20' ,'20').'</a>';
          $nav.='<a href="'.xtc_href_link(FILENAME_CATEGORIES,'sorting='.$sort.'-desc&'.xtc_get_all_get_params(array('action','sorting'))).'">';
          $nav.= xtc_image(DIR_WS_ICONS . 'sort_up.gif', '', '20' ,'20').'</a></div>';

          break;          

      }

      return $nav;

  }
?>