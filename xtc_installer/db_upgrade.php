<?php
  /* --------------------------------------------------------------
   $Id: upgrade.php 2010-07-22 DokuMan $   

   xtc-Modified 
   http://www.xtc-modified.org

   Copyright (c) 2010 xtc-Modified
   Released under the GNU General Public License
   --------------------------------------------------------------
   based on:
   (c) 2003	nextcommerce (install_step7.php,v 1.26 2003/08/17); www.nextcommerce.org
   (c) 2009 xtcModified (install_step7.php,v 1.00 2009/07/13); www.www.xtc-modified.org 
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('../includes/configure.php'); 
  require('../includes/database_tables.php');

  require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');

  $restore_query = '';
  $used_files_display = '';
  $lang = 'german'; //hard coded language
  include('language/'.$lang.'.php');
  
  // Definitions
  define ('TITLE_UPGRADE','<br /><strong><h1>xtcModified Upgrade</h1></strong>');
  define ('SUBMIT_VALUE', 'Datenbankupgrade durchf&uuml;hren');
  define ('SUCCESS_MESSAGE', '<strong>Datenbankupgrade erfolgreich!</strong><br /><br />Ausgef&uuml;hrte SQL-Befehle:<br /><br />');
  define ('UPGRADE_NOT_NECESSARY', 'Kein Datenbankupgrade notwendig, sie sind auf dem aktuellesten Stand!');
  define ('USED_FILES', 'Folgende Dateien werden für das Upgrade auf die neueste Datenbank-Version verwendet:<br /><br />');
  define ('CURRENT_DB_VERSION', 'Ihre aktuelle Datenbank-Version ist');
  define ('FINAL_TEXT', '<br /><br /><strong>Bitte l&ouml;schen Sie jetzt aus Sicherheitsgr&uuml;nden die Upgrade-Datei vom Server:</strong><br /> ');

  // get DB version
  xtc_db_connect() or die('Unable to connect to database server!');  
  $version_query = xtc_db_query("select version from " . TABLE_DATABASE_VERSION);
  $version_array = xtc_db_fetch_array($version_query);
  $db_version = substr($version_array['version'], -7, 7); //return version, e.g. '1.0.5.0' when 'xtcM_1.0.5.0'
  $db_version_update = 'update_' . $db_version;

  // get all SQL update_files
  $ordner = opendir('.');
  while($datei = readdir($ordner)) {
    if(preg_match('/update_/i', $datei)) {
           $farray[] = $datei;
     }   
  }
  closedir($ordner);
  sort($farray);
  
  // drop unnecessary SQL update_files less than "$db_version"
  foreach($farray as $key => $item) {
    if(preg_match("/$db_version_update/", $item)){
      break;
    }
    else {
      unset ($farray[$key]);
    }
  }

  // Load and process all remaining SQL files
  foreach($farray as $sqlFileToExecute) {
    $used_files_display .= $sqlFileToExecute.'<br />';
    $f = fopen($sqlFileToExecute,'rb');
    $restore_query .= fread($f,filesize($sqlFileToExecute));
    fclose($f);
  }
  
  //$sql_array = explode(";\n",$restore_query);

  // SQL parsing taken from xtc_db_install.inc.php
  $sql_array = array();
  $sql_length = strlen($restore_query);
  $pos = strpos($restore_query, ';');
  for ($i=$pos; $i<$sql_length; $i++) {
    if ($restore_query[0] == '#') {
      $restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
      $sql_length = strlen($restore_query);
      $i = strpos($restore_query, ';')-1;
      continue;
    }
    if ($restore_query[($i+1)] == "\n") {
      for ($j=($i+2); $j<$sql_length; $j++) {
        if (trim($restore_query[$j]) != '') {
          $next = substr($restore_query, $j, 6);
          if ($next[0] == '#') {
            // find out where the break position is so we can remove this line (#comment line)
            for ($k=$j; $k<$sql_length; $k++) {
              if ($restore_query[$k] == "\n") break;
            }
            $query = substr($restore_query, 0, $i+1);
            $restore_query = substr($restore_query, $k);
            // join the query before the comment appeared, with the rest of the dump
            $restore_query = $query . $restore_query;
            $sql_length = strlen($restore_query);
            $i = strpos($restore_query, ';')-1;
            continue 2;
          }
          break;
        }
      }
      if ($next == '') { // get the last insert query
        $next = 'insert';
      }
      if ( (preg_match('/create/i', $next)) 
        || (preg_match('/insert/i', $next)) 
        || (preg_match('/drop t/i', $next)) 
        || (preg_match('/delete/i', $next)) 
        || (preg_match('/alter/i',  $next)) 
        || (preg_match('/update/i', $next)) ) {
        $next = '';
        $sql_array[] = substr($restore_query, 0, $i);
        $restore_query = ltrim(substr($restore_query, $i+1));
        $sql_length = strlen($restore_query);
        $i = strpos($restore_query, ';')-1;
      }
    }
  }
  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>xtc-Modified Updater</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
body { background: #eee; font-family: Arial, sans-serif; font-size: 12px;}
table,td,div { font-family: Arial, sans-serif; font-size: 12px;}
h1 { font-size: 18px; margin: 0; padding: 0; margin-bottom: 10px; }
</style>
</head>
<body>
<table width="800" style="border:30px solid #fff;" bgcolor="#f3f3f3" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td height="95" colspan="2" >
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td><img src="images/logo.gif" alt="" /></td>
        </tr>
      </table>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <table width="95%" border="0" cellpadding="0" cellspacing="0">
         <tr>
          <td><br />
          <?php
          if(isset($_POST['submit'])) { 
          
            // Write SQL-statements to database 
            foreach ($sql_array as $stmt) {
              xtc_db_query($stmt);
            }
            // get new DB-Version
            $version_query = xtc_db_query("select version from " . TABLE_DATABASE_VERSION);
            $version_array = xtc_db_fetch_array($version_query);            
            echo TITLE_UPGRADE ;
            echo CURRENT_DB_VERSION.' <strong>'. $version_array['version'].'</strong>.<br /><br />';
            echo SUCCESS_MESSAGE;
            echo '<div style="border:1px solid #ccc; background:#fff; padding:10px;">';

            // verbose SQL output on screen
            foreach ($sql_array as $stmt) {
              echo htmlentities($stmt).'<br />';
            }
            echo '</div>';
            echo FINAL_TEXT . htmlentities($_SERVER['PHP_SELF']);

          }
          else {         
            echo TITLE_UPGRADE ;
            echo CURRENT_DB_VERSION .' <strong>'. $version_array['version'] .'</strong>.<br /><br />';
            echo USED_FILES ;
            echo '<div style="border:1px solid #ccc; background:#fff; padding:10px;">';
              if ($used_files_display != '') {
                echo $used_files_display;
              }
              else {
                echo UPGRADE_NOT_NECESSARY;
              }
            echo '</div>';
          } 
          if (!isset($_POST['submit']) && $used_files_display != '') { 
            echo '<br /><form method="post" action="'.htmlentities($_SERVER['PHP_SELF']) .'">
            <input type="submit" name="submit" value="'.SUBMIT_VALUE.'"><br />
            </form>';
          } 
          ?>
          </td>
         </tr>
      </table>
      <br />     
    </td>
  </tr>
</table>

<br />
<div align="center" style="font-family:Arial, sans-serif; font-size:11px;"><?php echo TEXT_FOOTER; ?></div>
</body>
</html>