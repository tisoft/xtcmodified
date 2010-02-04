<?php
/* --------------------------------------------------------------
   $Id: configuration.php 229 2007-03-06 19:46:21Z mzanier $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'save':

      //BOF - Dokuman - 2009-10-02 - added entries for new moneybookers payment module version 2.4
			if ($_GET['gID']=='31') {

				// email check
				if (isset($_POST['_PAYMENT_MONEYBOOKERS_EMAILID'])) {
					 
					$url = 'https://www.moneybookers.com/app/email_check.pl?email='.$_POST['_PAYMENT_MONEYBOOKERS_EMAILID'].'&cust_id=8644877&password=1a28e429ac2fcd036aa7d789ebbfb3b0';
					 
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

					$result = curl_exec($ch);
					if ($result=='NOK') {
						$messageStack->add_session(MB_ERROR_NO_MERCHANT, 'error');
					}
						
					if (strstr($result,'OK,')) {
						$data = explode(',',$result);
						$_POST['_PAYMENT_MONEYBOOKERS_MERCHANTID'] = $data[1];
						$messageStack->add_session(sprintf(MB_MERCHANT_OK,$data[1]), 'success');
					}
				}
			}
      //EOF - Dokuman - 2009-10-02 - added entries for new moneybookers payment module version 2.4

          $configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . (int)$_GET['gID'] . "' order by sort_order");

          while ($configuration = xtc_db_fetch_array($configuration_query))
              xtc_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$_POST[$configuration['configuration_key']]."' where configuration_key='".$configuration['configuration_key']."'");

               xtc_redirect(FILENAME_CONFIGURATION. '?gID=' . (int)$_GET['gID']);
        break;
	
	//BOF - Dokuman - 2010-02-04 - delete cache files in admin section
    case 'delcache':
      $path = DIR_FS_CATALOG.'cache/';
      if ($dir = opendir($path)) {
        while (($file = readdir($dir)) !== false) {
          if (is_file($path.$file) and ($file != "index.html") and ($file != ".htaccess")) {
            unlink($path.$file);
          } //if
        } // while
        closedir($dir);
      }
      $messageStack->add_session(DELETE_CACHE_SUCCESSFUL, 'success');
      xtc_redirect(FILENAME_CONFIGURATION. '?gID=' . (int)$_GET['gID']);
      break;
    case 'deltempcache':
      $path = DIR_FS_CATALOG.'templates_c/';
      if ($dir = opendir($path)) {
        while (($file = readdir($dir)) !== false) {
          if (is_file($path.$file) and ($file != "index.html") and ($file != ".htaccess")) {
            unlink($path.$file);
          } //if
        } // while
        closedir($dir);
      }
      $messageStack->add_session(DELETE_TEMP_CACHE_SUCCESSFUL, 'success');
      xtc_redirect(FILENAME_CONFIGURATION. '?gID=' . (int)$_GET['gID']);
      break;
	//EOF - Dokuman - 2010-02-04 - delete cache files in admin section
    }
  }

  $cfg_group_query = xtc_db_query("select configuration_group_title, configuration_group_id from " . TABLE_CONFIGURATION_GROUP . " where configuration_group_id = '" . (int)$_GET['gID'] . "'"); // Hetfield - 2010-01-15 - multilanguage title in configuration
  $cfg_group = xtc_db_fetch_array($cfg_group_query);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script type="text/javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
		<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
		<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
			<!-- left_navigation //-->
			<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
			<!-- left_navigation_eof //-->
		</table>
		</td>
		<!-- body_text //-->
		<td class="boxCenter" width="100%" valign="top">
		<table border="0" width="100%" cellspacing="0" cellpadding="2">
			<tr>
				<td>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_configuration.gif'); ?></td>
						<td class="pageHeading"><?php 
						// BOF - Hetfield - 2010-01-15 - multilanguage title in configuration
						if (!@constant(BOX_CONFIGURATION_.$cfg_group['configuration_group_id'])) {
							echo $cfg_group['configuration_group_title'];
						} else {
							echo @constant(BOX_CONFIGURATION_.$cfg_group['configuration_group_id']);
						}
						// EOF - Hetfield - 2010-01-15 - multilanguage title in configuration
						?></td>
					</tr>
					<tr>
						<td class="main" valign="top">XT Configuration</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="border-top: 3px solid; border-color: #cccccc;" class="main">
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
         <?php
         	switch ($_GET['gID']) {
         		case 21:
         			echo AFTERBUY_URL;
         		case 19:
            // BOF - Tomcraft - 2009-10-03 - Paypal Express Modul
         		case 25:
            // EOF - Tomcraft - 2009-10-03 - Paypal Express Modul
            //BOF - Dokuman - 2009-10-02 - added entries for new moneybookers payment module version 2.4
         		case 31:
            //EOF - Dokuman - 2009-10-02 - added entries for new moneybookers payment module version 2.4
  


            //BOF - Dokuman - 2009-10-02 - added entries for new moneybookers payment module version 2.4        		
         			echo '<table class="infoBoxHeading" width="100%">
            				<tr>
                			<td width="150" align="center">
                			<a class="button" href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=21', 'NONSSL').'">Afterbuy</a>
                			</td>
                			<td width="1">|
                			</td>
                			<td width="150" align="center">
                			<a class="button" href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=19', 'NONSSL').'">Google Conversion</a>
                			</td>
                			<td width="1">|
                			</td>
                			<td width="150" align="center">
                			<a class="button" class="button" href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=25', 'NONSSL').'">PayPal</a>
                			</td>
                			<td width="1">|
                			</td>
                			<td width="150" align="center">
                			<a class="button" href="'.xtc_href_link(FILENAME_CONFIGURATION, 'gID=31', 'NONSSL').'">Moneybookers.com</a>
                			</td>
                			<td width="1">|
                			</td>
                			<td>
                			</td>
            				</tr>
        					</table>';
						if ($_GET['gID']=='31') echo MB_INFO;
//EOF - Dokuman - 2009-10-02 - added entries for new moneybookers & paypal payment module version 2.4        		
         			break;
         	}
         	?> 
         
          
          <tr>
            <td valign="top" align="right"><?php echo xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID'] . '&action=save'); ?>
            <table width="100%"  border="0" cellspacing="0" cellpadding="4">
<?php
  $configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . (int)$_GET['gID'] . "' order by sort_order");

  while ($configuration = xtc_db_fetch_array($configuration_query)) {
    if ($_GET['gID'] == 6) {
      switch ($configuration['configuration_key']) {
        case 'MODULE_PAYMENT_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $payment_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $payment_installed[$i]);
            }
          }
          break;

        case 'MODULE_SHIPPING_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $shipping_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/' . $shipping_installed[$i]);			
            }
          }
          break;

        case 'MODULE_ORDER_TOTAL_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $ot_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $ot_installed[$i]);			
            }
          }
          break;
      }
    }
    if (xtc_not_null($configuration['use_function'])) {
      $use_function = $configuration['use_function'];
      if (preg_match('/->/', $use_function)) { // Hetfield - 2009-08-19 - replaced depricated function ereg with preg_match to be ready for PHP >= 5.3
        $class_method = explode('->', $use_function);
        if (!is_object(${$class_method[0]})) {
          include(DIR_WS_CLASSES . $class_method[0] . '.php');
          ${$class_method[0]} = new $class_method[0]();
        }
        $cfgValue = xtc_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
      } else {
        $cfgValue = xtc_call_function($use_function, $configuration['configuration_value']);
      }
    } else {
      $cfgValue = $configuration['configuration_value'];
    }

    if (((!$_GET['cID']) || (@$_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $cfg_extra_query = xtc_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
      $cfg_extra = xtc_db_fetch_array($cfg_extra_query);

      $cInfo_array = xtc_array_merge($configuration, $cfg_extra);
      $cInfo = new objectInfo($cInfo_array);
    }
    if ($configuration['set_function']) {
        eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars($configuration['configuration_value']) . '");');
      } else {
        $value_field = xtc_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'],'size=40');
      }
   // add

   if (strstr($value_field,'configuration_value')) $value_field=str_replace('configuration_value',$configuration['configuration_key'],$value_field);

  // BOF vr - 2010-02-04 admin configuration pages 3 column layout 
  /* echo '
  <tr>
    <td width="300" valign="top" class="dataTableContent"><b>'.constant(strtoupper($configuration['configuration_key'].'_TITLE')).'</b></td>
    <td valign="top" class="dataTableContent">
    <table width="100%"  border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td style="background-color:#FCF2CF ; border: 1px solid; border-color: #CCCCCC;" class="dataTableContent">'.$value_field.'</td>
      </tr>
    </table>
    <br />'.constant(strtoupper( $configuration['configuration_key'].'_DESC')).'</td>
  </tr>
  ';*/
  
  echo '
  <tr>
    <td style="min-width:20%; border-bottom: 1px dotted #CCCCCC;" class="dataTableContent"><b>'.constant(strtoupper($configuration['configuration_key'].'_TITLE')).'</b></td>
    <td style="min-width:20%; border-bottom: 1px dotted #CCCCCC; background-color:#FCF2CF;" class="dataTableContent">'.$value_field.'</td>
    <td style="min-width:60%; border-bottom: 1px dotted #CCCCCC;" class="dataTableContent">'.constant(strtoupper( $configuration['configuration_key'].'_DESC')).'</td>
  </tr>
  ';
  // EOF vr - 2010-02-04 admin configuration pages 3 column layout 

  }
?>
            </table>
			<?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?>
			</form>
<?php
	//BOF - Dokuman - 2010-02-04 - delete cache files in admin section
	if ($_GET['gID']==11) {
	  echo xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID'] . '&action=delcache');
	  echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_DELETE_CACHE . '"/></form> ';
	  echo xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID'] . '&action=deltempcache');
	  echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_DELETE_TEMP_CACHE . '"/></form>';
	}
	//EOF - Dokuman - 2010-02-04 - delete cache files in admin section
?>
            </td>

          </tr>
        </table>
		</td>
      </tr>
    </table>
	</td>
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