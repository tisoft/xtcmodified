<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_output_warning.inc.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_output_warning.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
  function xtc_output_warning($warning) {
    new errorBox(array(array('text' => '<table style="width: 100%;"><tr><td style="vertical-align: center; padding-left: 5px;">' . xtc_image(DIR_WS_ICONS . 'output_warning.gif', ICON_WARNING) . ' </td><td style="vertical-align: center; text-align: center;"> ' . $warning . '</td></tr></table>')));
  }

 ?>