<?php

/* --------------------------------------------------------------
   $Id: start.php 1235 2005-09-21 19:11:43Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project 
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  $xx_mins_ago = (time() - 900);
require ('includes/application_top.php');
require_once 'includes/modules/carp/carp.php';
require_once (DIR_FS_INC.'xtc_validate_vatid_status.inc.php');
require_once (DIR_FS_INC.'xtc_get_geo_zone_code.inc.php');
require_once (DIR_FS_INC.'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC.'xtc_js_lang.php');

$customers_statuses_array = xtc_get_customers_statuses();
// remove entries that have expired
xtc_db_query("delete from " . TABLE_WHOS_ONLINE . " where time_last_click < '" . $xx_mins_ago . "'");

$language_id = (int) $_SESSION['languages_id'];
// customer stats
$customers_query = xtc_db_query('select cs.customers_status_name cust_group, count(*) cust_count   
                     from ' . TABLE_CUSTOMERS . ' c
                     join ' . TABLE_CUSTOMERS_STATUS . ' cs on cs.customers_status_id = c.customers_status
                     --  exclude admin
                     where c.customers_status > 0
                     -- restrict to current language setting
                     and cs.language_id = ' . $language_id . '
                     group by 1
                     union
                     select \'' . TOTAL_CUSTOMERS . '\', count(*)   
                     from ' . TABLE_CUSTOMERS . '
                     order by 2 desc');
// save query result
$customers = array();
while ($row = mysql_fetch_array($customers_query))
  $customers[] = $row;

$newsletter_query = xtc_db_query("select count(*) as count from " . TABLE_NEWSLETTER_RECIPIENTS. " where mail_status='1'");
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
	        
	        
$datelastyear=	date("Y");
$datelastmonth=	date("m")-1;
$datethismonth=	date("m");

//Query HEUTE
	$orders_today_query = xtDBquery(" SELECT sum(ot.value) FROM ".TABLE_ORDERS." o, ".TABLE_ORDERS_TOTAL." ot WHERE o.date_purchased LIKE '".date("Y-m-d")."%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' ");
	$orders_today = xtc_db_fetch_array($orders_today_query);
//Query GESTERN	
	$orders_yesterday_query = xtDBquery(" SELECT sum(ot.value) FROM ".TABLE_ORDERS." o, ".TABLE_ORDERS_TOTAL." ot WHERE o.date_purchased LIKE '".date("Y-m-d",time() - 86400)."%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' ");
	$orders_yesterday = xtc_db_fetch_array($orders_yesterday_query);
//Query DIESER MONAT ALLE	
	$orders_thismonth_query = xtDBquery(" SELECT sum(ot.value) FROM ".TABLE_ORDERS." o, ".TABLE_ORDERS_TOTAL." ot WHERE o.date_purchased LIKE '". $datelastyear . "-" . $datethismonth . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' ");
	$orders_thismonth = xtc_db_fetch_array($orders_thismonth_query);
//Query LETZTER MONAT ALLE	
	$orders_lastmonth_query = xtDBquery(" SELECT sum(ot.value) FROM ".TABLE_ORDERS." o, ".TABLE_ORDERS_TOTAL." ot WHERE o.date_purchased LIKE '". $datelastyear . "-" . $datelastmonth . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' ");
	$orders_lastmonth = xtc_db_fetch_array($orders_lastmonth_query);
//Query LETZTER MONAT OHNE STATUS OFFEN	
	$orders_lastmonth_bereinigt_query = xtDBquery(" SELECT sum(ot.value) FROM ".TABLE_ORDERS." o, ".TABLE_ORDERS_TOTAL." ot WHERE o.orders_status NOT LIKE 1 AND o.date_purchased LIKE '". $datelastyear . "-" . $datelastmonth . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' ");
	$orders_lastmonth_bereinigt = xtc_db_fetch_array($orders_lastmonth_bereinigt_query);	
//Query ALLE	
	$orders_total_query = xtDBquery(" SELECT sum(ot.value) FROM ".TABLE_ORDERS." o, ".TABLE_ORDERS_TOTAL." ot WHERE ot.orders_id = o.orders_id AND ot.class = 'ot_total' ");
	$orders_total = xtc_db_fetch_array($orders_total_query);
	
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Language" content="de">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<style type="text/css">
h1 {
	font-size:18px;
	font-weight:bold;
	padding-left:10px;
}
.h2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 13pt;
	font-weight: bold;
}
.h3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 9pt;
	font-weight: bold;
}
.startphp td {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	padding:5px;
}
.feedtitle a {
	font-size:12px;
	font-weight:bold;
}
</style>
</head>
<body style="margin:0" bgcolor="#FFFFFF">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- BOF - Tomcraft - 2009-06-16 - Added security check //-->
<table width="100%">
  <tr>
    <td><?php include(DIR_WS_MODULES.FILENAME_SECURITY_CHECK); ?></td>
  </tr>
</table>
<!-- EOF - Tomcraft - 2009-06-16 - Added security check //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="startphp">
<tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
     <td width="100%" valign="top">
	   <table border="0" width="100%" cellspacing="0" cellpadding="0">
	 <tr>
	 <td class="boxCenter" width="100%" valign="top"> 	 
     <table border="0" width="100%" cellspacing="0" cellpadding="0">          
          <tr>
               <td><h1>Willkommen im Adminbereich</h1></td>
          </tr>
          <tr>
               <td>
			   <!--  BOF START INFOS STATISTIK -->
			   <table width="100%" border="0" cellspacing="0">
	 <tr>
		  <td width="25%" valign="top"><table width="100%" border="0">
					<tr>
						 <td style="background:#eee"><strong>Umsatz heute:</strong></td>
						 <td  style="background:#eee" align="right"><?php echo number_format($orders_today['sum(ot.value)'],2); ?>&euro;</td>
					</tr>
					<tr>
						 <td style="background:#fff"><strong>Umsatz gestern:</strong></td>
						 <td style="background:#fff" align="right"><?php echo number_format($orders_yesterday['sum(ot.value)'],2); ?>&euro;</td>
					</tr>
					<tr>
						 <td style="background:#eee"><strong>aktueller Monat:</strong></td>
						 <td  style="background:#eee" align="right"><?php echo number_format($orders_thismonth['sum(ot.value)'],2); ?>&euro;</td>
					</tr>
					<tr>
						 <td style="background:#ccc"><strong>letzter Monat (alle):</strong></td>
						 <td style="background:#ccc" align="right"><?php echo number_format($orders_lastmonth['sum(ot.value)'],2); ?>&euro;</td>
					</tr>
					<tr>
						 <td style="background:#ccc"><strong>letzter Monat (bezahlt):</strong></td>
						 <td style="background:#ccc" align="right"><?php echo number_format($orders_lastmonth_bereinigt['sum(ot.value)'],2); ?>&euro;</td>
					</tr>
					<tr>
						 <td style="background:#666; color:#FFF"><strong>Umsatz gesamt:</strong></td>
						 <td style="background:#666; color:#FFF" align="right"><?php echo number_format($orders_total['sum(ot.value)'],2); ?>&euro;</td>
					</tr>
			   </table></td>
		  <td width="25%" valign="top"><table width="100%">
		  
		  <?php
	         foreach ($customers as $customer) {
                echo '<tr><td style="background:#e4e4e4"><strong>' . $customer['cust_group'] . ':</strong></td>';
                echo '<td style="background:#e4e4e4" align="center">' . $customer['cust_count'] . '</td></tr>';
             }
           ?>
					<tr>
						 <td style="background:#e4e4e4"><strong>davon Newsletter Abo:</strong></td>
						 <td style="background:#e4e4e4" align="center"><?php echo $newsletter['count']; ?></td>
					</tr>
			   </table></td>
		  <td width="25%" valign="top"><table width="100%">
					<tr>
						 <td style="background:#e4e4e4"><strong>Aktive Artikel:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $products['count']; ?></td>
					</tr>
					<tr>
						 <td style="background:#e4e4e4"><strong>Inaktive Artikel:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $products1['count']; ?></td>
					</tr>
					<tr>
						 <td style="background:#e4e4e4"><strong>Artikel gesamt:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $products2['count'] ?></td>
					</tr>
					<tr>
						 <td style="background:#e4e4e4"><strong>Sonderangebote:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $specials['count']; ?></td>
					</tr>
			   </table></td>
		  <td width="25%" valign="top"><table width="100%">
					<tr>
						 <td style="background:#e4e4e4"><strong>Offen:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $orders1['count']; ?></td>
					</tr>
					<tr>
						 <td style="background:#e4e4e4"><strong>In Bearbeitung:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $orders2['count']; ?></td>
					</tr>
					<tr>
						 <td style="background:#e4e4e4"><strong>Versendet:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $orders3['count']; ?></td>
					</tr>
					<tr>
						 <td style="background:#e4e4e4"><strong>Nicht zugeordnet:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $orders0['count']; ?></td>
					</tr>
			   </table></td>
	 </tr>
</table>			   
			   <!--  EOF START INFOS STATISTIK -->
			   </td>
          </tr>
          <tr>          
          <td>
          
          <table valign="top" width="100%" cellpadding="0" cellspacing="0">			
			<tr>
			  <td></td>
			  </tr>
			<tr>
			  <td>
			  <!--  BOF START INFOS USER ONLINE + NEUE KUNDEN  + LETZTE BESTELLUNGEN +  NEWSFEED-->
			  <table border="0" width="100%" cellspacing="0">
	<tr>
	  <td width="48%" class="infoBoxHeading" style="border: 1px solid #b40076; border-bottom: 1px solid #b40076;"><strong>User
        Online</strong></td>
	  <td width="4%"><p style="margin-left: 3px"></p>
	  </td>
	  <td width="48%" class="infoBoxHeading" style="border: 1px solid #b40076; border-bottom: 1px solid #b40076;"><font face="Verdana"><strong>Neue
          Kunden </strong>(letzten 15 neuen Kunden)</font></td>
	</tr>
	<tr>
	  <td style="background: #F9F0F1; border: 1px solid #b40076;" height="200" valign="top">&nbsp;<em><font face="Verdana" color="#7691A2">***für
          Infos zu dem User - auf Name des Users klicken***</font></em>
        <table border="0" width="98%" cellspacing="0" cellpadding="0">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent" bgcolor="#D9D9D9" height="20" width="22%"><strong> <font face="Verdana">Online
                  seit (min.)</font></strong></td>
            <td class="dataTableHeadingContent" bgcolor="#D9D9D9" height="20" width="33%"><strong> <font face="Verdana">Name</font></strong></td>
            <td class="dataTableHeadingContent" align="center" bgcolor="#D9D9D9" height="20" width="33%"><strong> <font face="Verdana">Letzter
                  Klick</font></strong></td>
            <td class="dataTableHeadingContent" align="center" bgcolor="#D9D9D9" height="20" width="33%"><strong><font face="Verdana">Infos</font></strong></td>
          </tr>
          <?php
	
	  $whos_online_query = xtc_db_query("select customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, session_id from " . TABLE_WHOS_ONLINE ." order by time_last_click desc");
	
	  while ($whos_online = xtc_db_fetch_array($whos_online_query)) {	
	    $time_online = (time() - $whos_online['time_entry']);	
	    if ( ((!$_GET['info']) || (@$_GET['info'] == $whos_online['session_id'])) && (!$info) ) {	
	      $info = $whos_online['session_id'];	
	    }	    
	?>
          <tr>
            <td class="dataTableContent" width="22%"><font face="Verdana"> <a href="whos_online.php?info=<?php echo $whos_online['session_id']; ?>"><?php echo gmdate('H:i:s', $time_online); ?></a></font></td>
            <td class="dataTableContent" width="33%"><font face="Verdana"> <a href="whos_online.php?info=<?php echo $whos_online['session_id']; ?>"><?php echo $whos_online['full_name']; ?></a></font></td>
            <td class="dataTableContent" align="center" width="33%"><font face="Verdana"> <a href="whos_online.php?info=<?php echo $whos_online['session_id']; ?>"><?php echo date('H:i:s', $whos_online['time_last_click']); ?></a></font></td>
            <td class="dataTableContent" align="center" width="33%"><font face="Verdana" color="#800000"><u><strong> <a href="whos_online.php?info=<?php echo $whos_online['session_id']; ?>"> <font color="#800000"><strong>mehr...</strong></font></a></strong></u></font></td>
          </tr>
    <?php	
	  }	
	?>          
        </table>
    </td>
	  <td width="4%">&nbsp;</td>
	  <td style="background: #F9F0F1; border: 1px solid #b40076;" height="200" valign="top">
      <table border="0" width="98%" cellspacing="0" cellpadding="0">
        <tr class="dataTableHeadingRow">
          <td class="dataTableHeadingContent" bgcolor="#D9D9D9" height="20" width="25%"><strong> <font face="Verdana">Name</font></strong></td>
          <td class="dataTableHeadingContent" bgcolor="#D9D9D9" height="20" width="25%"><strong> <font face="Verdana">Vorname</font></strong></td>
          <td class="dataTableHeadingContent" align="center" bgcolor="#D9D9D9" height="20" width="25%"><strong> <font face="Verdana">angemeldet
                am</font></strong></td>
          <td class="dataTableHeadingContent" align="center" bgcolor="#D9D9D9" height="20" width="12%"><strong><font face="Verdana">bearbeiten</font></strong></td>
          <td class="dataTableHeadingContent" align="center" bgcolor="#D9D9D9" height="20" width="12%"><strong><font face="Verdana">Bestellungen</font></strong></td>
        </tr>
        <?php
	
	 // $whos_online_query = xtc_db_query("select customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, session_id from " . TABLE_WHOS_ONLINE ." order by time_last_click desc");
		    
	?>
        <?php
	  $abfrage = "SELECT * FROM customers ORDER BY customers_date_added DESC LIMIT 15";
	  $ergebnis = mysql_query($abfrage);
	  while($row = mysql_fetch_object($ergebnis)){
	?>
        <tr>
          <td class="dataTableContent" width="25%"><?php  echo $row-> customers_lastname; ?>
          </td>
          <td class="dataTableContent" width="25%"><?php  echo $row-> customers_firstname; ?>
          </td>
          <td class="dataTableContent" align="center" width="25%"><?php  echo $row-> customers_date_added; ?>
          </td>
          <td class="dataTableContent" align="center" width="12%"><strong> <a href="customers.php?page=1&cID=<?php  echo $row-> customers_id; ?>&action=edit"> <font face="Verdana" color="#800000"><strong>hier...</strong></font></a></strong></td>
          <td class="dataTableContent" align="center" width="12%"><strong> <a href="orders.php?cID=<?php  echo $row-> customers_id; ?>"><font color="#7691A2" face="Verdana"><strong>anzeigen...</strong></font></a></strong></td>
        </tr>
        <?php
	
	  }
	
	?>       
      </table>
    </td>
	</tr>
	<tr>
	  <td width="48%">&nbsp;</td>
	  <td width="4%">&nbsp;</td>
	  <td width="48%">&nbsp;</td>
	</tr>
	<tr>
	  <td width="48%" class="infoBoxHeading" style="border: 1px solid #b40076; border-bottom: 1px solid #b40076;"><span style="margin-left: 3px"><strong><font face="Verdana"> letzten </font></strong><font face="Verdana"><strong>Bestellungen </strong> (letzten
          20 Bestellungen)</font></span></td>
	  <td width="4%"><p style="margin-left: 3px"></p>
	  </td>
	  <td width="48%" class="infoBoxHeading" style="border: 1px solid #b40076; border-bottom: 1px solid #b40076;"><span style="margin-left: 3px"><font face="Verdana"><strong> Zur
            Homepage von:</strong> <a href="http://www.xtc-modified.org" target="_blank">xtcModified.org</a></font></span></td>
	</tr>
	<tr>
	  <td style="background: #F9F0F1; border: 1px solid #b40076; height: 200px;" valign="top">
      <table border="0" width="98%" cellspacing="0" cellpadding="0">
        <tr class="dataTableHeadingRow">
          <td class="dataTableHeadingContent" bgcolor="#D9D9D9" height="20" width="25%"><strong> <font face="Verdana">Bestellnummer</font></strong></td>
          <td class="dataTableHeadingContent" bgcolor="#D9D9D9" height="20" width="25%"><p align="center"> <strong><font face="Verdana">Bestelldatum</font></strong></td>
          <td class="dataTableHeadingContent" align="center" bgcolor="#D9D9D9" height="20" width="25%"><p align="center"> <strong><font face="Verdana">Kundenname</font></strong></td>
          <td class="dataTableHeadingContent" align="center" bgcolor="#D9D9D9" height="20" width="12%"><p align="center"><strong><font face="Verdana">bearbeiten</font></strong></td>
          <td class="dataTableHeadingContent" align="center" bgcolor="#D9D9D9" height="20" width="12%"><p align="center"><strong><font face="Verdana">löschen</font></strong></td>
        </tr>
        <?php
	
	 // $whos_online_query = xtc_db_query("select customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, session_id from " . TABLE_WHOS_ONLINE ." order by time_last_click desc");
	    
	?>
        <?php
	  $abfrage = "SELECT * FROM orders ORDER BY orders_id DESC LIMIT 20";
	  $ergebnis = mysql_query($abfrage);
	  while($row = mysql_fetch_object($ergebnis)){
	?>
        <tr>
          <td class="dataTableContent" width="25%"><font face="Verdana">
            <?php  echo $row-> orders_id; ?>
          </font></td>
          <td class="dataTableContent" width="25%"><p align="center"> <font face="Verdana">
              <?php  echo $row-> date_purchased; ?>
          </font> </td>
          <td class="dataTableContent" align="center" width="25%"><font face="Verdana">
            <?php  echo $row-> delivery_name; ?>
          </font></td>
          <td class="dataTableContent" align="center" width="12%"><strong> <a href="orders.php?page=1&oID=<?php  echo $row-> orders_id; ?>&action=edit"> <font face="Verdana" color="#7691A2"><strong>hier...</strong></font></a></strong></td>
          <td class="dataTableContent" align="center" width="12%"><font face="Verdana" color="#800000"> <strong> <a href="orders.php?page=1&oID=<?php  echo $row-> orders_id; ?>&action=delete"> <font color="#800000"><strong>löschen...</strong></font></a></strong></font></td>
        </tr>
        <?php
	
	  }
	
	?>
        <tr>
          <td class="smallText" colspan="5"><em> <font face="Verdana"></font></em></td>
        </tr>
      </table>
    </td>
	  <td>&nbsp;</td>
	  <td style="background: #F9F0F1; border: 1px solid #b40076;" height="200" valign="top">
	  <table border="0" width="98%" cellspacing="0" cellpadding="0">
        <?php

CarpConf('iorder','link,date,desc');

        CarpConf('cborder','link,desc');
        CarpConf('caorder','image');
        CarpConf('bcb','<div style="background:#F0F1F1;font-size:11px; border:1px solid #999; padding:5px; font-weight: 700" align="left">');
        CarpConf('acb','</div>');
        CarpConf('bca','<span>');
        CarpConf('aca','</span>');
CarpConf('maxitems',3);
        
        // before each item
        CarpConf('bi','<br /><div class"feedtitle" style="padding:5px;font-size:11px;" align="left">');
        
        // after each item
        CarpConf('ai','</div><hr noshade="noshade" />');
		CarpShow('http://www.xtc-modified.org/feed/');
?>
      </table>
    </td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
  </tr>
	<tr>
	  <td colspan="3" style="padding:0px">
	  
<!--  BOF START INFOS GEBURTSTAGSLISTE -->
	  <table cellpadding="5" cellspacing="0" width="100%" id="table1" class="contentTable">
	<tr>
		<td class="infoBoxHeading"><span style="margin-left: 3px"></span><font face="Verdana"><strong>Geburtstagsliste</strong></font></span></td>
	</tr>
</table>
<table cellpadding="5" cellspacing="0" style="font-family:Verdana; font-size:11px; border: 1px solid #b40076; border-top:0px;" width="100%" id="AutoNumber1">
	<tr>
		<td width="100%" colspan="2" bgcolor="#F1F1F1" style="border-bottom: 1px solid #CCCCCC"><strong>Kunden, die heute Geburtstag haben:</strong></td>
	</tr>
<?php
$ergebnis = xtc_db_query("select
                          concat(customers_firstname, ' ', customers_lastname) name,
                          customers_dob dob,
                          if(day(customers_dob) = day(current_date), true, false) today
                          from " . TABLE_CUSTOMERS . "
                          where month(customers_dob) = month(current_date)
                          order by customers_dob");

$this_month = array();
while($row = mysql_fetch_array($ergebnis))
{
   if ($row['today'] == 1) {
     echo '<tr><td width="68%" bgcolor="#F9F0F1">' . $row['name'] . '</td>';
     echo '<td width="32%" bgcolor="#F9F0F1">' . xtc_date_long($row['dob']) . '</td></tr>';
  }
  else
    $this_month[] = array('name' => $row['name'], 'dob' => $row['dob']);
}
?>
  <tr>
     <td width="100%" colspan="2" style="border-top: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC" bgcolor="#F1F1F1"><strong>Kunden, die noch in diesem Monat Geburtstag haben:</strong></td>
  </tr>
<?php
foreach($this_month as $row) {
  echo '<tr><td width="68%" bgcolor="#F9F0F1">' . $row['name'] . '</td>';
  echo '<td width="32%" bgcolor="#F9F0F1">' . xtc_date_long($row['dob']) . '</td></tr>';
}
?>
</table>
<!--  EOF START INFOS GEBURTSTAGSLISTE -->

      </td>
	  </tr>
</table>
			  <!--  EOF START INFOS USER ONLINE + NEUE KUNDEN  + LETZTE BESTELLUNGEN +  NEWSFEED-->
			  </td>
			 </tr>
					  
          </table>
		  
          </td>          
       </tr>          
     </table>
	 </td></tr></table>
   </td>          
  </tr>  
</table>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
 	  	 

 	  	 
