<?php
  /* --------------------------------------------------------------
   $Id: index.php 1220 2005-09-16 15:53:13Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (index.php,v 1.18 2003/08/17); www.nextcommerce.org
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
   
  require('includes/application.php');

  // include needed functions
  require_once(DIR_FS_INC.'xtc_image.inc.php');
  require_once(DIR_FS_INC.'xtc_draw_separator.inc.php');
  require_once(DIR_FS_INC.'xtc_redirect.inc.php');
  require_once(DIR_FS_INC.'xtc_href_link.inc.php');
  
  include('language/english.php');

  // Include Developer - standard settings for installer
  //  require('developer_settings.php');  
  
 define('HTTP_SERVER','');
 define('HTTPS_SERVER','');
 define('DIR_WS_CATALOG','');

   $messageStack = new messageStack();

    $process = false;
  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    $process = true;

        
        $_SESSION['language'] = xtc_db_prepare_input($_POST['LANGUAGE']);

    $error = false;


      if ( ($_SESSION['language'] != 'german') && ($_SESSION['language'] != 'english') ) {
        $error = true;

        $messageStack->add('index', SELECT_LANGUAGE_ERROR);
        }
        

                    if ($error == false) {
                        xtc_redirect(xtc_href_link('install_step1.php', '', 'NONSSL'));
                }
        }


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>XT-Commerce Installer - Welcome</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.messageStackError, .messageStackWarning { font-family: Verdana, Arial, sans-serif; font-weight: bold; font-size: 10px; background-color: #; }
-->
</style>
</head>

<body>
<?php
//BOF - DokuMan - 2009-05-19 - removed webbug to www.xt-commerce.com
//<img src='http://www.xt-commerce.com/_banner/adview.php?what=zone:18&amp;n=a61c088d' border='0' alt=''>
//EOF - DokuMan - 2009-05-19 - removed webbug to www.xt-commerce.com
?>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="95" colspan="2" ><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="1"><img src="images/logo.gif"></td>
          <td background="images/bg_top.jpg">&nbsp;</td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td width="180" valign="top" bgcolor="F3F3F3" style="border-bottom: 1px solid; border-left: 1px solid; border-right: 1px solid; border-color: #6D6D6D;">
      <table width="180" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="17" background="images/bg_left_blocktitle.gif">
<div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong><font color="FFAF00">xtc:</font><font color="#999999">Install</font></strong></font></div></td>
        </tr>
        <tr>
          <td bgcolor="F3F3F3" ><br />
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="10">&nbsp;</td>
                <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_LANGUAGE; ?></font></td>
              </tr>
            </table>
            <br /></td>
        </tr>
      </table>
    </td>
    <td align="right" valign="top" style="border-top: 1px solid; border-bottom: 1px solid; border-right: 1px solid; border-color: #6D6D6D;">
      <br />
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><img src="images/title_index.gif" width="586" height="100" border="0"><br />
            <table width="100%"  border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td><br />                  <table width="100%"  border="0" cellpadding="2" cellspacing="2" style="border: 1px solid; border-color: #4CC534;" bgcolor="#C2FFB6">
                  <tr>
                    <td width="1"><img src="images/install.gif" border="0"></td>
<?php
//BOF - DokuMan - 2009-06-03 - changed links to www.xt-commerce.com
/*                   
                    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="http://www.xt-commerce.com/forum/showthread.php?t=35187" target="_blank">Installationsanleitung auf www.xt-commerce.com</a></font></td>
                  </tr>
                  <tr>
                    <td width="1"><img src="images/install.gif" border="0"></td>
                    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="http://www.xt-commerce.com/shop/product_info.php?products_id=1&utm_source=installer&utm_medium=link" target="_blank">xt:Commerce Support</a></font></td>
*/
?>

                    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="http://www.xtc-modified.org/" target="_blank">Installationsanleitung</a></font></td>
                  </tr>
                  <tr>
                    <td width="1"><img src="images/install.gif" border="0"></td>
                    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="http://www.xtc-modified.org/forum/" target="_blank">xt:Commerce Support</a></font></td>
<?php
//BOF - DokuMan - 2009-06-03 - changed links to www.xt-commerce.com
?>                    
                  </tr>
                </table></td>
              </tr>
            </table>
            <font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br /><br /><?php echo TEXT_WELCOME_INDEX; ?></font><br />
            <br /></td>
        </tr>
        <tr>
