<?
/* --------------------------------------------------------------
   $Id: footer.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(footer.php,v 1.12 2003/02/17); www.oscommerce.com 
   (c) 2003	 nextcommerce (footer.php,v 1.11 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
?>
<br />
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td align="center" class="smallText"><?php
/*
  The following copyright announcement is in compliance
  to section 2c of the GNU General Public License, and
  thus can not be removed, or can only be modified
  appropriately.

  Please leave this comment intact together with the
  following copyright announcement.
  
  Copyright announcement changed due to the permissions
  from LG Hamburg from 28th February 2003 / AZ 308 O 70/03
*/
?>E-Commerce Engine Copyright &copy; 2006 <a href="http://www.xt-commerce.com" target="_blank">xt:Commerce</a><br />
xt:Commerce provides no warranty and is redistributable under the <a href="http://www.fsf.org/licenses/gpl.txt" target="_blank">GNU General Public License</a></td>
  </tr>
  <tr>
    <td><?php echo xtc_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '5'); ?></td>
  </tr>
  <tr>
    <td align="center" class="smallText">Powered by <a href="http://www.xt-commerce.com" target="_blank">xt:Commerce</a></td>
  </tr>
</table>
<?php
/*
  echo ('<font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Session Debug:</strong><br />');
  echo "<pre>";
  print_r($_SESSION);
  echo "</pre>";
  echo '</font>';
  echo xtc_session_id();

*/
?>