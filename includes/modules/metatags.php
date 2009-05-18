<?php 


// ---------------------------------------------------------------------------------------
//	AUTOMATISCHE METATAGS für xt:Commerce 3.04
// ---------------------------------------------------------------------------------------
//	by Gunnar Tillmann
//	http://www.gunnart.de
// ---------------------------------------------------------------------------------------
//	based on:
//	(c) 2003 xt:Commerce (metatags.php, v.1140 2005/08/10); www.xt-commerce.de
//	(c) 2003 nextcommerce (metatags.php, v1.7 2003/08/14); www.nextcommerce.org
// ---------------------------------------------------------------------------------------
//	Version 0.9d / Security Fix 25. November 2008 / Fix für Shops ohne ShopStat
// ---------------------------------------------------------------------------------------
//	Inspired by "Dynamic Meta" - Ein WordPress-PlugIn von Michael Schwarz
//	http://www.php-vision.de/plugins-scripte/dynamicmeta-wpplugin.php
// ---------------------------------------------------------------------------------------
//	Getestet für xt:C 3.04 SP2.1, 
// 	Tauglich für Shops mit und ohne ShopStat-Erweiterung
//	Eventuell sollte die "includes/header.php" ein bisschen angepasst werden, um valides
//	XHTML zu gewährleisten
// ---------------------------------------------------------------------------------------



// ---------------------------------------------------------------------------------------
//	Konfiguration ... 
// ---------------------------------------------------------------------------------------
	global $metaStopWords, $metaGoWords, $metaMinLength, $metaMaxLength;
		$metaStopWords 	=	('aber,alle,alles,als,auch,auf,aus,bei,beim,beinahe,bin,bis,ist,dabei,dadurch,daher,dank,darum,danach,das,daß,dass,dein,deine,dem,den,der,des,dessen,dadurch,deshalb,die,dies,diese,dieser,diesen,diesem,dieses,doch,dort,durch,eher,ein,eine,einem,einen,einer,eines,einige,einigen,einiges,eigene,eigenes,eigener,endlich,euer,eure,etwas,fast,findet,für,gab,gibt,geben,hatte,hatten,hattest,hattet,heute,hier,hinter,ich,ihr,ihre,ihn,ihm,im,immer,in,ist,ja,jede,jedem,jeden,jeder,jedes,jener,jenes,jetzt,kann,kannst,kein,können,könnt,machen,man,mein,meine,mehr,mit,muß,mußt,musst,müssen,müßt,nach,nachdem,neben,nein,nicht,nichts,noch,nun,nur,oder,statt,anstatt,seid,sein,seine,seiner,sich,sicher,sie,sind,soll,sollen,sollst,sollt,sonst,soweit,sowie,und,uns,unser,unsere,unserem,unseren,unter,vom,von,vor,wann,warum,was,war,weiter,weitere,wenn,wer,werde,widmen,widmet,viel,viele,vieles,weil,werden,werdet,weshalb,wie,wieder,wieso,wir,wird,wirst,wohl,woher,wohin,wurdezum,zur,über');
		$metaGoWords 	=	('gola,adidas'); // Hier rein, was nicht gefiltert werden soll
		$metaMinLength 	=	9;		// Mindestlänge eines Keywords
		$metaMaxLength 	=	18;		// Maximallänge eines Keywords
		$metaDesLength 	=	364;	// maximale Länge der "description" (in Buchstaben)
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
		$Text = str_replace(array('&nbsp;','\t','\r','\n','\b'),' ',strip_tags($Text));
		$Text = metaHtmlEntities(metaNoEntities(strtolower($Text)),ENT_QUOTES);
		$Text = preg_replace("/(&([aou])[^;]*;)/",'$2e',$Text);
		$Text = preg_replace("/(&(s)[^;]*;)/",'$2$2',$Text);
		$Text = preg_replace("/(&([cizen])[^;]*;)/",'$2',$Text);
		$Text = preg_replace("/(&[^;]*;)/",' ',$Text);
		$Text = preg_replace("/([^0-9a-z|\-])/",' ',$Text);
		$Text = trim(preg_replace("/\s\s+/",' ',$Text));
		return($Text);
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
		$KeyWords 	= 	array_filter($KeyWords,filterKeyWordArray);
		natsort($KeyWords);
		return $KeyWords;
	}
