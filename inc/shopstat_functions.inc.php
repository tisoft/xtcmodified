<?PHP
/*-----------------------------------------------------------------------
    Version: $Id: shopstat_functions.inc.php,v 1.7 2005/05/20 08:00:12 Administrator Exp $
    xtC-SEO-Module by www.ShopStat.com (Hartmut König)
    http://www.shopstat.com
    info@shopstat.com
    © 2004 ShopStat.com
    All Rights Reserved.
------------------------------------------------------------------------*/
if(!function_exists('xtDBquery'))
    {
    require_once(DIR_FS_INC . 'shopstat_functions_xtc2.inc.php');
    }

function shopstat_getSEO(   $page               = '',
                            $parameters         = '',
                            $connection         = 'NONSSL',
                            $add_session_id     = true,
                            $search_engine_safe = true,
                            $mode               = 'user')
{
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

        global $languages_id;

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

        preg_match("/(?:cPath)\=([^\&]*)/",$parameters,$caterg);
        preg_match("/[&|?]{0,1}(?:^products_id)\=([^\&]*)/",$parameters,$proderg);
        preg_match("/(?:coID)\=([^\&]*)/",$parameters,$coerg);
        preg_match("/(?:content)\=([^\&]*)/",$parameters,$conterg);

        //-- Manufacturer
        preg_match("/(?:manufacturers_id)\=([^\&]*)/",$parameters,$manuerg);
        //preg_match("/(?:filter_id)\=([^\&]*)/",$parameters,$filtererg);

        //-- Paging
        preg_match("/(?:page)\=([^\&]*)/",$parameters,$pagerg);

        //-- Language
        preg_match("/(?:language)\=([^\&]*)/",$parameters,$lang);

//BOF - DokuMan - 2010-02-25 - set missing variables
        $link = '';
        $cPath = '';
        $prodid = '';
        $content    = '';
        $coid       = '';
        $maid       = '';
        //$filterid  = '';
        $pager      = '';

        isset($caterg[1]) ? $cPath = $caterg[1] : "";
        isset($proderg[1]) ? $prodid = $proderg[1] : "";
        isset($conterg[1]) ? $content = $conterg[1] : "";
        isset($coerg[1]) ? $coid = $coerg[1] : "";
        isset($manuerg[1]) ? $maid = $manuerg[1] : "";
        //isset($filtererg[1]) ? $filterid = $filtererg[1] : "";
        isset($pagerg[1]) ? $pager = $pagerg[1] : "";
/*
        $cPath      = $caterg[1];
        $prodid     = $proderg[1];
        $content    = $conterg[1];
        $coid       = $coerg[1];
        $maid       = $manuerg[1];
        //$filterid   = $filtererg[1];
        $pager      = $pagerg[1];
*/
//EOF - DokuMan - 2010-02-25 - set missing variables

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
                    $category['categories_name'] = shopstat_getRealPath($cPath);

                    $link .= shopstat_hrefCatlink($category['categories_name'],$cPath,$pager);
                    }
                else{
                    $category['categories_name'] = shopstat_getRealPath(xtc_get_product_path($prodid));

                    $link .= shopstat_hrefLink($category['categories_name'],
                                               xtc_get_products_name($prodid,$languages_id),
                                               $prodid
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
                    $content = shopstat_getContentName($coid, $languages_id);

//                    }

                $link .= shopstat_hrefContlink($content, $coid);

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

                $link .= shopstat_hrefManulink($maname, $maid, $pager);
                }

            $separator  = '?';
            }

    //-- Concat the lang-var
    //-- Check parameters and given language, just concat
    //-- if the language is different
    if(sizeof($lang)>0)
        {
        $lng = new language;
        if( $lng->catalog_languages[$lang[1]]['id'] != $languages_id &&
            $link != "")
            {
            $link .= $separator.$lang[0];
            }
        }

    return($link);
}
/*
 * FUNCTION shopstat_getRealPath
 * Get the 'breadcrumb'-path
 */
