<?php
/*******************************************************************************************
*                                                                                          *
*  CAO-Faktura f�r Windows Version 1.4 (http://www.cao-faktura.de)                         *
*  Copyright (C) 2009 Jan Pokrandt / Jan@JP-SOFT.de                                        *
*                                                                                          *
*  This program is free software; you can redistribute it and/or                           *
*  modify it under the terms of the GNU General Public License                             *
*  as published by the Free Software Foundation; either version 2                          *
*  of the License, or any later version.                                                   *
*                                                                                          *
*  This program is distributed in the hope that it will be useful,                         *
*  but WITHOUT ANY WARRANTY; without even the implied warranty of                          *
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                           *
*  GNU General Public License for more details.                                            *
*                                                                                          *
*  You should have received a copy of the GNU General Public License                       *
*  along with this program; if not, write to the Free Software                             *
*  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             *
*                                                                                          *
*  ******* This Scripts comes with ABSOLUTELY NO WARRANTY ***************                  *
*                                                                                          *
********************************************************************************************
*                                                                                          *
* Eine Entfernung oder Veraenderung dieses Dateiheaders ist nicht zulaessig !!!            *
* Wenn Sie diese Datei veraendern dann fuegen Sie ihre eigenen Copyrightmeldungen          *
* am Ende diese Headers an                                                                 *
*                                                                                          *
********************************************************************************************
*                                                                                          *
*  Programm     : CAO-Faktura                                                              *
*  Modul        : cao_xtc.php                                                              *
*  Stand        : 26.08.2009                                                               *
*  Version      : 1.56                                                                     *
*  Beschreibung : Script zum Datenaustausch CAO-Faktura <--> xtCommerce-Shop               *
*                                                                                          *
*  based on:                                                                               *
* (c) 2000 - 2001 The Exchange Project                                                     *
* (c) 2001 - 2003 osCommerce, Open Source E-Commerce Solutions                             *
* (c) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Pl�nkers                                      *
* (c) 2003 JP-Soft, Jan Pokrandt                                                           *
* (c) 2003 IN-Solution, Henri Schmidhuber                                                  *
* (c) 2003 www.websl.de, Karl Langmann                                                     *
* (c) 2003 RV-Design Raphael Vullriede                                                     *
* (c) 2004 XT-Commerce                                                                     *
*                                                                                          *
* Released under the GNU General Public License                                            *
*                                                                                          *
*  History :                                                                               *
*                                                                                          *
*  - 25.06.2003 JP Version 0.1 released                                                    *
*  - 26.06.2003 HS beim Orderexport orderstatus und comment hinzugefuegt                   *
*  - 29.06.2003 JP order_update entfernt und in die Datei cao_update.php verschoben        *
*  - 17.07.2003 tep_array_merge durch array_merge ersetzt                                  *
*  - 18.07.2003 Code fuer Image_Upload hinzugefuegt                                        *
*  - 20.07.2003 HS Shipping und Paymentklassen aufgenommen                                 *
*  - 02.08.2003 KL MANUFACTURERS_DESCRIPTION  language_id ge�ndert in languages_id         *
*  - 09.08.2003 JP fuer das Modul Banktransfer werden jetzt die daten bei der Bestll-      *
*                  uebermittlung mit ausgegeben                                            *
*  - 10.08.2003 JP Geburtsdatum wird jetzt in den Bestellungen mit uebergeben              *
*  - 18.08.2003 JP Bug bei Products/URL beseitigt                                          *
*  - 18.08.2003 HS Bankdaten werden nur bei Banktransfer ausgelesen                        *
*  - 23.08.2003 Code fuer Hersteller-Update hinzugefuegt                                   *
*  - 25.10.2003 RV Kunden-Export hinzugef�gt                                               *
*  - 24.11.2003 HS Fix Kunden-Export - Newsletterexport hinzugef�gt                        *
*  - 01.12.2003 RV Code f�r 3 Produktbilder-Erweiterung hinzugef�gt.                       *
*  - 31.01.2004 JP Resourcenverbrauch minimiert                                            *
*                  tep_set_time_limit ist jetzt per DEFINE zu- und abschaltbar             *
*  - 06.06.2004 JP per DEFINE kann jetzt die Option "3 Produktbilder" geschaltet werden    *
*  - 09.10.2004 RV automatisch Erkennung von 3 Bilder Contrib laut readme                  *
*  - 09.10.2004 RV vereinheitlicher Adress-Export bei Bestellungen und Kunden              *
*  - 09.10.2004 RV Kunden Vor- und Nachname bei Bestellungen getrennt exportieren          *
*  - 09.10.2004 RV SQL-Cleanup                                                             *
*  - 09.10.2004 RV CODE-Cleanup                                                            *
*  - 14.10.2004 RV L�nder bei Bestellungen als ISO-Code                                    *
*  - 25.10.2003 Kunden-Update hinzugef�gt                                                  *
*  - 01.11.2003 Status�nderung werden wenn m�glich in der Bestellsprache ausgef�hrt        *
*             Copyright (c) 2004 XT-Commerce                                               *
*              1.1  switching POST/GET vars for CAO imageUpload                            *
*              1.2  mulitlang inserts for Categories                                       *
*              1.3  xt:C v3.0 update                                                       *
*  - 03.12.2003 JP Bugfix beim Kunden-Export (Fehlende Felder)                             *
*               XTC  1.1 fixed bug with attributes and products qty > 1                    *
*               XTC  1.2 Updates for xt:C 3.0                                              *
*  - 10.12.2004 JP Anpassungen fuer CAO 1.2.6.x (customers_export, orders_export)          *
*  - 10.12.2004 JP Anpassungen an CAO-Faktura 1.2.6.1                                      *
*  - 01.06.2005 JP Bugfix MWST-Switch 
*  - 01.06.2005 KL/JP Anpassungen f�r IMAGE_MANIPULATOR (XTC 2.x und 3.x)                  *
*  - 19.08.2005 JP Bugfix beim Aktualisieren von Kunden (PW wurde neu gesetzt)             *
*  - 24.08.2005 TKI Bugfix MWST-Switch                                                     *
*               products_tax_class_id statt $products['products_tax_class_id']             *
*  - 04.10.2005 JP/KL Version 1.44 released, Scripte komplett ueberarbeitet                *
*  - 06.10.2005 KL/JP Bugfix bei xtc_set_time_limit                                        *
*  - 17.10.2005 JP Bugfixes fuer XTC 304                                                   *
*  - 21.10.2005 JP Bugfix bei Passwortuebergabe wenn das Passwort als erstes ein           *
*               numerisches Zeichen enthielt                                               *
*  - 02.11.2005 JP Fehler bei doppelter Funktion xtDBquery gefixt                          *
*  - 15.09.2006 xsell_update / erase durch Wolfgang eingebaut                              *
*               siehe : http://www.cao-faktura.de/index.php?option=com_forum&              *
*              Itemid=44&page=viewtopic&p=52192#52192                                      *    
*******************************************************************************************/


