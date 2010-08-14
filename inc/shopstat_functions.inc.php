<?PHP
/*-----------------------------------------------------------------------
    $Id$
    xtC-SEO-Module by www.ShopStat.com (Hartmut K�nig)
    http://www.shopstat.com
    info@shopstat.com
    � 2004 ShopStat.com
    All Rights Reserved.
	
   Version 1.06 rev.02 (c) by web28  - www.rpa-com.de
------------------------------------------------------------------------*/
//#################################

//-- Einstellungen f�r die Trennzeichen - 	Doppelpunkt oder Minuszeichen
define('SEO_SEPARATOR',':');  

//-- Soll die Sprachauswahl in der URL vorangestellt werden?
//--  Empfehlung: JA, die Suchmaschine findet ansonsten keinen
//--  einzigen Link ausser der Standardsprache !
//--  ACHTUNG spezielle .htaccess notwendig
define('LANG_DEPENDING', false); //default: true;  

//Sonderzeichen
define('SPECIAL_CHAR_FR', true);  	//Franz�sische Sonderzeichen
define('SPECIAL_CHAR_ES', true);	//Spanische/Italienische/Portugisische Sonderzeichen (nur aktivieren wenn auch franz�siche Sonderzeichen aktiviert sind)
define('SPECIAL_CHAR_MORE', true);	//Weitere Sonderzeichen

//#################################


//-- Definition f�r die Trennzeichen
define('CAT_DIVIDER',SEO_SEPARATOR.SEO_SEPARATOR.SEO_SEPARATOR);	//Kategorie ':::'
define('ART_DIVIDER',SEO_SEPARATOR.SEO_SEPARATOR);					//Artikel '::'
define('CNT_DIVIDER',SEO_SEPARATOR.'_'.SEO_SEPARATOR);				//Content ':_:'
define('MAN_DIVIDER',SEO_SEPARATOR.'.'.SEO_SEPARATOR);				//Hersteller '-.-'
define('PAG_DIVIDER',SEO_SEPARATOR);								//Seitennummer '-'


if(!function_exists('xtDBquery'))
    {
    require_once(DIR_FS_INC . 'shopstat_functions_xtc2.inc.php');
    }
if(!function_exists('language'))
    {
    include (DIR_WS_CLASSES.'language.php');
    }
	
