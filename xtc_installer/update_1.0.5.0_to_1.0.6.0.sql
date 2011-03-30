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
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'USE_ADMIN_THUMBS_IN_LIST', 'true', 1, 32, '', NOW() , NULL , 'xtc_cfg_select_option(array(\'true\', \'false\'),');

#DokuMan - 2010-08-13 - Google RSS Feed REFID configuration
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'GOOGLE_RSS_FEED_REFID', '', 17, 15, '', NOW(), NULL, NULL);

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

#Hendrik - 2010-08-29 - Xajax Support in Backend
ALTER TABLE admin_access ADD xajax INT(1) DEFAULT 1 NOT NULL;

#DokuMan - 2011-03-28 - Added address_format for Taiwan, Ireland, China and Great Britain
# 1 - Default, 2 - USA, 3 - Spain, 4 - Singapore, 5 - Germany , 6 - Ireland/Taiwan, 7 - China, 8 - UK/GB
INSERT INTO address_format VALUES (6, '$firstname $lastname$cr$streets$cr$city $state $postcode$cr$country','$country / $city');
INSERT INTO address_format VALUES (7, '$firstname $lastname$cr$streets, $city$cr$postcode $state$cr$country','$country / $city');
INSERT INTO address_format VALUES (8, '$firstname $lastname$cr$streets$cr$city$cr$state$cr$postcode$cr$country','$postcode / $country');

UPDATE countries SET address_format_id = 6 WHERE countries_id = 206;
UPDATE countries SET address_format_id = 6 WHERE countries_id = 103;
UPDATE countries SET address_format_id = 7 WHERE countries_id = 44;
UPDATE countries SET address_format_id = 8 WHERE countries_id = 222;

#DokuMan - 2010-09-21 - listing_template needs a default value
ALTER TABLE categories MODIFY listing_template varchar(64) NOT NULL DEFAULT '';
#DokuMan - 2010-10-13 - enlarge field 'manufacturers_name' from 32 characters to 64
ALTER TABLE manufacturers MODIFY manufacturers_name varchar(64) NOT NULL;
#DokuMan - 2010-10-13 - enlarge field 'comments' from carchar(255) to text
ALTER TABLE orders MODIFY comments text;

#DokuMan - 2010-09-28 - display VAT description multilingually
#Updating only the German tax rates here
UPDATE tax_rates SET tax_description = '19%' WHERE tax_description = 'MwSt 19%';
UPDATE tax_rates SET tax_description = '7%' WHERE tax_description = 'MwSt 7%';

# DokuMan - 2010-10-13 - add index idx_categories_id
ALTER TABLE products_to_categories
ADD INDEX idx_categories_id (categories_id,products_id);

# DokuMan - 2010-10-14 - keep index naming convention (idx_)
ALTER TABLE orders_products
DROP INDEX orders_id,
DROP INDEX products_id,
ADD INDEX idx_orders_id (orders_id),
ADD INDEX idx_products_id (products_id);

ALTER TABLE products_attributes
DROP INDEX products_id,
DROP INDEX options,
ADD INDEX idx_products_id (products_id),
ADD INDEX idx_options (options_id, options_values_id);

# DokuMan - 2010-10-29 - added missing HEADING_IMAGE_ definitions in Admin
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('','HEADING_IMAGE_WIDTH', '57', '4', '4', '', NOW() , NULL, NULL);
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'HEADING_IMAGE_HEIGHT', '40', '4', '4', '', NOW() , NULL, NULL);

# DokuMan - 2010-11-08 - remove unsupported payment module qenta
DROP TABLE IF EXISTS payment_qenta;

# Web28 - 2010-11-13 - add missing listproducts to admin_access
ALTER TABLE admin_access ADD listproducts INT( 1 ) NOT NULL DEFAULT '0' AFTER coupon_admin;
UPDATE admin_access SET listproducts = '1' WHERE customers_id = '1' LIMIT 1 ;
UPDATE admin_access SET listproducts = '3' WHERE customers_id = 'groups' LIMIT 1 ;

#franky_n - 2010-12-24 - added configuration_group entries for econda and moneybookers
INSERT INTO configuration_group VALUES (23,'Econda Tracking','Econda Tracking System',23,1);
INSERT INTO configuration_group VALUES (31,'Moneybookers','Moneybookers System',31,1);

# DokuMan - 2011-02-02 - added support for passwort+salt (SHA1)
ALTER TABLE customers MODIFY customers_password varchar(50) NOT NULL;

# DokuMan - 2011-02-03 - enlarge field for company names to 64 characters (instead of 32)
ALTER TABLE address_book MODIFY entry_company VARCHAR(64);
ALTER TABLE orders MODIFY customers_company VARCHAR(64);
ALTER TABLE orders MODIFY delivery_company VARCHAR(64);
ALTER TABLE orders MODIFY billing_company VARCHAR(64);

# Tomcraft - 2011-03-02 - Added status for cancelled orders
INSERT INTO orders_status VALUES ('',1,'Cancelled');
INSERT INTO orders_status VALUES ('',2,'Storniert');

# Web28 - 2011-03-25 - Fix address_format_id Switzerland
UPDATE countries SET address_format_id = '5' WHERE countries_id =204 LIMIT 1 ;

# Web28 - 2011-03-27 - Option no enlarge product image under default
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'PRODUCT_IMAGE_NO_ENLARGE_UNDER_DEFAULT', 'false', 4, 6, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''), ');

# DokuMan - 2011-03-30 - preset text for billing email subject from admin backend
UPDATE configuration SET configuration_value = 'Ihre Bestellung bei uns' WHERE configuration_key = 'EMAIL_BILLING_SUBJECT';

# Keep an empty line at the end of this file for the db_updater to work properly
