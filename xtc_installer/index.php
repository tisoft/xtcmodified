<?php
  /* --------------------------------------------------------------
   $Id: index.php 1220 2005-09-16 15:53:13Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (index.php,v 1.18 2003/08/17); www.nextcommerce.org
   (c) 2009 xtcModified (index.php,v 1.00 2009/07/13); www.www.xtc-modified.org
   
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
<title>xtcModified Installer - Welcome</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">

body { background: #eee; font-family: Arial, sans-serif; font-size: 12px;}
table,td,div { font-family: Arial, sans-serif; font-size: 12px;}
h1 { font-size: 18px; margin: 0; padding: 0; margin-bottom: 10px; }
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
<img src='http://count.xtc-modified.org/pixel_counter.gif?piwik_campaign=xtcModified-installs' border='0' alt=''>
<table width="800" style="border:30px solid #fff;" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="95" colspan="2" ><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="1"><img src="images/logo.gif" alt="" /></td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>

    <td align="right" valign="top">
      <br />
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><img src="images/title_index.gif" width="705" height="180" border="0"><br />
            
            <br /><br /><div style="border:1px solid #ccc; background:#fff; padding:10px;"><?php echo TEXT_WELCOME_INDEX; ?><br /><br /><a href="http://www.xtc-modified.org/spenden"><img src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" alt="<?php echo TEXT_INFO_DONATIONS_IMG_ALT; ?>" border="0"></a></div><br />
            <br /></td>
        </tr>
        
<?php
  // permission check to prevent DAU faults.
 $error_flag=false;
 $message='';
 $ok_message='';

 // config files
 if (!is_writeable(DIR_FS_CATALOG . 'includes/configure.php')) {
    $error_flag=true;
    $message .= 'FALSCHE DATEIRECHTE '.DIR_FS_CATALOG . 'includes/configure.php<br />';
 }
  if (!is_writeable(DIR_FS_CATALOG . 'includes/configure.org.php')) {
    $error_flag=true;
    $message .= 'FALSCHE DATEIRECHTE '.DIR_FS_CATALOG . 'includes/configure.org.php<br />';
 }
  if (!is_writeable(DIR_FS_CATALOG . 'admin/includes/configure.php')) {
    $error_flag=true;
    $message .= 'FALSCHE DATEIRECHTE '.DIR_FS_CATALOG . 'admin/includes/configure.php<br />';
 }
  if (!is_writeable(DIR_FS_CATALOG . 'admin/includes/configure.org.php')) {
    $error_flag=true;
    $message .= 'FALSCHE DATEIRECHTE '.DIR_FS_CATALOG . 'admin/includes/configure.org.php<br />';
 }
 $status='OK';
 if ($error_flag==true) $status='<strong><font color="#ff0000">FEHLER</font></strong>';
 $ok_message.='DATEIRECHTE .............................. '.$status.'<br /><hr noshade>';

 // smarty folders
 $folder_flag==false;
 
    if (!is_writeable(DIR_FS_CATALOG . 'admin/rss/xt-news.cache')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'admin/rss/xt-news.cache<br />';
 }
 
   if (!is_writeable(DIR_FS_CATALOG . 'templates_c/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'templates_c/<br />';
 }
    if (!is_writeable(DIR_FS_CATALOG . 'cache/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'cache/<br />';
 }

     if (!is_writeable(DIR_FS_CATALOG . 'admin/rss/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'admin/rss/<br />';
 }

      if (!is_writeable(DIR_FS_CATALOG . 'admin/images/graphs')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'admin/images/graphs<br />';
 }

    if (!is_writeable(DIR_FS_CATALOG . 'admin/backups/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'admin/backups/<br />';
 }

 // image folders
      if (!is_writeable(DIR_FS_CATALOG . 'images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'images/<br />';
 }
     if (!is_writeable(DIR_FS_CATALOG . 'images/categories/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'images/categories/<br />';
 }
     if (!is_writeable(DIR_FS_CATALOG . 'images/banner/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'images/banner/<br />';
 }
     if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/info_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'images/product_images/info_images/<br />';
 }
     if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/original_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'images/product_images/original_images/<br />';
 }
     if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/popup_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'images/product_images/popup_images/<br />';
 }
      if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/thumbnail_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'images/product_images/thumbnail_images/<br />';
 }
 
   if (!is_writeable(DIR_FS_CATALOG . 'admin/rss/xt-news.cache')) {
    $error_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'admin/rss/xt-news.cache<br />';
 }

 if (!is_writeable(DIR_FS_CATALOG . 'sitemap.xml')) {
    $error_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'sitemap.xml<br />';
 }
 
  if (!is_writeable(DIR_FS_CATALOG . 'import/')) {
    $error_flag=true;
    $message .= 'FALSCHE ORDNERRECHTE '.DIR_FS_CATALOG . 'import/<br />';
 }

 $status='OK';
 if ($folder_flag==true) $status='<strong><font color="#ff0000">FEHLER</font></strong>';
 $ok_message.='ORDNERRECHTE .............................. '.$status.'<br /><hr noshade>';

 // check PHP-Version

 $php_flag==false;
 //BOF - Dokuman - 2009-09-02: update PHP-Version check
 /*
 if (xtc_check_version()!=1) {
     $error_flag=true;
     $php_flag=true;
    $message .='<strong>ATTENTION!, your PHP Version is to old, xtc:Modified requires atleast PHP 4.1.3.</strong><br /><br />
                 Your php Version: <strong><?php echo phpversion(); ?></strong><br /><br />
                 xtc:Modified wont work on this server, update PHP or change Server.';
 }
 */
 if (function_exists('version_compare')) {
   if(version_compare(phpversion(), "4.3.3", "<")){
	   $error_flag = true;
     $php_flag = true;
     $message .= '<strong>ACHTUNG! Ihre PHP-Version ist zu alt. Der Shop setzt mindestens die Version 4.3.3 voraus.</b><br /><br />
                 Ihre PHP-Version: <b>' . phpversion() . '</strong>.';
	 }
	
 }
 else{
 		$error_flag = true;
    $php_flag = true;
    $message .= '<strong>ACHTUNG! Ihre PHP-Version ist zu alt. Der Shop setzt mindestens die Version 4.3.3 voraus.</b><br /><br />
                 Ihre PHP-Version: <b>' . phpversion() . '</strong>.';
 }
 //EOF - Dokuman - 2009-09-02: update PHP-Version check

 $status='OK';
 if ($php_flag==true) $status='<strong><font color="#ff0000">FEHLER</font></strong>';
 
 //PHP 5.3 WARNING 
 if (function_exists('version_compare')) {
	if(version_compare(phpversion(), "5.3.0", ">=")){
		$status = '<strong><font color="#FF0000">WARNUNG! Ihre PHP-Version ist zu neu! Bitte nur als Testshop installieren!</font></strong>';		
	}
 }
 //EOF PHP 5.3 WARNING
 
 $ok_message.='PHP VERSION .............................. '.phpversion(). '&nbsp;&nbsp;&nbsp;'.$status.'<br /><hr noshade>';
 
 // BOF - Tomcraft - 2009-11-22 - Check MySQL version
 if (function_exists('version_compare')) {
	if(version_compare(mysql_get_client_info(), "4.1.2", "<")){
		$error_flag = true;
		$php_flag = true;
		$message .= '<br /><strong>ACHTUNG! Ihre MySQL-Version ist zu alt. Der Shop setzt mindestens die Version 4.1.2 voraus.</b><br /><br />
					Ihre MySQL-Version: <b>' . mysql_get_client_info() . '</strong>.';
    }
	$status='OK';
	if ($php_flag==true) $status='<strong><font color="#ff0000">FEHLER</font></strong>';
	$ok_message.='MySQL-VERSION .............................. '.mysql_get_client_info() . ' '. $status.'<br /><hr noshade>'; 
 }
 // EOF - Tomcraft - 2009-11-22 - Check MySQL version
 
 $gd=gd_info();

 if ($gd['GD Version']=='') $gd['GD Version']='<strong><font color="#ff0000">FEHLER: KEINE GDLIB GEFUNDEN!</font></strong>';

 $status=$gd['GD Version'].' <br /> falls GDlib Version < 2+ , klicken Sie hier f&uuml;r weitere Informationen';

 // display GDlibversion
 $ok_message.='GDlib VERSION .............................. '.$status.'<br /><hr noshade>';

 if ($gd['GIF Read Support']==1 or $gd['GIF Support']==1) {
 $status='OK';
 } else {
 $status='<strong><font color="#ff0000">FEHLER</font></strong><br />Sie haben keine GIF-Unterst&uuml;tzung innerhalb der GDlib, so dass Sie im Shop keine GIF-Bilder und GIF-Wasserzeichen-Funktionen nutzen k&ouml;nnen!';
 }
 $ok_message.='GDlib GIF-Unterst&uuml;tzung .............................. '.$status.'<br /><hr noshade>';

