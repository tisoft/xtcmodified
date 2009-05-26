<?php
/*-----------------------------------------------------------------------
    Version: $Id: xtc_href_link_from_admin.inc.php,v 1.1 2005/01/18 18:48:56 Administrator Exp $

    xtC-SEO-Module by www.ShopStat.com (Hartmut König)
    http://www.shopstat.com
    info@shopstat.com
    © 2004 ShopStat.com
    All Rights Reserved.
------------------------------------------------------------------------*/

// The HTML href link wrapper function
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
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><strong>Error!</strong></font><br /><br /><strong>Unable to determine the page link ('.$page.')!<br /><br />');
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
        die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><strong>Error!</strong></font><br /><br /><strong>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
        }

    if (xtc_not_null($parameters)) {
      $link .= $page . '?' . $parameters;
      $separator = '&amp;';
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

        $seolink = shopstat_getSEO( $page,
                                    $parameters,
                                    $connection,
                                    $add_session_id,
                                    $search_engine_safe,
                                    'admin');
	if($seolink)
            {
            $link       = $seolink;
            $elements   = parse_url($link);
            (isset($elements['query']))
                ? $separator = '&amp;'
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

 ?>