<?
/* ---------------------------------------------------
   Basismodul imdxajax
   xajax.common.php
   ---------------------------------------------------
   03.2008 h.koch 
   --------------------------------------------------- */
define( 'XAJAX_SUPPORT_VERSION', 'XAjax Support v1.3.0 - 08.2010' );
define( 'IMDXAJAX_MODULE_INCLUDES', 'includes/xajax_imd' );

require_once ("includes/xajax_core/xajax.inc.php");
if (defined('SID') && xtc_not_null(SID)) {
  $sid = '?'.SID;
}

$imdxajax = new xajax("xajax.server.php".$sid);
define( XAJAX_SUPPORT_VERSION_XAJAX, $imdxajax->getVersion() );

if( $handle=opendir (IMDXAJAX_MODULE_INCLUDES) ) {
  while (false !== ($file = readdir ($handle))) {
    if( strpos($file, '.xajax.common.inc.php')!==false ) {
      include( IMDXAJAX_MODULE_INCLUDES.'/'.$file);
    }
  }
  closedir($handle);
}



?>