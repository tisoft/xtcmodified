<?PHP
/*-----------------------------------------------------------------------
    $Id$
    xtC-SEO-Module by www.ShopStat.com (Hartmut K�nig)
    http://www.shopstat.com
    info@shopstat.com
    � 2004 ShopStat.com
    All Rights Reserved.
	
   Version 1.06 rev.04 (c) by web28  - www.rpa-com.de   
------------------------------------------------------------------------*/
//#################################

//-- Einstellungen f�r die Trennzeichen - 	Doppelpunkt oder Minuszeichen
//-- Bei Minuszeichen wird eine spezielle htaccess Datei ben�tigt
define('SEO_SEPARATOR',':');

//Sonderzeichen
define('SPECIAL_CHAR_FR', true);  	//Franz�sische Sonderzeichen 
define('SPECIAL_CHAR_ES', true);	//Spanische/Italienische/Portugisische Sonderzeichen (nur aktivieren wenn auch franz�siche Sonderzeichen aktiviert sind) 
define('SPECIAL_CHAR_MORE', true);	//Weitere Sonderzeichen 

//#################################


//BOF - web28 - 2010-08-18 -- Definition f�r die Trennzeichen
define('CAT_DIVIDER',SEO_SEPARATOR.SEO_SEPARATOR.SEO_SEPARATOR);	//Kategorie ':::'
define('ART_DIVIDER',SEO_SEPARATOR.SEO_SEPARATOR);					//Artikel '::'
define('CNT_DIVIDER',SEO_SEPARATOR.'_'.SEO_SEPARATOR);				//Content ':_:'
define('MAN_DIVIDER',SEO_SEPARATOR.'.'.SEO_SEPARATOR);				//Hersteller ':.:'
define('PAG_DIVIDER',SEO_SEPARATOR);								//Seitennummer ':'
//EOF - web28 - 2010-08-18 -- Definition f�r die Trennzeichen

if (file_exists(DIR_FS_INC . 'search_replace_'.strtolower($_SESSION['language_charset']) .'.php')) {
	include (DIR_FS_INC . 'search_replace_'.strtolower($_SESSION['language_charset']) .'.php');
} else {
	include (DIR_FS_INC . 'search_replace_default.php');
}

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
        $maname = "";
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

//BOF - web28 - 2010-08-18 -- Die Parameter aufspalten
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
		
//EOF - web28 - 2010-08-18 -- Die Parameter aufspalten

//BOF web28 - 2010-08-18 -- Falls eine Sprache �bergeben wurde, wird diese als 'Linksprache' definiert
        if(strlen($lang)>0)
            {			
            $seolng  = new language;
            $lang_id = $seolng->catalog_languages[$lang]['id'];			
            }
        else{
            $lang_id    = $languages_id;
            }
//EOF- web28 - 2010-08-18 -- Falls eine Sprache �bergeben wurde, wird diese als 'Linksprache' definiert   

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
                    $category['categories_name'] = shopstat_getRealPath($cPath,'/',$lang_id);

                    $link .= shopstat_hrefCatlink(	$category['categories_name'],
													$cPath,
													$pager													
													);
                    }
                else{
                    $category['categories_name'] = shopstat_getRealPath(xtc_get_product_path($prodid),'/',$lang_id);

                    $link .= shopstat_hrefLink($category['categories_name'],
                                               xtc_get_products_name($prodid,$lang_id),
                                               $prodid											   
                                               );
                    }
                }
            elseif(xtc_not_null($coid))
                {
				
                $content = shopstat_getContentName($coid, $lang_id);

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
			
			//BOF web28 - 2010-08-18 -- Parameter f�r die Sprachumschaltung
			if(strlen($lang)>0 && $lang_id != $languages_id) $link .= $separator.'language='. $lang;
			//EOF web28 - 2010-08-18 -- Parameter f�r die Sprachumschaltung
			
            }	

    return($link);
}

/******************************************************
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
function shopstat_hrefLink($cat_desc, $product_name, $product_id)
{
    $link = "";

    if(shopstat_hrefSmallmask($cat_desc))
        {
        $link .= shopstat_hrefSmallmask($cat_desc)."/";
        }

    $link .= shopstat_hrefMask($product_name).ART_DIVIDER.$product_id.".html";

    return($link);
}
/*
 * FUNCTION shopstat_hrefCatlink
 */
function shopstat_hrefCatlink($category_name, $category_id, $pager=false)
{
    $link = shopstat_hrefSmallmask($category_name).CAT_DIVIDER.$category_id;

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
function shopstat_hrefContlink($content_name, $content_id)
{
    $link = shopstat_hrefMask($content_name). CNT_DIVIDER.$content_id.".html";

    return($link);
}
/*
 * FUNCTION shopstat_hrefManulink
 */
function shopstat_hrefManulink($content_name, $content_id, $pager=false)
{
    $link = shopstat_hrefMask($content_name).MAN_DIVIDER.$content_id;

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

    //BOF - web28 - 2010-08-18 -HTML-Codierung entfernen (&uuml; etc.)
    $newstring  = html_entity_decode($newstring, ENT_NOQUOTES , strtoupper($_SESSION['language_charset']));
    //EOF - web28 - 2010-08-18 -HTML-Codierung entfernen (&uuml; etc.)

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
	
	//--Anf�hrungszeichen weg.
	$newstring  = preg_replace("/'|\"|�|`/","",$newstring);       

    //-- Die nun noch (komisch aussehenden) doppelten Bindestriche entfernen
    $newstring  = preg_replace("/(-){2,}/","-",$newstring);
	
	//web28 - 2010-08-18 - M�gliches rechtstehendes Minuszeichen entfernen - wichtig f�r Minus Trennzeichen
	$newstring = rtrim($newstring,"-");
//if($_REQUEST['test']){print $newstring."<hr>";}	

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
	
	//web28 - 2010-08-17 - Eurozeichen ersetzen
	$newstring  = str_replace("&euro;","-EUR-",$newstring);

    //BOF - web28 - 2010-08-18 -HTML-Codierung entfernen (&uuml; etc.)
    $newstring  = html_entity_decode($newstring, ENT_NOQUOTES , strtoupper($_SESSION['language_charset']));
   //EOF - web28 - 2010-08-18 -HTML-Codierung entfernen (&uuml; etc.)
	
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

	//--Anf�hrungszeichen weg.
	$newstring  = preg_replace("/'|\"|�|`/","",$newstring);   

    //-- String URL-codieren
    $newstring  = urlencode($newstring);

    //-- Die nun noch (komisch aussehenden) doppelten Bindestriche entfernen
    $newstring  = preg_replace("/(-){2,}/","-",$newstring);
	
	//web28 - 2010-08-13 - M�gliches rechtstehendes Minuszeichen entfernen - wichtig f�r Minus Trennzeichen
	$newstring = rtrim($newstring,"-");
	
//if($_REQUEST['test']){print $newstring."<hr>";}
    return($newstring);
}

?>