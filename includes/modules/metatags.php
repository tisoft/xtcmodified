<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
 	 (c) 2003 nextcommerce (metatags.php, v1.7 2003/08/14); www.nextcommerce.org
   (c) 2006 xt:Commerce (metatags.php, v.1140 2005/08/10); www.xt-commerce.de

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------
   Modified by Gunnar Tillmann (August 2006)
   http://www.gunnart.de
   ---------------------------------------------------------------------------------------
  	AUTOMATISCHE METATAGS MULTILANGUAGE für xt:Commerce 3.04
   ---------------------------------------------------------------------------------------
      Version 0.96m / 26. August 2010 / DokuMan / xtcModified

    - 	Unterstützung für "canonical"-Tag
   ---------------------------------------------------------------------------------------
      Version 0.96 / 21. Juni 2009
    - 	Umwandlung von Umlauten in Keywords statt in ae und oe JETZT in &auml; &ouml;
    -	"Bindestrich-Wörter" (z.B. T-Shirt oder DVD-Player) werden in den Keywords nicht
      mehr getrennt
    - 	Metatags auch für ContentManager-Seiten (Achtung! Dazu Erweiterung erforderlich!)
    - 	Im ContentManager können auch automatische Metatags aus eingebundenen HTML- oder
      Text-Dateien erzeugt werden
    -	Standard-Meta-Angaben durch Content-Metas auch mehrsprachig möglich. Dazu eine
      Seite namens "STANDARD_META" anlegen
    - 	Bei automatisch erzeugen Keywords oder Descriptions werden Wörter nach Zeilen-
      umbrüchen nicht mehr "zusammengezogen"
    -	Eigene (mehrsprachige) Metas für die Shop-Startseite möglich - Dazu werden die
      Metas aus der "index"-Seite im ContentManager geholt
    -	Seiten-Nummer im Title bei Artikel-Listen (also Kategorien, Sonderangebote etc.)
    -	Eigener Title bei Suchergebnissen (Mit Seiten-Nummer, Suchbegriff, ggf. Hersteller
      und Kategorienname)
    - 	Bei allen Seiten, die nicht "Kategorie", "Startseite", "Content", "Produkt" o.ä.
      sind, wird der Title aus den Einträgen im $breadcrumb-Objekt zusammengesetzt
    - 	BugFix: BreadCrumb wird nicht mehr verkürzt
   ---------------------------------------------------------------------------------------
    Inspired by "Dynamic Meta" - Ein WordPress-PlugIn von Michael Schwarz
    http://www.php-vision.de/plugins-scripte/dynamicmeta-wpplugin.php
   ---------------------------------------------------------------------------------------
    Getestet für xt:C 3.04 SP2.1,
    Tauglich für Shops mit und ohne ShopStat-Erweiterung
    Eventuell sollte die "includes/header.php" ein bisschen angepasst werden, um valides
    XHTML zu gewährleisten
   ---------------------------------------------------------------------------------------
    Achtung: Vor Einbau bitte unbedingt dieses Modul installieren:
    --> http://www.xtc-load.de/2008/11/metatags-fur-content-seiten/
   ---------------------------------------------------------------------------------------*/


