die wi<?php

/* -----------------------------------------------------------------------------------------
   $Id: luup_orders.php 998 2006-06-09 14:18:20Z mz $   

   xt:Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2006 xt:Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) Eskil Hauge (eskil@luup.com)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

 
    if ($order->info['payment_method'] == 'luupws' && MODULE_PAYMENT_LUUPWS_USE_DB == 'Yes') {

      	$luup_action = (isset($_POST['luup_request']) ? $_POST['luup_request'] : '');
      
        $luup_query = xtc_db_query("select order_id, transaction_id, payment_status from LUUP where order_id = '" . (int)$_GET['oID']  . "'");
		$luup_info = xtc_db_fetch_array($luup_query);    	
        
        
        if (xtc_not_null($luup_info['transaction_id']) && xtc_not_null($luup_info['payment_status']) ){
		include_once(DIR_FS_CATALOG.'lang/'.$order->info['language'].'/modules/payment/luupws.php');
		include_once(DIR_FS_CATALOG.DIR_WS_INCLUDES.'modules/payment/luupws.php');

		$luup = new luupws();
		$luup_transid = $luup_info['transaction_id'];
        	$luup_status = $luup_info['payment_status'];
		$luup_result = '';
		$update = 0;
		
		if (xtc_not_null($luup_action)) {
		    switch($luup_action){
			case 'Cancel':
			$success = $luup->luup_cancelPayment( $luup_transid );
				if($success){
					$luup_result = MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_CANCELLED;
					$luup_status = 'Cancelled';
					$luup_update = 'update LUUP SET payment_status = "Cancelled" where transaction_id = "'.$luup_transid.'"';
					$update = 1;
				}
				else{
					$luup_result = MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_FAILED;
				}
			break;
			case 'Refund':
				$success = $luup->luup_refundPayment( $luup_transid );
				if($success){
					$luup_result = MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_REFUNDED;
					$luup_status = 'Refunded';
					$luup_update = 'update LUUP SET payment_status = "Refunded" where transaction_id = "'.$luup_transid.'"';
					$update = 1;
				}
				else{
					$luup_result = MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_FAILED;
				}
			break;
			case 'Collect':
				$success = $luup->luup_completePayment( $luup_transid );
				if($success){
					$luup_result = MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_COMPLETED;
					$luup_status = 'Completed';
					$luup_update = 'update LUUP SET payment_status = "Completed" where transaction_id = "'.$luup_transid.'"';
					$update = 1;
				}
				else{
					$luup_result = MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_FAILED;
				}
			break;
		    }
		}

		// run query (update status) if payment update was requested and completed
		if( $update == 1)
			xtc_db_query($luup_update);

		// display available payment action
		switch($luup_status){
			case 'Completed':
				$luup_button = MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_BUTTON_REFUND;
				break;
			case 'Pending':
				$luup_button = MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_BUTTON_PENDING;
				break;
			default:
				$luup_button = 'n/a'; // refunded or cancelled
				break;
		}
		
		
?>
<tr>
	<td colspan="2">
	<?php
	echo '<img src="../images/icons/luupay.gif">'; 
	echo xtc_draw_form('luup', FILENAME_ORDERS, xtc_get_all_get_params(array('action')).'action=edit');
	?>
	
	<table style="border: 1px solid; border-color: #4B2582; background: #FF7800;">
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
	    <td class="main"><?php echo MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_STATUS; ?></td>
	    <td class="main"><?php echo $luup_status; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_TRANSACTION_ID; ?></td>
            <td class="main"><?php echo $luup_transid; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_ACTION; ?></td>
            <td class="main"><?php echo $luup_button; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo $luup_result; ?></td>
          </tr>
      </table>
      </form>
     </td>
</tr>
<?php
    	}
    	echo '<!-- LUUP_eof //-->';
    }
?>