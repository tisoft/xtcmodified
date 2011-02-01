<?php
/* ------------------------------------------------------------
Copyright (c) 2009 Hamburger-Internetdienst - Michael Nochelski
---------------------------------------------------------------
Released under the GNU General Public License
---------------------------------------------------------------
Modul zum Testen des Einbaus des PayPal Express Moduls
Hilfe: www.forum.hamburger-internetdienst.de
Author: Michael Nochelski
Beginn: 18.04.2009
---------------------------------------------------------------
Stand: 09.01.2011
---------------------------------------------------------------*/
require('includes/application_top.php');
// Shop Programme - Shop Config nehmen falls da was anders ist als im Admin Config (MultiShop))
if(file_exists(DIR_FS_CATALOG.'includes/local/configure.php')):
	$shop_config=DIR_FS_CATALOG.'includes/local/configure.php';
else:
	$shop_config=DIR_FS_CATALOG.'includes/configure.php';
endif;
if(file_exists($shop_config)):
	$lines = file($shop_config);
	foreach($lines as $nr=>$line) {
		if(strpos($line,'\'DIR_FS_CATALOG\'')){eval(str_replace('\'DIR_FS_CATALOG\'','\'DIR_SHOP_CATALOG\'',$line));continue;}
		if(strpos($line,'\'DIR_FS_DOCUMENT_ROOT\'')){eval(str_replace('\'DIR_FS_DOCUMENT_ROOT\'','\'DIR_SHOP_ROOT\'',$line));continue;}
		if(strpos($line,'\'DIR_WS_INCLUDES\'')){eval(str_replace('\'DIR_WS_INCLUDES\'','\'DIR_SHOP_INCLUDES\'',str_replace('DIR_FS_DOCUMENT_ROOT','DIR_SHOP_ROOT',$line)));continue;}
		if(strpos($line,'\'DIR_WS_CLASSES\'')){eval(str_replace('\'DIR_WS_CLASSES\'','\'DIR_SHOP_CLASSES\'',str_replace('DIR_WS_INCLUDES','DIR_SHOP_INCLUDES',$line)));continue;}
		if(strpos($line,'\'DIR_WS_MODULES\'')){eval(str_replace('\'DIR_WS_MODULES\'','\'DIR_SHOP_MODULES\'',str_replace('DIR_WS_INCLUDES','DIR_SHOP_INCLUDES',$line)));continue;}
		if(strpos($line,'\'DIR_WS_LANGUAGES\'')){eval(str_replace('\'DIR_WS_LANGUAGES\'','\'DIR_SHOP_LANGUAGES\'',str_replace('DIR_FS_CATALOG','DIR_SHOP_CATALOG',$line)));continue;}
	}
