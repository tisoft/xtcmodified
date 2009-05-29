<?php
/* --------------------------------------------------------------
   $Id: start.php 1235 2009-05-29 00:00:00 dokuman $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project 
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------
   modified version:
   Custom start.php to show an overview of
   - users online
   - new customers
   - last orders
   - general statistics
   - customer birthday list
   --------------------------------------------------------------*/

  $xx_mins_ago = (time() - 900);
  require ('includes/application_top.php');
  require_once 'includes/modules/carp/carp.php';
  require_once (DIR_FS_INC.'xtc_validate_vatid_status.inc.php');
  require_once (DIR_FS_INC.'xtc_get_geo_zone_code.inc.php');
  require_once (DIR_FS_INC.'xtc_encrypt_password.inc.php');
  require_once (DIR_FS_INC.'xtc_js_lang.php');
  function array_qsort (&$array, $column=0, $order=SORT_ASC, $first=0, $last= -2)
  {
    // $array - the array to be sorted
    // $column - index (column) on which to sort
    // can be a string if using an associative array
    // $order - SORT_ASC (default) for ascending or SORT_DESC for descending
    // $first - start index (row) for partial array sort
    // $last - stop index (row) for partial array sort
    // $keys - array of key values for hash array sort
    $keys = array_keys($array);
    if($last == -2) $last = count($array) - 1;
    if($last > $first) {
        $alpha = $first;
        $omega = $last;
        $key_alpha = $keys[$alpha];
        $key_omega = $keys[$omega];
        $guess = $array[$key_alpha][$column];
      while($omega >= $alpha) {
      if($order == SORT_ASC) {
        while($array[$key_alpha][$column] < $guess) {$alpha++; $key_alpha =
        $keys[$alpha]; }
        while($array[$key_omega][$column] > $guess) {$omega--; $key_omega =
        $keys[$omega]; }
        } else {
        while($array[$key_alpha][$column] > $guess) {$alpha++; $key_alpha =
        $keys[$alpha]; }
        while($array[$key_omega][$column] < $guess) {$omega--; $key_omega =
        $keys[$omega]; }
      }
      if($alpha > $omega) break;
        $temporary = $array[$key_alpha];
        $array[$key_alpha] = $array[$key_omega]; $alpha++;
        $key_alpha = $keys[$alpha];
        $array[$key_omega] = $temporary; $omega--;
        $key_omega = $keys[$omega];
      }
      array_qsort ($array, $column, $order, $first, $omega);
      array_qsort ($array, $column, $order, $alpha, $last);
    }
  }
  
$customers_statuses_array = xtc_get_customers_statuses();

// remove entries that have expired
xtc_db_query("delete from " . TABLE_WHOS_ONLINE . " where time_last_click < '" . $xx_mins_ago . "'");
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<style type="text/css">
.h2 {
  font-family: Trebuchet MS,Palatino,Times New Roman,serif;
  font-size: 13pt;
  font-weight: bold;
}
.h3 {
  font-family: Verdana,Arial,Helvetica,sans-serif;
  font-size: 9pt;
  font-weight: bold;
}
</style> 
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
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
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_news.gif'); ?></td>
          <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
        </tr>
        <tr>
          <td class="main" valign="top">&nbsp;</td>
        </tr>
        </table>
      </td>
    </tr>
    <tr>
    <td>
<?php 

  include(DIR_WS_MODULES.FILENAME_SECURITY_CHECK);
  $customers_query = xtc_db_query("select count(*) as count from " . TABLE_CUSTOMERS); 
  $customers = xtc_db_fetch_array($customers_query); 
  $newsletter_query = xtc_db_query("select count(*) as count from " . TABLE_CUSTOMERS. " where customers_newsletter = '1'"); 
  $newsletter = xtc_db_fetch_array($newsletter_query); 
  $products_query = xtc_db_query("select count(*) as count from " . TABLE_PRODUCTS . " where products_status = '1'"); 
  $products = xtc_db_fetch_array($products_query); 
  $products1_query = xtc_db_query("select count(*) as count from " . TABLE_PRODUCTS . " where products_status = '0'"); 
  $products1 = xtc_db_fetch_array($products1_query); 
  $products2_query = xtc_db_query("select count(*) as count from " . TABLE_PRODUCTS . "");
  $products2 = xtc_db_fetch_array($products2_query);
  $orders0_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '0'"); 
  $orders0 = xtc_db_fetch_array($orders0_query);
  $orders1_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '1'"); 
  $orders1 = xtc_db_fetch_array($orders1_query); 
  $orders2_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '2'"); 
  $orders2 = xtc_db_fetch_array($orders2_query); 
  $orders3_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '3'"); 
  $orders3 = xtc_db_fetch_array($orders3_query); 
  $specials_query = xtc_db_query("select count(*) as count from " . TABLE_SPECIALS);
  $specials = xtc_db_fetch_array($specials_query);
