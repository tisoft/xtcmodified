<?php
/* --------------------------------------------------------------
   $Id: server_info.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(server_info.php,v 1.4 2003/03/17); www.oscommerce.com 
   (c) 2003	 nextcommerce (server_info.php,v 1.7 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  $system = xtc_get_system_information();
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td align="center"><table border="0" cellspacing="0" cellpadding="3">
              <tr>
                <td class="smallText"><strong><?php echo TITLE_SERVER_HOST; ?></strong></td>
                <td class="smallText"><?php echo $system['host'] . ' (' . $system['ip'] . ')'; ?></td>
                <td class="smallText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo TITLE_DATABASE_HOST; ?></strong></td>
                <td class="smallText"><?php echo $system['db_server'] . ' (' . $system['db_ip'] . ')'; ?></td>
              </tr>
              <tr>
                <td class="smallText"><strong><?php echo TITLE_SERVER_OS; ?></strong></td>
                <td class="smallText"><?php echo $system['system'] . ' ' . $system['kernel']; ?></td>
                <td class="smallText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo TITLE_DATABASE; ?></strong></td>
                <td class="smallText"><?php echo $system['db_version']; ?></td>
              </tr>
              <tr>
                <td class="smallText"><strong><?php echo TITLE_SERVER_DATE; ?></strong></td>
                <td class="smallText"><?php echo $system['date']; ?></td>
                <td class="smallText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo TITLE_DATABASE_DATE; ?></strong></td>
                <td class="smallText"><?php echo $system['db_date']; ?></td>
              </tr>
              <tr>
                <td class="smallText"><strong><?php echo TITLE_SERVER_UP_TIME; ?></strong></td>
                <td colspan="3" class="smallText"><?php echo $system['uptime']; ?></td>
              </tr>
              <tr>
                <td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><strong><?php echo TITLE_HTTP_SERVER; ?></strong></td>
                <td colspan="3" class="smallText"><?php echo $system['http_server']; ?></td>
              </tr>
              <tr>
                <td class="smallText"><strong><?php echo TITLE_PHP_VERSION; ?></strong></td>
                <td colspan="3" class="smallText"><?php echo $system['php'] . ' (' . TITLE_ZEND_VERSION . ' ' . $system['zend'] . ')'; ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td>
<?php
  if (function_exists('ob_start')) {
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();

    $phpinfo = str_replace('border: 1px', '', $phpinfo);
    preg_match('/(<style type=\"text\/css\">{1})(.*)(<\/style>{1})/s', $phpinfo, $regs); // Hetfield - 2009-08-19 - replaced depricated function ereg with preg_match to be ready for PHP >= 5.3
    echo '<style type="text/css">' . $regs[2] . '</style>';
    preg_match('/(<body>{1})(.*)(<\/body>{1})/s', $phpinfo, $regs); // Hetfield - 2009-08-19 - replaced depricated function ereg with preg_match to be ready for PHP >= 5.3
    echo $regs[2];
  } else {
    phpinfo();
  }
?>
        </td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>