endif;
$meldung='<tr class="dataTableRow" style="line-height:2em;"><td colspan="3" class="dataTableContent" align="%s"><strong>%s</strong></td></tr>';
switch($_GET['ppauto']) {
	case '1':
		def_texte(1);
		$html_output=inst01();
		if(!$html_output) // kein Fehler -> weiter zu Schritt 2
			xtc_redirect(xtc_href_link('module_paypal_install.php','set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&ppauto=2'));
		break;
	case '2':
		def_texte(2);
		$html_output=inst02();
		if(!$html_output) // kein Fehler -> Ende
			xtc_redirect(xtc_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module']));
		break;
	default: // Nix
		xtc_redirect(xtc_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module']));
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
		<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
			<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
			</table>
		</td>
		<td class="boxCenter" width="100%" valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td width="100%">
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_modules.gif'); ?></td>
								<td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
							</tr>
							<tr>
								<td class="main" valign="top">XT Modules <?php echo $_GET['module']; ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top">
									<table style="table-layout:fixed; border:0; width='100%';" cellspacing="0" cellpadding="2">
										<colgroup><col /><col width="80px" /><col width="70px" /></colgroup>
										<tr class="dataTableHeadingRow">
											<td class="dataTableHeadingContent">Programm</td>
											<td class="dataTableHeadingContent" align="center">Aktion</td>
											<td class="dataTableHeadingContent" align="center"><?php echo RESULT_00; ?></td>
										</tr>
										<?php echo $html_output; ?>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php');
return;
/**************************************************************/
/************* Funktionen für den Schritt 1 *******************/
/**************************************************************/
function inst01(){
	// Stand: 29.04.2009
	global $html_zeil,$meldung,$error,$warning,$detail;
	// Admin Programme - root
	$verz=DIR_FS_ADMIN;
	$html_head=sprintf($meldung,'left',PATH_01.'<br />'.$verz);
	$html_zeil='';
	vergleich01(progdat01(1),$verz,'');
	// Admin Programme /includes/classes
	$verz=DIR_FS_ADMIN.DIR_WS_CLASSES;
	vergleich01(progdat01(2),$verz,DIR_WS_CLASSES);
	// Admin Programme /includes/modules
	$verz=DIR_FS_ADMIN.DIR_WS_MODULES;
	vergleich01(progdat01(3),$verz,DIR_WS_MODULES);
	if($html_zeil!='')$html_output=$html_head.$html_zeil;
	// Shop Programme - root
	$verz=DIR_SHOP_CATALOG;
	$html_head=sprintf($meldung,'left',PATH_02.'<br />'.$verz);
	$html_zeil='';
	vergleich01(progdat01(4),$verz,'');
	// Shop Programme /callback/paypal
	$verz=DIR_SHOP_CATALOG.'callback/paypal/';
	vergleich01(progdat01(5),$verz,str_replace(DIR_SHOP_CATALOG,'',$verz));
	// Shop Programme /Template
	$verz=DIR_SHOP_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/';
	vergleich01(progdat01(6),$verz,str_replace(DIR_SHOP_CATALOG,'',$verz));
	// Shop Programme /includes/classes
	$verz=DIR_SHOP_CLASSES;
	vergleich01(progdat01(7),$verz,str_replace(DIR_SHOP_ROOT,'',$verz));
	// Shop Programme /includes/modules
	$verz=DIR_SHOP_MODULES.'payment/';
	vergleich01(progdat01(8),$verz,str_replace(DIR_SHOP_ROOT,'',$verz));
	// Shop Programme /lang
	$languages_query = xtc_db_query("select directory from " . TABLE_LANGUAGES . " order by sort_order");
	while($languages = xtc_db_fetch_array($languages_query)) {
		$verz=DIR_SHOP_LANGUAGES.$languages['directory'].'/';
		vergleich01(progdat01(9,$languages['directory']),$verz,str_replace(DIR_SHOP_CATALOG,'',$verz));
	}
	if($html_zeil!='')$html_output.=$html_head;
	if($error==1):
		$html_zeil.=sprintf($meldung,'center',ERROR_01);
		xtc_db_query("update ".TABLE_CONFIGURATION." SET configuration_value='False' where configuration_key='MODULE_PAYMENT_PAYPALEXPRESS_STATUS'");
		xtc_db_query("update ".TABLE_CONFIGURATION." SET configuration_value='False' where configuration_key='MODULE_PAYMENT_PAYPAL_STATUS'");
		$html_zeil.=sprintf($meldung,'center',ERROR_02);
		menue01(1,0,0,2);
	elseif($warning==1):
		$html_zeil.=sprintf($meldung,'center',ERROR_03);
		menue01(1,0,1,2);
	// Nur nötig falls mit OK Anzeige:
	//else:
		//$html_zeil.=sprintf($meldung,'center','Fertig - alles OK.');
		//menue01(0,1,0,2);
	endif;
	if($html_zeil!='')$html_output.=$html_zeil;
	return($html_output);
}
/**************************************************************/
function vergleich01($programme,$verz,$verzz){
	// Stand: 29.04.2009
	global $html_zeil,$error,$warning,$detail;
	foreach($programme as $programm=>$fcrc) {
		zeig01($verzz.$programm,ACT_01,such01($verz.$programm,$fcrc));
	}
	return;
}
/**************************************************************/
function such01($prog,$fcrc=0){
	// Stand: 29.04.2009
	global $error,$warning,$detail;
	if(file_exists($prog)):
		$ret="OK";
		if(file_crc($prog)!=$fcrc AND $fcrc!=0):
			$color='<font style="color:#F1761B">';
			$warning=1;
			$ret="Version?";
			// Nur nötig falls mit OK Anzeige
		//elseif(file_crc($prog)==$fcrc AND $fcrc!=0):
			//$color='<font style="color:#11E90C">';
		else:
			$color='<font style="color:#000000">';
		endif;
		$detail=(filesize($prog) / 1000)." Kb - ".date("d.m.Y", filemtime($prog));
		return($color.$ret.'</font>');
	else:
		$detail='';
		$error=1;
		return('<font style="color:#FF0000">'.RESULT_01.'</font>');
	endif;
	return;
}
/**************************************************************/
function zeig01($prog,$was,$result){
	// Stand: 29.04.2009
	global $html_zeil, $detail;
	// auskommentieren falls mit OK Anzeige:
	if(!ereg('OK',$result))
		$html_zeil.=
		'<tr class="dataTableRow">'.
			'<td class="dataTableContent"><table width="100%"><tr><td class="dataTableContent_products" align="left">'.$prog. '</td><td class="dataTableContent_products" align="right">'. $detail  .'</td></tr></table></td>'.
			'<td class="dataTableContent" align="center">'.$was.'</td>'.
			'<td class="dataTableContent" align="center"><strong>'.$result.'</strong></td>'.
		'</tr>';
	return;
}
/**************************************************************/
function menue01($a=0,$b=0,$c=0,$weiter){
	// Stand: 29.04.2009
	global $html_zeil;
	// a=Wiederholen
	// b=Weiter - nur falls mit OK Anzeige
	// c=Trotzdem Weiter
	$html_zeil.=
	'<tr class="dataTableRow" style="padding-top:10px;">'.
		'<td colspan="3" class="main" align="center">';
		if($a)
			$html_zeil.=
			xtc_draw_form('nochmal','module_paypal_install.php', 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&ppauto=1').
			'<input type="submit" class="button" onClick="this.blur();" value="'.MENU_01.'">&nbsp;&nbsp;&nbsp;&nbsp;'.
			'</form>';
		if($b OR $c):
			$html_zeil.=
			xtc_draw_form('modules','module_paypal_install.php', 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&ppauto='.$weiter).
			'&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" onClick="this.blur();" value="'.(($c==1)?MENU_02:'').MENU_03.'">'.
			'</form>';
		endif;
		$html_zeil.=
		'</td>'.
	'</tr>';
	return;
}
/**************************************************************/
function file_crc($file){
	// Stand: 29.04.2009
	if(function_exists('file_get_contents')):
		$file_string = file_get_contents($file);
	else:
		$handle = fopen($file, "r");
		$file_string = fread($handle, filesize($file));
		fclose($handle);
	endif;
	$crc = crc32($file_string);
	return sprintf("%u", $crc);
}
/**************************************************************/
/************* Funktionen für den Schritt 2 *******************/
/**************************************************************/
function inst02(){
	// Stand: 29.04.2009
	global $html_zeil,$html_prog,$meldung,$error,$detail;
	// Admin Programme
	$verz=DIR_FS_ADMIN;
	$html_head=sprintf($meldung,'left',PATH_01.'<br />'.$verz);
	$html_zeil='';
	vergleich02(progdat02(1),$verz,'');
	// Admin Programme /includes
	$verz=DIR_FS_ADMIN.DIR_WS_INCLUDES;
	vergleich02(progdat02(2),$verz,DIR_WS_INCLUDES);
	if($html_zeil!='')$html_output=$html_head.$html_zeil;
	// Shop Programme - root
	$verz=DIR_SHOP_CATALOG;
	$html_head=sprintf($meldung,'left',PATH_02.'<br />'.$verz);
	$html_zeil='';
	vergleich02(progdat02(3),$verz,'');
	// Shop Programme /includes
	$verz=DIR_SHOP_INCLUDES;
	vergleich02(progdat02(4),$verz,str_replace(DIR_SHOP_ROOT,'',$verz));
	// Shop Programme /includes/classes
	$verz=DIR_SHOP_CLASSES;
	vergleich02(progdat02(5),$verz,str_replace(DIR_SHOP_ROOT,'',$verz));
	// Shop Programme /lang
	$languages_query = xtc_db_query("select directory from " . TABLE_LANGUAGES . " order by sort_order");
	while($languages = xtc_db_fetch_array($languages_query)) {
		$verz=DIR_SHOP_LANGUAGES.$languages['directory'].'/';
		vergleich02(progdat02(6,$languages['directory']),$verz,str_replace(DIR_SHOP_CATALOG,'',$verz));
	}
	// Shop Programme /Template Teil1
	$languages_query = xtc_db_query("select directory from " . TABLE_LANGUAGES . " order by sort_order");
	while($languages = xtc_db_fetch_array($languages_query)) {
		$verz=DIR_SHOP_CATALOG.'templates/'.CURRENT_TEMPLATE.'/mail/';
		vergleich02(progdat02(7,$languages['directory']),$verz,str_replace(DIR_SHOP_CATALOG,'',$verz));
	}
	// Shop Programme /Template Teil2
	$verz=DIR_SHOP_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/';
	vergleich02(progdat02(8),$verz,str_replace(DIR_SHOP_CATALOG,'',$verz));
	if($html_zeil!='')$html_output.=$html_head;
	if($error==1):
		$html_zeil.=sprintf($meldung,'center',ERROR_04);
		menue02(1,0,1);
	// Nur nötig falls mit OK Anzeige:
	//else:
		//$html_zeil.=sprintf($meldung,'center','Fertig - alles OK.');
		//menue02(0,1,0);
	endif;
	if($html_zeil!='')$html_output.=$html_zeil;
	return($html_output);
}
/**************************************************************/
function vergleich02($programme,$verz,$verzz){
	// Stand: 29.04.2009
	global $html_zeil,$html_prog,$html_tzeil,$error,$terror,$detail;
	foreach($programme as $programm=>$quellen) {
		$html_prog='';
		zeig02($verzz.$programm,ACT_01,such02($verz.$programm));
		$html_tzeil='';
		$terror='';
		lese02($verz.$programm,$quellen);
		// auskommentieren falls mit OK Anzeige:
		if($html_tzeil!='' AND $terror!='')$html_zeil.=$html_prog.$html_tzeil.'<tr class="dataTableRow"><td class="dataTableContent" colspan="3" height="4px">&nbsp;</td></tr>';
		// Nur nötig falls mit OK Anzeige:
		//$html_zeil.=$html_prog.$html_tzeil.'<tr class="dataTableRow"><td class="dataTableContent" colspan="3" height="4px">&nbsp;</td></tr>';
	}
	return;
}
/**************************************************************/
function zeig02($prog,$was,$result){
	// Stand: 29.04.2009
	global $html_prog,$detail;
	$html_prog=
	'<tr class="dataTableRow">'.
		'<td class="dataTableContent_products"><table width="100%"><tr><td class="dataTableContent_products" align="left"><strong>'.$prog. '</strong></td><td class="dataTableContent_products" align="right">'. $detail  .'</td></tr></table></td>'.
		'<td class="dataTableContent_products" align="center">'.$was.'</td>'.
		'<td class="dataTableContent_products" align="center"><strong>'.$result.'</strong></td>'.
	'</tr>';
	return;
}
/**************************************************************/
function such02($prog){
	// Stand: 29.04.2009
	global $error,$detail;
	if(file_exists($prog)):
		$detail=(filesize($prog) / 1000)." Kb - ".date("d.m.Y", filemtime($prog));
		return('<font style="color:#11E90C">OK</font>');
	else:
		$detail='';
		$error=1;
		return('<font style="color:#FF0000">'.RESULT_01.'</font>');
	endif;
	return;
}
/**************************************************************/
function lese02($prog,$quellen){
	// Stand: 29.04.2009
	global $html_tzeil,$error,$terror,$detail;
	if(file_exists($prog)):
		if(function_exists('file_get_contents')):
			$file_string = file_get_contents($prog);
		else:
			$handle = fopen($prog, "r");
			$file_string = fread($handle, filesize($prog));
			fclose($handle);
		endif;
	endif;
	foreach($quellen as $nr=>$quelle) {
		if($nr==0 AND $quelle=='1'):
			$folge=True;
			continue;
		endif;
		if(!$folge)$poss=0;
		$posi=strpos($file_string,$quelle,$poss);
		if($posi === false):
			$error=1;
			$terror=RESULT_01;
			if($poss):
				$posw=strpos($file_string,$quelle);
				if(!($posw === false) AND $posw < $poss):
					$terror=RESULT_02;
					$file_string=substr_replace($file_string,'',$posw,strlen($quelle));
					$poss=$poss-strlen($quelle);
				endif;
			endif;
			$result='<font style="color:#FF0000">'.$terror.'</font>';
		else:
			$file_string=substr_replace($file_string,'',$posi,strlen($quelle));
			$result='<font style="color:#11E90C">OK</font>';
			$poss=$posi+1-strlen($quelle);
		endif;
		zeig02b((($folge)?$nr:0),$quelle,ACT_02,$result);
	}
	return;
}
/**************************************************************/
function zeig02b($nr,$quelle,$was,$result){
	// Stand: 29.04.2009
	global $html_tzeil;
	$html_tzeil.=
	'<tr class="dataTableRow">'.
		'<td class="dataTableContent_products" align="left">';$html_tzeil.=(($nr)?$nr.'.- ':'');$html_tzeil.=highlight_string($quelle,True);$html_tzeil.= '</td>'.
		'<td class="dataTableContent_products" align="center">'.$was.'</td>'.
		'<td class="dataTableContent_products" align="center"><strong>'.$result.'</strong></td>'.
	'</tr>';
	return;
}
/**************************************************************/
function menue02($a=0,$b=0,$c=0){
	// Stand: 29.04.2009
	global $html_zeil;
	// a=Wiederholen
	// b=Beenden - nur nötig falls mit OK Anzeige
	// c=Trotzdem Beenden
	$html_zeil.=
	'<tr class="dataTableRow" style="padding-top:10px;">'.
		'<td colspan="3" class="main" align="center">';
		if($a)
			$html_zeil.=
			xtc_draw_form('nochmal','module_paypal_install.php', 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&ppauto=2').
			'<input type="submit" class="button" onClick="this.blur();" value="'.MENU_01.'">&nbsp;&nbsp;&nbsp;&nbsp;'.
			'</form>';
		if($b OR $c)
			$html_zeil.=
			xtc_draw_form('weiter',FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module']).
			'<input type="submit" class="button" onClick="this.blur();" value="'.(($c==1)?MENU_02:'').MENU_04.'">'.
			'</form>';
		$html_zeil.=
		'</td>'.
	'</tr>';
	return;
}
/**************************************************************/
/************* Daten Funktion für den Schritt 2 ***************/
/**************************************************************/
function progdat02($wasn,$lang=''){
	// Stand: 29.04.2009
	switch($wasn){
		case 1: // im /admin
			return array(
			'configuration.php'=>array('conf.png','xt:Commerce','>PayPal</a>'),
			'modules.php'=>array('\'removepaypal\''),
			'orders.php'=>array('$_POST[\'paypaldelete\']','require(\'../includes/classes/paypal_checkout.php\');','if(defined(\'TABLE_PAYPAL\'))')
			);
			break;
		case 2: // im /admin/includes
			return array(
			'application_top.php'=>array('define(\'FILENAME_PAYPAL\'','define(\'FILENAME_PAYPAL_CHECKOUT\'','define(\'TABLE_PAYPAL\'','define(\'TABLE_PAYPAL_STATUS_HISTORY\''),
			'column_left.php'=>array('paypal.php')
			);
			break;
		case 3: // im Shop (root)
			return array(
			'checkout_payment.php'=>array('isset($_SESSION[\'reshash\']','unset($_SESSION[\'reshash\'])','unset($_SESSION[\'nvpReqArray\'])'),
			'checkout_payment_address.php'=>array('1','is_array($_SESSION[\'nvpReqArray\'])','xtc_href_link($link_checkout_payment','xtc_href_link($link_checkout_payment','xtc_href_link($link_checkout_payment','xtc_href_link($link_checkout_payment'),
			'checkout_process.php'=>array('1','is_array($_SESSION[\'nvpReqArray\']','xtc_href_link(FILENAME_PAYPAL_CHECKOUT','paypalexpress','xtc_href_link(FILENAME_PAYPAL_CHECKOUT','paypalexpress','xtc_href_link(FILENAME_PAYPAL_CHECKOUT','($$_SESSION[\'payment\']->tmpOrders == true)','$attributes_query','$attributes_query','isset($_SESSION[\'reshash\'][\'ACK\']','$o_paypal->EXPRESS_CANCEL_URL','$_SESSION[\'reshash\'][\'REDIRECTREQUIRED\']','unset($_SESSION[\'payment\'])'),
			'checkout_shipping.php'=>array('paypalexpress'),
			'checkout_shipping_address.php'=>array('1','is_array($_SESSION[\'nvpReqArray\'])','xtc_href_link($link_checkout_shipping','xtc_href_link($link_checkout_shipping','xtc_href_link($link_checkout_shipping','xtc_href_link($link_checkout_shipping'),
			'send_order.php'=>array('paypal_express_new_customer','NEW_PASSWORD'),
			'shopping_cart.php'=>array('1','paypal_warten','unset($_SESSION[\'paypal_fehler\'])','paypal_warten','order.php','$_SESSION[\'reshash\'][\'FORMATED_ERRORS\']','paypal_warten','BUTTON_PAYPAL','paypal_warten')
			);
			break;
		case 4: // Shop Programme /includes
			return array(
			'application_top.php'=>array('1','get_magic_quotes_gpc()','get_magic_quotes_gpc()','paypal_checkout.php','DIR_WS_INCLUDES.FILENAME_CART_ACTIONS'),
			'application_top_callback.php'=>array('DIR_WS_FUNCTIONS.\'sessions.php\''),
			'cart_actions.php'=>array('if(!is_numeric($_POST[\'products_qty\']))','paypal_express_checkout'),
			'database_tables.php'=>array('TABLE_PAYPAL','paypal','TABLE_PAYPAL_STATUS_HISTORY','paypal_status_history'),
			'filenames.php'=>array('FILENAME_PAYPAL_CHECKOUT','paypal_checkout.php','FILENAME_PAYPAL','paypal.php')
			);
			break;
		case 5: // Shop Programme /includes/classes
			return array(
			'order.php'=>array('xtc_db_query("select text, value from', '$pp_order_tax','$pp_order_fee+=$order_fee', 'xtc_db_query("select title, value', 'pp_total','pp_fee','delivery_country_iso_code_2','billing_country_iso_code_2','$oder_total_values[\'class\'] == \'ot_total\'','$this->tax_discount','price_formated','final_price_formated','$this->tax_discount','$this->tax_discount'),
			'order_total.php'=>array('function pp_output()'),
			'payment.php'=>array('$_SESSION[\'paypal_express_checkout\']','function giropay_process()'),
			'shipping.php'=>array('$quotes[\'error\']','$quotes[\'error\'][$i]'),
			'shopping_cart.php'=>array('$this->tax = 0;','$this->total_discount = array ();','$this->total_discount[$product[\'products_tax_class_id\']]','$this->total_discount[$product[\'products_tax_class_id\']]','$this->total_discount as $value','$gval=0;','gval+=$this->tax[$key][\'value\'];','return $gval;')
			);
			break;
		case 6: // Shop Programme /lang
			return array(
			$lang.'.php'=>array('NAVBAR_TITLE_PAYPAL_CHECKOUT','PAYPAL_ERROR','PAYPAL_NOT_AVIABLE','PAYPAL_FEHLER','PAYPAL_WARTEN','PAYPAL_NEUBUTTON','ERROR_ADDRESS_NOT_ACCEPTED','PAYPAL_GS','PAYPAL_TAX'),
			'lang_'.$lang.'.conf'=>array('text_or','[checkout_paypal]','text_accept_adr'),
			'admin/configuration.php'=>array('PAYPAL_MODE_TITLE','PAYPAL_API_IMAGE_TITLE','PAYPAL_ERROR_DEBUG_TITLE'),
			'admin/'.$lang.'.php'=>array('BOX_PAYPAL'),
			'admin/modules.php'=>array('TEXT_INFO_DELETE_PAYPAL'),
			'admin/orders.php'=>array('TEXT_INFO_PAYPAL_DELETE')
			);
			break;
		case 7: // Shop Programme /templates/xxx/mail
			return array(
			$lang.'/order_mail.html'=>array('NEW_PASSWORD'),
			$lang.'/order_mail.txt'=>array('NEW_PASSWORD')
			);
			break;
		case 8: // Shop Programme /templates/xxx/module
			return array(
			'shopping_cart.html'=>array('$error','BUTTON_PAYPAL')
			);
			break;
	}
	return;
}
/**************************************************************/
/************* Daten Funktion für den Schritt 1 ***************/
/**************************************************************/
function progdat01($wasn,$lang=''){
	// Stand: 09.01.2011
	switch($wasn){
		case 1: // im /admin
			return array('paypal.php'=>3254103159);
			break;
		case 2: // im /admin/includes/classes
			return array('class.paypal.php'=>4231728510);
			break;
		case 3: // im /admin/includes/modules
			return array(
			'paypal_capturetransaction.php'=>3244516489,
			'paypal_listtransactions.php'=>1792087862,
			'paypal_refundtransaction.php'=>1972559684,
			'paypal_searchtransaction.php'=>250391615,
			'paypal_transactiondetail.php'=>2141937079
			);
			break;
		case 4: // im shop root
			return array('paypal_checkout.php'=>3457460132);
			break;
		case 5: // im shop root/callback/paypal/
			return array('ipn.php'=>1725718444);
			break;
		case 6: // im shop root/TEMPLATE/module/
			return array('checkout_paypal.html'=>0);
			break;
		case 7: // im shop root/includes/classes/
			return array('paypal_checkout.php'=>484258104);
			break;
		case 8: // im shop root/includes/modules/payment/
			return array(
			'paypal.php'=>4120509783,
			'paypalexpress.php'=>474187669
			);
			break;
		case 9: // Shop Programme /lang
			return array(
			'admin/paypal.php'=>(($lang=='german')?2107463942:(($lang=='english')?3053301568:0)),
			'modules/payment/paypal.php'=>(($lang=='german')?1416185336:(($lang=='english')?3196299556:0)),
			'modules/payment/paypalexpress.php'=>(($lang=='german')?2552195224:(($lang=='english')?2126143357:0))
			);
			break;
	}
	return;
}
/**************************************************************/
/******** Texte - hier, da die Installation u.U. ja fehlt *****/
/**************************************************************/
function def_texte($wasn){
	// Stand: 29.04.2009
	if($_SESSION['language']=='german'):
		if($wasn==1):
			define('HEADING_TITLE', "Prüfen ob PayPal-Express-Modul-Programme vorhanden sind...<br /><font style='line-height:1.8em; font-size:0.6em; color:#FF0000;'>Achtung:<br />Es werden die Dateien für die PayPal Module aus dem Module-Verzeichnis '1-Ergaenzungen-new' gesucht.<br />Bei einem 'Fehlt!' kopieren Sie bitte die Datei aus dem Modul in Ihren Shop.<br />Es wird ein CRC Check der Programme durchgeführt. Haben Sie selbst ein Programm verändert oder ist ein Programm nicht auf dem neuesten Stand erscheint 'Version?'.<br />Wenn Sie nichts an dem Programm verändert haben, kopieren Sie die Original-Datei aus dem Modul in diesen Shop und drücken 'Wiederholen'.<br />Bei 'Trotzdem Weiter' geht das Programm zum nächsten Schritt und -falls dort kein Fehler auftritt- zum Ende.<br />Ein erneuter Test erfolgt erst beim 'Installieren' eines der PayPal Module.</font>");
		elseif($wasn==2):
			define('HEADING_TITLE', "Teile des PayPal-Express-Moduls in den Shop Programmen suchen...<br /><font style='line-height:1.8em; font-size:0.6em; color:#FF0000;'>Achtung:<br />Es werden die Dateien bzw. Teile des geänderten Codes für die PayPal Module in den Dateien aus dem Module-Verzeichnis '2-Aenderungen-changed' gesucht.<br />Es werden nur markante, gern vergessene Teile des Moduls gesucht - NICHT der komplette Code!<br />Ein '<font style='color:#11E90C'>OK</font>' heisst nur, dass ein Teil des Codes gefunden wurde bzw. die Datei überhaupt vorhanden ist.<br />Das Programm kann NICHT die tatsächliche Funktion des Moduls prüfen und ob der Code an der richtigen Stelle steht.<br />Ein 'Fehlt!' zeigt das wichtiger Code nicht vorhanden ist und das Modul nicht ordnungsgemäß funktionieren wird.<br />Ein 'Falsch!' zeigt das die Reihenfolge der Code-Zeilen nicht stimmt und das Modul nicht ordnungsgemäß funktionieren wird.<br />Bauen Sie in diesem Fall den fehlenden/falschen Code richtig in das Programm ein und drücken Sie auf 'Wiederholen'.<br />Bei 'Trotzdem Fertig' wird das Programm beendet.<br />Ein erneuter Test erfolgt erst beim 'Installieren' eines der PayPal Module.</font>");
		endif;
		define('ACT_01', "Suchen");
		define('ACT_02', "Vergleichen");
		define('PATH_01', "Admin-Verzeichnis: (ohne die /images zu lesen)");
		define('PATH_02', "Shop-Verzeichnis: (ohne die /images/icons zu suchen)");
		define('RESULT_00', "Ergebnis");
		define('RESULT_01', "Fehlt!");
		define('RESULT_02', "Falsch!");
		define('MENU_01', "Wiederholen");
		define('MENU_02', "Trotzdem ");
		define('MENU_03', "Weiter");
		define('MENU_04', "Fertig");
		define('ERROR_01', "Fehler! Dateien nicht vollständig vorhanden.");
		define('ERROR_02', "Beide PayPal Module wurden deaktiviert.");
		define('ERROR_03', "Warnung! Nicht alle Dateien entsprechen dem Neuesten- oder Original-Stand.");
		define('ERROR_04', "Fehler! Nicht alle markanten Teile des Codes in den Dateien gefunden.");
	else:
		if($wasn==1):
			define('HEADING_TITLE', "Examine whether PayPal express module programs are present…<br /><font style='line-height:1.8em; font-size:0.6em; color:#FF0000;'>Note:<br />The files for the PayPal modules become from the module listing ' 1-Ergaenzungen-new' searched.<br />With a 'missing!' copy the file from the module into your shop.<br />A CRC check of the programs is accomplished. If you changed a program or if a program not on the newest conditions appear 'Version?'.<br />If you did not change anything at the program, copy the original file from the module into this shop and press 'Try again'.<br />With 'Nevertheless next' the program goes to the next step and -if there no error occurrence- to the end.<br />A renewed test takes place only with the 'Install' one of the PayPal modules.</font>");
		elseif($wasn==2):
			define('HEADING_TITLE', "Search for Parts of the PayPal express module in the shop programs...<br /><font style='line-height:1.8em; font-size:0.6em; color:#FF0000;'> Note:<br />The files and/or parts of the changed code for the PayPal modules in the files become from the module listing ' 2-Aenderungen-changed' searched.<br />Only salient one, gladly forgotten parts of the module are not looked for - NOT the complete code!<br />A '<font style='color:#11E90C'>OK</font>' it means only that a part of the code was found and/or the file is at all present.<br />The program canNOT examine the actual function of the module and whether the code in the correct place stands.<br />A 'missing!' important code does not show is present and the module will not duly function.<br />A 'Wrong!' that shows the order of the code lines is not correct and the module will not duly function.<br />Build in this case the missing/wrong code correctly into the program and press you on 'Try Again'.<br />With 'Nevertheless End' the program is terminated.<br />A renewed test takes place only with the 'Install' one of the PayPal modules.</font>");
		endif;
		define('ACT_01', "Search");
		define('ACT_02', "Compare");
		define('PATH_01', "Admin-Path: (without reading /images)");
		define('PATH_02', "Shop-Path: (without searching in /images/icons)");
		define('RESULT_00', "Result");
		define('RESULT_01', "missing!");
		define('RESULT_02', "wrong!");
		define('MENU_01', "Try again");
		define('MENU_02', "Nevertheless ");
		define('MENU_03', "next");
		define('MENU_04', "End");
		define('ERROR_01', "Error! Not all programs are present.");
		define('ERROR_02', "Both PayPal Module are de-activated.");
		define('ERROR_03', "Warning! Not all of the programs are in the newest conditions - or original.");
		define('ERROR_04', "Error! Not all salient parts of the code in the files found.");
	endif;
	return;
}
?>