?>
<tr>
<td>
<div id="datum">
<?php

  $wochentag = date("l"); 
  switch ($wochentag) { 
    case "Monday": 
      $wochentag = "Montag"; 
    break; 
    case "Tuesday": 
      $wochentag = "Dienstag"; 
    break; 
    case "Wednesday": 
      $wochentag = "Mittwoch"; 
    break; 
    case "Thursday": 
      $wochentag = "Donnerstag"; 
    break; 
    case "Friday": 
      $wochentag = "Freitag"; 
    break; 
    case "Saturday": 
      $wochentag = "Samstag"; 
    break; 
    case "Sunday": 
      $wochentag = "Sonntag"; 
    break; 
  } 
  $datum = $wochentag . ", der " . date("d.m.Y"); 
  echo $datum; 
?></div>
</td></tr>
<tr>
      <td style="border: 0px solid; border-color: #ffffff;">
        <table valign="top" width="100%" cellpadding="0" cellspacing="0">
        
<table border="0" width="100%">
    <tr>
      <td width="100%">
        <div align="center">
          <table border="0" width="100%" cellspacing="0">
            <tr>
              <td width="48%" bgcolor="#FFCF9C" style="border-style: solid; border-width: 1">
                <p style="margin-left: 3"><font face="Verdana" size="1"><strong>User Online</strong></font></p>
              </td>
              <td width="4%">
                <p style="margin-left: 3"></p>
              </td>
              <td width="48%" bgcolor="#FFCF9C" style="border-style: solid; border-width: 1">
                <p style="margin-left: 3"><font face="Verdana" size="1"><strong>Neue Kunden</strong> (letzten 15 neuen Kunden)</font></p>
              </td>
            </tr>
            <tr>
              <td width="48%" bgcolor="#FFFBEF" style="border-style: solid; border-width: 1" height="200" valign="top">
                <div style="margin-left: 3">
				<!-- body //-->
                <font size="1">&nbsp;<i><font face="Verdana" color="#7691A2">f&uuml;r weitere Informationen zum jeweiligen User, auf den Namen des Users klicken</font></i></font>
                </div>
		 <div align="center">
			
			<table border="0" width="98%" cellspacing="0" cellpadding="0">
       <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="22%"><strong>
				<font size="1" face="Verdana">Online seit (min.)</font></strong></td>

        <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="33%"><strong>
				<font size="1" face="Verdana">Name</font></strong></td>

        <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="33%"><strong>
				<font size="1" face="Verdana">Letzter Klick</font></strong></td>

        <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="33%">
				<strong><font size="1" face="Verdana">Infos</font></strong></td>
			 </tr>

