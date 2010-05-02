<?php
  /* --------------------------------------------------------------
   $Id: install_step2.php 1119 2005-07-25 22:19:50Z novalis $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(install_2.php,v 1.4 2002/08/12); www.oscommerce.com 
   (c) 2003	 nextcommerce (install_step2.php,v 1.16 2003/08/1); www.nextcommerce.org
   (c) 2009 xtcModified (install_step2.php,v 1.00 2009/07/13); www.www.xtc-modified.org 

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('includes/application.php');

  // include needed functions
  require_once(DIR_FS_INC.'xtc_redirect.inc.php');
  require_once(DIR_FS_INC.'xtc_href_link.inc.php');
  require_once(DIR_FS_INC.'xtc_not_null.inc.php');

  //BOF - web28 - 2010.02.11 - NEW LANGUAGE HANDLING IN application.php
  //include('language/'.$_SESSION['language'].'.php');
  include('language/'.$lang.'.php');
  //EOF - web28 - 2010.02.11 - NEW LANGUAGE HANDLING IN application.php
  
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
  
  // BOF - web28 - 2010.02.20 - NEW STEP2-4 Handling
  if($_POST['install_db'] == 1) {
   $test_welcome_step2 = TEXT_WELCOME_STEP2;
  } else {
   $test_welcome_step2 = TEXT_WELCOME_STEP2A;
  }
  // EOF - web28 - 2010.02.20 - NEW STEP2-4 Handling
  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>xtcModified Installer - STEP 2 / DB Connection</title>
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
          <td> <img src="images/step2.gif" width="705" height="180" border="0"><br />
            <br />
            <br />
            <div style="border:1px solid #ccc; background:#fff; padding:10px;"><?php echo $test_welcome_step2; ?></div></td>
        </tr>
      </table>

    <br />

      <table width="95%" border="0" cellpadding="0" cellspacing="0"> 
      <tr>
    <td> 
      <?php
  // BOF - web28 - 2010.02.20 - NEW STEP4 Handling
  //if (xtc_in_array('database', $_POST['install'])) {
  // EOF - web28 - 2010.02.20 - NEW STEP4 Handling
    $db = array();
    $db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
    $db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
    $db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
    $db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));

    $db_error = false;
    xtc_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);
	
	// BOF - vr - 2010-01-14 - check MySQL *server* version
	if (!$db_error) {
		if (function_exists('version_compare')) {
		    //BOF - web28 - 2010-02-10 - check for 'native'
			if(version_compare(mysql_get_server_info(), "4.1.2", "<") && strpos(strtolower(mysql_get_server_info()), 'native')=== false){
			//EOF - web28 - 2010-02-10 - check for 'native'
				$db_error = '<br /><strong>' . TEXT_DB_SERVER_VERSION_ERROR .  ' 4.1.2. <br /><br />' . TEXT_DB_SERVER_VERSION . mysql_get_server_info() . '</strong>.';
			}
		}
	}
	// EOF - vr - 2010-01-14 - check MySQL *server* version
	
	// BOF - web28 - 2010-02-10 - check MySQL *client* version
	$db_warning = '';
	if (!$db_error) {
		if (function_exists('version_compare')) {
		    //BOF - web28 - 2010-02-10 - check for 'native'
			if(version_compare(mysql_get_client_info(), "4.1.2", "<") && strpos(strtolower(mysql_get_client_info()), 'native')=== false){
			//EOF - web28 - 2010-02-10 - check for 'native'
				$db_warning = '<strong>' . TEXT_DB_CLIENT_VERSION_WARNING .  '<br /><br />' . TEXT_DB_CLIENT_VERSION . mysql_get_client_info() . '</strong>.';
			}
		}
	}
	// EOF - web28 - 2010-02-10 - check MySQL *client* version

    if (!$db_error) {
      xtc_db_test_create_db_permission($db['DB_DATABASE']);
    }

    if ($db_error) {
	
	
?>
      <br />
      <table width="95%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td><h1><?php echo TEXT_CONNECTION_ERROR; ?></h1></td>
   
        </tr>
      </table>
      <table width="100%" cellpadding="0" cellspacing="0">
<tr><td>
     <div style="border:1px solid #ccc; background:#fff; padding:10px;">
          <p><?php echo TEXT_DB_ERROR; ?></p></div>
          <p class="boxme">
          <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="f3f3f3">
            <tr>
              <td><div style="border:1px solid #ccc; background:#ff0000; color:#fff; padding:10px;"><?php echo $db_error; ?></div></td>
  </tr>
</table>
          </p> 
          
          <div style="border:1px solid #ccc; background:#fff; padding:10px;"><p><?php echo TEXT_DB_ERROR_1; ?></p>
          <p><?php echo TEXT_DB_ERROR_2; ?></p></div>

<form name="install" action="install_step1.php" method="post">

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
    <td align="right"><a href="index.php"><img src="images/button_cancel.gif" border="0" alt="Cancel"></a> <input type="image" src="images/button_back.gif" border="0" alt="Back"></td>
  </tr>
</table>
<br />
</td></tr></table>
</form>
<?php
    } else {
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td><h1><?php echo TEXT_CONNECTION_SUCCESS; ?></h1></td>
    
  </tr>
</table>
<?php // BOF - web28 - 2010.02.20 - NEW STEP2-4 Handling
  if($_POST['install_db'] == 1) {
?>
<div style="border:1px solid #ccc; background:#fff; padding:10px;">
<p><?php echo TEXT_PROCESS_1; ?></p>
      <p><?php echo TEXT_PROCESS_2; ?></p>
      <p><?php echo TEXT_PROCESS_3; ?> <b><?php echo DIR_FS_CATALOG . 'xtc_installer/xtcommerce.sql'; ?></b>.</p>
</div>
<?php // EOF - web28 - 2010.02.20 - NEW STEP2-4 Handling
  }
?>
<?php //BOF - web28 - 2010-02-10 - DB CLIENT WARNING 
if ($db_warning != '') {
?>
<div style="border:1px solid #ccc; background:#ff0000; color:#fff; padding:10px;"><?php echo $db_warning; ?></div>
<?php
} 
//EOF - web28 - 2010-02-10 - DB CLIENT WARNING ?>

<?php
// BOF - web28 - 2010.02.20 - NEW STEP2-4 Handling
if($_POST['install_db'] == 1) {
   echo '<form name="install" action="install_step3.php" method="post">';
   $install_db = 1;
  } else {
   echo '<form name="install" action="install_step4.php" method="post">';
  }
if($_POST['install_cfg'] == 1) {$create_config = 1;};
//<form name="install" action="install_step3.php" method="post">
// EOF - web28 - 2010.02.20 - NEW STEP2-4 Handling
?>
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
    <?php// BOF - web28 - 2010.02.20 - NEW STEP2-4 Handling ?>
    <td align="right"><a href="install_step1.php?db=<?php echo $install_db;?>&cfg=<?php echo $create_config;?>"><img src="images/button_cancel.gif" border="0" alt="Cancel"></a> <input type="image" src="images/button_continue.gif"></td>
    <?php// EOF - web28 - 2010.02.20 - NEW STEP2-4 Handling ?>
  </tr>
</table>

</form>


<?php
    }
  // BOF - web28 - 2010.02.20 - NEW STEP2-4 Handling
  //} 
  //EOF - web28 - 2010.02.20 - NEW STEP2-4 Handling
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