if ($error_flag==true) {
?><tr>
        <td>
<h1>Attention // Achtung:</h1><br />
<div style="background:#fff; padding:10px; border:1px solid #ccc">
Die folgenden Dateien und Ordner benötigen Schreibrechte ( Chmod 0777 )<br />
The following files must be writeable ( Chmod 0777 )
</div><br /> 
<div style="background:#ff0000; color:#ffffff; padding:10px; border:1px solid #cf0000">
<?php echo $message; ?>
</div>

</td></tr>
<?php } ?>

<tr>
<?php
if ($ok_message!='') {
?>
<td height="20"></td></tr><tr>
<td style="border: 1px solid; border-color: #4CC534; padding:10px;" bgcolor="#C2FFB6">
<strong>Checking:<br /></strong>
<?php echo $ok_message; ?>

</td>
<?php } ?>
</tr>

      </table>
      <p><img src="images/break-el.gif" width="100%" height="1"></p>


      <table width="98%" border="0" align="right" cellpadding="0" cellspacing="0">
        <tr>
          <td><strong><?php echo TITLE_SELECT_LANGUAGE; ?></strong><br />
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
             <form name="language" method="post" action="index.php">

              <table width="300" border="0" cellpadding="0" cellspacing="4">
                <tr>
                  <td width="98"><img src="images/icons/arrow02.gif" width="13" height="6">German</td>
                  <td width="192"><img src="images/icons/icon-deu.gif" width="30" height="16">
                    <?php echo xtc_draw_radio_field_installer('LANGUAGE', 'german'); ?>
                  </td>
                </tr>
                <tr>
                  <td><img src="images/icons/arrow02.gif" width="13" height="6">English</td>
                  <td><img src="images/icons/icon-eng.gif" width="30" height="16">
                    <?php echo xtc_draw_radio_field_installer('LANGUAGE', 'english'); ?> </td>
                </tr>
              </table>

              <input type="hidden" name="action" value="process">
              <?php if ($error_flag==false) { ?><br /><input type="image" src="images/button_continue.gif"> <?php } ?><br />
                
              
            </form>

          </td>
        </tr>
      </table></td>
  </tr>
</table><br />
<div align="center" style="font-family:Arial, sans-serif; font-size:11px;">eCommerce Engine 2006 based on xt:Commerce<br />
eCommerce Engine &copy; 2008 - 2009 xtcModified.org licensed under GNU/GPL</div>
</body>
</html>
