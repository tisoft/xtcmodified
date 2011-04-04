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

//BOF - GTB - 2011-04-04 - only include configure to minimize Problems with Captcha
//require ('includes/application_top.php');
if (file_exists('includes/local/configure.php')) {
	include ('includes/local/configure.php');
} else {
	include ('includes/configure.php');
}
//EOF - GTB - 2011-04-04 - only include configure to minimize Problems with Captcha

require_once (DIR_FS_INC.'xtc_render_vvcode.inc.php');
require_once (DIR_FS_INC.'xtc_random_charcode.inc.php');

$visual_verify_code = xtc_random_charcode(6);
$_SESSION['vvcode'] = $visual_verify_code;
$vvimg = vvcode_render_code($visual_verify_code);
?>