function shopstat_getSEO(   $page               = '',
                            $parameters         = '',
                            $connection         = 'NONSSL',
                            $add_session_id     = true,
                            $search_engine_safe = true,
                            $mode               = 'user')
{
        global $languages_id;
		$link = "";		
		
        if($mode == 'admin')
            {
            require_once(DIR_FS_INC . 'xtc_parse_category_path.inc.php');
            require_once(DIR_FS_INC . 'xtc_get_product_path.inc.php');
            require_once(DIR_FS_INC . 'xtc_get_parent_categories.inc.php');
            require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
            }
        else{
            require_once(DIR_FS_INC . 'xtc_get_products_name.inc.php');
            require_once(DIR_FS_INC . 'xtc_get_manufacturers.inc.php');
            }

        //-- XTC
        (!isset($languages_id)) ? $languages_id = $_SESSION['languages_id'] : false;

        $go     = true;
        //-- Nur bei der index.php und product_info.php
        if( $page != "index.php" &&
            $page != "product_info.php" &&
            $page != "shop_content.php")
            {
            $go = false;
            }     
        //-- Unter diesen Bedingungen werden die URLs nicht umgewandelt
        //-- Sortieren
        elseif(preg_match("/sort=/",$parameters))
            {
            $go = false;
            }
        //-- Sortieren der Herstellerprodukte
        elseif(preg_match("/filter_id=/",$parameters))
            {
            $go = false;
            }
        //-- Andere Aktion
        elseif(preg_match("/action=/",$parameters))
            {
            $go = false;
            }

//-- [1.2] Die Parameter aufspalten
        $pararray = array();
        foreach(explode("&",$parameters) as $pair)
            {
            $values = explode("=",$pair);
            if(!empty($values[0]))
                {
                $pararray[$values[0]] = $values[1];
                }
            }
        $cPath      = (isset($pararray['cPath']))?$pararray['cPath']:false;
        $prodid     = (isset($pararray['products_id']))?$pararray['products_id']:false;
        $content    = (isset($pararray['content']))?$pararray['content']:false;
        $coid       = (isset($pararray['coID']))?$pararray['coID']:false;
        $maid       = (isset($pararray['manufacturers_id']))?$pararray['manufacturers_id']:false;
        $pager      = (isset($pararray['page']))?$pararray['page']:false;
        $lang       = (isset($pararray['language']))?$pararray['language']:false;
//------------------------------

//-- Falls eine Sprache �bergeben wurde, wird diese als 'Linksprache' definiert
        if(strlen($lang)>0)
            {
            $seolng  = new language;
            $lang_id = $seolng->catalog_languages[$lang]['id'];
			if(!LANG_DEPENDING) $go = false; //Wichtig f�r die Sprachumschaltung
            }
        else{
            $lang_id    = $languages_id;
            }
//------------------------------

        if ($go &&
            (   xtc_not_null($maid) ||
                xtc_not_null($cPath) ||
                xtc_not_null($prodid) ||
                xtc_not_null($coid)
                )
             )
            {
            if ($connection == 'SSL')
                {
                if (ENABLE_SSL == true)
                    {
                    $link = HTTPS_SERVER . DIR_WS_CATALOG;
                    }
                else{
                    $link = HTTP_SERVER . DIR_WS_CATALOG;
                    }
                }
            else{
                $link = HTTP_SERVER . DIR_WS_CATALOG;
                }

//-- Die Sprache voranstellen
            if(LANG_DEPENDING)
                {
                include(DIR_WS_CLASSES.'language.php');
                $seolng = new language;
                foreach($seolng->catalog_languages as $seolangs)
                    {
                    if($seolangs['id'] == $lang_id)
                        {
                        $link .= $seolangs['code'].'/';
                        break;
                        }
                    }
                }
//------------------------------

            if((xtc_not_null($cPath) || xtc_not_null($prodid)) )
                {
                $cPath_array        = xtc_parse_category_path($cPath);
                $cPath              = implode('_', $cPath_array);
                $current_category_id= $cPath_array[(sizeof($cPath_array)-1)];

                if(!$current_category_id && $prodid)
                    {
                    $current_category_id = xtc_get_product_path($prodid);
                    }

                // -------------------------------------------------
                if(!$prodid)
                    {
                    $category['categories_name'] = shopstat_getRealPath($cPath,'/',$lang_id);

                    $link .= shopstat_hrefCatlink(	$category['categories_name'],
													$cPath,
													$pager,
													CAT_DIVIDER
													);
                    }
                else{
                    $category['categories_name'] = shopstat_getRealPath(xtc_get_product_path($prodid),'/',$lang_id);

                    $link .= shopstat_hrefLink($category['categories_name'],
                                               xtc_get_products_name($prodid,$lang_id),
                                               $prodid,
											   ART_DIVIDER
                                               );
                    }
                }
            elseif(xtc_not_null($coid))
                {
//-- 05.03.2006
/*
                if(xtc_not_null($content))
                    {
                    //-- Trennen von Name und Erweiterung und ID einmontieren
                    if(preg_match("/\./",$content))
                        {
                        $content = substr($content,0,strrpos ($content, "."));
                        }
                    }
                else{
*/
                    $content = shopstat_getContentName($coid, $lang_id);

//                    }

                $link .= shopstat_hrefContlink($content, $coid, CNT_DIVIDER);

                }
            elseif(xtc_not_null($maid))
                {
                $manufacturers = xtc_get_manufacturers();
                foreach($manufacturers as $manufacturer)
                    {
                    if($manufacturer['id'] == $maid)
                        {
                        $maname = $manufacturer['text'];
                        break;
                        }
                    }

                $link .= shopstat_hrefManulink($maname, $maid, $pager, MAN_DIVIDER);
                }

            $separator  = '?';
            }

    return($link);
}
/*
 * FUNCTION shopstat_getRealPath
 * Get the 'breadcrumb'-path
 */
