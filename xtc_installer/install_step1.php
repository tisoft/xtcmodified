<?php
/* --------------------------------------------------------------
   $Id: install_step1.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(install.php,v 1.7 2002/08/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (install_step1.php,v 1.10 2003/08/17); www.nextcommerce.org
   (c) 2009 xtcModified (install_step1.php,v 1.00 2009/07/13); www.www.xtc-modified.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
  
  require('includes/application.php');

  include('language/'.$_SESSION['language'].'.php');
  
  if (!$script_filename = str_replace('\\', '/', getenv('PATH_TRANSLATED'))) {
    $script_filename = getenv('SCRIPT_FILENAME');
  }
  $script_filename = str_replace('//', '/', $script_filename);

  if (!$request_uri = getenv('REQUEST_URI')) {
    if (!$request_uri = getenv('PATH_INFO')) {
      $request_uri = getenv('SCRIPT_NAME');
    }

    if (getenv('QUERY_STRING')) $request_uri .=  '?' . getenv('QUERY_STRING');
  }

  $dir_fs_www_root_array = explode('/', dirname($script_filename));
  $dir_fs_www_root = array();
  for ($i=0; $i<sizeof($dir_fs_www_root_array)-2; $i++) {
    $dir_fs_www_root[] = $dir_fs_www_root_array[$i];
  }
  $dir_fs_www_root = implode('/', $dir_fs_www_root);

  $dir_ws_www_root_array = explode('/', dirname($request_uri));
  $dir_ws_www_root = array();
  for ($i=0; $i<sizeof($dir_ws_www_root_array)-1; $i++) {
    $dir_ws_www_root[] = $dir_ws_www_root_array[$i];
  }
  $dir_ws_www_root = implode('/', $dir_ws_www_root);
  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>xtcModified Installer - STEP 1 / Settings</title>
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
<table width="800" style="border:30px solid #fff;" height="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td height="95" colspan="2" ><table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
          <td width="1"><img src="images/logo.gif" alt="" /></td>
        </tr>
      </table>
  </tr>
  <tr> 
    
    <td align="center" valign="top"> 
      <br />
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><img src="images/step1.gif" width="705" height="180" border="0"><br />
            <br />
            <br />
            <div style="border:1px solid #ccc; background:#fff; padding:10px;"><?php echo TEXT_WELCOME_STEP1; ?></div></td>
        </tr>
      </table>

      <br />

      <form name="install" method="post" action="install_step2.php">
            <table width="95%" border="0" cellpadding="0" cellspacing="0">
          <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td><h1><?php echo TITLE_CUSTOM_SETTINGS; ?></h1></td>
                  
                </tr>
              </table>
             <div style="border:1px solid #ccc; background:#fff; padding:10px;">
              <p><?php echo xtc_draw_checkbox_field_installer('install[]', 'database', true); ?>
                <b><?php echo TEXT_IMPORT_DB; ?></b><br />
                <?php echo TEXT_IMPORT_DB_LONG; ?></p>
              <p><?php echo xtc_draw_checkbox_field_installer('install[]', 'configure', true); ?> 
                <b><?php echo TEXT_AUTOMATIC; ?></b><br />
                <?php echo TEXT_AUTOMATIC_LONG; ?></p>
                </div>

</td>
  </tr>
</table>
        <br />

                <br />
        <table width="95%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td>
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><h1><?php echo TITLE_DATABASE_SETTINGS; ?></h1></td>
                 
                </tr>
              </table>
              <div style="border:1px solid #ccc; background:#fff; padding:10px;">
              <p><b><?php echo TEXT_DATABASE_SERVER; ?></b><br />
                <?php echo xtc_draw_input_field_installer('DB_SERVER'); ?><br />
                <?php echo TEXT_DATABASE_SERVER_LONG; ?></p>
              <p><b><?php echo TEXT_USERNAME; ?></b><br />
                <?php echo xtc_draw_input_field_installer('DB_SERVER_USERNAME'); ?><br />
                <?php echo TEXT_USERNAME_LONG; ?></p>
              <p><b><?php echo TEXT_PASSWORD; ?></b><br />
                <?php echo xtc_draw_input_field_installer('DB_SERVER_PASSWORD'); ?><br />
                <?php echo TEXT_PASSWORD_LONG; ?></p>
              <p><b><?php echo TEXT_DATABASE; ?></b><br />
                <?php echo xtc_draw_input_field_installer('DB_DATABASE'); ?><br />
                <?php echo TEXT_DATABASE_LONG; ?></p>
                
                </div>
                </td>
          </tr>
        </table>
                <br />
                              <br />
                <table width="95%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td><h1>
                    <?php echo TITLE_WEBSERVER_SETTINGS; ?> </h1></td>
                  
                </tr>
              </table>
             <div style="border:1px solid #ccc; background:#fff; padding:10px;">
              <p><b><?php echo TEXT_WS_ROOT; ?></b><br />
                <?php echo xtc_draw_input_field_installer('DIR_FS_DOCUMENT_ROOT', $dir_fs_www_root,'','size=60'); ?><br />
                <?php echo TEXT_WS_ROOT_LONG; ?></p>
              <p><b><?php echo TEXT_WS_XTC; ?></b><br />
                <?php echo xtc_draw_input_field_installer('DIR_FS_CATALOG', $local_install_path,'','size=60'); ?><br />
                <?php echo TEXT_WS_XTC_LONG; ?></p>
              <p><b><?php echo TEXT_WS_ADMIN; ?></b><br />
                <?php echo xtc_draw_input_field_installer('DIR_FS_ADMIN', $local_install_path.'admin/','','size=60'); ?><br />
               <?php echo TEXT_WS_ADMIN_LONG; ?></p>
              <p><b> <?php echo TEXT_WS_CATALOG; ?></b><br />
                <?php echo xtc_draw_input_field_installer('DIR_WS_CATALOG', $dir_ws_www_root . '/','','size=60'); ?><br />
                 <?php echo TEXT_WS_CATALOG_LONG; ?></p>
              <p><b> <?php echo TEXT_WS_ADMINTOOL; ?></b><br />
                <?php echo xtc_draw_input_field_installer('DIR_WS_ADMIN', $dir_ws_www_root . '/admin/','','size=60'); ?><br />
                 <?php echo TEXT_WS_ADMINTOOL_LONG; ?></p>
              </div>
              </td>
          </tr>
        </table>
<br />


<table border="0" width="95%" cellspacing="0" cellpadding="0">
  <tr>
        <td align="right"><a href="index.php"><img src="images/button_cancel.gif" border="0" alt="Cancel"></a> <input type="image" src="images/button_continue.gif" border="0" alt="Continue"></td>
  </tr>
</table>
</form>
      </td>
  </tr>
</table>
<br />
<div align="center" style="font-family:Arial, sans-serif; font-size:11px;"><?php echo TEXT_FOOTER; ?></div>
</body>
</html>