function shopstat_getRealPath($cPath, $delimiter = '/')
{
    if(empty($cPath)) return;

	//BOF - web28 - 2010-05-12 - set missing variable $languages_id
	global $languages_id; 
	//EOF  - web28 - 2010-05-12 - set missing variable $languages_id
	
    $path       = explode("_",$cPath);
    $categories = array();

    foreach($path as $key => $value)
        {
		//BOF - web28 - 2010-05-12 - set missing variable $languages_id
        //$categories[$key] = shopstat_getCategoriesName($value, $language);		
        $categories[$key] = shopstat_getCategoriesName($value, $languages_id);
		//EOF - web28 - 2010-05-12 - set missing variable $languages_id
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
function shopstat_hrefLink($cat_desc, $product_name, $product_id)
{
    $link = "";

    if(shopstat_hrefSmallmask($cat_desc))
        {
        $link .= shopstat_hrefSmallmask($cat_desc)."/";
        }

    $link .= shopstat_hrefMask($product_name)."::".$product_id.".html";

    return($link);
}
/*
 * FUNCTION shopstat_hrefCatlink
 */
function shopstat_hrefCatlink($category_name, $category_id, $pager=false)
{
    $link = shopstat_hrefSmallmask($category_name).":::".$category_id;

    if($pager && $pager != 1)
        {
        $link .= ":".$pager.".html";
        }
    else{
        $link .= ".html";
        }

    return($link);
}
/*
 * FUNCTION shopstat_hrefContlink
 */
function shopstat_hrefContlink($content_name, $content_id)
{
    $link = shopstat_hrefMask($content_name).":_:".$content_id.".html";

    return($link);
}
/*
 * FUNCTION shopstat_hrefManulink
 */
function shopstat_hrefManulink($content_name, $content_id, $pager=false)
{
    $link = shopstat_hrefMask($content_name).":.:".$content_id;

    if($pager && $pager != 1)
        {
        $link .= ":".$pager.".html";
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

    //-- <br> neutralisieren
    $newstring  = preg_replace("/<br>/i","-",$string);

    //-- HTML entfernen
    $newstring  = strip_tags($newstring);

    //-- Schrägstriche entfernen
    $newstring  = preg_replace("/\s\/\s/","+",$newstring);

    //-- Definierte Zeichen entfernen
    $newstring  = preg_replace($search,$replace,$newstring);

    //-- Die nun noch (komisch aussehenden) doppelten Bindestriche entfernen
    $newstring  = preg_replace("/(-){2,}/","-",$newstring);

    return($newstring);
}
/*
 * FUNCTION shopstat_hrefMask
 */
function shopstat_hrefMask($string)
{
    shopstat_getRegExps($search, $replace);

    //-- <br> neutralisieren
    $newstring  = preg_replace("/<br>/i","-",$string);

    //-- HTML entfernen
    $newstring  = strip_tags($newstring);

    //-- Schrägstriche entfernen
    $newstring  = preg_replace("/\//","-",$newstring);

    //-- Definierte Zeichen entfernen
    $newstring  = preg_replace($search,$replace,$newstring);
//if($_REQUEST['test']){print $newstring."<br />";}

    //-- String URL-codieren
    $newstring  = urlencode($newstring);

    //-- Die nun noch (komisch aussehenden) doppelten Bindestriche entfernen
    $newstring  = preg_replace("/(-){2,}/","-",$newstring);
//if($_REQUEST['test']){print $newstring."<hr>";}
    return($newstring);
}
function shopstat_getRegExps(&$search, &$replace)
{
    $search     = array(
                        "'\s&\s'",          //--Kaufmännisches Und mit Blanks muss raus
						"'[\r\n\s]+'",	    // strip out white space
						"'&(quote|#34);'i",	//--Anführungszeichen oben replace html entities
						"'&(amp|#38);'i",   //--Ampersand-Zeichen, kaufmännisches Und
						"'&(lt|#60);'i",	//--öffnende spitze Klammer
						"'&(gt|#62);'i",	//--schließende spitze Klammer
						"'&(nbsp|#160);'i",	//--Erzwungenes Leerzeichen					
						//BOF - web28 - 2010-04-16 -  UFT-8 kompatibel +  Eingetragene Marke, Trademark, Eurozeichen
						"'&(iexcl|#161);|¡'i", 		//umgekehrtes Ausrufezeichen
						"'&(cent|#162);|¢'i", 		//Cent-Zeichen
						"'&(pound|#163);|£'i", 		//Pfund-Zeichen
						"'&(curren|#164);|¤'i",   	//Währungszeichen--currency wird zu '-'
						"'&(yen|#165);|¥'i",   		//Yen  wird zu Yen
						"'&(brvbar|#166);|¦'i",		//durchbrochener Strich
						"'&(sect|#167);|§'i",		//Paragraph-Zeichen
						"'&(copy|#169);|©'i",		//Copyright-Zeichen 					
						"'&(reg|#174);|®'i",		//Eingetragene Marke wird zu -R-
						"'&(deg|#176);|°'i",		//Grad-Zeichen -- degree wird zu -Grad-
						"'&(plusmn|#177);|±'i",		//Plusminus-Zeichen
						"'&(trade|#8482);|™'i",   	//--Trademark wird zu -TM-
						"'&(euro|#8364);|€'i",   	//--Eurozeichen wird zu EUR
						"'&(laquo|#171);|«'i", 	 	//-- Left angle quotes Left Winkel Zitate
						"'&(raquo|#187);|»'i", 		//--Right angle quotes Winkelgetriebe Zitate						
						//EOF - web28 - 2010-04-16 -  UFT-8 kompatibel +  Eingetragene Marke, Trademark, Eurozeichen
                        "'&'",              //--Kaufmännisches Und 
                        "'%'",              //--Prozent muss weg
                        "/[\[\({]/",        //--öffnende Klammern nach Bindestriche
                        "/[\)\]\}]/",       //--schliessende Klammern weg
                        "/ß/",              //--Umlaute etc.
                        "/ä/",              //--Umlaute etc.
                        "/ü/",              //--Umlaute etc.
                        "/ö/",              //--Umlaute etc.
                        "/Ä/",              //--Umlaute etc.
                        "/Ü/",              //--Umlaute etc.
                        "/Ö/",              //--Umlaute etc.
						//BOF  - web28 - 2010-05-12 - Französisch
						"'&(Agrave|#192);|À'i",		// Capital A-grave Capital A-Grab
						"'&(agrave|#224);|à'i",		//Lowercase a-grave Kleinbuchstaben a-Grab
						"'&(Acirc|#194);|Â'i",		//Capital A-circumflex Capital A-Zirkumflex
						"'&(acirc|#226);|â'i",		//Lowercase a-circumflex Kleinbuchstaben a-Zirkumflex
						"'&(AElig|#198);|Æ'i",		//Capital AE Ligature Capital AE Ligature
						"'&(aelig|#230);|æ'i",		//Lowercase AE Ligature Kleinbuchstabe ae
						"'&(Ccedil|#199);|Ç'i",		//Capital C-cedilla Capital-C Cedille
						"'&(ccedil|#231);|ç'i",		//Lowercase c-cedilla Kleinbuchstaben c-Cedille
						"'&(Egrave|#200);|È'i",		//Capital E-grave Capital E-Grab
						"'&(egrave|#232);|è'i",		//Lowercase e-grave Kleinbuchstaben e-Grab
						"'&(Eacute|#201);|É'i",		//Capital E-acute E-Capital akuten
						"'&(eacute|#233);|é'i",		//Lowercase e-acute Kleinbuchstaben e-acute
						"'&(Ecirc|#202);|Ê'i",		//Capital E-circumflex E-Capital circumflexa
						"'&(ecirc|#234);|ê'i",		//Lowercase e-circumflex Kleinbuchstaben e-Zirkumflex
						"'&(Euml|#203);|Ë'i",		//Capital E-umlaut Capital E-Umlaut
						"'&(euml|#235);|ë'i",		//Lowercase e-umlaut Kleinbuchstaben e-Umlaut
						"'&(Icirc|#206);|Î'i",		//Capital I-circumflex Capital I-Zirkumflex
						"'&(icirc|#238);|î'i",		//Lowercase i-circumflex Kleinbuchstaben i-Zirkumflex
						"'&(Iuml|#207);|Ï'i",		//Capital I-umlaut Capital I-Umlaut
						"'&(iuml|#239);|ï'i",		//Lowercase i-umlaut Kleinbuchstaben i-Umlaut
						"'&(Ocirc|#212);|Ô'i",		//Capital O-circumflex O-Capital circumflexa
						"'&(ocirc|#244);|ô'i",		//Lowercase o-circumflex Kleinbuchstabe o-Zirkumflex
						"'&(OElig|#140);|Œ'i",		//Capital OE ligature Capital OE Ligatur
						"'&(oelig|#156);|œ'i",		//Lowercase oe ligature Kleinbuchstaben oe Ligatur
						"'&(Ugrave|#217);|Ù'i",		//Capital U-grave Capital U-Grab
						"'&(ugrave|#249);|ù'i",		//Lowercase u-grave Kleinbuchstaben u-Grab
						"'&(Ucirc|#219);|Û'i",		//Capital U-circumflex Capital U-Zirkumflex
						"'&(ucirc|#251);|û'i",		//Lowercase U-circumflex Kleinbuchstaben U-Zirkumflex
						//EOF  - web28 - 2010-05-12 - Französisch
                        "/'|\"|´|`/",       //--Anführungszeichen weg.
                        "/[:,\.!?\*\+]/",   //--Doppelpunkte, Komma, Punkt etc. weg.
                        );
    $replace    = array(
                        "-",
						"-",
					    "\"",
						"-", 	//--Ampersand-Zeichen, kaufmännisches Und
						"<",
						">",
						"",
						//BOF - web28 - 2010-04-16 -  UFT-8 kompatibel +  Eingetragene Marke, Trademark, Eurozeichen
						"-", 	//chr(161),
						"-", 	//chr(162),
						"-", 	//chr(163),
						"-", 	//chr(164),
						"Yen", 	//chr(165),
						"-",	//durchbrochener Strich
						"-",	//Paragraph-Zeichen
						"-",	//Copyright-Zeichen											
						"-R-", 	//chr(174),
						"-GRAD-", 	//chr(176),
						"",		//Plusminus-Zeichen
						"-TM-",
						"EUR",
						"<<",
						">>",
						//EOF - web28 - 2010-04-16 -  UFT-8 kompatibel +  Eingetragene Marke, Trademark, Eurozeichen
                        "-",  	//--Kaufmännisches Und 
						"-",
                        "-",
                        "",
                        "ss",
                        "ae",
                        "ue",
                        "oe",
                        "Ae",
                        "Ue",
                        "Oe",
						//BOF  - web28 - 2010-05-12 - Französisch
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
						//EOF  - web28 - 2010-05-12 - Französisch
                        "",
                        ""
                        );

}
?>