<?php
/* --------------------------------------------------------------
   $Id: products_vpe.php 1125 2005-07-28 09:59:44Z novalis $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(order_status.php,v 1.19 2003/02/06); www.oscommerce.com 
   (c) 2003	 nextcommerce (order_status.php,v 1.9 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
   
   define('DEFAULT_PRODUCTS_VPE_ID','1');

  require('includes/application_top.php');

  switch ($_GET['action']) {
    case 'insert':
    case 'save':
      $products_vpe_id = xtc_db_prepare_input($_GET['oID']);

      $languages = xtc_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $products_vpe_name_array = $_POST['products_vpe_name'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = array('products_vpe_name' => xtc_db_prepare_input($products_vpe_name_array[$language_id]));

        if ($_GET['action'] == 'insert') {
          if (!xtc_not_null($products_vpe_id)) {
            $next_id_query = xtc_db_query("select max(products_vpe_id) as products_vpe_id from " . TABLE_PRODUCTS_VPE . "");
            $next_id = xtc_db_fetch_array($next_id_query);
            $products_vpe_id = $next_id['products_vpe_id'] + 1;
          }

          $insert_sql_data = array('products_vpe_id' => $products_vpe_id,
                                   'language_id' => $language_id);
          $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
          xtc_db_perform(TABLE_PRODUCTS_VPE, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {
          xtc_db_perform(TABLE_PRODUCTS_VPE, $sql_data_array, 'update', "products_vpe_id = '" . xtc_db_input($products_vpe_id) . "' and language_id = '" . $language_id . "'");
        }
      }

      if ($_POST['default'] == 'on') {
        xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($products_vpe_id) . "' where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
      }

      xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $products_vpe_id));
      break;

    case 'deleteconfirm':
      $oID = xtc_db_prepare_input($_GET['oID']);

      $products_vpe_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
      $products_vpe = xtc_db_fetch_array($products_vpe_query);
      if ($products_vpe['configuration_value'] == $oID) {
        xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
      }

      xtc_db_query("delete from " . TABLE_PRODUCTS_VPE . " where products_vpe_id = '" . xtc_db_input($oID) . "'");

      xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page']));
      break;

    case 'delete':
      $oID = xtc_db_prepare_input($_GET['oID']);


      $remove_status = true;
      if ($oID == DEFAULT_PRODUCTS_VPE_ID) {
        $remove_status = false;
        $messageStack->add(ERROR_REMOVE_DEFAULT_PRODUCTS_VPE, 'error');
      } 
      break;
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>" /> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
<script type="text/javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
    <td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_configuration.gif'); ?></td>
    <td class="pageHeading"><?php echo BOX_PRODUCTS_VPE; ?></td>
  </tr>
  <tr>
    <td class="main" valign="top">XT Configuration</td>
  </tr>
</table></td>
      </tr>
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_VPE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $products_vpe_query_raw = "select products_vpe_id, products_vpe_name from " . TABLE_PRODUCTS_VPE . " where language_id = '" . $_SESSION['languages_id'] . "' order by products_vpe_id";
  $products_vpe_split = new splitPageResults($_GET['page'], '20', $products_vpe_query_raw, $products_vpe_query_numrows);
  $products_vpe_query = xtc_db_query($products_vpe_query_raw);
  while ($products_vpe = xtc_db_fetch_array($products_vpe_query)) {
    if (((!$_GET['oID']) || ($_GET['oID'] == $products_vpe['products_vpe_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $oInfo = new objectInfo($products_vpe);
    }

    if ( (is_object($oInfo)) && ($products_vpe['products_vpe_id'] == $oInfo->products_vpe_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $products_vpe['products_vpe_id']) . '\'">' . "\n";
    }

    if (DEFAULT_PRODUCTS_VPE_ID == $products_vpe['products_vpe_id']) {
      echo '                <td class="dataTableContent"><b>' . $products_vpe['products_vpe_name'] . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {
      echo '                <td class="dataTableContent">' . $products_vpe['products_vpe_name'] . '</td>' . "\n";
    }
?>
<!-- BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
<!--
                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($products_vpe['products_vpe_id'] == $oInfo->products_vpe_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $products_vpe['products_vpe_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
-->
                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($products_vpe['products_vpe_id'] == $oInfo->products_vpe_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ICON_ARROW_RIGHT); } else { echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $products_vpe['products_vpe_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
<!-- EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $products_vpe_split->display_count($products_vpe_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS_VPE); ?></td>
                    <td class="smallText" align="right"><?php echo $products_vpe_split->display_links($products_vpe_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (substr($_GET['action'], 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&action=new') . '">' . BUTTON_INSERT . '</a>'; ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_PRODUCTS_VPE . '</b>');

      $contents = array('form' => xtc_draw_form('status', FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

      $products_vpe_inputs_string = '';
      $languages = xtc_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $products_vpe_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('products_vpe_name[' . $languages[$i]['id'] . ']');
      }

      $contents[] = array('text' => '<br />' . TEXT_INFO_PRODUCTS_VPE_NAME . $products_vpe_inputs_string);
      $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_INSERT . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_PRODUCTS_VPE . '</b>');

      $contents = array('form' => xtc_draw_form('status', FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

      $products_vpe_inputs_string = '';
      $languages = xtc_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $products_vpe_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('products_vpe_name[' . $languages[$i]['id'] . ']', xtc_get_products_vpe_name($oInfo->products_vpe_id, $languages[$i]['id']));
      }

      $contents[] = array('text' => '<br />' . TEXT_INFO_PRODUCTS_VPE_NAME . $products_vpe_inputs_string);
      if (DEFAULT_PRODUCTS_VPE_ID != $oInfo->products_vpe_id) $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id) . '">' . BUTTON_CANCEL . '</a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRODUCTS_VPE . '</b>');

      $contents = array('form' => xtc_draw_form('status', FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $oInfo->products_vpe_name . '</b>');
      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_DELETE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id) . '">' . BUTTON_CANCEL . '</a>');
      break;

    default:
      if (is_object($oInfo)) {

        $heading[] = array('text' => '<b>' . $oInfo->products_vpe_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');

        $products_vpe_inputs_string = '';
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $products_vpe_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_get_products_vpe_name($oInfo->products_vpe_id, $languages[$i]['id']);
        }

        $contents[] = array('text' => $products_vpe_inputs_string);
      }
      break;
  }

  if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
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