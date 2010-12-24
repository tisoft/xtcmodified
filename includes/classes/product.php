<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(Coding Standards); www.oscommerce.com
   (c) 2006 XT-Commerce (product.php 1316 2005-10-21)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class product {

  /**
  *
  * Constructor
  *
  */
  function product($pID = 0) {
    // BOF - DokuMan - 2010-08-28 - typecasting
    //$this->pID = $pID;
    $this->pID = (int)$pID;
    // EOF - DokuMan - 2010-08-28 - typecasting

    // BOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
    //$this->useStandardImage=false;
    $this->useStandardImage=true;
    // EOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
    $this->standardImage='noimage.gif';
    // BOF - DokuMan - 2010-03-12 - bugfix, wrong comparison
    //if ($pID = 0) {
    if ($pID == 0) {
    // EOF - DokuMan - 2010-03-12 - bugfix, wrong comparison
      $this->isProduct = false;
      return;
    }
    // query for Product
    $group_check = "";
    if (GROUP_CHECK == 'true') {
      $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
    }

    $fsk_lock = "";
    if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
      $fsk_lock = ' and p.products_fsk18!=1';
    }

    $product_query = "select * FROM
                               ".TABLE_PRODUCTS." p,
                               ".TABLE_PRODUCTS_DESCRIPTION." pd
                               where p.products_status = 1
                               and p.products_id = ".$this->pID."
                               and pd.products_id = p.products_id
                               ".$group_check
                               .$fsk_lock."
                               and pd.language_id = ".(int) $_SESSION['languages_id'];

    $product_query = xtDBquery($product_query);

    if (!xtc_db_num_rows($product_query, true)) {
      $this->isProduct = false;
    } else {
      $this->isProduct = true;
      $this->data = xtc_db_fetch_array($product_query, true);
    }
  }

  /**
  *
  *  Query for attributes count
  *
  */

  function getAttributesCount() {

    $products_attributes_query = xtDBquery("
                            select count(*) as total
                            from ".TABLE_PRODUCTS_OPTIONS." popt,
                                 ".TABLE_PRODUCTS_ATTRIBUTES." patrib
                            where patrib.products_id=".$this->pID."
                            and patrib.options_id = popt.products_options_id
                            and popt.language_id = ".(int) $_SESSION['languages_id']
                            );
    $products_attributes = xtc_db_fetch_array($products_attributes_query, true);
    return $products_attributes['total'];
  }

  /**
  *
  * Query for reviews count
  *
  */

  function getReviewsCount() {
    $reviews_query = xtDBquery("
                            select count(*) as total
                            from ".TABLE_REVIEWS." r,
                                 ".TABLE_REVIEWS_DESCRIPTION." rd
                            where r.products_id = ".$this->pID."
                            and r.reviews_id = rd.reviews_id
                            and rd.languages_id = ".(int)$_SESSION['languages_id']."
                            and rd.reviews_text !=''
                            ");
    $reviews = xtc_db_fetch_array($reviews_query, true);
    return $reviews['total'];
  }

  /**
  *
  * select reviews
  *
  */

  function getReviews() {

    $data_reviews = array ();
    $reviews_query = xtDBquery("select
                                      r.reviews_rating,
                                      r.reviews_id,
                                      r.customers_name,
                                      r.date_added,
                                      r.last_modified,
                                      r.reviews_read,
                                      rd.reviews_text
                                      from ".TABLE_REVIEWS." r,
                                      ".TABLE_REVIEWS_DESCRIPTION." rd
                                      where r.products_id = '".$this->pID."'
                                      and r.reviews_id = rd.reviews_id
                                      and rd.languages_id = '".(int)$_SESSION['languages_id']."'
                                      order by reviews_id DESC
                                      ");
    if (xtc_db_num_rows($reviews_query, true)) {
      $row = 0;
      $data_reviews = array ();
      while ($reviews = xtc_db_fetch_array($reviews_query, true)) {
        $row ++;
        $data_reviews[] = array ('AUTHOR' => $reviews['customers_name'],
                                 'DATE' => xtc_date_short($reviews['date_added']),
                                 'RATING' => xtc_image('templates/'.CURRENT_TEMPLATE.'/img/stars_'.$reviews['reviews_rating'].'.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])),
                                 'TEXT' => $reviews['reviews_text']);
        if ($row == PRODUCT_REVIEWS_VIEW)
          break;
      }
    }
    return $data_reviews;

  }

  /**
  *
  * return model if set, else return name
  *
  */

  function getBreadcrumbModel() {

    if ($this->data['products_model'] != "")
      return $this->data['products_model'];
    return $this->data['products_name'];
  }

  /**
  *
  * get also purchased products related to current
  *
  */

  function getAlsoPurchased() {
    global $xtPrice;

    $module_content = array ();

    $fsk_lock = "";
    if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
      $fsk_lock = ' and p.products_fsk18!=1';
    }
    $group_check = "";
    if (GROUP_CHECK == 'true') {
      $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
    }

    // BOF - vr - 2010-04-21 make sql human readable, update to SQL-92-Standard
    $orders_query = "select p.products_fsk18,
                            p.products_id,
                            p.products_price,
                            p.products_tax_class_id,
                            p.products_image,
                            pd.products_name,
                            p.products_vpe,
                            p.products_vpe_status,
                            p.products_vpe_value,
                            pd.products_short_description
                            from ".TABLE_ORDERS_PRODUCTS." op1
                            join ".TABLE_ORDERS_PRODUCTS." op2 on op2.orders_id = op1.orders_id
                            join ".TABLE_ORDERS." o on o.orders_id = op2.orders_id
                            join ".TABLE_PRODUCTS." p on p.products_id = op2.products_id
                            join ".TABLE_PRODUCTS_DESCRIPTION." pd on pd.products_id = op2.products_id
                            where op1.products_id = ".$this->pID."
                            and op2.products_id != ".$this->pID."
                            and p.products_status = 1
                            and pd.language_id = ".(int) $_SESSION['languages_id']
                            .$group_check
                            .$fsk_lock."
                            group by p.products_id
                            order by o.date_purchased desc
                            limit ".MAX_DISPLAY_ALSO_PURCHASED;
    // EOF - vr - 2010-04-21 make sql human readable
    $orders_query = xtDBquery($orders_query);
    while ($orders = xtc_db_fetch_array($orders_query, true)) {
      $module_content[] = $this->buildDataArray($orders);
    }
    return $module_content;
  }

  /**
  *
  *
  *  Get Cross sells
  *
  *
  */
  function getCrossSells() {
    global $xtPrice;

    $cs_groups = "SELECT products_xsell_grp_name_id FROM ".TABLE_PRODUCTS_XSELL." WHERE products_id = '".$this->pID."' GROUP BY products_xsell_grp_name_id";
    $cs_groups = xtDBquery($cs_groups);
    $cross_sell_data = array ();
    if (xtc_db_num_rows($cs_groups, true) > 0) {
      while ($cross_sells = xtc_db_fetch_array($cs_groups, true)) {
        $fsk_lock = '';
        if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
          $fsk_lock = ' and p.products_fsk18!=1';
        }
        $group_check = "";
        if (GROUP_CHECK == 'true') {
          $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
        }

        $cross_query = "select p.products_fsk18,
                               p.products_tax_class_id,
                               p.products_id,
                               p.products_image,
                               pd.products_name,
                               pd.products_short_description,
                               p.products_fsk18,
                               p.products_price,
                               p.products_vpe,
                               p.products_vpe_status,
                               p.products_vpe_value,
                               xp.sort_order
                          from ".TABLE_PRODUCTS_XSELL." xp,
                               ".TABLE_PRODUCTS." p,
                               ".TABLE_PRODUCTS_DESCRIPTION." pd
                         where xp.products_id = ".$this->pID."
                           and xp.xsell_id = p.products_id "
                               .$fsk_lock
                               .$group_check."
                           and p.products_id = pd.products_id
                           and xp.products_xsell_grp_name_id='".$cross_sells['products_xsell_grp_name_id']."'
                           and pd.language_id = ".(int)$_SESSION['languages_id']."
                           and p.products_status = 1
                         order by xp.sort_order asc";

        $cross_query = xtDBquery($cross_query);
        if (xtc_db_num_rows($cross_query, true) > 0)
          $cross_sell_data[$cross_sells['products_xsell_grp_name_id']] = array (
                                                                                'GROUP' => xtc_get_cross_sell_name($cross_sells['products_xsell_grp_name_id']),
                                                                                'PRODUCTS' => array ());
        while ($xsell = xtc_db_fetch_array($cross_query, true)) {
          $cross_sell_data[$cross_sells['products_xsell_grp_name_id']]['PRODUCTS'][] = $this->buildDataArray($xsell);
        }
      }
      return $cross_sell_data;
    }
  }

  /**
  *
  * get reverse cross sells
  *
  */

  function getReverseCrossSells() {
    global $xtPrice;
    $fsk_lock = '';
    if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
      $fsk_lock = ' and p.products_fsk18!=1';
    }
    $group_check = '';
    if (GROUP_CHECK == 'true') {
      $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
    }

    $cross_query = xtDBquery("select p.products_fsk18,
                                     p.products_tax_class_id,
                                     p.products_id,
                                     p.products_image,
                                     pd.products_name,
                                     pd.products_short_description,
                                     p.products_fsk18,
                                     p.products_price,
                                     p.products_vpe,
                                     p.products_vpe_status,
                                     p.products_vpe_value,
                                     xp.sort_order
                                     from ".TABLE_PRODUCTS_XSELL." xp,
                                     ".TABLE_PRODUCTS." p,
                                     ".TABLE_PRODUCTS_DESCRIPTION." pd
                                     where xp.xsell_id = '".$this->pID."'
                                     and xp.products_id = p.products_id "
                                     .$fsk_lock
                                     .$group_check."
                                     and p.products_id = pd.products_id
                                     and pd.language_id = ".(int)$_SESSION['languages_id']."
                                     and p.products_status = 1
                                     order by xp.sort_order asc");

    $cross_sell_data = array(); //DokuMan - 2010-03-12 - set undefined array
    while ($xsell = xtc_db_fetch_array($cross_query, true)) {
      $cross_sell_data[] = $this->buildDataArray($xsell);
    }
    return $cross_sell_data;
  }


  function getGraduated() {
    global $xtPrice;

    $discount = $xtPrice->xtcCheckDiscount($this->pID);  // Hetfield - 2010-03-15 - BUGFIX show VPE with discount for graduated prices
    $staffel_query = xtDBquery("SELECT
                                       quantity,
                                       personal_offer
                                  FROM ".TABLE_PERSONAL_OFFERS_BY.(int) $_SESSION['customers_status']['customers_status_id']."
                                 WHERE products_id = ".$this->pID."
                              ORDER BY quantity ASC");
    $staffel = array ();
    while ($staffel_values = xtc_db_fetch_array($staffel_query, true)) {
      $staffel[] = array (
                          'stk' => $staffel_values['quantity'],
                          'price' => $staffel_values['personal_offer']
                         );
    }
    $staffel_data = array ();
    for ($i = 0, $n = sizeof($staffel); $i < $n; $i ++) {
      //BOF - web28 - 2010-07-13 - BUGFIX display same quantity only once for graduated prices / FIX max value info for graduated prices
      /*
      if ($staffel[$i]['stk'] == 1) {
        $quantity = $staffel[$i]['stk'];
        if ($staffel[$i +1]['stk'] != '')
          $quantity = $staffel[$i]['stk'].'-'. ($staffel[$i +1]['stk'] - 1);
      } else {
        $quantity = ' > '.$staffel[$i]['stk'];
        if ($staffel[$i +1]['stk'] != '')
          $quantity = $staffel[$i]['stk'].'-'. ($staffel[$i +1]['stk'] - 1);
      }
      */
      //if ($staffel[$i]['stk'] == 1 || $staffel[$i +1]['stk'] != ''){
      if ($staffel[$i]['stk'] == 1 || (array_key_exists($i +1, $staffel) && $staffel[$i +1]['stk'] != '')){ //DokuMan - 2010-10-13 - added array_key_exists()
        $quantity = $staffel[$i]['stk'];
        //if ($staffel[$i +1]['stk'] != '' && $staffel[$i +1]['stk'] != $staffel[$i]['stk'] + 1)
        if (array_key_exists($i + 1, $staffel) && $staffel[$i +1]['stk'] != '' && $staffel[$i +1]['stk'] != $staffel[$i]['stk'] + 1) //DokuMan - 2010-10-13 - added array_key_exists()
          $quantity .= ' - '. ($staffel[$i +1]['stk'] - 1);
      } else {
        $quantity = GRADUATED_PRICE_MAX_VALUE.' '.$staffel[$i]['stk'];
      }
      //EOF - web28 - 2010-07-13 - BUGFIX display same quantity only once for graduated prices  /FIX max value info for graduated prices
      $vpe = '';
      // BOF - Hetfield - 2009-08-24 - BUGFIX show VPE for graduated prices
      if (isset($this->data) && $this->data['products_vpe_status'] == 1 && $this->data['products_vpe_value'] != 0.0 && $staffel[$i]['price'] > 0) {
        $vpe = $staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount;
        $vpe = $vpe * (1 / $this->data['products_vpe_value']);
        $vpe = BASICPRICE_VPE_TEXT.$xtPrice->xtcFormat($vpe, true, $this->data['products_tax_class_id']).TXT_PER.xtc_get_vpe_name($this->data['products_vpe']);
      }
      // EOF - Hetfield - 2009-08-24 - BUGFIX show VPE for graduated prices
      $staffel_data[$i] = array ('QUANTITY' => $quantity, 'VPE' => $vpe, 'PRICE' => $xtPrice->xtcFormat($staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount, true, $this->data['products_tax_class_id']));
    }
    return $staffel_data;
  }

  /**
  *
  * valid flag
  *
  */
  function isProduct() {
    return $this->isProduct;
  }

  // beta
  function getBuyNowButton($id, $name) {
    global $PHP_SELF;
    return '<a href="'.xtc_href_link(basename($PHP_SELF), 'action=buy_now&BUYproducts_id='.$id.'&'.xtc_get_all_get_params(array ('action')), 'NONSSL').'">'.xtc_image_button('button_buy_now.gif', TEXT_BUY.$name.TEXT_NOW).'</a>';
  }

  function getVPEtext($product, $price) {
    global $xtPrice;
    require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');
    if (!is_array($product))
      $product = $this->data;
    if (isset($product['products_vpe_status']) && $product['products_vpe_status'] == 1 && $product['products_vpe_value'] != 0.0 && $price > 0) {
      return $xtPrice->xtcFormat($price * (1 / $product['products_vpe_value']), true).TXT_PER.xtc_get_vpe_name($product['products_vpe']);
    }
    return;
  }

  function buildDataArray(&$array,$image='thumbnail') {
    global $xtPrice,$main;

    //$tax_rate = $xtPrice->TAX[$array['products_tax_class_id']];
    $tax_rate = isset($xtPrice->TAX[$array['products_tax_class_id']]) ? $xtPrice->TAX[$array['products_tax_class_id']] : 0; //DokuMan: set Undefined index

    $products_price = $xtPrice->xtcGetPrice($array['products_id'], $format = true, 1, $array['products_tax_class_id'], $array['products_price'], 1);

    $buy_now = ''; //DokuMan: Undefined variable: buy_now

    if ($_SESSION['customers_status']['customers_status_show_price'] != '0') {
      if ($_SESSION['customers_status']['customers_fsk18'] == '1') {
        if (isset($array['products_fsk18']) && $array['products_fsk18'] == '0')
          $buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);
      } else {
        $buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);
      }
    }

    //BOF - DokuMan - 2010-02-26 - Set Undefined index: products_shippingtime
    //$shipping_status_name = $main->getShippingStatusName($array['products_shippingtime']);
    //$shipping_status_image = $main->getShippingStatusImage($array['products_shippingtime']);
    if (isset($array['products_shippingtime'])) {
      $shipping_status_name = $main->getShippingStatusName($array['products_shippingtime']);
      $shipping_status_image = $main->getShippingStatusImage($array['products_shippingtime']);
    } else {
      $shipping_status_name = '';
      $shipping_status_image = '';
    }
    //EOF - DokuMan - 2010-02-26 - Set Undefined index: products_shippingtime

    $productData = array (
                          'PRODUCTS_NAME' => $array['products_name'],
                          'COUNT' => isset($array['ID']) ? $array['ID'] : 0,
                          'PRODUCTS_ID'=> $array['products_id'],
                          'PRODUCTS_MODEL'=> isset($array['products_model']) ? $array['products_model'] : '',
                          'PRODUCTS_VPE' => $this->getVPEtext($array, $products_price['plain']),
                          'PRODUCTS_IMAGE' => $this->productImage($array['products_image'], $image),
                          'PRODUCTS_LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name'])),
                          'PRODUCTS_PRICE' => $products_price['formated'],
                          'PRODUCTS_TAX_INFO' => $main->getTaxInfo($tax_rate),
                          'PRODUCTS_SHIPPING_LINK' => $main->getShippingLink(),
                          'PRODUCTS_BUTTON_BUY_NOW' => $buy_now,
                          'PRODUCTS_SHIPPING_NAME'=>$shipping_status_name,
                          'PRODUCTS_SHIPPING_IMAGE'=>$shipping_status_image,
                          'PRODUCTS_DESCRIPTION' => isset($array['products_description']) ? $array['products_description'] : '', //DokuMan - 2010-02-26 - set Undefined index
                          'PRODUCTS_EXPIRES' => isset($array['expires_date']) ? $array['expires_date'] : 0, //DokuMan - 2010-02-26 - set Undefined index
                          'PRODUCTS_CATEGORY_URL' => isset($array['cat_url']) ? $array['cat_url'] : '', //DokuMan - 2010-02-26 - set Undefined index
                          'PRODUCTS_SHORT_DESCRIPTION' => isset($array['products_short_description']) ? $array['products_short_description'] : '', //DokuMan - 2010-02-26 - set Undefined index
                          'PRODUCTS_FSK18' => isset($array['products_fsk18']) ? $array['products_fsk18'] : 0, //DokuMan - 2010-02-26 - set Undefined index
                          //BOF - GTB - 2010-08-27 make Button Details global
                          'PRODUCTS_BUTTON_DETAILS' => '<a href="'.xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name'])).'">'.xtc_image_button('button_product_more.gif', $array['products_name'].TEXT_NOW).'</a>',
                          //EOF - GTB - 2010-08-27 make Button Details global
                          //BOF - Tomcraft - 2010-07-15 - Added PRODUCTS_QUANTITY for further use in template
                          'PRODUCTS_QUANTITY' => isset($array['products_quantity']) ? $array['products_quantity'] : '',
                          //EOF - Tomcraft - 2010-07-15 - Added PRODUCTS_QUANTITY for further use in template
                         );

    return $productData;
  }

  function productImage($name, $type) {
    switch ($type) {
      case 'info' :
        $path = DIR_WS_INFO_IMAGES;
        break;
      case 'thumbnail' :
        $path = DIR_WS_THUMBNAIL_IMAGES;
        break;
      case 'popup' :
        $path = DIR_WS_POPUP_IMAGES;
        break;
    }

    // BOF - vr - 2010-04-09 no distinction between "name is null" and "name == ''"
    // if ($name == '')) {
    if (empty($name)) {
      // EOF - vr - 2010-04-09 no distinction between "name is null" and "name == ''"
      // BOF - Tomcraft - 2009-11-12 - noimage.gif is displayed, when no image is defined
      //if ($this->useStandardImage == 'true' && $this->standardImage != '') // comment in when "noimage.gif" should be displayed when there is no image defined in the database
      //  return $path.$this->standardImage; // comment in when "noimage.gif" should be displayed when there is no image defined in the database
      return $name; // comment out when "noimage.gif" should be displayed when there is no image defined in the database
      // EOF - Tomcraft - 2009-11-12 - noimage.gif is displayed, when no image is defined
    } else {
      // check if image exists
      if (!file_exists($path.$name)) {
        if ($this->useStandardImage == 'true' && $this->standardImage != '')
          $name = $this->standardImage;
      }
      //BOF - GTB - 2010-08-03 - Security Fix - Base
      return DIR_WS_BASE.$path.$name;
      //return $path.$name;
      //EOF - GTB - 2010-08-03 - Security Fix - Base
    }
  }
}
?>