<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(create_account.php,v 1.63 2003/05/28); www.oscommerce.com
   (c) 2003 nextcommerce (create_account.php,v 1.27 2003/08/24); www.nextcommerce.org
   (c) 2006 XT-Commerce (create_guest_account.php 176 2007-02-15)

   Released under the GNU General Public License

   Guest account idea by Ingo T. <xIngox@web.de>
   ---------------------------------------------------------------------------------------*/
define('DISPLAY_PRIVACY_CHECK','true');
include ('includes/application_top.php');

if (ACCOUNT_OPTIONS == 'account')
  xtc_redirect(FILENAME_DEFAULT);

if (isset ($_SESSION['customer_id'])) {
  xtc_redirect(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}

// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

// include needed functions
//require_once (DIR_FS_INC . 'xtc_draw_radio_field.inc.php');
require_once (DIR_FS_INC . 'xtc_get_country_list.inc.php');
//require_once (DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php');
//require_once (DIR_FS_INC . 'xtc_draw_password_field.inc.php');
require_once (DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC . 'xtc_create_password.inc.php');
//require_once (DIR_FS_INC . 'xtc_draw_hidden_field.inc.php');
//require_once (DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php');
require_once (DIR_FS_INC . 'xtc_get_geo_zone_code.inc.php');

// needs to be included earlier to set the success message in the messageStack
//  require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . FILENAME_CREATE_ACCOUNT);

$process = false;
if (isset ($_POST['action']) && ($_POST['action'] == 'process')) {
  $process = true;

  if (ACCOUNT_GENDER == 'true')
  //BOF - Dokuman - 2010-03-19 - set undefined variable
  //$gender = xtc_db_prepare_input($_POST['gender']);
    $gender = isset($_POST['gender']) ? xtc_db_prepare_input($_POST['gender']) : '';
  //EOF - Dokuman - 2010-03-19 - set undefined variable
  $firstname = xtc_db_prepare_input($_POST['firstname']);
  $lastname = xtc_db_prepare_input($_POST['lastname']);
  if (ACCOUNT_DOB == 'true')
    $dob = xtc_db_prepare_input($_POST['dob']);
  $email_address = xtc_db_prepare_input($_POST['email_address']);
  //BOF - Dokuman - 2010-03-19 - set undefined variable
  //$confirm_email_address = xtc_db_prepare_input($_POST['confirm_email_address']); // Hetfield - 2009-08-15 - confirm e-mail at registration
  $confirm_email_address = isset($_POST['confirm_email_address']) ? xtc_db_prepare_input($_POST['confirm_email_address']) : 0; // Hetfield - 2009-08-15 - confirm e-mail at registration
  //EOF - Dokuman - 2010-03-19 - set undefined variable
  if (ACCOUNT_COMPANY == 'true')
    $company = xtc_db_prepare_input($_POST['company']);
  if (ACCOUNT_COMPANY_VAT_CHECK == 'true')
    $vat = xtc_db_prepare_input($_POST['vat']);
  $street_address = xtc_db_prepare_input($_POST['street_address']);
  if (ACCOUNT_SUBURB == 'true')
    $suburb = xtc_db_prepare_input($_POST['suburb']);
  $postcode = xtc_db_prepare_input($_POST['postcode']);
  $city = xtc_db_prepare_input($_POST['city']);
  //BOF - Dokuman - 2010-03-19 - set undefined variable
  //$zone_id = xtc_db_prepare_input($_POST['zone_id']);
    $zone_id = isset($_POST['zone_id']) ? xtc_db_prepare_input($_POST['zone_id']) : 0;
  //EOF - Dokuman - 2010-03-19 - set undefined variable
  if (ACCOUNT_STATE == 'true')
    $state = xtc_db_prepare_input($_POST['state']);
  $country = xtc_db_prepare_input($_POST['country']);
  $telephone = xtc_db_prepare_input($_POST['telephone']);
  $fax = xtc_db_prepare_input($_POST['fax']);
  //BOF - Dokuman - 2010-03-19 - no newsletter for guest accounts
  //$newsletter = xtc_db_prepare_input($_POST['newsletter']);
  $newsletter = '';
  //EOF - Dokuman - 2010-03-19 - no newsletter for guest accounts
  $password = xtc_db_prepare_input($_POST['password']);
  $confirmation = xtc_db_prepare_input($_POST['confirmation']);
  //BOF - Dokuman - 2010-08-31 - set undefined index
  //$privacy = xtc_db_prepare_input($_POST['privacy']);
    $privacy = isset($_POST['privacy']) ? xtc_db_prepare_input($_POST['privacy']) : 0;
  //EOF - Dokuman - 2010-08-31 - set undefined index

  $error = false;

  if (ACCOUNT_GENDER == 'true') {
    if (($gender != 'm') && ($gender != 'f')) {
      $error = true;

      $messageStack->add('create_account', ENTRY_GENDER_ERROR);
    }
  }

  if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;

    $messageStack->add('create_account', ENTRY_FIRST_NAME_ERROR);
  }

  if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;

    $messageStack->add('create_account', ENTRY_LAST_NAME_ERROR);
  }

  if (ACCOUNT_DOB == 'true') {
    //BOF - DokuMan - 2011-01-07 - Sanitize parameters
    //if (checkdate(substr(xtc_date_raw($dob), 4, 2), substr(xtc_date_raw($dob), 6, 2), substr(xtc_date_raw($dob), 0, 4)) == false) {
    if ((is_numeric(xtc_date_raw($dob)) == false) || (@checkdate(substr(xtc_date_raw($dob), 4,
2), substr(xtc_date_raw($dob), 6, 2), substr(xtc_date_raw($dob), 0, 4)) == false)) {
    //EOF - DokuMan - 2011-01-07 - Sanitize parameters
      $error = true;

      $messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);
    }
  }

  // New VAT Check
  if (ACCOUNT_COMPANY_VAT_CHECK == 'true'){
    require_once (DIR_WS_CLASSES . 'vat_validation.php');
    $vatID = new vat_validation($vat, '', '', $country, true);

    $customers_status = $vatID->vat_info['status'];
    //BOF - Dokuman - 2010-08-31 - set undefined index
    //$customers_vat_id_status = $vatID->vat_info['vat_id_status'];
    $customers_vat_id_status = isset($vatID->vat_info['vat_id_status']) ? $vatID->vat_info['vat_id_status'] : '';

    //$vat_error = $vatID->vat_info['error'];
    $vat_error = isset($vatID->vat_info['error']) ? $vatID->vat_info['error'] : '';
    //EOF - Dokuman - 2010-08-31 - set undefined index

    if ($vat_error==1){
      $messageStack->add('create_account', ENTRY_VAT_ERROR);
      $error = true;
    }
  }
  // New VAT CHECK END

