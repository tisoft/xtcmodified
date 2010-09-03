<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2006 XT-Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

   // Put CSS-Definitions here, these CSS-files will be loaded at the TOP of every page
?>
<link rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/stylesheet.css" type="text/css" />
<link rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/css/thickbox.css" type="text/css" media="screen" />

<?php 
// BOF - web28 - 2010-07-09 - TABS/ACCORDION in product_info
if (strstr($PHP_SELF, FILENAME_PRODUCT_INFO )) {
//BOF - GTB - 2010-08-03 - Security Fix - Base ?>
<link rel="stylesheet" href="<?php echo DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE; ?>/css/jquery-ui.css" type="text/css" media="screen" />
<?php
/* <link rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/css/jquery-ui.css" type="text/css" media="screen" />
//EOF - GTB - 2010-08-03 - Security Fix - Base */
}
// EOF - web28 - 2010-07-09 - TABS/ACCORDION in product_info
?>