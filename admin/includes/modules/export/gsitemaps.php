<?php
/* -----------------------------------------------------------------------------------------
   $Id: gsitemaps.php 
   h.koch 01.2008 (hendrik.koch@gmx.de)
   
   Google Sitemaps by hendrik (http://www.ecombase.de/)
   V1.1 August 2006
   -----------------------------------------------------------------------------------------
   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (invoice.php,v 1.6 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

define('MODULE_GSITEMAPS_TEXT_DESCRIPTION', 'Export - Google Sitemaps im xml Format');
define('MODULE_GSITEMAPS_TEXT_TITLE', 'Google Sitemaps - xml (v1.3.4, Shopstat kompatibel)');
define('MODULE_GSITEMAPS_FILE_TITLE' , '<hr noshade>Dateiname');
define('MODULE_GSITEMAPS_FILE_DESC' , 'Geben Sie einen Dateinamen ein, falls die Exportadatei am Server gespeichert werden soll.<br />(Verzeichnis export/)');
define('MODULE_GSITEMAPS_STATUS_DESC','Modulstatus');
define('MODULE_GSITEMAPS_STATUS_TITLE','Status');
define('EXPORT_YES','Nur Herunterladen');
define('EXPORT_NO','Am Server Speichern');
define('EXPORT','Bitte den Sicherungsprozess AUF KEINEN FALL unterbrechen. Dieser kann einige Minuten in Anspruch nehmen.');
define('EXPORT_TYPE','<hr noshade><b>Speicherart:</b>');
define('CHARSET','iso-8859-1');

define('MODULE_GSITEMAPS_INSTALL_TITLE' , '<hr noshade><b>Installation im Root?</b>');
define('MODULE_GSITEMAPS_INSTALL_DESC' , 'Soll die Ergebnisdatei gleich im Rootverzeichnis abgelegt werden?');
// ----------------------
define ('SITEMAP_PAR_PRIORITY_LIST',    '0.5');        
define ('SITEMAP_PAR_PRIORITY_PRODUCT', '0.8');        
define ('SITEMAP_PAR_CHANGEFREQ', 'weekly');        
// ----------------------

// include needed functions
require_once( DIR_FS_CATALOG.'/includes/classes/language.php');

  class gsitemaps {
    var $code, $title, $description, $enabled;


    function gsitemaps() {
      global $order;

      $this->code = 'gsitemaps';
      $this->title = MODULE_GSITEMAPS_TEXT_TITLE;
      $this->description = MODULE_GSITEMAPS_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_GSITEMAPS_SORT_ORDER;
      $this->enabled = ((MODULE_GSITEMAPS_STATUS == 'True') ? true : false);

      $sql = 'SELECT code FROM `languages`';
      $sql = xtDBquery($sql);
      $this->language_codes = array();
      while( $data=xtc_db_fetch_array($sql) ) {
        $this->language_codes[] = $data['code'];  
      }
      
    }
    
// -------------------- XML Generator ----------------------
    function xls_sitemap_top( ) {
      $ret ='<?xml version="1.0" encoding="UTF-8"?>'."\n";
      $ret.='<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
      return $ret;
    }
    
    function xls_sitemap_bottom() {
      $ret ='  </urlset>'."\n";
      return $ret;
    }
    
    private function gmt_diff() {
      preg_match_all("/([\+|\-][0-9][0-9])([0-9][0-9])/", date("O"), $ausgabe, PREG_PATTERN_ORDER);
      return $ausgabe[1][0] . ":" . $ausgabe[2][0];
    }

    private function xls_sitemap_entry( $url, $lastmod='', $priority=SITEMAP_PAR_PRIORITY_LIST, $changefreq=SITEMAP_PAR_CHANGEFREQ ) {
      if( $lastmod!='' ) {
        $lastmod = str_replace(' ', 'T', $lastmod);
        $lastmod.= $this->gmt_diff();
      }
      
      $url=str_replace('&', '&amp;', $url);
      
      $ret ="\t<url>\n";
      $ret.="\t\t<loc>" . $url . "</loc>\n";
      if( $lastmod != '' ) {
        $ret.="\t\t<lastmod>" . $lastmod . "</lastmod>\n";
      }
      $ret.="\t\t<changefreq>" . $changefreq . "</changefreq>\n";
      $ret.="\t\t<priority>" . $priority . "</priority>\n";
      $ret.="\t</url>\n";
      
      return $ret;
    }
    
// -------------------- Contents ----------------------
    function process_contents( &$schema ) {
      $content_query="SELECT
 					              content_id,
 					              categories_id,
 					              parent_id,
 					              content_title,
 					              content_group
 					            FROM ".TABLE_CONTENT_MANAGER."
 					            WHERE languages_id='".(int)$_SESSION['languages_id']."'
 					               ".$group_check." and content_status=1 order by sort_order";
      $content_query = xtDBquery($content_query);

      while ($content_data=xtc_db_fetch_array($content_query,true)) {
        reset($this->language_codes);
        foreach( $this->language_codes as $lcode ) {
          $link = $this->xtc_href_link('shop_content.php','coID='.$content_data['content_group'].'&language='.$lcode); //.'/'.xtc_cleanName($content_data['content_title']));
//        (preg_match("/\?/",$link)) ? $link .= '&' : $link .= '?';
//        $link .= 'language='.$language_code;

          $entry=$this->xls_sitemap_entry( $link );     
          $schema .= $entry;          
        }
      }
      
    }
      

// -------------------- Categories ----------------------
    function process_categories( &$schema, $language_code ) {
      $categories_query ="SELECT c.categories_image,
									          c.categories_id,
  									        cd.categories_name 
                          FROM 
                            " . TABLE_CATEGORIES . " c left join
  									        " . TABLE_CATEGORIES_DESCRIPTION ." cd on c.categories_id = cd.categories_id
                          WHERE 
                            c.categories_status = '1'                      and 
                            cd.language_id = ".$_SESSION['languages_id']." and 
                            c.parent_id = '0' ".$group_check."
                          ORDER BY 
                            c.sort_order ASC";

      $categories_query = xtDBquery($categories_query);
      while ($categories = xtc_db_fetch_array($categories_query,true)) {
        $link = $this->xtc_href_link('index.php','cPath='.$categories['categories_id'].'&language='.$language_code);
//        (preg_match("/\?/",$link)) ? $link .= '&' : $link .= '?';
//        $link .= 'language='.$language_code;

        $entry=$this->xls_sitemap_entry( $link );     
        $schema .= $entry;          

//        $category_tree=$this->get_category_tree($categories['categories_id']);
        $category_tree=$this->get_category_tree($categories['categories_id'], '', '','',false, '',$language_code );
        foreach( $category_tree as $category_entry ) {
//echo "#".$category_entry['categories_id']."<br />\n";
//          $link = $this->xtc_href_link('index.php','cPath='.$category_entry['link']);
//            $link = $this->xtc_href_link('index.php','cPath='.$category_entry['categories_id'].'&language='.$language_code);
          $link = $category_entry['link'];
          
          $entry=$this->xls_sitemap_entry( $link );     
          $schema .= $entry;          
        }
      }
     
    }
    

// -------------------- Products ----------------------
    function process_products( &$schema ) {
      // create File
      
      $export_query =xtc_db_query("SELECT
                                     p.products_id,
                                     p.products_last_modified, 
                                     pd.products_name
                                   FROM
                                     " . TABLE_PRODUCTS . " p, 
                                     " . TABLE_PRODUCTS_DESCRIPTION . " pd
                                   WHERE
                                     products_status = 1 and
                                     p.products_id=pd.products_id and
                                     pd.language_id=".$_SESSION['languages_id']."
                                     
                                   ORDER BY
                                     p.products_id");

      while ($products = xtc_db_fetch_array($export_query)) {
        reset($this->language_codes);
        foreach( $this->language_codes as $lcode ) {
          $link = $this->xtc_href_link('product_info.php',xtc_product_link($products['products_id'], $products['products_name']).'&language='.$lcode );
//        (preg_match("/\?/",$link)) ? $link .= '&' : $link .= '?';
//        $link .= 'language='.$language_code;
        
          $entry=$this->xls_sitemap_entry( $link, $products['products_last_modified'], SITEMAP_PAR_PRIORITY_PRODUCT);     
          $schema .= $entry;          
        }
        
        
//if( $iii++>5 ) break;
      }

    }


    function process($file) {

      @xtc_set_time_limit(0);
     
      $schema = $this->xls_sitemap_top();

      $schema.= $this->xls_sitemap_entry( HTTP_SERVER . DIR_WS_CATALOG . "index.html" );
      

      $this->process_contents($schema);
      
      reset($this->language_codes);
      foreach( $this->language_codes as $lcode ) {
        $this->process_categories($schema, $lcode);
      }
      
      $this->process_products($schema);


      $schema.= $this->xls_sitemap_bottom();
//echo "<pre>".htmlentities($schema)."</pre>";
      

// -------------------------------------------------------------
      // create File
      if( $_POST['gsitemaps_rootinstall'] == 'yes' ) {
        $filename = DIR_FS_DOCUMENT_ROOT.$file; 
      } else {
        $filename=DIR_FS_DOCUMENT_ROOT.'export/' . $file;
      }
      
      $fp = fopen($filename, "w+");
      fputs($fp, $schema);
      fclose($fp);

      switch ($_POST['export']) {
        case 'yes':
            // send File to Browser
            $extension = substr($file, -3);
            $fp = fopen($filename,"rb");
            $buffer = fread($fp, filesize($filename));
            fclose($fp);
            header('Content-type: application/x-octet-stream');
            header('Content-disposition: attachment; filename=' . $file);
            echo $buffer;
            exit;
        break;
      }

    }

    function display() {

      return array('text' => 
                            MODULE_GSITEMAPS_INSTALL_TITLE.'<br />'.
                            MODULE_GSITEMAPS_INSTALL_DESC.'<br />'.
                            xtc_draw_radio_field('gsitemaps_rootinstall', 'no',true).'nein'.'<br />'.
                            xtc_draw_radio_field('gsitemaps_rootinstall', 'yes',false).'ja'.'<br />'.
                            EXPORT_TYPE.'<br />'.
                            EXPORT.'<br />'.
                            xtc_draw_radio_field('export', 'no',false).EXPORT_NO.'<br />'.
                            xtc_draw_radio_field('export', 'yes',true).EXPORT_YES.'<br />'.
                            '<br />' . xtc_button(BUTTON_EXPORT) .
                            xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=gsitemaps')));
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_GSITEMAPS_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_GSITEMAPS_FILE', 'gsitemaps.xml',  '6', '1', '', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_GSITEMAPS_STATUS', 'True',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_GSITEMAPS_STATUS','MODULE_GSITEMAPS_FILE');
    }

    function get_category_tree( $parent_id = '0', 
                                $spacing = '', 
                                $exclude = '', 
                                $category_tree_array = '', 
                                $include_itself = false, 
                                $cPath = '',
                                $language_code='' ) {
      global $SITEMAP;

	    if ($parent_id == 0){ 
        $cPath = ''; 
      } else {	
        $cPath .= $parent_id . '_'; 
      }
      if (!is_array($category_tree_array)) 
        $category_tree_array = array();
        
      if ($include_itself) {
        $category_query = "select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . $_SESSION['languages_id'] . "' and c.categories_status = '1' and cd.categories_id = '" . $parent_id . "'";
        $category_query = xtDBquery($category_query);
        $category = xtc_db_fetch_array($category_query,true);
        $category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
      }

      $categories_query = "select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . $_SESSION['languages_id'] . "' and c.parent_id = '" . $parent_id . "' and c.categories_status = '1' order by c.sort_order, cd.categories_name";
      $categories_query = xtDBquery($categories_query);
      while ($categories = xtc_db_fetch_array($categories_query,true)) {
        if ($exclude != $categories['categories_id']) {
          $listing_data = array();

          if($SITEMAP['get_products'] && xtc_count_products_in_category($categories['categories_id']) > 0) {
            $listing_data = get_all_products($categories['categories_id']);
          }

          if( $language_code!='' ) {
            $l='&language='.$language_code;
          }
          $link = $this->xtc_href_link('index.php','cPath='.$cPath.$categories['categories_id'].$l);

          $category_tree_array[] = array( 'id'    => $categories['categories_id'],
                                          'categories_id' => $categories['categories_id'],
                                          'text'  => $spacing . $categories['categories_name'],
                                          'link'  => $link,
                                          'pcount'=> sizeof($listing_data),
                                          'products'=>$listing_data
                                        ); //-- DIR_WS_CATALOG . 'index.php?cPath=' . $cPath . $categories['categories_id']
          $category_tree_array = $this->get_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array, false, $cPath, $language_code);
        }
      }

      return $category_tree_array;
    }
    
    
    function xtc_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
      if( file_exists(DIR_FS_INC . 'shopstat_functions.inc.php') ) {    // wenn kein Shopstat installiert
        return $this->xtc_href_link_from_admin(   $page, $parameters, $connection, $add_session_id, $search_engine_safe );
      }

      global $request_type, $session_started, $http_domain, $https_domain,$truncate_session_id;

      if (!xtc_not_null($page)) {
        die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine the page link!<br /><br />');
      }

      if ($connection == 'NONSSL') {
        $link = HTTP_SERVER . DIR_WS_CATALOG;
      } elseif ($connection == 'SSL') {
        if (ENABLE_SSL == true) {
          $link = HTTPS_SERVER . DIR_WS_CATALOG;
        } else {
          $link = HTTP_SERVER . DIR_WS_CATALOG;
        }
      } else {
        die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</b><br /><br />');
      }

      if (xtc_not_null($parameters)) {
        $link .= $page . '?' . $parameters;
        $separator = '&';
      } else {
        $link .= $page;
        $separator = '?';
      }

      while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

  // Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
      if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
        if (defined('SID') && xtc_not_null(SID)) {
          $sid = SID;
        } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
          if ($http_domain != $https_domain) {
            $sid = session_name() . '=' . session_id();
          }
        }        
      }
	  
	  // remove session if useragent is a known Spider
      if ($truncate_session_id) $sid=NULL;

      if (isset($sid)) {
        $link .= $separator . $sid;
      }

      if ( (SEARCH_ENGINE_FRIENDLY_URLS == 'true') && ($search_engine_safe == true) ) {
        while (strstr($link, '&&')) $link = str_replace('&&', '&', $link);

        $link = str_replace('?', '/', $link);
        $link = str_replace('&', '/', $link);
        $link = str_replace('=', '/', $link);
        $separator = '?';
      }

      return $link;
    }
    
    
    
    
/*-----------------------------------------------------------------------
    Version: $Id: xtc_href_link_from_admin.inc.php,v 1.1 2005/01/18 18:48:56 Administrator Exp $

    xtC-SEO-Module by www.ShopStat.com (Hartmut König)
    http://www.shopstat.com
    info@shopstat.com
    © 2004 ShopStat.com
    All Rights Reserved.
------------------------------------------------------------------------*/
    
    
    function xtc_href_link_from_admin
                            (   $page               = '',
                                $parameters         = '',
                                $connection         = 'NONSSL',
                                $add_session_id     = true,
                                $search_engine_safe = true)
    {

    global $request_type, $session_started, $http_domain, $https_domain;
    
    require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');

    if (!xtc_not_null($page)) {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine the page link ('.$page.')!<br /><br />');
    }

    if ($connection == 'NONSSL')
        {
        $link = HTTP_SERVER . DIR_WS_CATALOG;
        }
    elseif ($connection == 'SSL')
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
        die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</b><br /><br />');
        }

    if (xtc_not_null($parameters)) {
      $link .= $page . '?' . $parameters;
      $separator = '&';
    } else {
      $link .= $page;
      $separator = '?';
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
    if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
      if (defined('SID') && xtc_not_null(SID)) {
        $sid = SID;
      } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
        if ($http_domain != $https_domain) {
          $sid = session_name() . '=' . session_id();
        }
      }
    }

//--- SEO Hartmut König -----------------------------------------//
    if ($_REQUEST['test'] ||
        ((SEARCH_ENGINE_FRIENDLY_URLS == 'true') && ($search_engine_safe == true)) )
        {
        require_once(DIR_FS_INC . 'shopstat_functions.inc.php');
//echo "mk1 parameters=$parameters<br />\n"; flush();
        $seolink = shopstat_getSEO( $page,
                                    $parameters,
                                    $connection,
                                    $add_session_id,
                                    $search_engine_safe,
                                    'admin');
//echo "mk2 seolink=$seolink<br /><br />\n"; flush();
	if($seolink)
            {
            $link       = $seolink;
            $elements   = parse_url($link);
            (isset($elements['query']))
                ? $separator = '&'
                : $separator = '?';
            }
        }
//--- SEO Hartmut König -----------------------------------------//

    if (xtc_check_agent()==1) {

    $sid=NULL;

    }
    if (isset($sid)) {
      $link .= $separator . $sid;
    }

//--- SEO Hartmut König -------------------------//

    return $link;
  }    
    
    
    
    

        
    
  }
  
  
  
?>