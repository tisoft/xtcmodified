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
// USAGE: /login_admin.php?repair=se_friendly
// USAGE: /login_admin.php?repair=sess_write
// USAGE: /login_admin.php?repair=sess_default

if(isset($_GET['repair'])) {
  $action = 'login_admin.php';
} else {
  $action = 'login.php?action=process';
}

if(isset($_POST['repair'])) {
  include('includes/application_top.php');
  require_once (DIR_FS_INC.'xtc_validate_password.inc.php');
  $check_customer_query = xtc_db_query('
                          select
                           customers_id,
                           customers_password,
                           customers_email_address
                          from '. TABLE_CUSTOMERS .'
                          where customers_email_address = "'. xtc_db_input($_POST['email_address']) .'"
                          and customers_status = 0');

  $check_customer = xtc_db_fetch_array($check_customer_query);

  //BOF - DokuMan - 2011-02-02 - added support for passwort+salt (SHA1)
  if(!xtc_validate_password(xtc_db_input($_POST['password']),
                            $check_customer['customers_password'],
                            $check_customer['customers_email_address'])) {
    die(TEXT_LOGIN_ERROR);
  //EOF - DokuMan - 2011-02-02 - added support for passwort+salt (SHA1)
  } else {
    switch($_POST['repair']) {
      case 'se_friendly':
        xtc_db_query('
          UPDATE configuration
          SET    configuration_value = "false"
          WHERE  configuration_key   = "SEARCH_ENGINE_FRIENDLY_URLS"
        ');
        die('Report: Die Einstellung "Suchmaschinenfreundliche URLs verwenden" wurde deaktiviert.');
        break;

      case 'sess_write':
        xtc_db_query('
          UPDATE configuration
          SET    configuration_value = "'.DIR_FS_CATALOG.'cache"
          WHERE  configuration_key   = "SESSION_WRITE_DIRECTORY"
        ');
        die('Report: SESSION_WRITE_DIRECTORY wurde auf das Cache-Verzeichnis gerichtet.');
        break;

      case 'sess_default':
        xtc_db_query('
          UPDATE configuration
          SET    configuration_value = "False"
          WHERE  configuration_key   = "SESSION_FORCE_COOKIE_USE"
        ');
        xtc_db_query('
          UPDATE configuration
          SET    configuration_value = "False"
          WHERE  configuration_key   = "SESSION_CHECK_SSL_SESSION_ID"
        ');
        xtc_db_query('
          UPDATE configuration
          SET    configuration_value = "False"
          WHERE  configuration_key   = "SESSION_CHECK_USER_AGENT"
        ');
        xtc_db_query('
          UPDATE configuration
          SET    configuration_value = "False"
          WHERE  configuration_key   = "SESSION_CHECK_IP_ADDRESS"
        ');
        xtc_db_query('
          UPDATE configuration
          SET    configuration_value = "False"
          WHERE  configuration_key   = "SESSION_RECREATE"
        ');
        die('Report: Die Session-Einstellungen wurden auf die Standardwerte zurückgesetzt.');
        break;

      default:
        die('Report: repair-Befehl ungültig.');
    }
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr">
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