// BOF - Tomcraft - 2009-11-28 - Included xs:booster
  // xs:booster prefill (customer group)
  if(isset($_SESSION['xtb0']['DEFAULT_CUSTOMER_GROUP']) && $_SESSION['xtb0']['DEFAULT_CUSTOMER_GROUP']!='')
    $customers_status = $_SESSION['xtb0']['DEFAULT_CUSTOMER_GROUP'];
  // xs:booster prefill end
// EOF - Tomcraft - 2009-11-28 - Included xs:booster

  //BOF - Hetfield - 2009-08-15 - confirm e-mail at registration
  if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR);
  } elseif ($email_address != $confirm_email_address) {
    $error = true;
    $messageStack->add('create_account', ENTRY_EMAIL_ERROR_NOT_MATCHING);
  } elseif (xtc_validate_email($email_address) == false) {
    $error = true;
    $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
  }
  //EOF - Hetfield - 2009-08-15 - confirm e-mail at registration

  if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_STREET_ADDRESS_ERROR);
  }

  if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_POST_CODE_ERROR);
  }

  if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_CITY_ERROR);
  }

  if (is_numeric($country) == false) {
    $error = true;
    $messageStack->add('create_account', ENTRY_COUNTRY_ERROR);
  }

  if (ACCOUNT_STATE == 'true') {
    $zone_id = 0;
    $check_query = xtc_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . (int) $country . "'");
    $check = xtc_db_fetch_array($check_query);
    $entry_state_has_zones = ($check['total'] > 0);
    if ($entry_state_has_zones == true) {
      $zone_query = xtc_db_query("select distinct zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int) $country . "' and (zone_name like '" . xtc_db_input($state) . "%' or zone_code like '%" . xtc_db_input($state) . "%')");
      if (xtc_db_num_rows($zone_query) > 1) {
        $zone_query = xtc_db_query("select distinct zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int) $country . "' and zone_name = '" . xtc_db_input($state) . "'");
      }
      if (xtc_db_num_rows($zone_query) >= 1) {
        $zone = xtc_db_fetch_array($zone_query);
        $zone_id = $zone['zone_id'];
      } else {
        $error = true;

        $messageStack->add('create_account', ENTRY_STATE_ERROR_SELECT);
      }
    } else {
      if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
        $error = true;
        $messageStack->add('create_account', ENTRY_STATE_ERROR);
      }
    }
  }

  if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
    $error = true;
    $messageStack->add('create_account', ENTRY_TELEPHONE_NUMBER_ERROR);
  }

  if (DISPLAY_PRIVACY_CHECK == 'true') {
    if(!isset($privacy) || empty($privacy) || $privacy!='privacy') {
    $error = true;
    $messageStack->add('create_account', ENTRY_PRIVACY_ERROR);
    }
  }

  if (!isset($customers_status) || $customers_status == 0)
    $customers_status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
  $password = xtc_create_password(8);

  if (!$newsletter)
    $newsletter = '';
  if ($error == false) {
    $sql_data_array = array (
      'customers_vat_id' => $vat,
      'customers_vat_id_status' => $customers_vat_id_status,
      'customers_status' => $customers_status,
      'customers_firstname' => $firstname,
      'customers_lastname' => $lastname,
      'customers_email_address' => $email_address,
      'customers_telephone' => $telephone,
      'customers_fax' => $fax,
      'customers_newsletter' => $newsletter,
      'account_type' => '1',
      'customers_password' => xtc_encrypt_password($password),
      'customers_date_added' => 'now()',
      'customers_last_modified' => 'now()',
      );

    $_SESSION['account_type'] = '1';

    if (ACCOUNT_GENDER == 'true')
      $sql_data_array['customers_gender'] = $gender;
    if (ACCOUNT_DOB == 'true')
      $sql_data_array['customers_dob'] = xtc_date_raw($dob);

    xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);

    $_SESSION['customer_id'] = xtc_db_insert_id();

    $sql_data_array = array (
      'customers_id' => $_SESSION['customer_id'],
      'entry_firstname' => $firstname,
      'entry_lastname' => $lastname,
      'entry_street_address' => $street_address,
      'entry_postcode' => $postcode,
      'entry_city' => $city,
      'entry_country_id' => $country
    );

    if (ACCOUNT_GENDER == 'true')
      $sql_data_array['entry_gender'] = $gender;
    if (ACCOUNT_COMPANY == 'true')
      $sql_data_array['entry_company'] = $company;
    if (ACCOUNT_SUBURB == 'true')
      $sql_data_array['entry_suburb'] = $suburb;
    if (ACCOUNT_STATE == 'true') {
      if ($zone_id > 0) {
        $sql_data_array['entry_zone_id'] = $zone_id;
        $sql_data_array['entry_state'] = '';
      } else {
        $sql_data_array['entry_zone_id'] = '0';
        $sql_data_array['entry_state'] = $state;
      }
    }

    xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

    $address_id = xtc_db_insert_id();

    xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '" . (int)$address_id . "' where customers_id = '" . (int) $_SESSION['customer_id'] . "'");

    xtc_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . (int) $_SESSION['customer_id'] . "', '0', now())");

    if (SESSION_RECREATE == 'True') {
      xtc_session_recreate();
    }

    $_SESSION['customer_first_name'] = $firstname;
    $_SESSION['customer_last_name'] = $lastname;
    $_SESSION['customer_default_address_id'] = $address_id;
    $_SESSION['customer_country_id'] = $country;
    $_SESSION['customer_zone_id'] = $zone_id;
    $_SESSION['customer_vat_id'] = $vat;

    // restore cart contents
    $_SESSION['cart']->restore_contents();

    if (isset ($_SESSION['tracking']['refID'])) {
      $campaign_check_query_raw = "SELECT *
                                        FROM " . TABLE_CAMPAIGNS . "
                                        WHERE campaigns_refID = '" . $_SESSION[tracking][refID] . "'";
      $campaign_check_query = xtc_db_query($campaign_check_query_raw);
      if (xtc_db_num_rows($campaign_check_query) > 0) {
        $campaign = xtc_db_fetch_array($campaign_check_query);
        $refID = $campaign['campaigns_id'];
      } else {
        $refID = 0;
      }

      xtc_db_query("update " . TABLE_CUSTOMERS . " set
                                       refferers_id = '" . $refID . "'
                                       where customers_id = '" . (int) $_SESSION['customer_id'] . "'");

      $leads = $campaign['campaigns_leads'] + 1;
      xtc_db_query("update " . TABLE_CAMPAIGNS . " set
                                   campaigns_leads = '" . $leads . "'
                                       where campaigns_id = '" . $refID . "'");
    }

    if ($newsletter == 1) {
      require_once (DIR_WS_CLASSES . 'class.newsletter.php');
      $newsletter = new newsletter;
      $newsletter->AddUserAuto($email_address);
    }

    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  }
}

