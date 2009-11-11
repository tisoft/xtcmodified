<?php


// (c) 2006 Web4Business GmbH - Designs - Modules. www.web4business.ch

defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");

function showSpecialsBox() {
			// include localized categories specials strings
			  require_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/categories_specials.php');


			// if editing an existing product

			if(isset($_GET['pID'])) {

				$specials_query = "select p.products_tax_class_id,
												p.products_id,
												pd.products_name,
												p.products_price,
												s.specials_id,
												s.specials_quantity,
												s.specials_new_products_price,
												s.specials_date_added,
												s.specials_last_modified,
												s.expires_date from
												" . TABLE_PRODUCTS . " p,
												" . TABLE_PRODUCTS_DESCRIPTION . " pd,
												" . TABLE_SPECIALS . "
												s where p.products_id = pd.products_id
												and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
												and p.products_id = s.products_id
												and s.products_id = '" . (int)$_GET['pID'] . "'";

				$specials_query = xtDBquery($specials_query);

				// if there exists already a special for this product

				if(xtc_db_num_rows($specials_query, true) > 0) {

					$special = xtc_db_fetch_array($specials_query, true);
					$sInfo = new objectInfo($special);
				}
			}

			$price=$sInfo->products_price;
			$new_price=$sInfo->specials_new_products_price;

			if (PRICE_IS_BRUTTO=='true') {

 				$price_netto=xtc_round($price,PRICE_PRECISION);
				$new_price_netto=xtc_round($new_price,PRICE_PRECISION);
				$price= ($price*(xtc_get_tax_rate($sInfo->products_tax_class_id)+100)/100);
				$new_price= ($new_price*(xtc_get_tax_rate($sInfo->products_tax_class_id)+100)/100);
			}

			$price=xtc_round($price,PRICE_PRECISION);
			$new_price=xtc_round($new_price,PRICE_PRECISION);

			// build the expires date in the format YYYY-MM-DD

			if(isset($_GET['pID']) and xtc_db_num_rows($specials_query, true) > 0 and $sInfo->expires_date != 0)

				$expires_date = substr($sInfo->expires_date, 0, 4)."-".
								substr($sInfo->expires_date, 5, 2)."-".
								substr($sInfo->expires_date, 8, 2);

			else
				$expires_date = "";

			// tell the storing script if to update existing special,
			// or to insert a new one

			echo xtc_draw_hidden_field('specials_action',
					((isset($_GET['pID']) and xtc_db_num_rows($specials_query, true) > 0)
						? "update"
						: "insert"
					)
				);

			if(isset($_GET['pID']) and xtc_db_num_rows($specials_query, true) > 0)
				echo xtc_draw_hidden_field('specials_id', $sInfo->specials_id);

		?>

<script type="text/javascript">
  var specialExpires = new ctlSpiffyCalendarBox("specialExpires", "new_product", "specials_expires","btnDate2","<?php echo $expires_date; ?>",2);
</script>
<script language="JavaScript" type="text/JavaScript">  
  function showSpecial() {
    //alert(document.getElementById("special").style.display);	
	if (document.getElementById("special").style.display =="none" || document.getElementById("special").style.display =="") {
		document.getElementById("special").style.display="block";
		document.getElementById('butSpecial').innerHTML= '<a href="JavaScript:showSpecial()" class="button">&laquo; Sonderangebot</a>';
	}	 	       	  
	else {
		document.getElementById("special").style.display="none";
		document.getElementById('butSpecial').innerHTML= '<a href="JavaScript:showSpecial()" class="button">Sonderangebot &raquo;</a>';
    }		
  }
</script>
<style type='text/css'>#special{display: none;}</style>
<noscript>
<style type="text/css">#special{display: block;}</style>
</noscript>
     <div id="special">
      <div style="padding: 8px 0px 3px 5px;">
        <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main"><strong><?php echo SPECIALS_TITLE; ?></strong></td>            
          </tr>
        </table>
	  </div>	  
	<table bgcolor="f3f3f3" style="width: 100%; border: 1px solid; border-color: #aaaaaa; padding:5px;">		
		<tr>
		<td>
      <table width="100%" border="0" cellpadding="3" cellspacing="0" style="border: 0px dotted black;">
          <tr>
            <td class="main" style="width:270px;"><?php echo TEXT_SPECIALS_SPECIAL_PRICE; ?>&nbsp;</td>
            <td class="main" style="width:250px;"><?php echo xtc_draw_input_field('specials_price', $new_price, 'style="width: 135px"');?> </td>
            <td class="main" style="width:340px;">&nbsp;<?php if(isset($_GET['pID']) and xtc_db_num_rows($specials_query, true) > 0) { ?>
			<input type="checkbox" name="specials_delete" value="true"
				id="input_specials_delete"
				onclick="if(this.checked==true)return confirm('<?php echo TEXT_INFO_DELETE_INTRO; ?>');"style="vertical-align:middle;"/><label for="input_specials_delete">&nbsp;<?php echo TEXT_INFO_HEADING_DELETE_SPECIALS; ?></label>        
		    <?php } ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_SPECIALS_SPECIAL_QUANTITY; ?>&nbsp;</td>
            <td class="main"><?php echo xtc_draw_input_field('specials_quantity', $sInfo->specials_quantity, 'style="width: 135px"');?> </td>
            <td class="main">&nbsp;</td>
          </tr>
		<?php if(isset($_GET['pID']) and xtc_db_num_rows($specials_query, true) > 0) { ?>
			<tr>
	          <td class="main"><?php echo TEXT_INFO_DATE_ADDED; ?></td>
	          <td class="main"><?php echo xtc_date_short($sInfo->specials_date_added); ?></td>
        	  <td class="main">&nbsp;</td>
			</tr>
			<tr>
	          <td class="main"><?php echo TEXT_INFO_LAST_MODIFIED; ?></td>
	          <td class="main"><?php echo xtc_date_short($sInfo->specials_last_modified); ?></td>
        	  <td class="main">&nbsp;</td>
			</tr>
		<?php } ?>
          <tr>
          <td class="main"><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?></td>
          <td class="main"><script type="text/javascript">specialExpires.writeControl(); specialExpires.dateFormat="yyyy-MM-dd";</script>
				<noscript>
                <?php echo  xtc_draw_input_field('specials_expires', $expires_date ,'style="width: 135px"'); ?>
                </noscript>
          </td>				
		  <td class="main">&nbsp;</td>
          </tr>
		<tr>
			<td colspan="3" class="main" style="padding:3px; background: #D8D8D8;">
				<?php echo TEXT_SPECIALS_PRICE_TIP; ?>
			</td>
		</tr>
		
      </table>
	  </td></tr></table>
	  </div>
<?php
}