function shopstat_getRealPath($cPath, $delimiter = '/', $language = '')
{
    if(empty($cPath)) return;
	if(empty($language)) $language = $_SESSION['languages_id'];
	
    $path       = explode("_",$cPath);
    $categories = array();

    foreach($path as $key => $value)
    {		
		$categories[$key] = shopstat_getCategoriesName($value, $language);		
    }

    $realpath = implode($delimiter,$categories);

    return($realpath);
}
function shopstat_getContentName($coid, $language = '')
{
    if(empty($coid)) return;
    if(empty($language)) $language = $_SESSION['languages_id'];

  // BOF - Tomcraft - 2009-06-03 - fix shopstat security issue
  //	$content_query  = "SELECT content_title FROM ".TABLE_CONTENT_MANAGER." WHERE languages_id='".$language."' AND content_group = ".$coid;
    $content_query  = "SELECT content_title FROM ".TABLE_CONTENT_MANAGER." WHERE languages_id='".intval($language)."' AND content_group = ".intval($coid);
  // EOF - Tomcraft - 2009-06-03 - fix shopstat security issue
    $content_query  = xtDBquery($content_query);
    $content_data   = xtc_db_fetch_array($content_query, true);

    return($content_data['content_title']);

}
/*
 * FUNCTION shopstat_getCategoriesName
 * Get the Category-Name from a give CID
 */
function shopstat_getCategoriesName($categories_id, $language = '')
{
    if(empty($categories_id)) return;
    if(empty($language)) $language = $_SESSION['languages_id'];

// BOF - Tomcraft - 2009-06-03 - fix shopstat security issue
//    $categories_query   = "SELECT categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '" . $categories_id . "' and language_id = '" . $language . "'";
    $categories_query = "SELECT categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '" . intval($categories_id) . "' and language_id = '" . intval($language) . "'";
// EOF - Tomcraft - 2009-06-03 - fix shopstat security issue
    $categories_query   = xtDBquery($categories_query);
    $categories         = xtc_db_fetch_array($categories_query,true);

    return $categories['categories_name'];
}
/*
 * FUNCTION shopstat_hrefLink
 */
function shopstat_hrefLink($cat_desc, $product_name, $product_id, $divider)
{
    $link = "";

    if(shopstat_hrefSmallmask($cat_desc))
        {
        $link .= shopstat_hrefSmallmask($cat_desc)."/";
        }

    $link .= shopstat_hrefMask($product_name).$divider.$product_id.".html";

    return($link);
}
/*
 * FUNCTION shopstat_hrefCatlink
 */
function shopstat_hrefCatlink($category_name, $category_id, $pager=false, $divider)
{
    $link = shopstat_hrefSmallmask($category_name).$divider.$category_id;

    if($pager && $pager != 1)
        {
        $link .= PAG_DIVIDER.$pager.".html";
        }
    else{
        $link .= ".html";
        }

    return($link);
}
/*
 * FUNCTION shopstat_hrefContlink
 */
function shopstat_hrefContlink($content_name, $content_id, $divider)
{
    $link = shopstat_hrefMask($content_name).$divider.$content_id.".html";

    return($link);
}
/*
 * FUNCTION shopstat_hrefManulink
 */
function shopstat_hrefManulink($content_name, $content_id, $pager=false, $divider)
{
    $link = shopstat_hrefMask($content_name).$divider.$content_id;

    if($pager && $pager != 1)
        {
        $link .= PAG_DIVIDER.$pager.".html";
        }
    else{
        $link .= ".html";
        }

    return($link);
}
/*
 * FUNCTION shopstat_hrefSmallmask
 */
function shopstat_hrefSmallmask($string)
{
    shopstat_getRegExps($search, $replace);
	
	$newstring = $string;

    //--[1.2] HTML-Codierung entfernen (&uuml; etc.)
    $newstring  = html_entity_decode($newstring, ENT_NOQUOTES , strtoupper($_SESSION['language_charset']));

    //-- <br> neutralisieren  
    //BOF - DokuMan - 2010-08-13 - optimize shopstat_getRegExps
    //$newstring  = preg_replace("/<br>/i","-",$string);
    $newstring  = preg_replace("/<br(\s+)?\/?>/i","-",$newstring);
    //EOF - DokuMan - 2010-08-13 - optimize shopstat_getRegExps

    //-- HTML entfernen
    $newstring  = strip_tags($newstring);

    //-- Schr�gstriche entfernen
    $newstring  = preg_replace("/\s\/\s/","+",$newstring);

    //-- Definierte Zeichen entfernen
    $newstring  = preg_replace($search,$replace,$newstring);

    //-- Die nun noch (komisch aussehenden) doppelten Bindestriche entfernen
    $newstring  = preg_replace("/(-){2,}/","-",$newstring);
	
	//--M�gliches rechtstehendes Minuszeichen entfernen - wichtig f�r Minus Trennzeichen
	$newstring = rtrim($newstring,"-");

    return($newstring);
}
/*
 * FUNCTION shopstat_hrefMask
 */
