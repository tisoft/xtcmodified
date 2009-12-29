<?php
/* --------------------------------------------------------------
   $Id: orders_edit.php,v 1.0

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   XTC-Bestellbearbeitung:
   http://www.xtc-webservice.de / Matthias Hinsche
   info@xtc-webservice.de

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(orders.php,v 1.27 2003/02/16); www.oscommerce.com 
   (c) 2003	 nextcommerce (orders.php,v 1.7 2003/08/14); www.nextcommerce.org

   Released under the GNU General Public License 

	--------------------------------------------------------------*/
?>


<!-- Adressbearbeitung Anfang //-->
<?php if ($_GET['edit_action']=='address'){

 echo xtc_draw_form('adress_edit', FILENAME_ORDERS_EDIT, 'action=address_edit', 'post');
 echo xtc_draw_hidden_field('oID', $_GET['oID']);
 echo xtc_draw_hidden_field('cID', $order->customer['ID']);
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width="10%" align="left">&nbsp;</td>
<td class="dataTableHeadingContent" width="30%" align="left"><?php echo TEXT_INVOICE_ADDRESS;?></td>
<td class="dataTableHeadingContent" width="30%" align="left"><?php echo TEXT_SHIPPING_ADDRESS;?></td>
<td class="dataTableHeadingContent" width="30%" align="left"><?php echo TEXT_BILLING_ADDRESS;?></td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_COMPANY;?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('customers_company', $order->customer['company']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('delivery_company', $order->delivery['company']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('billing_company', $order->billing['company']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_NAME;?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('customers_name', $order->customer['name']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('delivery_name', $order->delivery['name']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('billing_name', $order->billing['name']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_STREET;?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('customers_street_address', $order->customer['street_address']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('delivery_street_address', $order->delivery['street_address']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('billing_street_address', $order->billing['street_address']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_ZIP;?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('customers_postcode', $order->customer['postcode']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('delivery_postcode', $order->delivery['postcode']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('billing_postcode', $order->billing['postcode']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_CITY;?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('customers_city', $order->customer['city']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('delivery_city', $order->delivery['city']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('billing_city', $order->billing['city']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_COUNTRY;?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('customers_country', $order->customer['country']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('delivery_country', $order->delivery['country']);?>
</td>
<td class="dataTableContent" align="left">
<?php echo xtc_draw_input_field('billing_country', $order->billing['country']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left" colspan="4">
&nbsp;
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_CUSTOMER_GROUP;?>
</td>
<td class="dataTableContent" align="left" colspan="3">
<?php echo xtc_draw_pull_down_menu('customers_status', xtc_get_customers_statuses(), $order->info['status']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_CUSTOMER_EMAIL;?>
</td>
<td class="dataTableContent" align="left" colspan="3">
<?php echo xtc_draw_input_field('customers_email_address', $order->customer['email_address']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_CUSTOMER_TELEPHONE;?>
</td>
<td class="dataTableContent" align="left" colspan="3">
<?php echo xtc_draw_input_field('customers_telephone', $order->customer['telephone']);?>
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left">
<?php echo TEXT_CUSTOMER_UST;?>
</td>
<td class="dataTableContent" align="left" colspan="3">
<?php echo xtc_draw_input_field('customers_vat_id', $order->customer['vat_id']);?>
</td>
</tr>


<tr class="dataTableRow">
<td class="dataTableContent" align="left" colspan="4">
&nbsp;
</td>
</tr>

<tr class="dataTableRow">
<td class="dataTableContent" align="left" colspan="4">
<?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>'; ?>
</td>
</tr>

<tr>
<td class="dataTableHeadingContent" width="10%" align="left">&nbsp;</td>
<td class="dataTableHeadingContent" width="30%" align="left">&nbsp;</td>
<td class="dataTableHeadingContent" width="30%" align="left">&nbsp;</td>
<td class="dataTableHeadingContent" width="30%" align="left">&nbsp;</td>
</tr>
</table>
</form>
<br /><br />
<?php } ?>
<!-- Adressbearbeitung Ende //-->