function saveSpecialsData($products_id) {

		// decide whether to insert a new special,
		// or to update an existing one

  if($_POST['specials_action'] == "insert"
	and isset($_POST['specials_price'])
	and !empty($_POST['specials_price'])) {

	 // insert a new special, code taken from /admin/specials.php, and modified

	 if(!isset($_POST['specials_quantity']) or empty($_POST['specials_quantity']))
		$_POST['specials_quantity'] = 0;

     if (PRICE_IS_BRUTTO=='true' && substr($_POST['specials_price'], -1) != '%'){
        $sql="select tr.tax_rate from " . TABLE_TAX_RATES . " tr, " . TABLE_PRODUCTS . " p  where tr.tax_class_id = p. products_tax_class_id  and p.products_id = '". $products_id . "' ";
        $tax_query = xtc_db_query($sql);
        $tax = xtc_db_fetch_array($tax_query);
        $_POST['specials_price'] = ($_POST['specials_price']/($tax['tax_rate']+100)*100);
     }
     
     
     if (substr($_POST['specials_price'], -1) == '%')  {
     	$new_special_insert_query = xtc_db_query("select products_id,products_tax_class_id, products_price from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
        $new_special_insert = xtc_db_fetch_array($new_special_insert_query);
        $_POST['products_price'] = $new_special_insert['products_price'];
      $_POST['specials_price'] = ($_POST['products_price'] - (($_POST['specials_price'] / 100) * $_POST['products_price']));
      }
     
     
      $expires_date = '';
      if ($_POST['specials_expires']) {
        $expires_date = str_replace("-", "", $_POST['specials_expires']);
      }
     
      xtc_db_query("insert into " . TABLE_SPECIALS . " (products_id, specials_quantity, specials_new_products_price, specials_date_added, expires_date, status) values ('" . $products_id . "', '" . $_POST['specials_quantity'] . "', '" . $_POST['specials_price'] . "', now(), '" . $expires_date . "', '1')");

  }

  elseif($_POST['specials_action'] == "update"
	and isset($_POST['specials_price']) and isset($_POST['specials_quantity'])) {

	  // update the existing special for this product, code taken from /admin/specials.php, and modified

      if (PRICE_IS_BRUTTO=='true' && substr($_POST['specials_price'], -1) != '%'){
        $sql="select tr.tax_rate from " . TABLE_TAX_RATES . " tr, " . TABLE_PRODUCTS . " p  where tr.tax_class_id = p. products_tax_class_id  and p.products_id = '". $products_id . "' ";
        $tax_query = xtc_db_query($sql);
        $tax = xtc_db_fetch_array($tax_query);
        $_POST['specials_price'] = ($_POST['specials_price']/($tax[tax_rate]+100)*100);
     }

      if (substr($_POST['specials_price'], -1) == '%')  {
      $_POST['specials_price'] = ($_POST['products_price'] - (($_POST['specials_price'] / 100) * $_POST['products_price']));
      }
      $expires_date = '';
      if ($_POST['specials_expires']) {
        $expires_date = str_replace("-", "", $_POST['specials_expires']);
      }
      //BOF BUGFIX - Änderungen wurden bei Update nicht übernommen
	  //xtc_db_query("update " . TABLE_SPECIALS . " set specials_quantity = '" . $_POST['specials_quantity'] . "', specials_new_products_price = '" . $_POST['specials_price'] . "', specials_last_modified = now(), expires_date = '" . $expires_date . "' where specials_id = '" . $products_id  . "'");
      xtc_db_query("update " . TABLE_SPECIALS . " set specials_quantity = '" . $_POST['specials_quantity'] . "', specials_new_products_price = '" . $_POST['specials_price'] . "', specials_last_modified = now(), expires_date = '" . $expires_date . "' where specials_id = '" . xtc_db_input($_POST['specials_id'])  . "'");
      //BOF BUGFIX - Änderungen wurden bei Update nicht übernommen
  }

  if(isset($_POST['specials_delete'])) {

	// delete existing special for this product, code taken from /admin/specials.php, and modified

	xtc_db_query("delete from " . TABLE_SPECIALS . " where specials_id = '" . xtc_db_input($_POST['specials_id']) . "'");
  }


}
?>