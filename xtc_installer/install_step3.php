<?php
  /* --------------------------------------------------------------
   $Id: install_step3.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(install_3.php,v 1.6 2002/08/15); www.oscommerce.com 
   (c) 2009 xtcModified (install_step3.php,v 1.00 2009/07/13); www.www.xtc-modified.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

   require('includes/application.php');

   //BOF - web28 - 2010.02.11 - NEW LANGUAGE HANDLING IN application.php
  //include('language/'.$_SESSION['language'].'.php');
  include('language/'.$lang.'.php');
  //EOF - web28 - 2010.02.11 - NEW LANGUAGE HANDLING IN application.php
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>xtcModified Installer - STEP 3 / DB Import</title>
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
<table width="800" style="border:30px solid #fff;" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td height="95" colspan="2" ><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td><img src="images/logo.gif" alt="" /></td>
        </tr>
      </table>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <br />
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><img src="images/step3.gif" width="705" height="180" border="0"><br />
            <br />
            <br />
            <div style="border:1px solid #ccc; background:#fff; padding:10px;">
            <?php echo TEXT_WELCOME_STEP3; ?></div></td>
        </tr>
      </table>
<br />
      <table width="95%" border="0">
        <tr>
          <td> 
            <?php
  //BOF - web28 - 2010-03-18 - change install[]  to install_cfg
  //if (xtc_in_array('database', $_POST['install'])) {
  if($_POST['install_db'] == 1) {
  //EOF - web28 - 2010-03-18 - change install[]  to install_cfg
    $db = array();
    $db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
    $db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
    $db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
    $db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));

    xtc_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);

    $db_error = false;
    $sql_file = DIR_FS_CATALOG . 'xtc_installer/xtcommerce.sql';
//    $script_filename = (($SCRIPT_FILENAME) ? $SCRIPT_FILENAME : $HTTP_SERVER_VARS['SCRIPT_FILENAME']);
//    $script_directory = dirname($script_filename);
//    $sql_file = $script_directory . '/nextcommerce.sql';

//    @xtc_set_time_limit(0);
    xtc_db_install($db['DB_DATABASE'], $sql_file);

    if ($db_error) {
?>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr> 
                <td style="border-bottom: 1px solid; border-color: #CFCFCF">
                  <h1><?php echo TEXT_TITLE_ERROR; ?></h1></td>
                <td style="border-bottom: 1px solid; border-color: #CFCFCF">&nbsp;</td>
              </tr>
            </table>
            <table border="0" cellpadding="0" cellspacing="0" bgcolor="f3f3f3">
            <tr>
              <td><b><?php echo $db_error; ?></b></td>
  </tr>
</table>

<form name="install" action="install_step3.php" method="post">

<?php
      reset($_POST);
      while (list($key, $value) = each($_POST)) {
        if ($key != 'x' && $key != 'y') {
          if (is_array($value)) {
            for ($i=0; $i<sizeof($value); $i++) {
              echo xtc_draw_hidden_field_installer($key . '[]', $value[$i]);
            }
          } else {
            echo xtc_draw_hidden_field_installer($key, $value);
          }
        }
      }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="index.php"><img src="images/button_cancel.gif" border="0" alt="Cancel"></a></td>
    <td align="center"><input type="image" src="images/button_retry.gif" border="0" alt="Retry"></td>
  </tr>
</table>

</form>

            <?php
    } else {
?>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr> 
                <td align="center"><div style="border:1px solid #ccc; background:#fff; padding:10px;"><h1><?php echo TEXT_TITLE_SUCCESS; ?></h1></div></td>
                </tr>
            </table>
            <form name="install" action="install_step4.php" method="post">

<?php
      reset($_POST);
      while (list($key, $value) = each($_POST)) {
        if ($key != 'x' && $key != 'y') {
          if (is_array($value)) {
            for ($i=0; $i<sizeof($value); $i++) {
              echo xtc_draw_hidden_field_installer($key . '[]', $value[$i]);
            }
          } else {
            echo xtc_draw_hidden_field_installer($key, $value);
          }
        }
      }
?>
<br />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
<?php
      //BOF - web28 - 2010-03-18 - change install[]  to install_cfg
	  //if (xtc_in_array('configure', $_POST['install'])) {
      if($_POST['install_cfg'] == 1) {
	  //EOF - web28 - 2010-03-18 - change install[]  to install_cfg
?>

    <td align="right"><input type="image" src="images/button_continue.gif"></td>
<?php
      } else {
?>

    <td align="right"><a href="index.php"><img src="images/button_continue.gif"></a></td>
<?php
      }
?>
  </tr>
</table>

</form>

<?php
    }
  }
?>
                  
                  </td>
        </tr>
      </table>
      
      </td>
  </tr>
</table>

<br />
<div align="center" style="font-family:Arial, sans-serif; font-size:11px;"><?php echo TEXT_FOOTER; ?></div>

</body>
</html>