define('SET_TIME_LIMIT',1);   // use set_time_limit(0);
define('CHARSET','iso-8859-1');

$version_nr    = '1.56';
$version_datum = '2009.08.26';

// falls die MWST vom shop vertauscht wird, hier true setzen.
define('SWITCH_MWST',false);

define ('LOGGER',false); // Um das Loggen einzuschalten false durch true ersetzen.

define('USE_3IMAGES',false);
define('USE_VPE',false);

// Emails beim Kundenanlegen versenden ?
define('SEND_ACCOUNT_MAIL',false);

// Default-Sprache
$LangID = 2;
$Lang_folder = 'german';

// Steuer Einstellungen f�r CAO-Faktura

$order_total_class['ot_cod_fee']['prefix'] = '+';
$order_total_class['ot_cod_fee']['tax'] = '19';

$order_total_class['ot_customer_discount']['prefix'] = '-';
$order_total_class['ot_customer_discount']['tax'] = '19';

$order_total_class['ot_gv']['prefix'] = '-';
$order_total_class['ot_gv']['tax'] = '0';

$order_total_class['ot_loworderfee']['prefix'] = '+';
$order_total_class['ot_loworderfee']['tax'] = '19';

$order_total_class['ot_shipping']['prefix'] = '+';
$order_total_class['ot_shipping']['tax'] = '19';


