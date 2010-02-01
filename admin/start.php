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
require_once ('includes/modules/carp/carp.php');
require_once (DIR_FS_INC.'xtc_validate_vatid_status.inc.php');
require_once (DIR_FS_INC.'xtc_get_geo_zone_code.inc.php');
require_once (DIR_FS_INC.'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC.'xtc_js_lang.php');

require_once(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

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

// newsletter
$newsletter_query = xtc_db_query("select count(*) as count 
                    from " . TABLE_NEWSLETTER_RECIPIENTS. " where mail_status='1'");
$newsletter = xtc_db_fetch_array($newsletter_query);
  
// products  
$products_query = xtc_db_query('select 
                  count(if(products_status = 0, products_id, null)) inactive_count,
                  count(if(products_status = 1, products_id, null)) active_count, 
                  count(*) total_count 
                  from ' . TABLE_PRODUCTS);
$products = xtc_db_fetch_array($products_query);					  
		
// orders (status)		
$orders_query = xtc_db_query('select os.orders_status_name status, coalesce(o.order_count, 0) order_count
                from ' . TABLE_ORDERS_STATUS . ' os
                left join (select orders_status, count(*) order_count
                           from ' . TABLE_ORDERS . ' 
                           group by 1) o on o.orders_status = os.orders_status_id
                where os.language_id = ' . $language_id . '
                order by os.orders_status_id');
$orders = array();
while ($row = mysql_fetch_array($orders_query))
  $orders[] = $row;
	
// specials	
$specials_query = xtc_db_query("select count(*) as specials_count from " . TABLE_SPECIALS);
$specials = xtc_db_fetch_array($specials_query);	        
	        
// turnover
$turnover_query = xtc_db_query('select 
  round(coalesce(sum(if(date(o.date_purchased) = current_date, ot.value, null)), 0), 2) today,
  round(coalesce(sum(if(date(o.date_purchased) = current_date - interval 1 day, ot.value, null)), 0), 2) yesterday, 
  round(coalesce(sum(if(extract(year_month from o.date_purchased) = extract(year_month from current_date), ot.value, null)), 0), 2) this_month,
  round(coalesce(sum(if(extract(year_month from o.date_purchased) = extract(year_month from current_date - interval 1 year_month), ot.value, null)), 0), 2) last_month,
  round(coalesce(sum(if(extract(year_month from o.date_purchased) = extract(year_month from current_date - interval 1 year_month) and o.orders_status <> 1, ot.value, null)), 0), 2) last_month_paid,
  round(coalesce(sum(ot.value), 0), 2) total   
  from ' . TABLE_ORDERS . ' o 
  join ' . TABLE_ORDERS_TOTAL . ' ot on ot.orders_id = o.orders_id 
  where ot.class = \'ot_total\'');
$turnover = mysql_fetch_array($turnover_query);	
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
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
						 <td style="background:#eee"><strong><?php echo TURNOVER_TODAY; ?>:</strong></td>
						 <td  style="background:#eee" align="right"><?php echo $currencies->format($turnover['today']); ?></td>
					</tr>
					<tr>
						 <td style="background:#fff"><strong><?php echo TURNOVER_YESTERDAY; ?>:</strong></td>
						 <td style="background:#fff" align="right"><?php echo $currencies->format($turnover['yesterday']); ?></td>
					</tr>
					<tr>
						 <td style="background:#eee"><strong><?php echo TURNOVER_THIS_MONTH; ?>:</strong></td>
						 <td  style="background:#eee" align="right"><?php echo $currencies->format($turnover['this_month']); ?></td>
					</tr>
					<tr>
						 <td style="background:#ccc"><strong><?php echo TURNOVER_LAST_MONTH; ?>:</strong></td>
						 <td style="background:#ccc" align="right"><?php echo $currencies->format($turnover['last_month']); ?></td>
					</tr>
					<tr>
						 <td style="background:#ccc"><strong><?php echo TURNOVER_LAST_MONTH_PAID; ?>:</strong></td>
						 <td style="background:#ccc" align="right"><?php echo $currencies->format($turnover['last_month_paid']); ?></td>
					</tr>
					<tr>
						 <td style="background:#666; color:#FFF"><strong><?php echo TOTAL_TURNOVER; ?>:</strong></td>
						 <td style="background:#666; color:#FFF" align="right"><?php echo $currencies->format($turnover['total']); ?></td>
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
						 <td style="background:#e4e4e4"><strong><?php echo TOTAL_SUBSCRIBERS; ?>:</strong></td>
						 <td style="background:#e4e4e4" align="center"><?php echo $newsletter['count']; ?></td>
					</tr>
			   </table></td>
		  <td width="25%" valign="top"><table width="100%">
					<tr>
						 <td style="background:#e4e4e4"><strong><?php echo TOTAL_PRODUCTS_ACTIVE; ?>:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $products['active_count']; ?></td>
					</tr>
					<tr>
						 <td style="background:#e4e4e4"><strong><?php echo TOTAL_PRODUCTS_INACTIVE; ?>:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $products['inactive_count']; ?></td>
					</tr>
					<tr>
						 <td style="background:#e4e4e4"><strong><?php echo TOTAL_PRODUCTS; ?>:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $products['total_count'] ?></td>
					</tr>
					<tr>
						 <td style="background:#e4e4e4"><strong><?php echo TOTAL_SPECIALS; ?>:</strong></td>
						 <td style="background:#e4e4e4"><?php echo $specials['specials_count']; ?></td>
					</tr>
			   </table></td>
		  <td width="25%" valign="top">
		  <table width="100%">
		  <?php
	         foreach ($orders as $order) {
                echo '<tr><td style="background:#e4e4e4"><strong>' . $order['status'] . ':</strong></td>';
                echo '<td style="background:#e4e4e4">' . $order['order_count'] . '</td></tr>';
             }
           ?>		
		  </table>
		  </td>
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
	  $abfrage = "SELECT * FROM " . TABLE_CUSTOMERS . " ORDER BY customers_date_added DESC LIMIT 15";
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
	  $abfrage = "SELECT * FROM " . TABLE_ORDERS . " ORDER BY orders_id DESC LIMIT 20";
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
        CarpConf('bi','<br /><div class="feedtitle" style="padding:5px;font-size:11px;" align="left">');
        
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