// ---------------------------------------------------------------------------------------
//	Konfiguration ...
// ---------------------------------------------------------------------------------------
	global $metaStopWords, $metaGoWords, $metaMinLength, $metaMaxLength, $metaDesLength;
		$metaStopWords 	=	('versandkosten,zzgl,mwst,lieferzeit,aber,alle,alles,als,auch,auf,aus,bei,beim,beinahe,bin,bis,ist,dabei,dadurch,daher,dank,darum,danach,das,daß,dass,dein,deine,dem,den,der,des,dessen,dadurch,deshalb,die,dies,diese,dieser,diesen,diesem,dieses,doch,dort,durch,eher,ein,eine,einem,einen,einer,eines,einige,einigen,einiges,eigene,eigenes,eigener,endlich,euer,eure,etwas,fast,findet,für,gab,gibt,geben,hatte,hatten,hattest,hattet,heute,hier,hinter,ich,ihr,ihre,ihn,ihm,im,immer,in,ist,ja,jede,jedem,jeden,jeder,jedes,jener,jenes,jetzt,kann,kannst,kein,können,könnt,machen,man,mein,meine,mehr,mit,muß,mußt,musst,müssen,müßt,nach,nachdem,neben,nein,nicht,nichts,noch,nun,nur,oder,statt,anstatt,seid,sein,seine,seiner,sich,sicher,sie,sind,soll,sollen,sollst,sollt,sonst,soweit,sowie,und,uns,unser,unsere,unserem,unseren,unter,vom,von,vor,wann,warum,was,war,weiter,weitere,wenn,wer,werde,widmen,widmet,viel,viele,vieles,weil,werden,werdet,weshalb,wie,wieder,wieso,wir,wird,wirst,wohl,woher,wohin,wurdezum,zur,über');
		$metaGoWords 	=	('tracht,dirndl,kleid,mode,modern,bluse,trachten,hose,leder,schmuck,t-shirt,t-shirts,schuh,schuhe'); // Hier rein, was nicht gefiltert werden soll
		$metaMinLength 	=	3;		// Mindestlänge eines Keywords
		$metaMaxLength 	=	18;		// Maximallänge eines Keywords
		$metaDesLength 	=	150;	// maximale Länge der "description" (in Buchstaben)
// ---------------------------------------------------------------------------------------
	$addPagination 			= 	true; 	// Seiten-Nummern anzeigen, ja/nein?
// ---------------------------------------------------------------------------------------
	$addCatShopTitle 		= 	true; 	// Shop-Titel bei Kategorien anhängen, ja/nein?
	$addProdShopTitle 		= 	true; 	// Shop-Titel bei Produkten anhängen, ja/nein?
	$addContentShopTitle 	= 	true; 	// Shop-Titel bei Contentseiten anhängen, ja/nein?
	$addSpecialsShopTitle = 	true; 	// Shop-Titel bei Angeboten anhängen, ja/nein?
	$addNewsShopTitle 		= 	true; 	// Shop-Titel bei Neuen Artikeln anhängen, ja/nein?
	$addSearchShopTitle 	= 	true; 	// Shop-Titel bei Suchergebnissen anhängen, ja/nein?
	$addOthersShopTitle 	= 	true; 	// Shop-Titel bei sonstigen Seiten anhängen, ja/nein?
// ---------------------------------------------------------------------------------------
	$noIndexUnimportant		= 	false; 	// "unwichtige" Seiten mit noindex versehen
// ---------------------------------------------------------------------------------------
//	Diese Seiten sind "wichtig"! (ist nur relevant, wenn $noIndexUnimportand == true)
// ---------------------------------------------------------------------------------------
	$pagesToShow = array(
		FILENAME_DEFAULT,
		FILENAME_PRODUCT_INFO,
		FILENAME_CONTENT,
		FILENAME_ADVANCED_SEARCH_RESULT,
		FILENAME_SPECIALS,
		FILENAME_PRODUCTS_NEW
	);
// ---------------------------------------------------------------------------------------
//	Ende Konfiguration
// ---------------------------------------------------------------------------------------


// 	Ab hier lieber nix mehr machen!


// ---------------------------------------------------------------------------------------
//	Title für "sonstige" Seiten
// ---------------------------------------------------------------------------------------
	//$breadcrumbTitle = 	array_pop($breadcrumb->_trail);
	$breadcrumbTitle = 	end($breadcrumb->_trail); // <-- BugFix
	$breadcrumbTitle = 	$breadcrumbTitle['title'];
// ---------------------------------------------------------------------------------------


