<?php
/* --------------------------------------------------------------
   $Id: header.php 1025 2005-07-14 11:57:54Z gwinger $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(header.php,v 1.19 2002/04/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (header.php,v 1.17 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  if ($messageStack->size > 0) {
    echo $messageStack->output();
  }
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="1"><?php echo xtc_image(DIR_WS_IMAGES . 'logo.gif', 'xt:Commerce'); ?></td>
    <td valign="bottom" align="right" background="images/bg_top.jpg"><?php echo xtc_image(DIR_WS_IMAGES . 'img_spacer.jpg', '', '', ''); ?><?php echo '<a href="start.php"  class="headerLink">'. xtc_image(DIR_WS_IMAGES . 'top_index.gif', '', '', '').'</a>'; ?><?php echo xtc_image(DIR_WS_IMAGES . 'img_spacer.jpg', '', '', ''); ?><?php echo '<a href="'.xtc_href_link('../index.php').'" class="headerLink">'. xtc_image(DIR_WS_IMAGES . 'top_shop.gif', '', '', '').'</a>'; ?><?php echo xtc_image(DIR_WS_IMAGES . 'img_spacer.jpg', '', '', ''); ?><?php echo '<a href="' . xtc_href_link(FILENAME_LOGOUT, '', 'NONSSL') . '" class="headerLink">'. xtc_image(DIR_WS_IMAGES . 'top_logout.gif', '', '', '').'</a>'; ?><?php echo xtc_image(DIR_WS_IMAGES . 'img_spacer.jpg', '', '', ''); ?><?php echo '<a href="' . xtc_href_link(FILENAME_CREDITS, '', 'NONSSL') . '" class="headerLink">'. xtc_image(DIR_WS_IMAGES . 'top_credits.gif', '', '', '').'</a>'; ?><?php echo xtc_image(DIR_WS_IMAGES . 'img_line.jpg', '', '', ''); ?></td>
  </tr>
</table>
