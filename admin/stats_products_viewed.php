<?php
/* --------------------------------------------------------------
   $Id$   

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(stats_products_viewed.php,v 1.27 2003/01/29); www.oscommerce.com 
   (c) 2003	nextcommerce (stats_products_viewed.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2006 xt:Commerce

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('includes/application_top.php');
  //BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics
  require(DIR_FS_INC. 'xtc_remove_non_numeric.inc.php');
  
  if ($_POST['maxrows']){
    $maxrows = xtc_remove_non_numeric(xtc_db_prepare_input($_POST['maxrows']));
  } else {
    $maxrows = $_GET['maxrows'];
  }
  if ($maxrows <= '20') $maxrows=20;
  
  if ($_GET['clear_id']){
      xtc_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = '0' where products_id ='".$_GET['clear_id']."'");
  }
  if ($_GET['clear_all']=='true'){
    xtc_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = '0' ");
  }  
  //EOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics
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
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_statistic.gif'); ?></td>
    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
  </tr>
  <tr>
    <td class="main" valign="top">XT Statistics</td>
  </tr>
</table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NUMBER; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_VIEWED; ?>&nbsp;</td>
<?php /* BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics */ ?>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_RESET; ?>&nbsp;</td>                
<?php /* BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics */ ?>
              </tr>
<?php
  //BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics
  //if ($_GET['page'] > 1) $rows = $_GET['page'] * '20' - '20';
  $rows = 0;
  if (isset($_GET['page']) && $_GET['page'] > 1) $rows = $_GET['page'] * $maxrows - $maxrows;
  //EOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics

  $products_query_raw = "select
                          p.products_id,
                          pd.products_name,
                          pd.products_viewed,
                          l.name
                         from
                          " . TABLE_PRODUCTS . " p,
                          " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                          " . TABLE_LANGUAGES . " l
                         where p.products_id = pd.products_id
                         and l.languages_id = pd.language_id
                         order by pd.products_viewed DESC";

  //BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics
  //$products_split = new splitPageResults($_GET['page'], '20', $products_query_raw, $products_query_numrows);
  $products_split = new splitPageResults($_GET['page'], $maxrows, $products_query_raw, $products_query_numrows);
  //EOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics
  $products_query = xtc_db_query($products_query_raw);
  while ($products = xtc_db_fetch_array($products_query)) {
    $rows++;
    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
?>
              <tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='pointer'" onmouseout="this.className='dataTableRow'">
                <td class="dataTableContent"><?php echo $rows; ?>.</td>
                <td class="dataTableContent"><?php echo  $products['products_name'] . ' (' . $products['name'] . ')'; ?></td>
                <td class="dataTableContent" align="center"><?php echo $products['products_viewed']; ?>&nbsp;</td>
<?php /* BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics */ ?>
                <td class="dataTableContent" align="center"><?php echo '<a href="'.basename($_SERVER['SCRIPT_FILENAME']).'?clear_id='.$products['products_id'].'&page='.$_GET['page'].'&maxrows='.$maxrows.'"><img src="images/icon_delete.gif" alt="reset" style="border:0px;" /> </a>'; ?></td>
<?php /* BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics */ ?>
              </tr>
<?php
  }
?>
<?php /* BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics */ ?>
              <tr>
                <td class="dataTableContent" colspan="4" align="right" style="padding-right:20px">
                <?php echo xtc_draw_form('resetall', FILENAME_STATS_PRODUCTS_PURCHASED, 'clear_all=true&page='.$_GET['page'].'&maxrows='.$maxrows);?>               
                <img src="images/icons/warning.gif" alt="" style="border:0px;" />
                <input type="submit" value="<?php echo BUTTON_RESET_PRODUCTS_PURCHASED; ?>" onclick="this.blur();" class="button" />
                <img src="images/icons/warning.gif" alt="" style="border:0px;" />
                </form>
                </td>
              </tr>
<?php /* EOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics */ ?>
            </table></td>
          </tr>
          <tr>
            <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
<?php /* BOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics 
                <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                <td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
      */
?>
                <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, $maxrows, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                <td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, $maxrows, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], 'maxrows='.$maxrows); ?></td>
              </tr>
              <tr>             
                <td class="smallText"><?php echo TEXT_ROWS.'&nbsp;'.xtc_draw_form('getmaxrows', FILENAME_STATS_PRODUCTS_VIEWED, 'page='.$_GET['page']) . xtc_draw_input_field('maxrows', $maxrows, 'style="width:50px"'); ?>
                <input type="image" src="images/icon_arrow_right.gif" style="vertical-align:bottom" alt="los" title="los" />
                </form></td>
<?php /* EOF - DokuMan - 2010-08-12 - added possibility to reset admin statistics */ ?>
              </tr>
            </table></td>
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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>