$breadcrumb->add(NAVBAR_TITLE_CREATE_GUEST_ACCOUNT, xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'));

require (DIR_WS_INCLUDES . 'header.php');

// BOF - Tomcraft - 2009-11-28 - Included xs:booster
// xs:booster start (v1.041)
if(@isset($_SESSION['xtb0']['tx'][0]))
{
  $GLOBALS['gender']=      'm';
  $GLOBALS['firstname']=    substr($_SESSION['xtb0']['tx'][0]['XTB_EBAY_NAME'],0,strpos($_SESSION['xtb0']['tx'][0]['XTB_EBAY_NAME']," "));
  $GLOBALS['lastname']=    substr($_SESSION['xtb0']['tx'][0]['XTB_EBAY_NAME'],strpos($_SESSION['xtb0']['tx'][0]['XTB_EBAY_NAME']," ")+1,strlen($_SESSION['xtb0']['tx'][0]['XTB_EBAY_NAME']));
  $GLOBALS['street_address']=  $_SESSION['xtb0']['tx'][0]['XTB_EBAY_STREET'];
  $GLOBALS['postcode']=    $_SESSION['xtb0']['tx'][0]['XTB_EBAY_POSTALCODE'];
  $GLOBALS['city']=      $_SESSION['xtb0']['tx'][0]['XTB_EBAY_CITY'];
  $GLOBALS['country']=    $_SESSION['xtb0']['tx'][0]['XTB_EBAY_COUNTRYNAME'];
  $GLOBALS['email_address']=  $_SESSION['xtb0']['tx'][0]['XTB_EBAY_EMAIL'];
  $GLOBALS['telephone']=    $_SESSION['xtb0']['tx'][0]['XTB_EBAY_PHONE'];
}
// xs:booster end
// EOF - Tomcraft - 2009-11-28 - Included xs:booster

if ($messageStack->size('create_account') > 0) {
  $smarty->assign('error', $messageStack->output('create_account'));
}
//BOF - web28 - 2010-04-10 - Removed JavaScript formcheck
//$smarty->assign('FORM_ACTION', xtc_draw_form('create_account', xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'), 'post', 'onsubmit="return check_form(create_account);"') . xtc_draw_hidden_field('action', 'process'));
$smarty->assign('FORM_ACTION', xtc_draw_form('create_account', xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'), 'post') . xtc_draw_hidden_field('action', 'process'));
//EOF - web28 - 2010-04-10 - Removed JavaScript formcheck

if (ACCOUNT_GENDER == 'true') {
  $smarty->assign('gender', '1');
  $smarty->assign('INPUT_MALE', xtc_draw_radio_field(array('name' => 'gender','suffix' => MALE), 'm'));
  $smarty->assign('INPUT_FEMALE', xtc_draw_radio_field(array('name' => 'gender','suffix' => FEMALE, 'text' => (xtc_not_null(ENTRY_GENDER_TEXT) ? '<span class="inputRequirement">' . ENTRY_GENDER_TEXT . '</span>' : '')), 'f'));
} else {
  $smarty->assign('gender', '0');
}

$smarty->assign('INPUT_FIRSTNAME', xtc_draw_input_fieldNote(array ('name' => 'firstname','text' => '&nbsp;' . (xtc_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>' : ''))));
$smarty->assign('INPUT_LASTNAME', xtc_draw_input_fieldNote(array ('name' => 'lastname','text' => '&nbsp;' . (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : ''))));

if (ACCOUNT_DOB == 'true') {
  $smarty->assign('birthdate', '1');
  $smarty->assign('INPUT_DOB', xtc_draw_input_fieldNote(array ('name' => 'dob','text' => '&nbsp;' . (xtc_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="inputRequirement">' . ENTRY_DATE_OF_BIRTH_TEXT . '</span>' : ''))));
} else {
  $smarty->assign('birthdate', '0');
}
//BOF - Dokuman - 2009-08-28 - added ondragstart onselectstart to void duplication of the emailaddress by the user
$smarty->assign('INPUT_EMAIL', xtc_draw_input_fieldNote(array ('name' => 'email_address','text' => '&nbsp;' . (xtc_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>' : '')), '','ondragstart="return false" onselectstart="return false"'));
//BOF - Hetfield - 2009-08-15 - confirm e-mail at registration
$smarty->assign('INPUT_CONFIRM_EMAIL', xtc_draw_input_fieldNote(array ('name' => 'confirm_email_address', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">'.ENTRY_EMAIL_ADDRESS_TEXT.'</span>' : '')), '','ondragstart="return false" onselectstart="return false"'));
//EOF - Hetfield - 2009-08-15 - confirm e-mail at registration
//EOF - Dokuman - 2009-08-28 - added ondragstart onselectstart to void duplication of the emailaddress by the user
if (ACCOUNT_COMPANY == 'true') {
  $smarty->assign('company', '1');
  $smarty->assign('INPUT_COMPANY', xtc_draw_input_fieldNote(array (
    'name' => 'company',
    'text' => '&nbsp;' . (xtc_not_null(ENTRY_COMPANY_TEXT
  ) ? '<span class="inputRequirement">' . ENTRY_COMPANY_TEXT . '</span>' : ''))));
} else {
  $smarty->assign('company', '0');
}

if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
  $smarty->assign('vat', '1');
  $smarty->assign('INPUT_VAT', xtc_draw_input_fieldNote(array ('name' => 'vat','text' => '&nbsp;' . (xtc_not_null(ENTRY_VAT_TEXT) ? '<span class="inputRequirement">' . ENTRY_VAT_TEXT . '</span>' : ''))));
} else {
  $smarty->assign('vat', '0');
}

$smarty->assign('INPUT_STREET', xtc_draw_input_fieldNote(array ('name' => 'street_address','text' => '&nbsp;' . (xtc_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>' : ''))));

if (ACCOUNT_SUBURB == 'true') {
  $smarty->assign('suburb', '1');
  $smarty->assign('INPUT_SUBURB', xtc_draw_input_fieldNote(array ('name' => 'suburb','text' => '&nbsp;' . (xtc_not_null(ENTRY_SUBURB_TEXT) ? '<span class="inputRequirement">' . ENTRY_SUBURB_TEXT . '</span>' : ''))));

} else {
  $smarty->assign('suburb', '0');
}

$smarty->assign('INPUT_CODE', xtc_draw_input_fieldNote(array ('name' => 'postcode','text' => '&nbsp;' . (xtc_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="inputRequirement">' . ENTRY_POST_CODE_TEXT . '</span>' : ''))));
$smarty->assign('INPUT_CITY', xtc_draw_input_fieldNote(array ('name' => 'city','text' => '&nbsp;' . (xtc_not_null(ENTRY_CITY_TEXT) ? '<span class="inputRequirement">' . ENTRY_CITY_TEXT . '</span>' : ''))));

if (ACCOUNT_STATE == 'true') {
  $smarty->assign('state', '1');

  if ($process == true) {
    if ($entry_state_has_zones == true) {
      $zones_array = array ();
      $zones_query = xtc_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int) $country . "' order by zone_name");
      while ($zones_values = xtc_db_fetch_array($zones_query)) {
        $zones_array[] = array (
          'id' => $zones_values['zone_name'],
          'text' => $zones_values['zone_name']
        );
      }
      $state_input = xtc_draw_pull_down_menuNote(array ('name' => 'state','text' => '&nbsp;' . (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">' . ENTRY_STATE_TEXT . '</span>' : '')), $zones_array);
    } else {
      $state_input = xtc_draw_input_fieldNote(array ('name' => 'state','text' => '&nbsp;' . (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">' . ENTRY_STATE_TEXT . '</span>' : '')));
    }
  } else {
    $state_input = xtc_draw_input_fieldNote(array ('name' => 'state','text' => '&nbsp;' . (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">' . ENTRY_STATE_TEXT . '</span>' : '')));
  }

  $smarty->assign('INPUT_STATE', $state_input);
} else {
  $smarty->assign('state', '0');
}

if (isset($_POST['country'])) {
  $selected = $_POST['country'];
} else {
  $selected = STORE_COUNTRY;
}

$smarty->assign('SELECT_COUNTRY', xtc_get_country_list(array ('name' => 'country','text' => '&nbsp;' . (xtc_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>' : '')), $selected));
$smarty->assign('INPUT_TEL', xtc_draw_input_fieldNote(array ('name' => 'telephone','text' => '&nbsp;' . (xtc_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="inputRequirement">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>' : ''))));
$smarty->assign('INPUT_FAX', xtc_draw_input_fieldNote(array ('name' => 'fax','text' => '&nbsp;' . (xtc_not_null(ENTRY_FAX_NUMBER_TEXT) ? '<span class="inputRequirement">' . ENTRY_FAX_NUMBER_TEXT . '</span>' : ''))));
$smarty->assign('CHECKBOX_NEWSLETTER', xtc_draw_checkbox_field('newsletter', '1').'&nbsp;'. (xtc_not_null(ENTRY_NEWSLETTER_TEXT) ? '<span class="inputRequirement">'.ENTRY_NEWSLETTER_TEXT.'</span>' : ''));
if (DISPLAY_PRIVACY_CHECK == 'true') {
  $smarty->assign('PRIVACY_CHECKBOX', '<input type="checkbox" value="privacy" name="privacy" />');
  //BOF - Dokuman - 2009-08-13 - SSL for Content-Links per getContentLink
  //$smarty->assign('PRIVACY_LINK', $main->getContentLink(2, MORE_INFO));
  $smarty->assign('PRIVACY_LINK', $main->getContentLink(2, MORE_INFO, $request_type));
  //EOF - Dokuman - 2009-08-13 - SSL for Content-Links per getContentLink
}
$smarty->assign('FORM_END', '</form>');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
$main_content = $smarty->fetch(CURRENT_TEMPLATE . '/module/create_account_guest.html');
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined('RM'))
  $smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE . '/index.html');
include ('includes/application_bottom.php');
?>