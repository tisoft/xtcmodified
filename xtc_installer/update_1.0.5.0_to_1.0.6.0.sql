# -----------------------------------------------------------------------------------------
#  $Id$
#
#  xtc-Modified
#  http://www.xtc-modified.org
#
#  Copyright (c) 2010 xtc-Modified
#  -----------------------------------------------------------------------------------------

#Tomcraft - 2010-07-19 - changed database_version
UPDATE database_version SET version = 'xtcM_1.0.6.0';

#DokuMan - 2010-08-05 - mark out of stock products red by default
UPDATE configuration SET configuration_value = '<span style="color:red">***</span>' WHERE configuration_key = 'STOCK_MARK_PRODUCT_OUT_OF_STOCK';

#Hendrik - 2010-08-11 - Thumbnails in admin products list
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'USE_ADMIN_THUMBS_IN_LIST', 'true', 1, 32, NULL , NOW( ) , NULL , 'xtc_cfg_select_option(array(\'true\', \'false\'),');

#DokuMan - 2010-08-13 - Google RSS Feed REFID configuration
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'GOOGLE_RSS_FEED_REFID', '', 17, 15, NULL, NOW(), NULL, NULL);

#DokuMan - 2010-08-17 - Replace GLS shipping module with newer version
DELETE FROM configuration WHERE configuration_key = 'MODULE_SHIPPING_GLS_STATUS';
DELETE FROM configuration WHERE configuration_key = 'MODULE_SHIPPING_GLS_HANDLING';
DELETE FROM configuration WHERE configuration_key = 'MODULE_SHIPPING_GLS_ALLOWED';
DELETE FROM configuration WHERE configuration_key = 'MODULE_SHIPPING_GLS_SORT_ORDER';
DELETE FROM configuration WHERE configuration_key = 'MODULE_SHIPPING_GLS_TAX_CLASS';
DELETE FROM configuration WHERE configuration_key = 'MODULE_SHIPPING_GLS_ZONE';
DROP TABLE IF EXISTS gls_country_to_postal;
DROP TABLE IF EXISTS gls_postal_to_weight;
DROP TABLE IF EXISTS gls_weight;

#Hendrik 2010-08-29 Xajax Support in Backend
ALTER TABLE admin_access ADD xajax INT(1) DEFAULT 1 NOT NULL;

#DokuMan - 2010-09-01 - Added Taiwan and Chinese address_format
# 1 - Default, 2 - USA, 3 - Spain, 4 - Singapore, 5 - Germany , 6 - Taiwan , 7 - China
INSERT INTO address_format VALUES (6, '$firstname$lastname$cr$country$cr$postcode$city$cr$streets ','$country / $city');
INSERT INTO address_format VALUES (7, '$firstname$lastname$cr$country$cr$postcode$city$cr$streets ','$country / $city');
UPDATE countries SET address_format_id = 6 WHERE countries_id = 206;
UPDATE countries SET address_format_id = 7 WHERE countries_id = 44;

#DokuMan - 2010-09-21 - listing_template needs a default value
ALTER TABLE categories MODIFY listing_template varchar(64) NOT NULL DEFAULT '';

#DokuMan - 2010-09-28 - display VAT description multilingually
#Updating only the German tax rates here
UPDATE tax_rates SET tax_description = '19%' WHERE tax_description = 'MwSt 19%';
UPDATE tax_rates SET tax_description = '7%' WHERE tax_description = 'MwSt 7%';

#DokuMan - 2010-10-12 - set session configuration to recommended settings
UPDATE configuration SET configuration_value = 'True' WHERE configuration_key = 'SESSION_RECREATE';
UPDATE configuration SET configuration_value = 'True' WHERE configuration_key = 'SESSION_CHECK_USER_AGENT';

# Keep an empty line at the end of this file for the db_updater to work properly
