<?php
/* -----------------------------------------------------------------------------------------
   $Id: general.js.php 1262 2005-09-30 10:00:32Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


   // this javascriptfile get includes at the BOTTOM of every template page in shop
   // you can add your template specific js scripts here
?>
<script src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/jquery.js" type="text/javascript"></script>
<script src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/thickbox.js" type="text/javascript"></script>

<?php// BOF - web28 - 2010-07-09 - TABS/ACCORDION in product_info ?>
<?php
if (strstr($PHP_SELF, FILENAME_PRODUCT_INFO )) {
?>
<script src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$("#tabbed_product_info").tabs();
	});
	
	$(function() {
		$("#accordion_product_info").accordion();
	});
</script>
<noscript> 
	<style type="text/css">#tabbed_product_info ul {display: none;}</style>
</noscript>
<?php
}
?>
<?php// EOF - web28 - 2010-07-09 - TABS/ACCORDION in product_info ?>