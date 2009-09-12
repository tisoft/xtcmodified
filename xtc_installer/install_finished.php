<?php
  /* --------------------------------------------------------------
   $Id: install_finished.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (install_finished.php,v 1.5 2003/08/17); www.nextcommerce.org
   (c) 2009 xtcModified (install_finished,v 1.00 2009/07/13); www.www.xtc-modified.org
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
  
  require('includes/application.php');
  require('../admin/includes/configure.php'); 
  
  include('language/'.$_SESSION['language'].'.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>xtcModified Installer - Finished</title>
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
<table width="800" style="border:30px solid #fff;" bgcolor="#f3f3f3" border="0" align="center" cellpadding="0" cellspacing="0">
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
          <td><img src="images/step8.gif" width="705" height="180" border="0"><br />
            <br />
            <br />
            <div style="border:1px solid #ccc; background:#fff; padding:10px;"><?php echo TEXT_WELCOME_FINISHED; ?></div></td>
        </tr>
      </table>

      <br />

      <table width="95%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>
                        
                      
            <div style="border:1px solid #ccc; background:#fff; padding:10px;"><?php echo TEXT_SHOP_CONFIG_SUCCESS; ?><br />
              <br />
              <?php echo TEXT_TEAM; ?></div><br />
              </p>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr> 
                <td align="center"><a href="<?php echo HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'index.php'; ?>" target="_blank"><img src="images/button_catalog.gif" border="0" alt="Catalog"></a></td>
                
              </tr>
            </table>
            <p align="center"><br />
              </p></td>
        </tr>
      </table>
      <br />    </td>
  </tr>
</table>

<br />
<div align="center" style="font-family:Arial, sans-serif; font-size:11px;">eCommerce Engine 2006 based on xt:Commerce<br />
eCommerce Engine © 2008 - 2009 xtcModified.org licensed under GNU/GPL</div>

</body>
</html>