// ---------------------------------------------------------------------------------------
//	GoWords- und Längen-Filter: 
//	Alles, was zu kurz ist, fliegt raus, sofern nicht in der GoWords-Liste
// ---------------------------------------------------------------------------------------
	function filterKeyWordArray($KeyWord) {
		global $metaMinLength, $metaMaxLength;
		$GoWords = WordArray(getGoWords());
		if(!in_array($KeyWord,$GoWords)) {
			$Length = strlen($KeyWord);
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
//	Meta-Angaben genommen. 
// ---------------------------------------------------------------------------------------
	function getGoWords(){
		global $metaGoWords;
		$GoWords = $metaGoWords.' '.META_KEYWORDS;
		if(!empty($categories_meta['categories_meta_keywords'])) {
			$GoWords .= ' '.$categories_meta['categories_meta_keywords'];
		}
		if(!empty($product->data['products_meta_keywords'])) {
			$GoWords .= ' '.$product->data['products_meta_keywords'];
		}
		return $GoWords;
	}
// ---------------------------------------------------------------------------------------
//	Aufräumen: Leerzeichen und HTML-Code raus, kürzen, Umlaute und Sonderzeichen wandeln
// ---------------------------------------------------------------------------------------
	function metaClean($Text,$Length=false,$Abk=' ...') {
		$Text = metaNoEntities($Text);
		$Text = strip_tags($Text);
		$Text = str_replace(array('&nbsp;','\t','\r','\n','\b'),' ',$Text);
		$Text = trim(preg_replace("/\s\s+/",' ',$Text));
		if(($Length)&&($Length > 0)) {
			if(strlen($Text) > $Length) {
	        	$Length -= strlen($Abk);
	            $Text = preg_replace('/\s+?(\S+)?$/', '', substr($Text, 0, $Length+1));
	            $Text = substr($Text, 0, $Length).$Abk;
			}
		}
		return metaHtmlEntities($Text,ENT_QUOTES);
	}
// ---------------------------------------------------------------------------------------
//	metaTitle und metaKeyWords, Rückgabe bzw. Formatierung
// ---------------------------------------------------------------------------------------
	function metaTitle($Title=array()) {
		$Title = func_get_args();
		$Title = array_filter($Title,metaClean);
		return implode(' - ',$Title);
	}
// ---------------------------------------------------------------------------------------
	function metaKeyWords($Text) {
		$KeyWords = cleanKeyWords($Text);
		return implode(', ',$KeyWords);
	}
// ---------------------------------------------------------------------------------------



// ---------------------------------------------------------------------------------------
//	Daten holen: Produktdetails
// ---------------------------------------------------------------------------------------
	if(basename($_SERVER['SCRIPT_NAME']) == FILENAME_PRODUCT_INFO) { 
		if($product->isProduct()) { 
			if(!empty($product->data['products_meta_keywords'])) { 
				$meta_keyw = metaKeyWords($product->data['products_meta_keywords']); 
			} else { 
				$meta_keyw = metaKeyWords($product->data['products_name'].' '.$product->data['products_description']);
			} 
			if(!empty($product->data['products_meta_description'])) { 
				$meta_descr = $product->data['products_meta_description']; 
			} else { 
				$meta_descr = $product->data['products_name'].': '. 
				$product->data['products_description']; 
			} 
			$meta_title = metaTitle($product->data['products_name'],$product->data['manufacturers_name'],TITLE); 
		} 
	} 
// ---------------------------------------------------------------------------------------
//	Daten holen: Kategorie
// ---------------------------------------------------------------------------------------
	elseif(basename($_SERVER['SCRIPT_NAME']) == FILENAME_DEFAULT) { 
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

		if(!empty($_GET['manu'])) {
			$manu_id = $_GET['manu'];
		}
		if(!empty($_GET['manufacturers_id'])) {
			$manu_id = $_GET['manufacturers_id'];
		}
		if(!empty($_GET['filter_id'])) {
			$manu_id = $_GET['filter_id'];
		}

		if($manu_id) {
			$manu_name_query = xtDBquery("
				select 	manufacturers_name 
				from 	".TABLE_MANUFACTURERS." 
				where 	manufacturers_id ='".intval($manu_id)."'
			");
			$manu_name = implode('',xtc_db_fetch_array($manu_name_query,true));
		}
		
		// KeyWords ...
		if(!empty($categories_meta['categories_meta_keywords'])) { 
			$meta_keyw = metaKeyWords($categories_meta['categories_meta_keywords']); 
		} else{ 
			$meta_keyw = metaKeyWords($categories_meta['categories_name'].' '.$manu_name.' '.$categories_meta['categories_description']);
		} 
		
		// Description ...
		if(!empty($categories_meta['categories_meta_description'])) { 
			$meta_descr = $categories_meta['categories_meta_description']; 
		} else{ 
			$meta_descr = TITLE.' - '.$categories_meta['categories_name'];
			if(!empty($manu_name)) {
				$meta_descr .= ' von: '.$manu_name;
			} 
			if(!empty($categories_meta['categories_description'])) {
				$meta_descr .= ' - '.$categories_meta['categories_description'];
			}
		} 
		
		// Title ...
		if(!empty($categories_meta['categories_meta_title'])) { 
			$meta_title = metaTitle($categories_meta['categories_meta_title'],TITLE); 
		} else{ 
			$meta_title = metaTitle($categories_meta['categories_name'],$manu_name,TITLE); 
		} 
	} 
// ---------------------------------------------------------------------------------------
//	Daten holen: Inhalts-Seite (ContentManager)
// ---------------------------------------------------------------------------------------
	elseif(basename($_SERVER['SCRIPT_NAME']) == FILENAME_CONTENT) { 
		$contents_meta_query = xtDBquery("
			select 	content_title, 
					content_heading, 
					content_text 
			from 	".TABLE_CONTENT_MANAGER." 
			where 	content_group='".intval($_GET['coID'])."' 
			and 	languages_id='".intval($_SESSION['languages_id'])."'
		"); 
		$contents_meta = xtc_db_fetch_array($contents_meta_query,true); 
		
		if(count($contents_meta) > 0) { 
			$meta_title	= metaTitle($contents_meta['content_title'],$contents_meta['content_heading'],TITLE); 
			$meta_descr	= $contents_meta['content_heading'].': '.$contents_meta['content_text']; 
			$meta_keyw	= metaKeyWords($contents_meta['content_title'].' '.$contents_meta['content_heading'].' '.$contents_meta['content_text']); 
		}
	}
// ---------------------------------------------------------------------------------------
//	... und wenn nix drin, dann Standard-Werte nehmen
// ---------------------------------------------------------------------------------------
	if(empty($meta_keyw)) {
		$meta_keyw    = metaKeyWords(META_KEYWORDS); 
	} 
	if(empty($meta_descr)) {
		$meta_descr   = META_DESCRIPTION; 
	}
	if(empty($meta_title)) {
		$meta_title   = TITLE;
	}
// ---------------------------------------------------------------------------------------



?>
<title><?php echo metaClean($meta_title);?></title> 

<meta http-equiv="content-language" content="<?php echo $_SESSION['language_code']; ?>" /> 
<meta http-equiv="cache-control" content="no-cache" /> 

<meta name="keywords" content="<?php echo $meta_keyw; ?>" /> 
<meta name="description" content="<?php echo metaClean($meta_descr,$metaDesLength); ?>" /> 

<meta name="robots" content="<?php echo META_ROBOTS; ?>" />
<meta name="language" content="<?php echo $_SESSION['language_code']; ?>" />
<meta name="author" content="<?php echo metaClean(META_AUTHOR); ?>" />
<meta name="publisher" content="<?php echo metaClean(META_PUBLISHER); ?>" />
<meta name="company" content="<?php echo metaClean(META_COMPANY); ?>" />
<meta name="page-topic" content="<?php echo metaClean(META_TOPIC); ?>" />
<meta name="reply-to" content="<?php echo META_REPLY_TO; ?>" />
<meta name="distribution" content="global" />
<meta name="revisit-after" content="<?php echo META_REVISIT_AFTER; ?>" />