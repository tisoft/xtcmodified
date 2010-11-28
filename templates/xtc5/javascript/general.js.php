<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2006 XT-Commerce (general.js.php 1262 2005-09-30)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

 // this javascriptfile get includes at the BOTTOM of every template page in shop
 // you can add your template specific js scripts here

//BOF - web28 - 2010-09-03 - Security Fix - Base -> add DIR_WS_CATALOG to template path
//BOF - GTB - 2010-08-03 - Security Fix - Base
?>
<script src="<?php echo DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE; ?>/javascript/jquery.js" type="text/javascript"></script>
<script src="<?php echo DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE; ?>/javascript/thickbox.js" type="text/javascript"></script>
<?php
// BOF - web28 - 2010-07-26 - TABS/ACCORDION in product_info
if (strstr($PHP_SELF, FILENAME_PRODUCT_INFO )) {
?>
<script src="<?php echo DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE; ?>/javascript/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript">
<!-- Laden einer CSS Datei mit jquery
    $.get("<?php echo DIR_WS_BASE.'templates/'.CURRENT_TEMPLATE; ?>"+"/css/javascript.css",
    function(css) {
      $("head").append("<style type='text/css'>"+css+"<\/style>");
    });

  $(function() {
    $("#tabbed_product_info").tabs();
    $("#accordion_product_info").accordion({ autoHeight: false });
  });
//-->
</script>
<?php
}
// EOF - web28 - 2010-07-26 - TABS/ACCORDION in product_info
//EOF - GTB - 2010-08-03 - Security Fix - Base
//eOF - web28 - 2010-09-03 - Security Fix - Base -> add DIR_WS_CATALOG to template path
?>