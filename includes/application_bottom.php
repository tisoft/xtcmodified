<?php
/* -----------------------------------------------------------------------------------------
   $Id: application_bottom.php 1239 2005-09-24 20:09:56Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_bottom.php,v 1.14 2003/02/10); www.oscommerce.com
   (c) 2003	 nextcommerce (application_bottom.php,v 1.6 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

if (STORE_PAGE_PARSE_TIME == 'true') {
	$time_start = explode(' ', PAGE_PARSE_START_TIME);
	$time_end = explode(' ', microtime());
	$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
	error_log(strftime(STORE_PARSE_DATE_TIME_FORMAT) . ' - ' . getenv('REQUEST_URI') . ' (' . $parse_time . 's)' . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);

}

if (DISPLAY_PAGE_PARSE_TIME == 'true') {
	$time_start = explode(' ', PAGE_PARSE_START_TIME);
	$time_end = explode(' ', microtime());
	$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
	echo '<div class="parseTime">Parse Time: ' . $parse_time . 's</div>';
}

if ((GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded == true) && ($ini_zlib_output_compression < 1)) {
	if ((PHP_VERSION < '4.0.4') && (PHP_VERSION >= '4')) {
		xtc_gzip_output(GZIP_LEVEL);
	}
}
if (TRACKING_ECONDA_ACTIVE == 'true') {
	require_once (DIR_WS_INCLUDES . 'econda/econda.php');
}

//BOF - DokuMan - 2010-02-25 - Enhance page loading time by putting CSS on TOP of page and JavaScript on BOTTOM of page
//require('templates/'.CURRENT_TEMPLATE.'/javascript/general.js.php');
//EOF - DokuMan - 2010-02-25 - Enhance page loading time by putting CSS on TOP of page and JavaScript on BOTTOM of page

echo '</body></html>';

//--- SHOPSTAT -------------------------//
    $shopstat_ref = __FILE__;
    $shoplog_mode = true;
    require("shopstat/shopstat.php");
//--- SHOPSTAT -------------------------//
?>