<?php

  $whos_online_query = xtc_db_query("select customer_id,
                                            full_name,
                                            ip_address,
                                            time_entry,
                                            time_last_click,
                                            last_page_url,
                                            session_id
                                      from " . TABLE_WHOS_ONLINE ."
                                      order by time_last_click desc");

  while ($whos_online = xtc_db_fetch_array($whos_online_query)) {
    $time_online = (time() - $whos_online['time_entry']);

    if ( ((!$_GET['info']) || (@$_GET['info'] == $whos_online['session_id'])) && (!$info) ) {
      $info = $whos_online['session_id'];
    }   
?>
     <tr>
       <td class="dataTableContent" align="left" width="22%"><font size="1" face="Verdana">
				<a href="whos_online.php?info=<?php echo $whos_online['session_id']; ?>"><?php echo gmdate('H:i:s', $time_online); ?></a></font></td>

       <td class="dataTableContent" align="left" width="33%"><font size="1" face="Verdana">
				<a href="whos_online.php?info=<?php echo $whos_online['session_id']; ?>"><?php echo $whos_online['full_name']; ?></a></font></td>

       <td class="dataTableContent" align="left" width="33%">
				<font size="1" face="Verdana">
				<a href="whos_online.php?info=<?php echo $whos_online['session_id']; ?>"><?php echo date('H:i:s', $whos_online['time_last_click']); ?></a></font></td>

       <td class="dataTableContent" align="left" width="33%">
				<font size="1" face="Verdana" color="#800000"><u><strong>
				<a href="whos_online.php?info=<?php echo $whos_online['session_id']; ?>">
				<font color="#800000"><strong>mehr...</strong></font></a></strong></u></font></td>
      </tr>

<?php

  }

?>
      </table>
    </div>
<!-- body_eof //-->
</td>
              <td width="4%" height="200" valign="top">
                <p style="margin-left: 3"></td>
              <td width="48%" bgcolor="#FFFBEF" style="border-style: solid; border-width: 1" height="200" valign="top">
                <div align="center">
			<p style="margin-left: 3; margin-top: 0; margin-bottom: 0" align="left">
				<font size="1">&nbsp;</font>
			</p>
			<table border="0" width="98%" cellspacing="0" cellpadding="0">

              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="25%">
        <strong><font size="1" face="Verdana">Name</font></strong></td>

                <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="25%">
        <strong><font size="1" face="Verdana">Vorname</font></strong></td>

                <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="25%">
        <strong><font size="1" face="Verdana">angemeldet am</font></strong></td>

                <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="12%">
				<strong><font size="1" face="Verdana">bearbeiten</font></strong></td>

                <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="12%">
				<strong><font face="Verdana" size="1">Bestellungen</font></strong></td>
              </tr>

<?php

  $whos_online_query = xtc_db_query("select customer_id,
                                            full_name,
                                            ip_address,
                                            time_entry,
                                            time_last_click,
                                            last_page_url,
                                            session_id
                                     from " . TABLE_WHOS_ONLINE ."
                                     order by time_last_click desc");

  while ($whos_online = xtc_db_fetch_array($whos_online_query)) {
    $time_online = (time() - $whos_online['time_entry']);

    if ( ((!$_GET['info']) || (@$_GET['info'] == $whos_online['session_id'])) && (!$info) ) {
      $info = $whos_online['session_id'];
    }

  $abfrage = "SELECT * FROM customers ORDER BY customers_date_added DESC LIMIT 15";
  $ergebnis = mysql_query($abfrage);
  while($row = mysql_fetch_object($ergebnis)){
?>
  <td class="dataTableContent" align="left" width="25%">
<?php echo $row-> customers_lastname; ?></td>

  <td class="dataTableContent" align="left" width="25%">
<?php echo $row-> customers_firstname; ?></td>

  <td class="dataTableContent" align="left" width="25%">
<?php echo $row-> customers_date_added; ?></td>

<td class="dataTableContent" align="left" width="12%">
<strong>
<a href="customers.php?page=1&cID=<?php echo $row-> customers_id; ?>&action=edit"><font size="1" face="Verdana" color="#800000"><strong>hier...</strong></font></a></strong></td>
<td class="dataTableContent" align="center" width="12%">

<strong>
<a href="orders.php?cID=<?php echo $row-> customers_id; ?>"><font color="#7691A2" size="1" face="Verdana"><strong>anzeigen...</strong></font></a></strong></td>

              </tr>
<?php
}
?>
              <tr>
                <td class="smallText" colspan="5">&nbsp;</td>
              </tr>

            </table></div>

         <p style="margin-left: 3; margin-top:0; margin-bottom:0">

<?php
}
?>
 
				</p>
 
				</td>
            </tr>
            <tr>
              <td width="48%">&nbsp;</td>
              <td width="4%"></td>
              <td width="48%"><font face="Verdana" size="1">&nbsp;</font></td>
            </tr>
            <tr>
              <td width="48%" bgcolor="#FFCF9C" style="border-style: solid; border-width: 1">
                <p style="margin-left: 3"><strong><font face="Verdana" size="1">
				letzten </font></strong><font face="Verdana" size="1"><strong>Bestellungen </strong>
				(letzten 20 Bestellungen)</font></td>
              <td width="4%">
                <p style="margin-left: 3"></td>
              <td width="48%" bgcolor="#FFCF9C" style="border-style: solid; border-width: 1">
                <p style="margin-left: 3"><font face="Verdana" size="1"><strong>Allgemeine
                Statistik-&Uuml;bersicht </strong></font></td>
            </tr>
            <tr>
              <td width="48%" bgcolor="#FFFBEF" style="border-style: solid; border-width: 1" height="200" valign="top">
                <div align="center">
			<p style="margin-left: 3; margin-top: 0; margin-bottom: 0" align="left">
				<font size="1">&nbsp;</font>
			</p>
			<table border="0" width="98%" cellspacing="0" cellpadding="0">

              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="25%"><strong>
				<font size="1" face="Verdana">Bestellnummer</font></strong></td>

                <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="25%">
				<p align="left">
				<strong><font face="Verdana" size="1">Bestelldatum</font></strong></td>

                <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="25%">
				<p align="left">
				<strong><font face="Verdana" size="1">Kundenname</font></strong></td>

                <td class="dataTableHeadingContent" align="center" bgcolor="#D9D9D9" height="20" width="12%">
				<p align="left"><strong><font face="Verdana" size="1">bearbeiten</font></strong></td>

                <td class="dataTableHeadingContent" align="left" bgcolor="#D9D9D9" height="20" width="12%">
				<p align="left"><strong><font face="Verdana" size="1">l&ouml;schen</font></strong></td>

              </tr>

<?php

  $whos_online_query = xtc_db_query("select customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, session_id from " . TABLE_WHOS_ONLINE ." order by time_last_click desc");

  while ($whos_online = xtc_db_fetch_array($whos_online_query)) {

    $time_online = (time() - $whos_online['time_entry']);

    if ( ((!$_GET['info']) || (@$_GET['info'] == $whos_online['session_id'])) && (!$info) ) {

      $info = $whos_online['session_id'];

    }

  $abfrage = "SELECT * FROM orders ORDER BY orders_id DESC LIMIT 20";
  $ergebnis = mysql_query($abfrage);
  while($row = mysql_fetch_object($ergebnis)){
?>

  <td class="dataTableContent" align="left" width="12%">
  <font size="1" face="Verdana"><?php echo $row-> orders_id; ?> </font></td>

  <td class="dataTableContent" align="left" width="28%">
  <font size="1" face="Verdana"><?php echo $row-> date_purchased; ?> </font></td>

  <td class="dataTableContent" align="left" width="35%">
  <p align="center"><font size="1" face="Verdana"><?php echo $row-> delivery_name; ?> </font></p></td>

  <td class="dataTableContent" align="center" width="12%">
  <a href="orders.php?page=1&oID=<?php echo $row-> orders_id; ?>&action=edit">
  <font size="1" face="Verdana" color="#7691A2"><strong>hier...</strong></font></a></td>

  <td class="dataTableContent" align="left" width="12%">
  <a href="orders.php?page=1&oID=<?php echo $row-> orders_id; ?>&action=delete">
  <font size="1" face="Verdana" color="#800000"><strong>l&ouml;schen...</strong></font></a></td>
</tr>

<?php
}
?>

              <tr>

                <td class="smallText" colspan="5"><i>
				<font size="1" face="Verdana">&nbsp;</font></i></td>

              </tr>

            </table></div>

                <p style="margin-left: 3; margin-top:0; margin-bottom:0">

<?php
}
?>
 
				</p>

				</td>
              <td width="4%" height="200" valign="top">
                <p style="margin-left: 3"></td>
              <td width="48%" bgcolor="#FFFBEF" style="border-style: solid; border-width: 1" height="200" valign="top">
                <div align="left">
			<p style="margin-left: 3; margin-top: 0; margin-bottom: 0" align="left">
				<font size="1">&nbsp;</font>
		  </p>
			<table border="0" width="98%" cellspacing="0" cellpadding="0">

              <tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><strong>Besucher</strong></font></td>
      </tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana">Es sind: <?php echo sprintf(xtc_db_num_rows($whos_online_query)); ?> <a href="whos_online.php">Besucher online</a></font></td>
      </tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><strong>Kunden</strong></font></td>
      </tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><a href="customers.php">Kunden gesamt</a>: <?php echo $customers['count']; ?></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><strong>Newsletter</strong></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana">Newsletter Abos: <?php echo $newsletter['count']; ?></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><a href="module_newsletter.php?action=new" rel="nofollow">Jetzt Newsletter schreiben</a>:</font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><strong>Artikel</strong></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana">Aktive Artikel: <?php echo $products['count']; ?></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana">Inaktive Artikel: <?php echo $products1['count']; ?></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana">Gesamte Artikel: <?php echo $products2['count']; ?></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><a href="categories.php">Artikel bearbeiten!</font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><strong>Angebote</strong></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><a href="specials.php">Sonderangebote</a>: <?php echo $specials['count']; ?></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><strong>Bestellungen</strong></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><a href="orders.php?status=1" target="_self">Offene Bestellungen</a>: <?php echo $orders1['count']; ?></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><a href="orders.php?status=2">Bestellungen in Bearbeitung</a>: <?php echo $orders2['count']; ?></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana"><a href="orders.php?status=3">Versendete Bestellungen</a>: <?php echo $orders3['count']; ?></font></td>
      </tr>
      <tr>
      <td class="dataTableContent" width="100%">
      <font size="1" face="Verdana">Nicht best&auml;tigte Bestellungen: <?php echo $orders0['count']; ?></font></td>
      </tr>
              <tr>
                <td class="smallText" colspan="6"><i>
				<font size="1" face="Verdana">&nbsp;</font></i></td>
              </tr>

            </table></div>

                <p style="margin-left: 3; margin-top:0; margin-bottom:0"></p>

				</td>
            </tr>
            
    <span style="font-size: 11pt">
    <br />
      <div align="center">
        <table cellpadding="5" cellspacing="0" width="100%" id="table1" style="border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC; font-family: Verdana; font-size: 11px">
          <tr>
            <td bgcolor="#FFCC99">
            <p style="margin-left: 3"><font face="Verdana" size="1"><strong>Geburtstagsliste</strong></font></p> 
            </td>
          </tr>
        </table>
      </div>
    <table cellpadding="5" cellspacing="0" style="font-family:Verdana; font-size:10px" width="100%" id="AutoNumber1">
    <tr>
    <td width="100%" colspan="2" bgcolor="#F1F1F1" style="border-bottom: 1px solid #CCCCCC"><strong>Kunden, die heute Geburtstag haben:</strong></td>
    </tr>
<?php
$ergebnis=xtc_db_query("SELECT * FROM customers ORDER BY customers_dob");
$i=0;
while($row = mysql_fetch_object($ergebnis)) {
  $gebdat=strtotime($row->customers_dob);
  $gebjahr=date('Y',$gebdat); 
  $gebmonat=date('n',$gebdat); 
  $gebtag=date('j',$gebdat); 
  if ($gebmonat == date('n') and $gebtag == date('j')) { 
    echo '<tr><td width="78%" bgcolor="#FFF9E9">';
    echo $row->customers_firstname . " " . $row->customers_lastname;
    echo '</td><td width="22%" bgcolor="#FFF9E9">';
    echo xtc_date_long($row->customers_dob);
    echo '</td></tr>';
  }
  if ($gebmonat == date('n') and $gebtag > date('j')) { 
    //(nur zwischenspeichern und nach der schleife ausgeben)
    $geb_bald[$i][0]=$row->customers_firstname;
    $geb_bald[$i][1]=$row->customers_lastname;
    $geb_bald[$i][2]=$row->customers_dob;
    $i++;
  }
}
?>

<tr>
  <td width="100%" colspan="2" style="border-top: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC" bgcolor="#F1F1F1">
    <strong>Kunden, die noch in diesem Monat Geburtstag haben:</strong>
  </td>
</tr>

<?php
$anzahl = count($geb_bald);
for($i=0; $i<$anzahl; $i++) {
  $geb_bald_sort[$i][0] = $geb_bald[$i][0];
  $geb_bald_sort[$i][1] = $geb_bald[$i][1];
  $geb_bald_sort[$i][2] = substr($geb_bald[$i][2],8,2);
}
if ($anzahl > 0)
array_qsort($geb_bald_sort, 2); 

for($i=0;$i<$anzahl;$i++) {
  for($a=0;$a<$anzahl;$a++) {
    if (($geb_bald_sort[$i][0] == $geb_bald[$a][0]) && ($geb_bald_sort
    [$i][1] == $geb_bald[$a][1]))
    break;
  }
  $geb_bald_sort[$i][2] = $geb_bald[$a][2];
}

for($i=0; $i<$anzahl; $i++) {
  echo '<tr><td width="78%" bgcolor="#F9F0F1" style="border-bottom: 1px dotted #000000">';
  echo $geb_bald_sort[$i][0] . ' ' . $geb_bald_sort[$i][1];
  echo '</td><td width="22%" bgcolor="#F9F0F1" style="border-bottom: 1px dotted #000000">';
  echo xtc_date_long($geb_bald_sort[$i][2]);
  echo '</td></tr>';
}
echo '</table><br />';
unset($geb_bald);
?>

              </span>
             </td>
            </tr>
            <tr>
              <td width="48%" bgcolor="#FFFBEF">
                <p style="margin-left: 3">&nbsp;</td>
              <td width="4%">
                <p style="margin-left: 3"></td>
              <td width="48%" bgcolor="#FFFBEF">
                <p style="margin-left: 3">&nbsp;</td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>
</table></td>
      </tr>		 

    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<p>&nbsp;<?php require(DIR_WS_INCLUDES . 'footer.php'); ?></p>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>