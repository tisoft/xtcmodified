<?php

/* -----------------------------------------------------------------------------------------
   $Id: ot_coupon.php 1322 2010-05-23 13:58:22Z web28 $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_coupon.php,v 1.1.2.37.3); www.oscommerce.com

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class ot_coupon {
	var $title, $output;

	function ot_coupon() {
		global $xtPrice;

		$this->code = 'ot_coupon';
		$this->header = MODULE_ORDER_TOTAL_COUPON_HEADER;
		$this->title = MODULE_ORDER_TOTAL_COUPON_TITLE;
		$this->description = MODULE_ORDER_TOTAL_COUPON_DESCRIPTION;
		$this->user_prompt = '';
		$this->enabled = MODULE_ORDER_TOTAL_COUPON_STATUS;
		$this->sort_order = MODULE_ORDER_TOTAL_COUPON_SORT_ORDER;
		$this->include_shipping = MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING;
		$this->include_tax = MODULE_ORDER_TOTAL_COUPON_INC_TAX;
		$this->calculate_tax = MODULE_ORDER_TOTAL_COUPON_CALC_TAX;
		$this->tax_class = MODULE_ORDER_TOTAL_COUPON_TAX_CLASS;
		$this->credit_class = true;
		$this->output = array ();

	}

	//BOF -web28- 2010-05-23 - BUGFIX - tax_deduction, $order->info['subtotal']
	/*
	function process() {
		global $order, $xtPrice;

		$order_total = $this->get_order_total();
		$od_amount = $this->calculate_credit($order_total);
		$tod_amount = 0.0; //Fred
		$this->deduction = $od_amount;
	
		if ($this->calculate_tax != 'None') { //Fred - changed from 'none' to 'None'!
		   $tod_amount = $this->calculate_tax_deduction($order_total, $this->deduction, $this->calculate_tax);
		}

		if ($od_amount > 0) {
			$order->info['total'] = $order->info['total'] - $od_amount;
			$order->info['deduction'] = $od_amount;
			$this->output[] = array ('title' => $this->title.':'.$this->coupon_code.':', 'text' => '<strong><font color="#ff0000">-'.$xtPrice->xtcFormat($od_amount, true).'</font></strong>', 'value' => $od_amount); //Fred added hyphen
		}
	}
	*/
	function process() {
		global $order, $xtPrice;

		$order_total = $this->get_order_total(); //Betrag,  der f�r die Kuponberechnung verwendet wird
		$od_amount = $this->calculate_credit($order_total);	//Kuponbetrag berechnen	
		$this->deduction = $od_amount;		

		if ($od_amount > 0) {
			if ($this->calculate_tax != 'None') { 		   
				$od_amount = $this->new_calculate_tax_deduction($od_amount,$order_total);
			}
			$order->info['total'] = $order->info['total'] - $od_amount;
			$order->info['deduction'] = $od_amount;			
			$order->info['subtotal'] = $order->info['subtotal'] - $od_amount;					
			$this->output[] = array ('title' => $this->title.':'.$this->coupon_code.':', 'text' => '<strong><font color="#ff0000">-'.$xtPrice->xtcFormat($od_amount, true).'</font></strong>', 'value' => $od_amount); //Fred added hyphen
		}
	}
	//EOF -web28- 2010-05-23 - BUGFIX - tax_deduction, $order->info['subtotal']

	function selection_test() {
		return false;
	}

	function pre_confirmation_check($order_total) {

		return $this->calculate_credit($order_total);
	}

	function use_credit_amount() {
		return $output_string;
	}

	function credit_selection() {
		/*
			$selection_string = '';
			$selection_string .= '<tr>' . "\n";
			$selection_string .= ' <td  width="10">' . xtc_draw_separator('pixel_trans.gif', '10', '1') .'</td>';
			$selection_string .= ' <td  nowrap class="main">' . "\n";
			$selection_string .=  TEXT_ENTER_COUPON_CODE . '</td>';
			$selection_string .= ' <td  align="right">'. xtc_draw_input_field('gv_redeem_code').'</td>';
			$selection_string .= ' <td  width="10">' . xtc_draw_separator('pixel_trans.gif', '10', '1') . '</td>';
			$selection_string .= '</tr>' . "\n";
		    */
		return false;
	}

	function collect_posts() {
		global $xtPrice;
		if ($_POST['gv_redeem_code']) {

			// get some info from the coupon table
			$coupon_query = xtc_db_query("select coupon_id, coupon_amount, coupon_type, coupon_minimum_order,uses_per_coupon, uses_per_user, restrict_to_products,restrict_to_categories from ".TABLE_COUPONS." where coupon_code='".$_POST['gv_redeem_code']."' and coupon_active='Y'");
			$coupon_result = xtc_db_fetch_array($coupon_query);

			// SS ?
			if ($coupon_result['coupon_type'] != 'G') {

				if (xtc_db_num_rows($coupon_query) == 0) {
					xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='.urlencode(ERROR_NO_INVALID_REDEEM_COUPON), 'SSL'));
				}

				$date_query = xtc_db_query("select coupon_start_date from ".TABLE_COUPONS." where coupon_start_date <= now() and coupon_code='".$_POST['gv_redeem_code']."'");

				if (xtc_db_num_rows($date_query) == 0) {
					xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='.urlencode(ERROR_INVALID_STARTDATE_COUPON), 'SSL'));
				}

				$date_query = xtc_db_query("select coupon_expire_date from ".TABLE_COUPONS." where coupon_expire_date >= now() and coupon_code='".$_POST['gv_redeem_code']."'");

				if (xtc_db_num_rows($date_query) == 0) {
					xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='.urlencode(ERROR_INVALID_FINISDATE_COUPON), 'SSL'));
				}

				$coupon_count = xtc_db_query("select coupon_id from ".TABLE_COUPON_REDEEM_TRACK." where coupon_id = '".$coupon_result['coupon_id']."'");
				$coupon_count_customer = xtc_db_query("select coupon_id from ".TABLE_COUPON_REDEEM_TRACK." where coupon_id = '".$coupon_result['coupon_id']."' and customer_id = '".$_SESSION['customer_id']."'");

				if (xtc_db_num_rows($coupon_count) >= $coupon_result['uses_per_coupon'] && $coupon_result['uses_per_coupon'] > 0) {
					xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='.urlencode(ERROR_INVALID_USES_COUPON.$coupon_result['uses_per_coupon'].TIMES), 'SSL'));
				}

				if (xtc_db_num_rows($coupon_count_customer) >= $coupon_result['uses_per_user'] && $coupon_result['uses_per_user'] > 0) {
					xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='.urlencode(ERROR_INVALID_USES_USER_COUPON.$coupon_result['uses_per_user'].TIMES), 'SSL'));
				}
				if ($coupon_result['coupon_type'] == 'S') {
					$coupon_amount = $order->info['shipping_cost'];
				} else {
					$coupon_amount = $xtPrice->xtcFormat($coupon_result['coupon_amount'], true).' ';
				}
				if ($coupon_result['coupon_type'] == 'P')
					$coupon_amount = $coupon_result['coupon_amount'].'% ';
				if ($coupon_result['coupon_minimum_order'] > 0)
					$coupon_amount .= 'on orders greater than '.$coupon_result['coupon_minimum_order'];

				$_SESSION['cc_id'] = $coupon_result['coupon_id']; //Fred ADDED, set the global and session variable

			}
			if ($_POST['submit_redeem_coupon_x'] && !$_POST['gv_redeem_code'])
				xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='.urlencode(ERROR_NO_REDEEM_CODE), 'SSL'));
			}
	}

	function calculate_credit($amount) {
		global $order;

		$od_amount = 0;
		if (isset ($_SESSION['cc_id'])) {
			$coupon_query = xtc_db_query("select coupon_code from ".TABLE_COUPONS." where coupon_id = '".$_SESSION['cc_id']."'");
			if (xtc_db_num_rows($coupon_query) != 0) {
				$coupon_result = xtc_db_fetch_array($coupon_query);
				$this->coupon_code = $coupon_result['coupon_code'];
				$coupon_get = xtc_db_query("select coupon_amount, coupon_minimum_order, restrict_to_products, restrict_to_categories, coupon_type from ".TABLE_COUPONS." where coupon_code = '".$coupon_result['coupon_code']."'");
				$get_result = xtc_db_fetch_array($coupon_get);
				$c_deduct = $get_result['coupon_amount'];
				
				
				if ($get_result['coupon_type'] == 'S')
					$c_deduct = $order->info['shipping_cost'];
					
				if ($get_result['coupon_type']=='S' && $get_result['coupon_amount'] > 0 ) $c_deduct = $order->info['shipping_cost'] + $get_result['coupon_amount'];
				
				if ($get_result['coupon_minimum_order'] <= $this->get_order_total()) {

					if ($get_result['restrict_to_products'] || $get_result['restrict_to_categories']) {
						//BOF -web28- 2010-05-21 - FIX - restrict  max coupon amount
						$pr_c = 0;
						//EOF -web28- 2010-05-21 - FIX - restrict  max coupon amount
						for ($i = 0; $i < sizeof($order->products); $i ++) {
							if ($get_result['restrict_to_products']) {
								$pr_ids = explode(",", $get_result['restrict_to_products']); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3
								
								
								//BUG FOUND 09.04.2009
								//for ($ii = 0; $p < count($pr_ids); $ii ++) {
								
								//FIXT 09.04.2009
								for ($ii = 0; $ii < count($pr_ids); $ii ++) {
								// FIX END	
								
									
									
									if ($pr_ids[$ii] == xtc_get_prid($order->products[$i]['id'])) {
										if ($get_result['coupon_type'] == 'P') {
											
											$od_amount = $amount * $get_result['coupon_amount'] / 100;
											$pr_c = $this->product_price($pr_ids[$ii]); //Fred 2003-10-28, fix for the row above, otherwise the discount is calc based on price excl VAT!
											$pod_amount = round($pr_c*10)/10*$c_deduct/100;
											$od_amount = $od_amount + $pod_amount;
										
										} else {
											$od_amount = $c_deduct;
											//BOF -web28- 2010-05-21 - FIX - restrict  max coupon amount
											$pr_c += $this->product_price($pr_ids[$ii]);
											//EOF -web28- 2010-05-21 - FIX - restrict  max coupon amount
										}
									}
								}
							} else {
								$cat_ids = explode(",", $get_result['restrict_to_categories']); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3
								for ($i = 0; $i < sizeof($order->products); $i ++) {
									$my_path = xtc_get_product_path(xtc_get_prid($order->products[$i]['id']));
									$sub_cat_ids = explode("_", $my_path); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3
									for ($iii = 0; $iii < count($sub_cat_ids); $iii ++) {
										for ($ii = 0; $ii < count($cat_ids); $ii ++) {
											if ($sub_cat_ids[$iii] == $cat_ids[$ii]) {
												if ($get_result['coupon_type'] == 'P') {
													$pr_c = $this->product_price(xtc_get_prid($order->products[$i]['id'])); //Fred 2003-10-28, fix for the row above, otherwise the discount is calc based on price excl VAT!
													$pod_amount = round($pr_c*10)/10*$c_deduct/100;
													$od_amount = $od_amount + $pod_amount;
                                                       continue 3;      // v5.13a Tanaka 2005-4-30: to prevent double counting of a product discount
												} else {
													$od_amount = $c_deduct;
													//BOF -web28- 2010-05-21 - FIX - restrict  max coupon amount
													$pr_c += $this->product_price(xtc_get_prid($order->products[$i]['id']));
													//EOF -web28- 2010-05-21 - FIX - restrict  max coupon amount
													continue 3;
     											}
											}
										}
									}
								}
								
							}
						}
						//BOF -web28- 2010-05-21 - FIX - restrict  max coupon amount
						if ($get_result['coupon_type'] == 'F' && $od_amount > $pr_c )  {$od_amount = $pr_c;}
						//EOF -web28- 2010-05-21 - FIX - restrict  max coupon amount
					} else {
						if ($get_result['coupon_type'] != 'P') {
							$od_amount = $c_deduct;
						} else {
							$od_amount = $amount * $get_result['coupon_amount'] / 100;
						}
					}
				}
			}
			if ($od_amount > $amount)
				$od_amount = $amount;
		}
		return $od_amount;
	}
	
	//BOF -web28- 2010-05-23 - BUGFIX - tax_deduction
	/*
	function calculate_tax_deduction($amount, $od_amount, $method) {
		global $order;

		$coupon_query = xtc_db_query("select coupon_code from ".TABLE_COUPONS." where coupon_id = '".$_SESSION['cc_id']."'");
		if (xtc_db_num_rows($coupon_query) != 0) {
			$coupon_result = xtc_db_fetch_array($coupon_query);
			$coupon_get = xtc_db_query("select coupon_amount, coupon_minimum_order, restrict_to_products, restrict_to_categories, coupon_type from ".TABLE_COUPONS." where coupon_code = '".$coupon_result['coupon_code']."'");
			$get_result = xtc_db_fetch_array($coupon_get);

			if ($get_result['coupon_type'] != 'S') {

				//RESTRICTION--------------------------------
				if ($get_result['restrict_to_products'] || $get_result['restrict_to_categories']) {
					// What to do here.
					// Loop through all products and build a list of all product_ids, price, tax class
					// at the same time create total net amount.
					// then
					// for percentage discounts. simply reduce tax group per product by discount percentage
					// or
					// for fixed payment amount
					// calculate ratio based on total net
					// for each product reduce tax group per product by ratio amount.
					$products = $_SESSION['cart']->get_products();
					
					$valid_product = false;
					for ($i = 0; $i < sizeof($products); $i ++) {
						$valid_product = false;
					
						$t_prid = xtc_get_prid($products[$i]['id']);
						$cc_query = xtc_db_query("select products_tax_class_id from ".TABLE_PRODUCTS." where products_id = '".$t_prid."'");
						$cc_result = xtc_db_fetch_array($cc_query);
						
						if ($get_result['restrict_to_products']) {
							$pr_ids = explode(",", $get_result['restrict_to_products']); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3
							for ($p = 0; $p < sizeof($pr_ids); $p ++) {
								if ($pr_ids[$p] == $t_prid)
									$valid_product = true;
							}
						}
						                                          
						if ($get_result['restrict_to_categories']) {
	                        // v5.13a Tanaka 2005-4-30:  New code, this correctly identifies valid products in subcategories
	                        $cat_ids = explode(",", $get_result['restrict_to_categories']); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3
	                        $my_path = xtc_get_product_path($t_prid);
	                        $sub_cat_ids = explode("_", $my_path); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3
	                        for ($iii = 0; $iii < count($sub_cat_ids); $iii++) {
	                            for ($ii = 0; $ii < count($cat_ids); $ii++) {
	                                if ($sub_cat_ids[$iii] == $cat_ids[$ii]) {
	                                    $valid_product = true;
	                                    continue 2;
	                                }
	                            }
	                            
	                        }
						}					 
						
						if ($valid_product) {
							$price_excl_vat = $products[$i]['final_price'] * $products[$i]['quantity'];
							$price_incl_vat = $this->product_price($t_prid);
							$valid_array[] = array ('product_id' => $t_prid, 'products_price' => $price_excl_vat, 'products_tax_class' => $cc_result['products_tax_class_id']);
							$total_price += $price_excl_vat; 
						}
					}
					if (sizeof($valid_array) > 0) { // if ($valid_product) {
						if ($get_result['coupon_type'] == 'P') {
							$ratio = $get_result['coupon_amount'] / 100;
						} else {
							$ratio = $od_amount / $total_price;
						}
						if ($get_result['coupon_type'] == 'S')
							$ratio = 1;
						if ($method == 'Credit Note') {
							$tax_rate = xtc_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
							$tax_desc = xtc_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);

							if ($get_result['coupon_type'] == 'P') {
								$tod_amount = $od_amount / (100 + $tax_rate) * $tax_rate;
							} else {
								$tod_amount = $order->info['tax_groups'][$tax_desc] * $od_amount / 100;
							}
							$order->info['tax_groups'][$tax_desc] -= $tod_amount;
							$order->info['total'] -= $tod_amount;
							$order->info['tax'] -= $tod_amount;
						} else {
							for ($p = 0; $p < sizeof($valid_array); $p ++) {
								$tax_rate = xtc_get_tax_rate($valid_array[$p]['products_tax_class'], $order->delivery['country']['id'], $order->delivery['zone_id']);
								$tax_desc = xtc_get_tax_description($valid_array[$p]['products_tax_class'], $order->delivery['country']['id'], $order->delivery['zone_id']);
								if ($tax_rate > 0) {
									$tod_amount = ($valid_array[$p]['products_price'] * $tax_rate) / 100 * $ratio;
									$order->info['tax_groups'][$tax_desc] -= ($valid_array[$p]['products_price'] * $tax_rate) / 100 * $ratio;
									$order->info['total'] -= ($valid_array[$p]['products_price'] * $tax_rate) / 100 * $ratio;
									$order->info['tax'] -= ($valid_array[$p]['products_price'] * $tax_rate) / 100 * $ratio;
								}
							}
						}
					}
				//NO RESTRICTION--------------------------------
				} else {
					if ($get_result['coupon_type'] == 'F') {
						$tod_amount = 0;
						if ($method == 'Credit Note') {
							$tax_rate = xtc_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
							$tax_desc = xtc_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
							$tod_amount = $od_amount / (100 + $tax_rate) * $tax_rate;
							$order->info['tax_groups'][TAX_ADD_TAX.$tax_desc] -= $tod_amount;
						} else {
							reset($order->info['tax_groups']);
							while (list ($key, $value) = each($order->info['tax_groups'])) {
								$ratio1 = $od_amount / ($amount - $order->info['tax_groups'][$key]); 
								$tax_rate = xtc_get_tax_rate_from_desc( str_replace(TAX_ADD_TAX, "", $key) );
								$net = $tax_rate * $order->info['tax_groups'][$key];
								if ($net > 0) {
									$god_amount = $od_amount * $tax_rate / (100 + $tax_rate);
									$tod_amount += $god_amount;
									$order->info['tax_groups'][$key] -= $god_amount;
								}
							}
						}
					$order->info['total'] -= $tod_amount;						
$order->info['tax'] -= $tod_amount;
					}
					
					if ($get_result['coupon_type'] == 'P') {
						$tod_amount = 0;
						if ($method == 'Credit Note') {
							$tax_desc = xtc_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
							$tod_amount = $order->info['tax_groups'][$tax_desc] * $od_amount / 100;
							$order->info['tax_groups'][TAX_ADD_TAX.$tax_desc] -= $tod_amount;
						} else {
							reset($order->info['tax_groups']);
							while (list ($key, $value) = each($order->info['tax_groups'])) {
								$god_amout = 0;
								$tax_rate = xtc_get_tax_rate_from_desc( str_replace(TAX_ADD_TAX, "", $key) );
								$net = $tax_rate * $order->info['tax_groups'][$key];
								if ($net > 0) {
									$god_amount = $order->info['tax_groups'][$key] * $get_result['coupon_amount'] / 100;
									$tod_amount += $god_amount;
									$order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount;
								}
							}
						}
						$order->info['tax'] -= $tod_amount;
					}
				}
			}
		}
		return $tod_amount;
	}
	*/	
	
	function new_calculate_tax_deduction($od_amount, $order_total) {
		global $order;
		if ($_SESSION['customers_status']['customers_status_show_price'] != 0) {
		    
			//Wenn der Kupon ohne Steuer definiert wurde, muss die Bestellsumme korrigiert werden
			if ($this->include_tax == 'false'){
				$order_total = $order_total + $order->info['tax'];
			}
			//Gutscheinwert in % berechnen, vereinheitlicht die Berechnungen
			$od_amount_pro = $od_amount/$order_total * 100;			
			reset($order->info['tax_groups']);
			$tax_betrag = 0;
			$tod_amount = 0;
			$tax_rate_amount = xtc_get_tax_rate($this->tax_class); //Steuersatz von Kupon festgelegt - Standard ist 0 !
			// bei $tax_rate = 0 wurde kein Steuersatz definiert		
			while (list ($key, $value) = each($order->info['tax_groups'])) {				
				
				//Steuersumme aus Bestellung ermitteln - ACHTUNG - Unterscheidung mit TAX_ADD_TAX und  TAX_NO_TAX
				$tax_rate_order = xtc_get_tax_rate_from_desc( str_replace(TAX_ADD_TAX, "", $key) );  //inkl. UST
                if ($_SESSION['customers_status']['customers_status_show_price_tax'] != '1') {
					$tax_rate_order = xtc_get_tax_rate_from_desc( str_replace(TAX_NO_TAX, "", $key) );  //exkl. UST
				}
				
				//Steuer neu berechnen							
				$t_flag = false;
				//Wenn ein Kupon Steuersatz definiert ist, dann nur mit diesem Steuersatz die Steuer neu berechnen (DEAKTIVIERT)
				//Testen ob Steuers�tze �bereinstimmen
				//if ($tax_rate_amount > 0 && ($tax_rate_amount - $tax_rate_order < 0.0001)) $t_flag = true;
				//Wenn kein Kupon Steuersatz definiert ist, dann Steuersatz automatisch zuordnen
				if ($tax_rate_amount == 0) $t_flag = true;
				$net = $tax_rate_order * $order->info['tax_groups'][$key];	
				if ($net > 0 && $t_flag) {					
                    //Bei Anzeige von Netto Preisen muss anders gerechnet werden					
					if ($_SESSION['customers_status']['customers_status_show_price_tax'] != '1') { //NETTO Preise
						$god_amount = $order->info['tax_groups'][$key] - $order->info['tax_groups'][$key] * $od_amount_pro / 100;
						$order->info['tax_groups'][$key] = $god_amount; //bei NETTO Preisen ersetzen
					} else { //BRUTTO Preise
						$god_amount = $order->info['tax_groups'][$key] * $od_amount_pro / 100;
						$order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount; //bei BRUTTO Preisen abziehen
					}
					//echo $god_amount . '<br>';
					$tod_amount += $god_amount; //hier wird die Steuer aufaddiert					
				}
				
			}			
			
			//Gesamtsteuer neu berechnen
			$order->info['tax'] -= $tod_amount; //bei BRUTTO Preisen abziehen
			if ($_SESSION['customers_status']['customers_status_show_price_tax'] != '1') {
				$order->info['tax'] = $tod_amount; //bei NETTO Preisen ersetzen
			}
			
			return $od_amount;
		}
	}
	//EOF -web28- 2010-05-23 - BUGFIX - tax_deduction

	function update_credit_account($i) {
		return false;
	}

	function apply_credit() {
		global $insert_id, $REMOTE_ADDR;

		if ($this->deduction != 0) {
			xtc_db_query("insert into ".TABLE_COUPON_REDEEM_TRACK." (coupon_id, redeem_date, redeem_ip, customer_id, order_id) values ('".$_SESSION['cc_id']."', now(), '".$REMOTE_ADDR."', '".$_SESSION['customer_id']."', '".$insert_id."')");
		}
		unset ($_SESSION['cc_id']);
	}

	function get_order_total() {
		global $order, $xtPrice;

		$order_total = $order->info['total'];
		// Check if gift voucher is in cart and adjust total
		$products = $_SESSION['cart']->get_products();
		for ($i = 0; $i < sizeof($products); $i ++) {
			$t_prid = xtc_get_prid($products[$i]['id']);
			$gv_query = xtc_db_query("select products_price, products_tax_class_id, products_model from ".TABLE_PRODUCTS." where products_id = '".$t_prid."'");
			$gv_result = xtc_db_fetch_array($gv_query);
			if (preg_match('/^GIFT/', addslashes($gv_result['products_model']))) { // Hetfield - 2009-08-19 - replaced deprecated function ereg with preg_match to be ready for PHP >= 5.3
				$qty = $_SESSION['cart']->get_quantity($t_prid);
				$products_tax = $xtPrice->TAX[$gv_result['products_tax_class_id']];
				if ($this->include_tax == 'false') {
					$gv_amount = $gv_result['products_price'] * $qty;
				} else {
					$gv_amount = ($gv_result['products_price'] + $xtPrice->calcTax($gv_result['products_price'], $products_tax)) * $qty;
				}
				$order_total = $order_total - $gv_amount;
			}
		}
		if ($this->include_tax == 'false')
			$order_total = $order_total - $order->info['tax'];
		if ($this->include_shipping == 'false')
			$order_total = $order_total - $order->info['shipping_cost'];
		
		//BOF -web28- 2010-05-23 - FIX - unnecessary 
		/*
		// OK thats fine for global coupons but what about restricted coupons
		// where you can only redeem against certain products/categories.
		// and I though this was going to be easy !!!
		$coupon_query = xtc_db_query("select coupon_code from ".TABLE_COUPONS." where coupon_id='".$_SESSION['cc_id']."'");
		if (xtc_db_num_rows($coupon_query) != 0) {
			$coupon_result = xtc_db_fetch_array($coupon_query);
			$coupon_get = xtc_db_query("select coupon_amount, coupon_minimum_order,restrict_to_products,restrict_to_categories, coupon_type from ".TABLE_COUPONS." where coupon_code='".$coupon_result['coupon_code']."'");
			$get_result = xtc_db_fetch_array($coupon_get);
			$in_cat = true;
			if ($get_result['restrict_to_categories']) {
				$cat_ids = explode(",", $get_result['restrict_to_categories']); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3
				$in_cat = false;
				for ($i = 0; $i < count($cat_ids); $i ++) {
					if (is_array($this->contents)) {
						reset($this->contents);
						while (list ($products_id,) = each($this->contents)) {
							$cat_query = xtc_db_query("select products_id from products_to_categories where products_id = '".$products_id."' and categories_id = '".$cat_ids[$i]."'");
							if (xtc_db_num_rows($cat_query) != 0) {
								$in_cat = true;
								$total_price += $this->get_product_price($products_id);
							}
						}
					}
				}
			}
			$in_cart = true;
			if ($get_result['restrict_to_products']) {

				$pr_ids = explode(",", $get_result['restrict_to_products']); // Hetfield - 2009-08-18 - replaced deprecated function split with explode to be ready for PHP >= 5.3

				$in_cart = false;
				$products_array = $_SESSION['cart']->get_products();

				for ($i = 0; $i < sizeof($pr_ids); $i ++) {
					for ($ii = 1; $ii <= sizeof($products_array); $ii ++) {
						if (xtc_get_prid($products_array[$ii -1]['id']) == $pr_ids[$i]) {
							$in_cart = true;
							$total_price += $this->get_product_price($products_array[$ii -1]['id']);
						}
					}
				}
				$order_total = $total_price;
			}
		}
		*/
		//EOF -web28- 2010-05-23 - FIX - unnecessary 
		
		return $order_total;
	}

	function get_product_price($product_id) {
		global $order,$xtPrice;
		$products_id = xtc_get_prid($product_id);
		// products price
		//BOF - 2010-01-19 - Dokuman - ot_coupon Bugfixes
		//$qty = $_SESSION['cart']->contents[$products_id]['qty'];
		$qty = $_SESSION['cart']->contents[$product_id]['qty'];
		//EOF - 2010-01-19 - Dokuman - ot_coupon Bugfixes
		
		//$product_query = xtc_db_query("select products_id, products_price, products_tax_class_id, products_weight from ".TABLE_PRODUCTS." where products_id='".$product_id."'");
		$product_query = xtc_db_query("select products_id, products_price, products_tax_class_id, products_weight from ".TABLE_PRODUCTS." where products_id='".$products_id."'");		
		if ($product = xtc_db_fetch_array($product_query)) {
			$prid = $product['products_id'];


			if ($this->include_tax == 'true') {
$total_price += $qty * $xtPrice->xtcGetPrice($product['products_id'], $format = false, 1, $product['products_tax_class_id'], $product['products_price'], 1);
$_SESSION['total_price']=$total_price;
			} else {
$total_price += $qty * $xtPrice->xtcGetPrice($product['products_id'], $format = false, 1, 0, $product['products_price'], 1);
			}

			// attributes price
			if (isset ($_SESSION['cart']->contents[$product_id]['attributes'])) {
				reset($_SESSION['cart']->contents[$product_id]['attributes']);
				while (list ($option, $value) = each($_SESSION['cart']->contents[$product_id]['attributes'])) {
					$attribute_price_query = xtc_db_query("select options_values_price, price_prefix from ".TABLE_PRODUCTS_ATTRIBUTES." where products_id = '".$prid."' and options_id = '".$option."' and options_values_id = '".$value."'");
					$attribute_price = xtc_db_fetch_array($attribute_price_query);
					if ($attribute_price['price_prefix'] == '+') {
						if ($this->include_tax == 'true') {
							$total_price += $qty * ($attribute_price['options_values_price'] + xtc_calculate_tax($attribute_price['options_values_price'], $products_tax));
						} else {
							$total_price += $qty * ($attribute_price['options_values_price']);
						}
					} else {
						if ($this->include_tax == 'true') {
							$total_price -= $qty * ($attribute_price['options_values_price'] + xtc_calculate_tax($attribute_price['options_values_price'], $products_tax));
						} else {
							$total_price -= $qty * ($attribute_price['options_values_price']);
						}
					}
				}
			}
		}
		if ($this->include_shipping == 'true') {

			$total_price += $order->info['shipping_cost'];
		}
		return $total_price;
	}

	function product_price($product_id) {
		$total_price = $this->get_product_price($product_id);
		if ($this->include_shipping == 'true')
			$total_price -= $order->info['shipping_cost'];
		return $total_price;
	}

	function check() {
		if (!isset ($this->check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_ORDER_TOTAL_COUPON_STATUS'");
			$this->check = xtc_db_num_rows($check_query);
		}

		return $this->check;
	}

	function keys() {
		return array ('MODULE_ORDER_TOTAL_COUPON_STATUS', 
					  'MODULE_ORDER_TOTAL_COUPON_SORT_ORDER', 
					  'MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING', 
					  'MODULE_ORDER_TOTAL_COUPON_INC_TAX',
//BOF -web28- 2010-05-23 - FIX - unnecessary  COUPON_TAX_CLASS					  
					  'MODULE_ORDER_TOTAL_COUPON_CALC_TAX'); 
					  //'MODULE_ORDER_TOTAL_COUPON_TAX_CLASS');
//EOF -web28- 2010-05-23 - FIX - unnecessary  COUPON_TAX_CLASS	
	}

	function install() {
		xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('', 'MODULE_ORDER_TOTAL_COUPON_STATUS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('', 'MODULE_ORDER_TOTAL_COUPON_SORT_ORDER', '25', '6', '2', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('', 'MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING', 'false', '6', '5', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('', 'MODULE_ORDER_TOTAL_COUPON_INC_TAX', 'true', '6', '6','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
		//BOF -web28- 2010-05-23 - FIX - unnecessary  Credit Note
		//xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('', 'MODULE_ORDER_TOTAL_COUPON_CALC_TAX', 'Standard', '6', '7','xtc_cfg_select_option(array(\'None\', \'Standard\', \'Credit Note\'), ', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('', 'MODULE_ORDER_TOTAL_COUPON_CALC_TAX', 'Standard', '6', '7','xtc_cfg_select_option(array(\'None\', \'Standard\'), ', now())");
		//EOF -web28- 2010-05-23 - FIX - unnecessary  unnecessary  Credit Note
		xtc_db_query("insert into ".TABLE_CONFIGURATION." (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('', 'MODULE_ORDER_TOTAL_COUPON_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
	}

	function remove() {
		$keys = '';
		$keys_array = $this->keys();
		for ($i = 0; $i < sizeof($keys_array); $i ++) {
			$keys .= "'".$keys_array[$i]."',";
		}
		$keys = substr($keys, 0, -1);

		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in (".$keys.")");
	}
}
?>