define ('_VALID_XTC',false);

require('../includes/application_top_export.php');

// Kundengruppen ID f�r Neukunden (default "neue Kunden einstellungen in XTC")
define('STANDARD_GROUP',DEFAULT_CUSTOMERS_STATUS_ID);

//KL02062005
if (file_exists(DIR_FS_DOCUMENT_ROOT.'admin/includes/classes/image_manipulator.php'))
{
  // f�r XTC 2.x
  include(DIR_FS_DOCUMENT_ROOT.'admin/includes/classes/image_manipulator.php');
} else {
  // f�r XTC ab 3.x
  include(DIR_FS_DOCUMENT_ROOT.'admin/includes/classes/'.IMAGE_MANIPULATOR);
} //KL02062005_ENDE

if ((isset($_POST['user']))and(isset($_POST['password']))) 
{
   $user=$_POST['user'];
   $password=$_POST['password'];
} 
  else 
{
   $user=$_GET['user'];
   $password=$_GET['password'];
}

if ($user=='' or $password=='')
{
?>
<html><head><title></title></head><body>
<h3><a href="http://www.cao-faktura.de">CAO-Faktura - xt:Commerce Shopanbindung</a></h3>
<h4>Mehr dazu im <a href="http://www.cao-faktura.de/index.php?option=com_forum&Itemid=44">Forum</a></h4>
<h4>Version <?php echo $version_nr; ?> Stand : <?php echo $version_datum; ?></h4>
<br><br>
Aufruf des Scriptes mit <br><b><?php echo $PHP_SELF; ?>?user=<font color="red">ADMIN-EMAIL</font>&password=<font color="red">ADMIN-PASSWORD-IM-KLARTEXT</font>
</b>
</body></html>
<?php
  exit;
}
  else
{
  require ('cao_xtc_functions.php');
  
  // security  1.check if admin user with this mailadress exits, and got access to xml-export
  //           2.check if pasword = true
  if (column_exists ('admin_access','xml_export')==false)
  {
     xtc_db_query('ALTER TABLE admin_access ADD xml_export INT(1)  DEFAULT "0";');
     xtc_db_query('UPDATE admin_access SET xml_export= 1 WHERE customers_id=\'1\';');
  }
 
  $check_customer_query=xtc_db_query("select customers_id,
                                      customers_status,
                                      customers_password
                                      from " . TABLE_CUSTOMERS . " where
                                      customers_email_address = '" . $user . "'");

  if (!xtc_db_num_rows($check_customer_query))
  {
    SendXMLHeader ();
    print_xml_status (105, $_POST['action'], 'WRONG LOGIN', '', '', '');	 
    exit; 
  }
    else
  {
    $check_customer = xtc_db_fetch_array($check_customer_query);
    // check if customer is Admin
    if ($check_customer['customers_status']!='0') 
    {
      SendXMLHeader ();
      print_xml_status (106, $_POST['action'], 'WRONG LOGIN', '', '', '');	  
      exit;
    }

    // check if Admin is allowed to access xml_export
    $access_query=xtc_db_query("SELECT
                                xml_export
                                from admin_access
                                WHERE customers_id='".$check_customer['customers_id']."'");
    $access_data = xtc_db_fetch_array($access_query);
    if ($access_data['xml_export']!=1) 
    {
      SendXMLHeader ();
      print_xml_status (107, $_POST['action'], 'WRONG LOGIN', '', '', '');
      exit;
    }

    if (!( ($check_customer['customers_password'] == $password) or 
             ($check_customer['customers_password'] == md5($password)) or
             ($check_customer['customers_password'] == md5(substr($password,2,40)))
       ))
    {
      SendXMLHeader ();
      print_xml_status (108, $_POST['action'], 'WRONG PASSWORD', '', '', '');	  	
      exit;
    }
  }
}


if ($_SERVER['REQUEST_METHOD']=='GET') 
{
  switch ($_GET['action']) 
  {
     case 'version':        // Ausgabe Scriptversion
     
       SendXMLHeader ();
       SendScriptVersion ();
       exit; 
     
     case 'categories_export':
     
       SendXMLHeader ();
       SendCategories ();
       exit;
     
     case 'manufacturers_export':
     
       SendXMLHeader ();
       SendManufacturers ();
       exit;
     
     case 'orders_export':
     
       SendXMLHeader ();
       SendOrders ();
       exit;
     
     case 'products_export':
     
       SendXMLHeader ();
       SendProducts ();
       exit;
     
     case 'customers_export':
     
       SendXMLHeader ();
       SendCustomers ();
       exit;
     
     case 'customers_newsletter_export':
     
       SendXMLHeader ();
       SendCustomersNewsletter ();
       exit;
     
     case 'config_export':
     
       SendXMLHeader ();
       SendShopConfig ();
       exit;
       
     case 'update_tables':
     
       UpdateTables ();
       exit;
       
     case 'send_log':
     
       SendLog ();
       exit;
       
     default :     
       
       ShowHTMLMenu ();
       exit;
       
   } // End Case   
} // End Method POST
 else 
{
  if ($_SERVER['REQUEST_METHOD']=='POST') 
  {
    switch ($_POST['action']) 
    {
      case 'manufacturers_image_upload':
     
        SendXMLHeader ();
        ManufacturersImageUpload ();
        exit;
     
     case 'categories_image_upload':
     
        SendXMLHeader ();
        CategoriesImageUpload ();
        exit;
     
     case 'products_image_upload':
     
        SendXMLHeader ();
        ProductsImageUpload ();
        exit;   
        
     case 'products_image_upload_med':
     
        SendXMLHeader ();
        ProductsImageUploadMed ();
        exit;   
        
     case 'products_image_upload_large':
     
        SendXMLHeader ();
        ProductsImageUploadLarge ();
        exit;   
        
     case 'manufacturers_update':
     
        SendXMLHeader ();
        ManufacturersUpdate ();
        exit;   
        
      case 'manufacturers_erase':
     
        SendXMLHeader ();
        ManufacturersErase ();
        exit;   
        
      case 'products_update':
        
        SendXMLHeader ();
        ProductsUpdate ();
        exit;
        
      case 'products_erase':
        
        SendXMLHeader ();
        ProductsErase ();
        exit;
        
      case 'products_specialprice_update':
        
        SendXMLHeader ();
        ProductsSpecialPriceUpdate ();
        exit;
        
      case 'products_specialprice_erase':  
        
        SendXMLHeader ();
        ProductsSpecialPriceErase ();
        exit;
        
      case 'categories_update':
        
        SendXMLHeader ();
        CategoriesUpdate ();
        exit;
        
      case 'categories_erase':
        
        SendXMLHeader ();  
        CategoriesErase ();
        exit;
        
      case 'prod2cat_update':

        SendXMLHeader ();  
        Prod2CatUpdate ();
        exit;

      case 'prod2cat_erase':

        SendXMLHeader ();  
        Prod2CatErase ();
        exit;
        
      case 'order_update':
      
        SendXMLHeader ();  
        OrderUpdate ();
        exit;
        
      case 'customers_update':
      
        SendXMLHeader ();  
        CustomersUpdate ();
        exit;
      
      case 'customers_erase':
      
        SendXMLHeader ();  
        CustomersErase ();
        exit;
        
      case 'xsell_update':
     
        SendXMLHeader ();
        XsellUpdate ();
        exit;
       
      case 'xsell_erase':
     
        SendXMLHeader ();
        XsellErase ();
        exit;  
          
    } // End Case
  }  // End Method POST
}

?>