// ---------------------------------------------------------------------------------------
//	Noindex bei "unwichtigen" Seiten
// ---------------------------------------------------------------------------------------
	$meta_robots = META_ROBOTS;
	if($noIndexUnimportant && !in_array(basename($_SERVER['SCRIPT_NAME']),$pagesToShow)) {
		$meta_robots = 'noindex, follow';
	}
// ---------------------------------------------------------------------------------------


// ---------------------------------------------------------------------------------------
//  MultiLanguage-Metas
// ---------------------------------------------------------------------------------------

	// Wenn wir auf der Startseite sind, Metas aus der index-Seite holen
	if(	basename($_SERVER['SCRIPT_NAME'])==FILENAME_DEFAULT &&
		empty($_GET['cat']) &&
		empty($_GET['cPath']) &&
		empty($_GET['manufacturers_id'])
	) {
		$ml_meta_where = "content_group = 5";

	// ... ansonsten Metas aus STANDARD_META holen
	} else {
		$ml_meta_where = "content_title = 'STANDARD_META'";
	}

	// Dadadadatenbank
	$ml_meta_query = xtc_db_query("
		select 	content_meta_title,
				content_meta_description,
				content_meta_keywords
		from 	".TABLE_CONTENT_MANAGER."
		where 	".$ml_meta_where."
		and 	languages_id = '".intval($_SESSION['languages_id'])."'
	");
	$ml_meta = xtc_db_fetch_array($ml_meta_query,true);

// ---------------------------------------------------------------------------------------
//	Mehrsprachige Standard-Metas definieren. Wenn leer, werden die üblichen genommen
// ---------------------------------------------------------------------------------------
	define('ML_META_KEYWORDS',($ml_meta['content_meta_keywords'])?$ml_meta['content_meta_keywords']:META_KEYWORDS);
	define('ML_META_DESCRIPTION',($ml_meta['content_meta_description'])?$ml_meta['content_meta_description']:META_DESCRIPTION);
	define('ML_TITLE',($ml_meta['content_meta_title'])?$ml_meta['content_meta_title']:TITLE);
// ---------------------------------------------------------------------------------------
	$metaGoWords = getGoWords(); // <-- nur noch einmal ausführen
// ---------------------------------------------------------------------------------------


// ---------------------------------------------------------------------------------------
// 	Seitennummerierung im Title (Kategorien, Sonderangebote, Neue Artikel etc.)
// ---------------------------------------------------------------------------------------
	$Page = '';
	if(isset($_GET['page']) && $_GET['page'] > 1 && $addPagination) {
		// PREVNEXT_TITLE_PAGE_NO ist "Seite %d" aus der deutschen
		// bzw. "page %d" aus der englischen Sprachdatei ...
		$Page = trim(str_replace('%d','',PREVNEXT_TITLE_PAGE_NO)).' '.intval($_GET['page']);
	}
// ---------------------------------------------------------------------------------------


// ---------------------------------------------------------------------------------------
//	Aufräumen: Umlaute und Sonderzeichen wandeln.
// ---------------------------------------------------------------------------------------
	function metaNoEntities($Text){
	    $translation_table = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
	    $translation_table = array_flip($translation_table);
	    $Return= strtr($Text,$translation_table);
	    return preg_replace( '/&#(\d+);/me',"chr('\\1')",$Return);
	}
	function metaHtmlEntities($Text) {
		$translation_table=get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
		$translation_table[chr(38)] = '&';
		return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&amp;",strtr($Text,$translation_table));
	}
// ---------------------------------------------------------------------------------------
//	Array basteln: Text aufbereiten -> Array erzeugen -> Array unique ...
// ---------------------------------------------------------------------------------------
	function prepareWordArray($Text) {
		//$Text = str_replace(array('&nbsp;','\t','\r','\n','\b'),' ',strip_tags($Text));
		$Text = str_replace(array('&nbsp;','\t','\r','\n','\b'),' ',preg_replace("/<[^>]*>/",' ',$Text)); // <-- Besser bei Zeilenumbrüchen
		// BOF - web28 - 2010-09-16 - FIX html entities
		//$Text = metaHtmlEntities(metaNoEntities(strtolower($Text)),ENT_QUOTES);
		$Text = htmlentities(metaNoEntities(strtolower($Text)), ENT_QUOTES, strtoupper($_SESSION['language_charset']));
		// EOF - web28 - 2010-09-16 - FIX html entities
		$Text = preg_replace("/\s\-|\-\s/",' ',$Text); // <-- Gegen Trenn- und Gedankenstriche
		$Text = preg_replace("/(&[^aoucizens][^;]*;)/",' ',$Text);
		$Text = preg_replace("/[^0-9a-z|\-|&|;]/",' ',$Text); // <-- Bindestriche drin lassen
		$Text = trim(preg_replace("/\s\s+/",' ',$Text));
		return $Text;
	}
	function makeWordArray($Text) {
		$Text = func_get_args();
		$Words = array();
		foreach($Text as $Word) {
			if((!empty($Word))&&(is_string($Word))) {
				$Words = array_merge($Words,explode(' ',$Word));
			}
		}
		return array_unique($Words);
	}
	function WordArray($Text) {
		return makeWordArray(prepareWordArray($Text));
	}
// ---------------------------------------------------------------------------------------
//	KeyWords aufräumen:
// 	Stop- und KeyWords-Liste in Array umwandeln, StopWords löschen,
//	GoWords- und Längen-Filter anwenden
// ---------------------------------------------------------------------------------------
	function cleanKeyWords($KeyWords) {
		global $metaStopWords;
		$KeyWords 	= 	WordArray($KeyWords);
		$StopWords 	=	WordArray($metaStopWords);
		$KeyWords 	= 	array_diff($KeyWords,$StopWords);
		$KeyWords 	= 	array_filter($KeyWords,"filterKeyWordArray");
		return $KeyWords;
	}
// ---------------------------------------------------------------------------------------
//	GoWords- und Längen-Filter:
//	Alles, was zu kurz ist, fliegt raus, sofern nicht in der GoWords-Liste
// ---------------------------------------------------------------------------------------
	function filterKeyWordArray($KeyWord) {
		global $metaMinLength, $metaMaxLength, $metaGoWords;
		$GoWords = WordArray($metaGoWords);
		if(!in_array($KeyWord,$GoWords)) {
			//$Length = strlen($KeyWord);
			$Length = strlen(preg_replace("/(&[^;]*;)/",'#',$KeyWord)); // <-- Mindest-Länge auch bei Umlauten berücksichtigen
			if($Length < $metaMinLength) { // Mindest-Länge
				return false;
			} elseif($Length > $metaMaxLength) { // Maximal-Länge
				return false;
			}
		}
		return true;
	}
// ---------------------------------------------------------------------------------------
//	GoWords: Werden grundsätzlich nicht gefiltert
//	Sofern angelegt, werden (zusätzlich zu den Einstellungen oben) die "normalen"
//	Meta-Angaben genommen (gefixed anno Danno-Wanno)
// ---------------------------------------------------------------------------------------
	function getGoWords(){
		global $metaGoWords, $categories_meta, $product;
		//$GoWords = $metaGoWords.' '.META_KEYWORDS;
		$GoWords = $metaGoWords.' '.ML_META_KEYWORDS.' '.ML_TITLE; // <-- MultiLanguage
		$GoWords .= ' '.$categories_meta['categories_meta_keywords'];
		// BOF - DokuMan - 2010-09-17 - Undefined property: product::$data
		//$GoWords .= ' '.$product->data['products_meta_keywords'];
		if (isset($product->data['products_meta_keywords'])) $GoWords .= ' '.$product->data['products_meta_keywords'];
		// EOF - DokuMan - 2010-09-17 - Undefined property: product::$data
		return $GoWords;
	}
// ---------------------------------------------------------------------------------------
//	Aufräumen: Leerzeichen und HTML-Code raus, kürzen, Umlaute und Sonderzeichen wandeln
// ---------------------------------------------------------------------------------------
	function metaClean($Text,$Length=false,$Abk=' ...') {
		//$Text = strip_tags($Text);
		$Text = preg_replace("/<[^>]*>/",' ',$Text); // <-- Besser bei Zeilenumbrüchen
		$Text = metaNoEntities($Text);
		$Text = str_replace(array('&nbsp;','\t','\r','\n','\b'),' ',$Text);
		$Text = trim(preg_replace("/\s\s+/",' ',$Text));
		if($Length > 0) {
			if(strlen($Text) > $Length) {
	        	$Length -= strlen($Abk);
	            $Text = preg_replace('/\s+?(\S+)?$/','',substr($Text,0,$Length+1));
	            $Text = substr($Text,0,$Length).$Abk;
			}
		}
		// BOF - web28 - 2010-09-16 - FIX html entities
		//return metaHtmlEntities($Text,ENT_QUOTES);
		return htmlentities($Text, ENT_QUOTES, strtoupper($_SESSION['language_charset']));
		// EOF - web28 - 2010-09-16 - FIX html entities
		//return metaHtmlEntities(utf8_decode($Text),ENT_QUOTES); //DokuMan - 2010-08-26 - for future use with UTF8
	}
// ---------------------------------------------------------------------------------------
//	metaTitle und metaKeyWords, Rückgabe bzw. Formatierung
// ---------------------------------------------------------------------------------------
	function metaTitle($Title=array()) {
		$Title = func_get_args();
		$Title = array_filter($Title,"metaClean");
		return implode(' - ',$Title);
	}
// ---------------------------------------------------------------------------------------
	function metaKeyWords($Text) {
		$KeyWords = cleanKeyWords($Text);
		return implode(', ',$KeyWords);
	}
// ---------------------------------------------------------------------------------------


switch(basename($_SERVER['SCRIPT_NAME'])) { // Start Switch


// ---------------------------------------------------------------------------------------
//	Daten holen: Produktdetails
// ---------------------------------------------------------------------------------------
	case FILENAME_PRODUCT_INFO :

		if($product->isProduct()) {
			// KeyWords ...
			if(!empty($product->data['products_meta_keywords'])) {
				$meta_keyw = $product->data['products_meta_keywords'];
			} else {
				$meta_keyw = metaKeyWords($product->data['products_name'].' '.$product->data['products_description']);
			}

			// Description ...
			if(!empty($product->data['products_meta_description'])) {
				$meta_descr = $product->data['products_meta_description'];
				$metaDesLength = false;
			} else {
				$meta_descr = $product->data['products_name'].': '.$product->data['products_description'];
			}

			// Title ...
			if(!empty($product->data['products_meta_title'])) {
				$meta_title = $product->data['products_meta_title'].(($addProdShopTitle)?' - '.ML_TITLE:'');
			} else {
				$meta_title = metaTitle($product->data['products_name'],@$product->data['manufacturers_name'],($addProdShopTitle)?ML_TITLE:'');
			}

      //-- Canonical-URL
      //-- http://www.linkvendor.com/blog/der-canonical-tag-%E2%80%93-was-kann-man-damit-machen.html
      $canonical_url = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$product->data['products_id'].'&amp;language='.$_SESSION['language_code']);
		}
		break;
// ---------------------------------------------------------------------------------------
//	Daten holen: Kategorie
// ---------------------------------------------------------------------------------------
	case FILENAME_DEFAULT :

		// Sind wir in einer Kategorie?
		if(!empty($current_category_id)) {
			$categories_meta_query = xtDBquery("
				select 	categories_meta_keywords,
						categories_meta_description,
						categories_meta_title,
						categories_name,
						categories_description
				from 	".TABLE_CATEGORIES_DESCRIPTION."
				where 	categories_id='".intval($current_category_id)."'
				and 	language_id='".intval($_SESSION['languages_id'])."'
			");
			$categories_meta = xtc_db_fetch_array($categories_meta_query,true);
		}

		$manu_id = $manu_name = false;

		// Nachsehen, ob ein Hersteller gewählt ist
		if(!empty($_GET['manu'])) {
			$manu_id = $_GET['manu'];
		}
		if(!empty($_GET['manufacturers_id'])) {
			$manu_id = $_GET['manufacturers_id'];
		}
		if(!empty($_GET['filter_id']) && !$manu_id) {
			$manu_id = $_GET['filter_id'];
		}

		// ggf. Herstellernamen herausfinden ...
		if($manu_id) {
			$manu_name_query = xtDBquery("
				select 	manufacturers_name
				from 	".TABLE_MANUFACTURERS."
				where 	manufacturers_id ='".intval($manu_id)."'
			");
			//BOF - DokuMan - 2010-09-20 - check if query result is an array to avoid php warning
			//$manu_name = implode('',xtc_db_fetch_array($manu_name_query,true));
			$manu_name = xtc_db_fetch_array($manu_name_query,true);
			is_array($manu_name) ? $manu_name = implode('',$manu_name) :  $manu_name = '';
			//EOF - DokuMan - 2010-09-20 - check if query result is an array to avoid php warning
			$metaGoWords .= ','.$manu_name; // <-- zu GoWords hinzufügen
		}
		
		//BOF - web28- 2010-11-06 - FIX metatags site content: index
		//Wenn wir auf der Startseite sind - Meta Description und Meta Kewords aus Seiten Content: index übernehmen
		if (!$manu_id && empty($current_category_id)) {			
			$categories_meta['categories_meta_description'] = ML_META_DESCRIPTION;
			$categories_meta['categories_meta_keywords'] = ML_META_KEYWORDS;
		}
		//EOF - web28- 2010-11-06 - FIX metatags site: content index
		
		// KeyWords ...
		if(!empty($categories_meta['categories_meta_keywords'])) {
			$meta_keyw = $categories_meta['categories_meta_keywords']; // <-- 1:1 übernehmen!
		} else{
			$meta_keyw = metaKeyWords($categories_meta['categories_name'].' '.$manu_name.' '.$categories_meta['categories_description']);
		}

		// Description ...
		if(!empty($categories_meta['categories_meta_description'])) {
			// ggf. Herstellername hinzufügen
			$meta_descr = $categories_meta['categories_meta_description'].(($manu_name)?' - '.$manu_name:'');
			$metaDesLength = false;
		} elseif($categories_meta) {
			// ggf. Herstellername und Kategorientext hinzufügen
			$meta_descr = $categories_meta['categories_name'].(($manu_name)?' - '.$manu_name:'').(($categories_meta['categories_description'])?' - '.$categories_meta['categories_description']:'');
		}

		// Title ...
		if(!empty($categories_meta['categories_meta_title'])) {
			// Meta-Titel, ggf. Herstellername, ggf. Seiten-Nummer, ggf. Shop-Titel
			$meta_title = $categories_meta['categories_meta_title'].(($manu_name)?' - '.$manu_name:'').(($Page)?' - '.$Page:'').(($addCatShopTitle)?' - '.ML_TITLE:'');
		} else{
			$meta_title = metaTitle($categories_meta['categories_name'],$manu_name,$Page,($addCatShopTitle)?ML_TITLE:'');
		}

		//-- Canonical-URL
		//-- http://www.linkvendor.com/blog/der-canonical-tag-%E2%80%93-was-kann-man-damit-machen.html
		if(isset ($_REQUEST['cPath'])){
		$canonical_url = xtc_href_link(FILENAME_DEFAULT, 'cPath='.$_REQUEST['cPath'].'&amp;language='.$_SESSION['language_code']);
		}
		break;
// ---------------------------------------------------------------------------------------
//	Daten holen: Inhalts-Seite (ContentManager)
// ---------------------------------------------------------------------------------------
	case FILENAME_CONTENT :

		$contents_meta_query = xtc_db_query("
			select 	content_meta_title,
					content_meta_description,
					content_meta_keywords,
					content_title,
					content_heading,
					content_text,
					content_file
			from 	".TABLE_CONTENT_MANAGER."
			where 	content_group = '".intval($_GET['coID'])."'
			and 	languages_id = '".intval($_SESSION['languages_id'])."'
		");
		$contents_meta = xtc_db_fetch_array($contents_meta_query,true);

		if(count($contents_meta) > 0) {

			// NEU! Eingebundene Dateien auslesen
			if($contents_meta['content_file']) {
				// Nur Text- oder HTML-Dateien!
				if(preg_match("/\.(txt|htm|html)$/i", $contents_meta['content_file'])) {
					$contents_meta['content_text'] .= ' '.implode(' ', @file(DIR_FS_CATALOG.'media/content/'.$contents_meta['content_file']));
				}
			}

			// KeyWords ...
			if(!empty($contents_meta['content_meta_keywords'])) {
				$meta_keyw = $contents_meta['content_meta_keywords'];
			} else {
				$meta_keyw = metaKeyWords($contents_meta['content_title'].' '.$contents_meta['content_heading'].' '.$contents_meta['content_text']);
			}

			// Title ...
			if(!empty($contents_meta['content_meta_title'])) {
				$meta_title = $contents_meta['content_meta_title'].(($addContentShopTitle)?' - '.ML_TITLE:'');
			} else {
				$meta_title = metaTitle($contents_meta['content_title'],$contents_meta['content_heading'],($addContentShopTitle)?ML_TITLE:'');
			}

			// Description ...
			if(!empty($contents_meta['content_meta_description'])) {
				$meta_descr = $contents_meta['content_meta_description'];
				$metaDesLength = false;
			} else {
				$meta_descr = ($contents_meta['content_heading'])?$contents_meta['content_heading'].': ':'';
				$meta_descr .= $contents_meta['content_text'];
			}
		}

    //-- Canonical-URL
    //-- http://www.linkvendor.com/blog/der-canonical-tag-%E2%80%93-was-kann-man-damit-machen.html
    if(isset($_GET['coID'])){
    $canonical_url = xtc_href_link(FILENAME_CONTENT, 'coID='.$_GET['coID'].'&amp;language='.$_SESSION['language_code']);
    }
		break;
// ---------------------------------------------------------------------------------------
//	Title für Suchergebnisse - Mit Suchbegriff, Kategorien-Namen, Seiten-Nummer etc.
// ---------------------------------------------------------------------------------------
	case FILENAME_ADVANCED_SEARCH_RESULT :

		// ggf. Herstellernamen herausfinden ...
		if(!empty($_GET['manufacturers_id'])) {
			$manu_name_query = xtDBquery("
				select 	manufacturers_name
				from 	".TABLE_MANUFACTURERS."
				where 	manufacturers_id ='".intval($_GET['manufacturers_id'])."'
			");
			//BOF - DokuMan - 2010-09-20 - check if query result is an array to avoid php warning
			//$manu_name = implode('',xtc_db_fetch_array($manu_name_query,true));
			$manu_name = xtc_db_fetch_array($manu_name_query,true);
			is_array($manu_name) ? $manu_name = implode('',$manu_name) :  $manu_name = '';
			//EOF - DokuMan - 2010-09-20 - check if query result is an array to avoid php warning
			$metaGoWords .= ','.$manu_name; // <-- zu GoWords hinzufügen
		}
		// ggf. Kategorien-Namen herausfinden ...
		if(!empty($_GET['categories_id'])) {
			$cat_name_query = xtDBquery("
				select 	categories_name
				from 	".TABLE_CATEGORIES_DESCRIPTION."
				where 	categories_id='".intval($_GET['categories_id'])."'
				and 	language_id='".intval($_SESSION['languages_id'])."'
			");
			//BOF - DokuMan - 2010-09-20 - check if query result is an array to avoid php warning
			//$cat_name = implode('',xtc_db_fetch_array($cat_name_query,true));
			$cat_name = xtc_db_fetch_array($cat_name_query,true);
			is_array($cat_name) ? $cat_name = implode('',$cat_name) :  $cat_name = '';
			//EOF - DokuMan - 2010-09-20 - check if query result is an array to avoid php warning
		}

		$meta_title = metaTitle($breadcrumbTitle,
                            '&quot;'.trim($_GET['keywords']).'&quot;',
                            $Page,(isset($cat_name) ? $cat_name : ''),
                            (isset($manu_name) ? $manu_name :  ''),
                            ($addSearchShopTitle) ? ML_TITLE : '');
		break;
// ---------------------------------------------------------------------------------------
//	Title für Angebote
// ---------------------------------------------------------------------------------------
	case FILENAME_SPECIALS :

		$meta_title = metaTitle($breadcrumbTitle,$Page,($addSpecialsShopTitle)?ML_TITLE:'');
		break;
// ---------------------------------------------------------------------------------------
//	Title für Neue Artikel
// ---------------------------------------------------------------------------------------
	case FILENAME_PRODUCTS_NEW :

		$meta_title = metaTitle($breadcrumbTitle,$Page,($addNewsShopTitle)?ML_TITLE:'');
		break;
// ---------------------------------------------------------------------------------------
//	Title für sonstige Seiten
// ---------------------------------------------------------------------------------------
	default:

		$meta_title = metaTitle($breadcrumbTitle,($addOthersShopTitle)?ML_TITLE:'');
		break;
// ---------------------------------------------------------------------------------------


} // Ende Switch


// ---------------------------------------------------------------------------------------
//	... und wenn nix drin, dann Standard-Werte nehmen
// ---------------------------------------------------------------------------------------
	// KeyWords ...
	if(empty($meta_keyw)) {
		$meta_keyw    = ML_META_KEYWORDS;
	}
	// Description ...
	if(empty($meta_descr)) {
		$meta_descr   = ML_META_DESCRIPTION;
		$metaDesLength = false;
	}
	// Title ...
	if(empty($meta_title)) {
		$meta_title   = ML_TITLE;
	}
// ---------------------------------------------------------------------------------------

?>
<title><?php echo metaClean($meta_title);?></title>

<meta http-equiv="content-language" content="<?php echo $_SESSION['language_code']; ?>" />
<meta http-equiv="cache-control" content="no-cache" />

<meta name="keywords" content="<?php echo metaClean($meta_keyw); ?>" />
<meta name="description" content="<?php echo metaClean($meta_descr,$metaDesLength); ?>" />

<meta name="robots" content="<?php echo $meta_robots; ?>" />
<meta name="language" content="<?php echo $_SESSION['language_code']; ?>" />
<meta name="author" content="<?php echo metaClean(META_AUTHOR); ?>" />
<meta name="publisher" content="<?php echo metaClean(META_PUBLISHER); ?>" />
<meta name="company" content="<?php echo metaClean(META_COMPANY); ?>" />
<meta name="page-topic" content="<?php echo metaClean(META_TOPIC); ?>" />
<meta name="reply-to" content="<?php echo META_REPLY_TO; ?>" />
<meta name="distribution" content="global" />
<meta name="revisit-after" content="<?php echo META_REVISIT_AFTER; ?>" />
<?php if(isset($canonical_url)) echo '<link rel="canonical" href="'.$canonical_url.'" />'."\n"; ?>