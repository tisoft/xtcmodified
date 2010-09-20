<?php
  /* --------------------------------------------------------------
   $Id: upgrade.php 2010-07-22 DokuMan $

   xtc-Modified
   http://www.xtc-modified.org

   Copyright (c) 2010 xtc-Modified
   Released under the GNU General Public License
   --------------------------------------------------------------
   based on:
   (c) 2010 xtcModified (db_upgrade.php,v 1.00 2010/07/22); www.www.xtc-modified.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('../includes/configure.php');
  require('../includes/database_tables.php');

  require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');

  $restore_query = '';
  $used_files_display = '';

  //get browser language
  preg_match("/^([a-z]+)-?([^,;]*)/i", $_SERVER["HTTP_ACCEPT_LANGUAGE"], $lang);
  if ($lang[1] == 'de') {
    // German definitions
    define ('TITLE_UPGRADE','<br /><strong><h1>xtcModified-Datenbank Upgradevorgang</h1></strong>');
    define ('SUBMIT_VALUE', 'Datenbankupgrade durchf&uuml;hren');
    define ('SUCCESS_MESSAGE', '<br /><br /><strong>Datenbankupgrade erfolgreich!</strong><br /><br />Ausgef&uuml;hrte SQL-Befehle:<br /><br />');
    define ('UPGRADE_NOT_NECESSARY', 'Kein Datenbankupgrade notwendig, sie sind auf dem aktuellesten Stand!');
    define ('USED_FILES', '<br /><br />Folgende Dateien werden für das Upgrade auf die neueste Datenbank-Version verwendet:<br /><br />');
    define ('CURRENT_DB_VERSION', '<br />Ihre aktuelle Datenbank-Version ist: ');
    define ('FINAL_TEXT', 'Bitte l&ouml;schen Sie jetzt aus Sicherheitsgr&uuml;nden die Upgrade-Datei vom Server: ');
    define('TEXT_FOOTER','<a href="http://www.xtc-modified.org" target="_blank">xtcModified</a>' . '&nbsp;' . '&copy;' . date('Y') . '&nbsp;' . 'provides no warranty and is redistributable under the <a href="http://www.fsf.org/licensing/licenses/gpl.txt" target="_blank">GNU General Public License</a><br />eCommerce Engine 2006 based on <a href="http://www.xt-commerce.com/" rel="nofollow" target="_blank">xt:Commerce</a>');
    define('TEXT_TITLE','xtcModified Datenbankupgrade');
    define('OPTIMIZE_TABLE','(OPTIMIZE TABLE): Datenbanktabellen nach dem Upgrade optimieren (empfohlen)');
    define('OPTIMIZE_TABLE_GAIN','Die Optimierung der Datenbank führte zu einem Speicherplatzgewinn von: ');
  } else {
    // English definitions
    define ('TITLE_UPGRADE','<br /><strong><h1>xtcModified database upgrade process</h1></strong>');
    define ('SUBMIT_VALUE', 'Execute database upgrade');
    define ('SUCCESS_MESSAGE', '<br /><br /><strong>Database upgrade was executed successfully!</strong><br /><br />Used SQL-statements:<br /><br />');
    define ('UPGRADE_NOT_NECESSARY', 'Database upgrade not necessary, you are up to date!');
    define ('USED_FILES', '<br /><br />The following files will be used for the upgrade to the newest database version:<br /><br />');
    define ('CURRENT_DB_VERSION', '<br />Your current database version is: ');
    define ('FINAL_TEXT', 'Please delete the update file from your server for security reasons now: ');
    define('TEXT_FOOTER','<a href="http://www.xtc-modified.org" target="_blank">xtcModified</a>' . '&nbsp;' . '&copy;' . date('Y') . '&nbsp;' . 'provides no warranty and is redistributable under the <a href="http://www.fsf.org/licensing/licenses/gpl.txt" target="_blank">GNU General Public License</a><br />eCommerce Engine 2006 based on <a href="http://www.xt-commerce.com/" rel="nofollow" target="_blank">xt:Commerce</a>');
    define('TEXT_TITLE','xtcModified database upgrade');
    define('OPTIMIZE_TABLE','(OPTIMIZE TABLE): Optimize database tables after the upgrade (recommended)');
    define('OPTIMIZE_TABLE_GAIN','The optimization of the database saved: ');
  }

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
      $next = '';
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
      if (empty($next)) { // get the last insert query
        $next = 'insert';
      }

      // compare first 6 letters, if it fits an SQL statement to start a new line
      if ((strtoupper($next) == 'DROP T')
      || (strtoupper($next) == 'CREATE')
      || (strtoupper($next) == 'INSERT')
      || (strtoupper($next) == 'DELETE')
      || (strtoupper($next) == 'ALTER ')
      || (strtoupper($next) == 'TRUNCA')
      || (strtoupper($next) == 'UPDATE')) {
        $next = '';
        $sql_query = substr($restore_query, 0, $i);
        $sql_array[] = trim($sql_query);
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
<title><?php echo TEXT_TITLE; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
body {background: #eee; font-family: arial, sans-serif; font-size: 12px;}
table,td,div {font-family: arial, sans-serif; font-size: 12px;}
h1 {font-size: 18px; margin: 0; padding: 0; margin-bottom: 10px;}
a {color:#893769;}
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
          echo TITLE_UPGRADE;

          if(isset($_POST['submit'])) {
            // Write SQL-statements to database
            foreach ($sql_array as $stmt) {
              xtc_db_query($stmt);
            }
            // get new DB-Version
            $version_query = xtc_db_query("select version from " . TABLE_DATABASE_VERSION);
            $version_array = xtc_db_fetch_array($version_query);
            echo CURRENT_DB_VERSION.' <strong>'.$version_array['version'].'</strong>';
            echo SUCCESS_MESSAGE;
            echo '<div style="border:1px solid #ccc; background:#fff; padding:10px;">';

            // verbose SQL output on screen
            foreach ($sql_array as $stmt) {
              echo htmlentities($stmt).'<br />';
            }
            echo '</div>';

            // OPTIMIZE TABLES
            if (isset($_POST['submit']) && isset($_POST['optimizetables'])) {
              $tables = xtc_db_query('SHOW TABLE STATUS FROM ' . DB_DATABASE);
              while($row = xtc_db_fetch_array($tables)) {
                if ( $row['Data_free'] > 0 ) {
                xtc_db_query('OPTIMIZE TABLE '.$row['Name']);
                $gain += $row['Data_free']/1024/1024;
                }
              }
              echo '<br/><div style="border:1px solid #ccc; background:#fff; padding:10px;">';
              echo '<strong>'.OPTIMIZE_TABLE_GAIN . number_format($gain,3).' MB</strong></div>';
            }
            echo '<p style="color:red">'.FINAL_TEXT . basename($_SERVER['SCRIPT_FILENAME']).'<p>';

          } else {
            echo CURRENT_DB_VERSION.' <strong>'.$version_array['version'].'</strong>';
            echo USED_FILES ;
            echo '<div style="border:1px solid #ccc; background:#fff; padding:10px;">';
              if ($used_files_display != '') {
                echo $used_files_display;
              }
              else {
                echo UPGRADE_NOT_NECESSARY;
              }
            echo '<p style="color:red;font-weight:bold">'.FINAL_TEXT . basename($_SERVER['SCRIPT_FILENAME']).'<p>';              
            echo '</div>';
          }

          //HTML-input form
          if (!isset($_POST['submit']) && $used_files_display != '') {
            echo '<br /><form method="post" action="'.basename($_SERVER['SCRIPT_FILENAME']) .'">';
            echo '<input type="checkbox" name="optimizetables" value="1" checked="checked"/>'.OPTIMIZE_TABLE.'<br />';
            echo '<input type="submit" name="submit" value="'.SUBMIT_VALUE.'"/><br />
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
<div align="center" style="font-family:arial,sans-serif; font-size:11px; color:#666;"><?php echo TEXT_FOOTER; ?></div>
</body>
</html>