<?php
  // permission check to prevent DAU faults.
 $error_flag=false;
 $message='';
 $ok_message='';

 // config files
 if (!is_writeable(DIR_FS_CATALOG . 'includes/configure.php')) {
    $error_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'includes/configure.php<br />';
 }
  if (!is_writeable(DIR_FS_CATALOG . 'includes/configure.org.php')) {
    $error_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'includes/configure.org.php<br />';
 }
  if (!is_writeable(DIR_FS_CATALOG . 'admin/includes/configure.php')) {
    $error_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'admin/includes/configure.php<br />';
 }
  if (!is_writeable(DIR_FS_CATALOG . 'admin/includes/configure.org.php')) {
    $error_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'admin/includes/configure.org.php<br />';
 }
 $status='OK';
 if ($error_flag==true) $status='<strong><font color="#ff0000">ERROR</font></strong>';
 $ok_message.='FILE Permissions .............................. '.$status.'<br /><hr noshade>';

 // smarty folders
 $folder_flag==false;
 
    if (!is_writeable(DIR_FS_CATALOG . 'admin/rss/xt-news.cache')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'admin/rss/xt-news.cache<br />';
 }
 
   if (!is_writeable(DIR_FS_CATALOG . 'templates_c/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'templates_c/<br />';
 }
    if (!is_writeable(DIR_FS_CATALOG . 'cache/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'cache/<br />';
 }

     if (!is_writeable(DIR_FS_CATALOG . 'admin/rss/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'admin/rss/<br />';
 }

      if (!is_writeable(DIR_FS_CATALOG . 'admin/images/graphs')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'admin/images/graphs<br />';
 }

    if (!is_writeable(DIR_FS_CATALOG . 'admin/backups/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'admin/backups/<br />';
 }

 // image folders
      if (!is_writeable(DIR_FS_CATALOG . 'images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'images/<br />';
 }
     if (!is_writeable(DIR_FS_CATALOG . 'images/categories/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'images/categories/<br />';
 }
     if (!is_writeable(DIR_FS_CATALOG . 'images/banner/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'images/banner/<br />';
 }
     if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/info_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'images/product_images/info_images/<br />';
 }
     if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/original_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'images/product_images/original_images/<br />';
 }
     if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/popup_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'images/product_images/popup_images/<br />';
 }
      if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/thumbnail_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'images/product_images/thumbnail_images/<br />';
 }
 
   if (!is_writeable(DIR_FS_CATALOG . 'admin/rss/xt-news.cache')) {
    $error_flag=true;
    $message .= 'WRONG PERMISSION on '.DIR_FS_CATALOG . 'admin/rss/xt-news.cache<br />';
 }

 $status='OK';
 if ($folder_flag==true) $status='<strong><font color="#ff0000">ERROR</font></strong>';
 $ok_message.='FOLDER Permissions .............................. '.$status.'<br /><hr noshade>';

 // check PHP-Version

 $php_flag==false;
 if (xtc_check_version()!=1) {
     $error_flag=true;
     $php_flag=true;
    $message .='<strong>ATTENTION!, your PHP Version is to old, XT-Commerce requires atleast PHP 4.1.3.</strong><br /><br />
                 Your php Version: <strong><?php echo phpversion(); ?></strong><br /><br />
                 XT-Commerce wont work on this server, update PHP or change Server.';
 }

 $status='OK';
 if ($php_flag==true) $status='<strong><font color="#ff0000">ERROR</font></strong>';
 $ok_message.='PHP VERSION .............................. '.$status.'<br /><hr noshade>';


 $gd=gd_info();

 if ($gd['GD Version']=='') $gd['GD Version']='<strong><font color="#ff0000">ERROR NO GDLIB FOUND!</font></strong>';

 $status=$gd['GD Version'].' <br />  if GDlib Version < 2+ , klick here for further instructions';

 // display GDlibversion
 $ok_message.='GDlib VERSION .............................. '.$status.'<br /><hr noshade>';

 if ($gd['GIF Read Support']==1 or $gd['GIF Support']==1) {
 $status='OK';
 } else {
 $status='<strong><font color="#ff0000">ERROR</font></strong><br />You don\'t have GIF support within your GDlib, you won\'t be able to use GIF images, and GIF overlayfunctions in XT-Commerce!';
 }
 $ok_message.='GDlib GIF-Support .............................. '.$status.'<br /><hr noshade>';

if ($error_flag==true) {
?>
        <td style="border: 1px solid; border-color: #ff0000;" bgcolor="#FFCCCC">
<font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>Attention:<br /></strong>
<?php echo $message; ?>
</font>
</td>
<?php } ?>
</tr>
<tr>
<?php
if ($ok_message!='') {
?>
<td height="20"></td></tr><tr>
<td style="border: 1px solid; border-color: #4CC534;" bgcolor="#C2FFB6">
<font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>Checking:<br /></strong>
<?php echo $ok_message; ?>
</font>
</td>
<?php } ?>
</tr>

      </table>
      <p><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/break-el.gif" width="100%" height="1"></font></p>


      <table width="98%" border="0" align="right" cellpadding="0" cellspacing="0">
        <tr>
          <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"><img src="images/icons/arrow-setup.jpg" width="16" height="16">
            <?php echo TITLE_SELECT_LANGUAGE; ?></font></strong><br />
            <img src="images/break-el.gif" width="100%" height="1"><br />
                                                        <?php
  if ($messageStack->size('index') > 0) {
?><br />
<table border="0" cellpadding="0" cellspacing="0" bgcolor="f3f3f3">
            <tr>
              <td><?php echo $messageStack->output('index'); ?></td>
  </tr>
</table>


<?php
  }
?>
            </font> <form name="language" method="post" action="index.php">

              <table width="300" border="0" cellpadding="0" cellspacing="4">
                <tr>
                  <td width="98"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/icons/arrow02.gif" width="13" height="6">German</font></td>
                  <td width="192"><img src="images/icons/icon-deu.gif" width="30" height="16">
                    <?php echo xtc_draw_radio_field_installer('LANGUAGE', 'german'); ?>
                  </td>
                </tr>
                <tr>
                  <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/icons/arrow02.gif" width="13" height="6">English</font></td>
                  <td><img src="images/icons/icon-eng.gif" width="30" height="16">
                    <?php echo xtc_draw_radio_field_installer('LANGUAGE', 'english'); ?> </td>
                </tr>
              </table>

              <input type="hidden" name="action" value="process">
              <p> <?php if ($error_flag==false) { ?><input type="image" src="images/button_continue.gif" border="0" alt="Continue"> <?php } ?><br />
                <br />
              </p>
            </form>

          </td>
        </tr>
      </table></td>
  </tr>
</table>

<p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo TEXT_FOOTER; ?>  </font></p>
<p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
  </font></p>
</body>
</html>
