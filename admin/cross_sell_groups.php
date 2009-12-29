<?php
/* --------------------------------------------------------------
   $Id: cross_sell_groups.php 1231 2005-09-21 13:05:36Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(orders_status.php,v 1.19 2003/02/06); www.oscommerce.com 
   (c) 2003	 nextcommerce (orders_status.php,v 1.9 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  switch ($_GET['action']) {
    case 'insert':
    case 'save':
      $cross_sell_id = xtc_db_prepare_input($_GET['oID']);

      $languages = xtc_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $cross_sell_name_array = $_POST['cross_sell_group_name'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = array('groupname' => xtc_db_prepare_input($cross_sell_name_array[$language_id]));

        if ($_GET['action'] == 'insert') {
          if (!xtc_not_null($cross_sell_id)) {
            $next_id_query = xtc_db_query("select max(products_xsell_grp_name_id) as products_xsell_grp_name_id from " . TABLE_PRODUCTS_XSELL_GROUPS . "");
            $next_id = xtc_db_fetch_array($next_id_query);
            $cross_sell_id = $next_id['products_xsell_grp_name_id'] + 1;
          }

          $insert_sql_data = array('products_xsell_grp_name_id' => $cross_sell_id,
                                   'language_id' => $language_id);
          $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
          xtc_db_perform(TABLE_PRODUCTS_XSELL_GROUPS, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {
          xtc_db_perform(TABLE_PRODUCTS_XSELL_GROUPS, $sql_data_array, 'update', "products_xsell_grp_name_id = '" . xtc_db_input($cross_sell_id) . "' and language_id = '" . $language_id . "'");
        }
      }


      xtc_redirect(xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $cross_sell_id));
      break;

    case 'deleteconfirm':
      $oID = xtc_db_prepare_input($_GET['oID']);

      xtc_db_query("delete from " . TABLE_PRODUCTS_XSELL_GROUPS . " where products_xsell_grp_name_id = '" . xtc_db_input($oID) . "'");

      xtc_redirect(xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page']));
      break;

    case 'delete':
      $oID = xtc_db_prepare_input($_GET['oID']);

      $cross_sell_query = xtc_db_query("select count(*) as count from " . TABLE_PRODUCTS_XSELL . " where products_xsell_grp_name_id = '" . xtc_db_input($oID) . "'");
      $status = xtc_db_fetch_array($cross_sell_query);

      $remove_status = true;
      if ($status['count'] > 0) {
        $remove_status = false;
        $messageStack->add(ERROR_STATUS_USED_IN_CROSS_SELLS, 'error');
      }
      break;
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
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
    <td class="pageHeading"><?php echo BOX_ORDERS_XSELL_GROUP; ?></td>
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
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_XSELL_GROUP_NAME; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $cross_sell_query_raw = "select products_xsell_grp_name_id, groupname from " . TABLE_PRODUCTS_XSELL_GROUPS . " where language_id = '" . $_SESSION['languages_id'] . "' order by products_xsell_grp_name_id";
  $cross_sell_split = new splitPageResults($_GET['page'], '20', $cross_sell_query_raw, $cross_sell_query_numrows);
  $cross_sell_query = xtc_db_query($cross_sell_query_raw);
  while ($cross_sell = xtc_db_fetch_array($cross_sell_query)) {
    if (((!$_GET['oID']) || ($_GET['oID'] == $cross_sell['products_xsell_grp_name_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $oInfo = new objectInfo($cross_sell);
    }

    if ( (is_object($oInfo)) && ($cross_sell['products_xsell_grp_name_id'] == $oInfo->products_xsell_grp_name_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $cross_sell['products_xsell_grp_name_id']) . '\'">' . "\n";
    }


      echo '                <td class="dataTableContent">' . $cross_sell['groupname'] . '</td>' . "\n";
    
?>
<!-- BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
<!--
                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($cross_sell['products_xsell_grp_name_id'] == $oInfo->products_xsell_grp_name_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $cross_sell['products_xsell_grp_name_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
-->
                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($cross_sell['products_xsell_grp_name_id'] == $oInfo->products_xsell_grp_name_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ICON_ARROW_RIGHT); } else { echo '<a href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $cross_sell['products_xsell_grp_name_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
<!-- EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $cross_sell_split->display_count($cross_sell_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_XSELL_GROUP); ?></td>
                    <td class="smallText" align="right"><?php echo $cross_sell_split->display_links($cross_sell_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (substr($_GET['action'], 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&action=new') . '">' . BUTTON_INSERT . '</a>'; ?></td>
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
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_XSELL_GROUP . '</b>');

      $contents = array('form' => xtc_draw_form('status', FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

      $cross_sell_inputs_string = '';
      $languages = xtc_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $cross_sell_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('cross_sell_group_name[' . $languages[$i]['id'] . ']');
      }

      $contents[] = array('text' => '<br />' . TEXT_INFO_XSELL_GROUP_NAME . $cross_sell_inputs_string);
      $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_INSERT . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_XSELL_GROUP . '</b>');

      $contents = array('form' => xtc_draw_form('status', FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

      $cross_sell_inputs_string = '';
      $languages = xtc_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $cross_sell_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('cross_sell_group_name[' . $languages[$i]['id'] . ']', xtc_get_cross_sell_name($oInfo->products_xsell_grp_name_id, $languages[$i]['id']));
      }

      $contents[] = array('text' => '<br />' . TEXT_INFO_XSELL_GROUP_NAME . $cross_sell_inputs_string);
      $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id) . '">' . BUTTON_CANCEL . '</a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_XSELL_GROUP . '</b>');

      $contents = array('form' => xtc_draw_form('status', FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $oInfo->orders_status_name . '</b>');
      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_DELETE . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id) . '">' . BUTTON_CANCEL . '</a>');
      break;

    default:
      if (is_object($oInfo)) {
        $heading[] = array('text' => '<b>' . $oInfo->orders_status_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');

        $cross_sell_inputs_string = '';
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $cross_sell_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_get_cross_sell_name($oInfo->products_xsell_grp_name_id, $languages[$i]['id']);
        }

        $contents[] = array('text' => $cross_sell_inputs_string);
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