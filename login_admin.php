<?php
/* -----------------------------------------------------------------------------------------
   $Id$   

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2008 Gambio OHG - login_admin.php 2008-08-10 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

if(isset($_GET['repair'] )) {
	$action = 'login_admin.php';
} else {
	$action = 'login.php?action=process';
}

if(isset($_POST['repair'] )) {
	include('includes/application_top.php');

	$result = mysql_query('
		SELECT customers_id
		FROM customers
		WHERE
			customers_email_address = 		"'. xtc_db_prepare_input($_POST['email_address']) .'" 	AND
			customers_password 			= md5("'. xtc_db_prepare_input($_POST['password']			) .'")	AND
			customers_status				= 0
	');
	if(mysql_num_rows($result) > 0)
	{
		switch($_POST['repair']) {
			case 'se_friendly':
				mysql_query('
					UPDATE configuration
					SET		configuration_value = "false"
					WHERE	configuration_key 	= "SEARCH_ENGINE_FRIENDLY_URLS"
				');
				die('Report: Die Einstellung "Suchmaschinenfreundliche URLs verwenden" wurde deaktiviert.');
				break;

			case 'sess_write':
				mysql_query('
					UPDATE configuration
					SET		configuration_value = "'.DIR_FS_CATALOG.'cache"
					WHERE	configuration_key 	= "SESSION_WRITE_DIRECTORY"
				');
				die('Report: SESSION_WRITE_DIRECTORY wurde auf das Cache-Verzeichnis gerichtet.');
				break;

			case 'sess_default':
				mysql_query('
					UPDATE configuration
					SET		configuration_value = "False"
					WHERE	configuration_key 	= "SESSION_FORCE_COOKIE_USE"
				');
				mysql_query('
					UPDATE configuration
					SET		configuration_value = "False"
					WHERE	configuration_key 	= "SESSION_CHECK_SSL_SESSION_ID"
				');
				mysql_query('
					UPDATE configuration
					SET		configuration_value = "False"
					WHERE	configuration_key 	= "SESSION_CHECK_USER_AGENT"
				');
				mysql_query('
					UPDATE configuration
					SET		configuration_value = "False"
					WHERE	configuration_key 	= "SESSION_CHECK_IP_ADDRESS"
				');
				mysql_query('
					UPDATE configuration
					SET		configuration_value = "False"
					WHERE	configuration_key 	= "SESSION_RECREATE"
				');
				die('Report: Die Session-Einstellungen wurden auf die Standardwerte zurückgesetzt.');
				break;

			default:
				die('Report: repair-Befehl ungültig.');
		}
	}
	else {
		die('Zugriff verweigert.');
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="de">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-15" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="content-language" content="de" />
<meta http-equiv="cache-control" content="no-cache" />
<title>Admin-Login</title>
</head>
<body>
<br/><br/>
<form name="login" method="post" action="<?php echo $action; ?>">
  <table border="0" align="center" cellpadding="5" cellspacing="0" bgcolor="#F0F0F0" style="border:1px #aaaaaa solid;">
    <tr>
      <td class="main"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">E-Mail</font></td>
      <td><div><input type="text" name="email_address" style="width:150px" maxlength="50" /></div></td>
    </tr>
    <tr>
      <td class="main"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Passwort</font>&nbsp;</td>
      <td><div><input type="password" name="password" style="width:150px" maxlength="30" /></div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Anmelden" />
      <input type="hidden" name="repair" value="<?php echo $_GET['repair']; ?>" /></td>
    </tr>
  </table>
</form>
</body>
</html>