function shopstat_hrefMask($string)
{
    shopstat_getRegExps($search, $replace);	

    //BOF - DokuMan - 2010-08-13 - optimize shopstat_getRegExps
    $newstring = $string;
	
	$newstring  = preg_replace("'�'","EUR",$newstring);

    //--[1.2] HTML-Codierung entfernen (&uuml; etc.)
    $newstring  = html_entity_decode($newstring, ENT_NOQUOTES , strtoupper($_SESSION['language_charset']));
    //EOF - DokuMan - 2010-08-13 - optimize shopstat_getRegExps
	
    //-- <br> neutralisieren
    //BOF - DokuMan - 2010-08-13 - optimize shopstat_getRegExps
    //$newstring  = preg_replace("/<br>/i","-",$string);
    $newstring  = preg_replace("/<br(\s+)?\/?>/i","-",$newstring);  
    //EOF - DokuMan - 2010-08-13 - optimize shopstat_getRegExps

    //-- HTML entfernen
    $newstring  = strip_tags($newstring);

    //-- Schr�gstriche entfernen
    $newstring  = preg_replace("/\//","-",$newstring);

    //-- Definierte Zeichen entfernen
    $newstring  = preg_replace($search,$replace,$newstring);
//if($_REQUEST['test']){print $newstring."<br />";}

    //-- String URL-codieren
    $newstring  = urlencode($newstring);

    //-- Die nun noch (komisch aussehenden) doppelten Bindestriche entfernen
    $newstring  = preg_replace("/(-){2,}/","-",$newstring);
	
	//--M�gliches rechtstehendes Minuszeichen entfernen - wichtig f�r Minus Trennzeichen
	$newstring = rtrim($newstring,"-");
	
//if($_REQUEST['test']){print $newstring."<hr>";}
    return($newstring);
}
function shopstat_getRegExps(&$search, &$replace)
{
    $search     = array(
						"'\s&\s'",                	//--Kaufm�nnisches Und mit Blanks muss raus
						"'[\r\n\s]+'",	          	// strip out white space
						"'&(quote|#34);'i",	      	//--Anf�hrungszeichen oben replace html entities
						"'&(amp|#38);'i",        	//--Ampersand-Zeichen, kaufm�nnisches Und
						"'&(lt|#60);'i",	     	//--�ffnende spitze Klammer
						"'&(gt|#62);'i",	     	//--schlie�ende spitze Klammer
						"'&(nbsp|#160);'i",	      	//--Erzwungenes Leerzeichen					
						//BOF - web28 - 2010-04-16 - UFT-8 kompatibel +  Eingetragene Marke, Trademark, Eurozeichen
						"'&(iexcl|#161);|�'i", 		//umgekehrtes Ausrufezeichen
						"'&(cent|#162);|�'i", 		//Cent-Zeichen
						"'&(pound|#163);|�'i", 		//Pfund-Zeichen
						"'&(curren|#164);|�'i",   	//W�hrungszeichen--currency 
						"'&(yen|#165);|�'i",   		//Yen  wird zu Yen
						"'&(brvbar|#166);|�'i",		//durchbrochener Strich
						"'&(sect|#167);|�'i",		//Paragraph-Zeichen
						"'&(copy|#169);|�'i",		//Copyright-Zeichen 					
						"'&(reg|#174);|�'i",		//Eingetragene Marke wird zu -R-
						"'&(deg|#176);|�'i",		//Grad-Zeichen -- degree wird zu -Grad-
						"'&(plusmn|#177);|�'i",		//Plusminus-Zeichen
						"'&(sup2|#178);|�'i",	    //Hoch-2-Zeichen 
						"'&(sup3|#179);|�'i", 		//Hoch-3-Zeichen 
						"'&(micro|#181);|�'i",		//Mikro-Zeichen
						"'&(trade|#8482);|�'i",   	//--Trademark wird zu -TM-
						"'&(euro|#8364);|�'i",   	//--Eurozeichen wird zu EUR
						"'&(laquo|#171);|�'i", 	 	//-- Left angle quotes Left Winkel Zitate
						"'&(raquo|#187);|�'i", 		//--Right angle quotes Winkelgetriebe Zitate
						//BOF - web28 - 2010-05-13 - Benannte Zeichen f�r Interpunktion
						"'&(ndash|#8211);|�'i", 	//-- Gedankenstrich Breite n 	
						"'&(mdash|#8212);|�'i", 	//-- Gedankenstrich Breite m 	
						"'&(lsquo|#8216);|�'i", 	//-- einfaches Anf�hrungszeichen links 	
						"'&(rsquo|#8217);|�'i", 	//-- einfaches Anf�hrungszeichen rechts 	
						"'&(sbquo|#8218);|�'i", 	//-- einfaches low-9-Zeichen 	
						"'&(ldquo|#8220);|�'i", 	//-- doppeltes Anf�hrungszeichen links 
						"'&(rdquo|#8221);|�'i", 	//-- doppeltes Anf�hrungszeichen rechts 
						"'&(bdquo|#8222);|�'i", 	//-- doppeltes low-9-Zeichen rechts 
						//EOF - web28 - 2010-05-13 - Benannte Zeichen f�r Interpunktion
						//EOF - web28 - 2010-04-16 - UFT-8 kompatibel +  Eingetragene Marke, Trademark, Eurozeichen
						"'&'", 	                  //--Kaufm�nnisches Und 
						"'%'", 	                  //--Prozent muss weg
						"/[\[\({]/",              //--�ffnende Klammern nach Bindestriche
						"/[\)\]\}]/",             //--schliessende Klammern weg
						"/�/",                    //--Umlaute etc.
						"/�/",                    //--Umlaute etc.
						"/�/",                    //--Umlaute etc.
						"/�/",                    //--Umlaute etc.
						"/�/",                    //--Umlaute etc.
						"/�/",                    //--Umlaute etc.
						"/�/",                    //--Umlaute etc.						
						"/'|\"|�|`/",             //--Anf�hrungszeichen weg.						
						"/[:,\.!?\*\+]/",         //--Doppelpunkte, Komma, Punkt etc. weg. 
                        );
						
	
	if (SPECIAL_CHAR_FR) {					
	$search2 = array(	//BOF  - web28 - 2010-05-12 - Franz�sisch
						"'&(Agrave|#192);|�'i",		// Capital A-grave Capital A-Grab
						"'&(agrave|#224);|�'i",		//Lowercase a-grave Kleinbuchstaben a-Grab
						"'&(Acirc|#194);|�'i",		//Capital A-circumflex Capital A-Zirkumflex
						"'&(acirc|#226);|�'i",		//Lowercase a-circumflex Kleinbuchstaben a-Zirkumflex
						"'&(AElig|#198);|�'i",		//Capital AE Ligature Capital AE Ligature
						"'&(aelig|#230);|�'i",		//Lowercase AE Ligature Kleinbuchstabe ae
						"'&(Ccedil|#199);|�'i",		//Capital C-cedilla Capital-C Cedille
						"'&(ccedil|#231);|�'i",		//Lowercase c-cedilla Kleinbuchstaben c-Cedille
						"'&(Egrave|#200);|�'i",		//Capital E-grave Capital E-Grab
						"'&(egrave|#232);|�'i",		//Lowercase e-grave Kleinbuchstaben e-Grab
						"'&(Eacute|#201);|�'i",		//Capital E-acute E-Capital akuten
						"'&(eacute|#233);|�'i",		//Lowercase e-acute Kleinbuchstaben e-acute
						"'&(Ecirc|#202);|�'i",		//Capital E-circumflex E-Capital circumflexa
						"'&(ecirc|#234);|�'i",		//Lowercase e-circumflex Kleinbuchstaben e-Zirkumflex
						"'&(Euml|#203);|�'i",		//Capital E-umlaut Capital E-Umlaut
						"'&(euml|#235);|�'i",		//Lowercase e-umlaut Kleinbuchstaben e-Umlaut
						"'&(Icirc|#206);|�'i",		//Capital I-circumflex Capital I-Zirkumflex
						"'&(icirc|#238);|�'i",		//Lowercase i-circumflex Kleinbuchstaben i-Zirkumflex
						"'&(Iuml|#207);|�'i",		//Capital I-umlaut Capital I-Umlaut
						"'&(iuml|#239);|�'i",		//Lowercase i-umlaut Kleinbuchstaben i-Umlaut
						"'&(Ocirc|#212);|�'i",		//Capital O-circumflex O-Capital circumflexa
						"'&(ocirc|#244);|�'i",		//Lowercase o-circumflex Kleinbuchstabe o-Zirkumflex
						"'&(OElig|#338);|�'i",		//Capital OE ligature Capital OE Ligatur
						"'&(oelig|#339);|�'i",		//Lowercase oe ligature Kleinbuchstaben oe Ligatur
						"'&(Ugrave|#217);|�'i",		//Capital U-grave Capital U-Grab
						"'&(ugrave|#249);|�'i",		//Lowercase u-grave Kleinbuchstaben u-Grab
						"'&(Ucirc|#219);|�'i",		//Capital U-circumflex Capital U-Zirkumflex
						"'&(ucirc|#251);|�'i",		//Lowercase U-circumflex Kleinbuchstaben U-Zirkumflex
						"'&(Yuml|#376);|�'i",		//Gro�es Y mit Diaeresis
						"'&(yuml|#255);|�'i"		//Kleines y mit Diaeresis
						//EOF - web28 - 2010-05-12 - Franz�sisch	
						);
						
	$search = array_merge($search,$search2);
	}
	
	if (SPECIAL_CHAR_ES) {
	$search3 = array(	//BOF - web28 - 2010-08-13 - Spanisch
						"'&(Aacute|#193);|�'i",		//Gro�es A mit Akut
						"'&(aacute|#225);|�'i",		//Kleines a mit Akut
						"'&(Iacute|#205);|�'i",		//Gro�es I mit Akut
						"'&(iacute|#227);|�'i",		//Kleines i mit Akut
						"'&(Ntilde|#209);|�'i",		//Gro�es N mit Tilde
						"'&(ntilde|#241);|�'i",		//Kleines n mit Tilde
						"'&(Oacute|#211);|�'i",		//Gro�es O mit Akut
						"'&(oacute|#243);|�'i",		//Kleines o mit Akut
						"'&(Uacute|#218);|�'i",		//Gro�es U mit Akut
						"'&(uacute|#250);|�'i",		//Kleines u mit Akut
						"'&(ordf|#170);|�'i",		//Weibliche Ordnungszahl
						"'&(ordm|#186);|�'i",		//m�nnliche Ordnungszahl
						"'&(iexcl|#161);|�'i",		//umgekehrtes Ausrufungszeichen
						"'&(iquest|#191);|�'i",		//umgekehrtes Fragezeichen
						//EOF - web28 - 2010-08-13 - Spanisch
						//EOF - web28 - 2010-05-12 - Portugiesisch	
						"'&(Atilde|#195);|�'i",		//Gro�es A mit Tilde
						"'&(atilde|#227);|�'i",		//Kleines a mit Tilde
						"'&(Otilde|#213);|�'i",		//Gro�es O mit Tilde
						"'&(otilde|#245);|�'i",		//Kleines o mit Tilde
						//BOF - web28 - 2010-05-12 - Portugiesisch
						//BOF - web28 - 2010-05-12 - Italienisch
						"'&(Igrave|#204);|�'i",		//Gro�es I mit Grave
						"'&(igrave|#236);|�'i"		//Kleines i mit Grave						
						//EOF - web28 - 2010-05-12 - Italienisch
						);
	
	$search = array_merge($search,$search3);
	}
	
    if (SPECIAL_CHAR_MORE) {	
	$search4 = array(	//BOF - web28 - 2010-05-12 - Weitere Sonderzeichen
						"'&(Ograve|#210);|�'i",		//Gro�es O mit Grave
						"'&(ograve|#242);|�'i",		//Kleines o mit Grave
						"'&(Ograve|#210);|�'i",		//Gro�es O mit Grave
						"'&(ograve|#242);|�'i",		//Kleines o mit Grave
						"'&(Oslash|#216);|�'i",		//Gro�es O mit Schr�gstrich
						"'&(oslash|#248);|�'i",		//Kleines o mit Schr�gstrich
						"'&(Aring|#197);|�'i",		//Gro�es A mit Ring (Krouzek)
						"'&(aring|#229);|�'i",		//Kleines a mit Ring (Krouzek)
						"'&(THORN|#222);|�'i",		//Gro�es Thorn (isl�ndischer Buchstabe)
						"'&(thorn|#254);|�'i",		//Kleines thorn (isl�ndischer Buchstabe)
						"'&(divide|#247);|�'i",		//Divisions-Zeichen ("Geteilt durch ...")
						"'&(times|#215);|�'i",		//Multiplikationszeichen; "Multipliziert mit ..."
						"'&(ETH|#272;)|�'i",		//Gro�es D mit Querstrich (isl�ndischer Buchstabe)
						"'&(eth|#273;)|�'i",		//Kleines d mit Querstrich (isl�ndischer Buchstabe)
						"'&(Yacute|#221;)|�'i",		//Gro�es Y mit Akut
						"'&(yacute|#253;)|�'i"		//Kleines y mit Akut
						//EOF - web28 - 2010-05-12 - Weitere Sonderzeichen
						);
						
	$search = array_merge($search,$search4);
	}
	
//*****************************************************************
    
	$replace    = array(
						"-",		//--Kaufm�nnisches Und mit Blanks
						"-",		// strip out white space
						"\"",		//--Anf�hrungszeichen oben 
						"-",		//--Ampersand-Zeichen, kaufm�nnisches Und
						"<",		//--�ffnende spitze Klammer
						">",		//--schlie�ende spitze Klammer
						"",			//--Erzwungenes Leerzeichen
						//BOF - web28 - 2010-04-16 - UFT-8 kompatibel +  Eingetragene Marke, Trademark, Eurozeichen
						"", 		//chr(161), //umgekehrtes Ausrufezeichen
						"ct", 		//chr(162), //Cent-Zeichen
						"GBP", 		//chr(163), //Pfund-Zeichen
						"", 		//chr(164), //W�hrungszeichen--currency 
						"Yen", 		//chr(165), //Yen-Zeichen
						"",			//chr(166),durchbrochener Strich
						"",			//chr(167),Paragraph-Zeichen
						"",			//chr(169),Copyright-Zeichen											
						"", 		//chr(174), //Eingetragene Marke
						"~GRAD~", 	//chr(176), //Grad-Zeichen
						"~",		//chr(177) Plusminus-Zeichen
						"", 		//chr(178) Hoch-2-Zeichen 
						"", 		//chr(179) Hoch-3-Zeichen
						"", 		//chr(181) Mikro-Zeichen
						"~TM~",		//--Trademark wird zu -TM-
						"EUR",		//--Eurozeichen wird zu EUR
						"<<",		//chr(171) -- Left angle quotes Left Winkel Zitate
						">>",		//chr(187) -- Right angle quotes Right Winkel Zitate
						//BOF - web28 - 2010-05-13 - Benannte Zeichen f�r Interpunktion
						"-", 		//-- Gedankenstrich Breite n 	
						"-", 		//-- Gedankenstrich Breite m 	
						"", 		//-- einfaches Anf�hrungszeichen links 	
						"", 		//-- einfaches Anf�hrungszeichen rechts 	
						"", 		//-- einfaches low-9-Zeichen 	
						"", 		//-- doppeltes Anf�hrungszeichen links 
						"", 		//-- doppeltes Anf�hrungszeichen rechts 
						"", 		//-- doppeltes low-9-Zeichen rechts
						//EOF - web28 - 2010-05-13 - Benannte Zeichen f�r Interpunktion	
						//EOF - web28 - 2010-04-16 - UFT-8 kompatibel +  Eingetragene Marke, Trademark, Eurozeichen
						"-",		//--Kaufm�nnisches Und 
						"-",		//--Prozent 
			            "-",		//--�ffnende Klammern
			            "",			//--schliessende Klammern 
			            "ss",		//--Umlaute etc.
			            "ae",		//--Umlaute etc.
			            "ue",		//--Umlaute etc.
			            "oe",		//--Umlaute etc.
			            "Ae",		//--Umlaute etc.
			            "Ue",		//--Umlaute etc.
			            "Oe",		//--Umlaute etc.											
						"",			//--Anf�hrungszeichen 			
						"-"			//--Doppelpunkte, Komma, Punkt etc. 
                        );
						
	if (SPECIAL_CHAR_FR) {					
	$replace2 = array(	//BOF - web28 - 2010-05-12 - Franz�sisch
						"A",		// Capital A-grave Capital A-Grab
						"a",		//Lowercase a-grave Kleinbuchstaben a-Grab
						"A",		//Capital A-circumflex Capital A-Zirkumflex
						"a",		//Lowercase a-circumflex Kleinbuchstaben a-Zirkumflex
						"AE",		//Capital AE Ligature Capital AE Ligature
						"ae",		//Lowercase AE Ligature Kleinbuchstabe ae
						"C",		//Capital C-cedilla Capital-C Cedille
						"c",		//Lowercase c-cedilla Kleinbuchstaben c-Cedille
						"E",		//Capital E-grave Capital E-Grab
						"e",		//Lowercase e-grave Kleinbuchstaben e-Grab
						"E",		//Capital E-acute E-Capital akuten
						"e",		//Lowercase e-acute Kleinbuchstaben e-acute
						"E",		//Capital E-circumflex E-Capital circumflexa
						"e",		//Lowercase e-circumflex Kleinbuchstaben e-Zirkumflex
						"E",		//Capital E-umlaut Capital E-Umlaut
						"e",		//Lowercase e-umlaut Kleinbuchstaben e-Umlaut
						"I",		//Capital I-circumflex Capital I-Zirkumflex
						"i",		//Lowercase i-circumflex Kleinbuchstaben i-Zirkumflex
						"I",		//Capital I-umlaut Capital I-Umlaut
						"i",		//Lowercase i-umlaut Kleinbuchstaben i-Umlaut
						"O",		//Capital O-circumflex O-Capital circumflexa
						"o",		//Lowercase o-circumflex Kleinbuchstabe o-Zirkumflex
						"OE",		//Capital OE ligature Capital OE Ligatur
						"oe",		//Lowercase oe ligature Kleinbuchstaben oe Ligatur
						"U",		//Capital U-grave Capital U-Grab
						"u",		//Lowercase u-grave Kleinbuchstaben u-Grab
						"U",		//Capital U-circumflex Capital U-Zirkumflex						
						"u",		//Lowercase U-circumflex Kleinbuchstaben U-Zirkumflex
						"Y",		//Gro�es Y mit Diaeresis
						"y"			//Kleines y mit Diaeresis
						//EOF - web28 - 2010-05-12 - Franz�sisch
						);
						
	$replace = array_merge($replace,$replace2);
	}
	
	if (SPECIAL_CHAR_ES) {
	$replace3 = array(	//BOF - web28 - 2010-08-13 - Spanisch
						"A",		//Gro�es A mit Akut
						"a",		//Kleines a mit Akut
						"I",		//Gro�es I mit Akut
						"i",		//Kleines i mit Akut
						"N",		//Gro�es N mit Tilde
						"n",		//Kleines n mit Tilde
						"O",		//Gro�es O mit Akut
						"o",		//Kleines o mit Akut
						"U",		//Gro�es U mit Akut
						"u",		//Kleines u mit Akut
						"",			//Weibliche Ordnungszahl
						"",			//m�nnliche Ordnungszahl
						"",			//umgekehrtes Ausrufungszeichen
						"",			//umgekehrtes Fragezeichen
						//EOF - web28 - 2010-08-13 - Spanisch
						//EOF - web28 - 2010-08-13 - Portugiesisch	
						"A",		//Gro�es A mit Tilde
						"a",		//Kleines a mit Tilde
						"O",		//Gro�es O mit Tilde
						"o",		//Kleines o mit Tilde
						//BOF - web28 - 2010-08-13 - Portugiesisch
						//BOF - web28 - 2010-08-13 - Italienisch
						"I",		//Gro�es I mit Grave
						"i"			//Kleines i mit Grave						
						//EOF - web28 - 2010-08-13 - Italienisch
						);
	
	$replace = array_merge($replace,$replace3);
	}
	
    if (SPECIAL_CHAR_MORE) {	
	$replace4 = array(	//BOF -web28 - 2010-08-13 - Weitere Sonderzeichen
						"O",		//Gro�es O mit Grave
						"o",		//Kleines o mit Grave
						"O",		//Gro�es O mit Grave
						"o",		//Kleines o mit Grave
						"O",		//Gro�es O mit Schr�gstrich
						"o",		//Kleines o mit Schr�gstrich
						"A",		//Gro�es A mit Ring (Krouzek)
						"a",		//Kleines a mit Ring (Krouzek)
						"Th",		//Gro�es Thorn (isl�ndischer Buchstabe)
						"th",		//Kleines thorn (isl�ndischer Buchstabe)
						"-",		//Divisions-Zeichen ("Geteilt durch ...")
						"x",		//Multiplikationszeichen; "Multipliziert mit ..."
						"D",		//Gro�es D mit Querstrich (isl�ndischer Buchstabe)
						"d",		//Kleines d mit Querstrich (isl�ndischer Buchstabe)
						"Y",		//Gro�es Y mit Akut
						"y"			//Kleines y mit Akut
						//EOF - web28 - 2010-08-13 - Weitere Sonderzeichen	
						);
						
	$replace = array_merge($replace,$replace4);
	}

}
?>