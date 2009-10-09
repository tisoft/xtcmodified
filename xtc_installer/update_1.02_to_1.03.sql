#Dokuman - 2009-08-20 - Added Bulgaria and Romania to EU Zones (since 01.01.2007)
UPDATE zones_to_geo_zones SET geo_zone_id= 5 WHERE zone_country_id IN (33,175);

#Dokuman - 2009-08-21 - Bundeslšnder->ISO-3166-2
UPDATE zones SET zone_code = 'NI' WHERE zone_id = 79;
UPDATE zones SET zone_code = 'BW' WHERE zone_id = 80;
UPDATE zones SET zone_code = 'BY' WHERE zone_id = 81;
UPDATE zones SET zone_code = 'BE' WHERE zone_id = 82;
UPDATE zones SET zone_code = 'BR' WHERE zone_id = 83;
UPDATE zones SET zone_code = 'HB' WHERE zone_id = 84;
UPDATE zones SET zone_code = 'HH' WHERE zone_id = 85;
UPDATE zones SET zone_code = 'HE' WHERE zone_id = 86;
UPDATE zones SET zone_code = 'MV' WHERE zone_id = 87;
UPDATE zones SET zone_code = 'NW' WHERE zone_id = 88;
UPDATE zones SET zone_code = 'RP' WHERE zone_id = 89;
UPDATE zones SET zone_code = 'SL' WHERE zone_id = 90;
UPDATE zones SET zone_code = 'SN' WHERE zone_id = 91;
UPDATE zones SET zone_code = 'ST' WHERE zone_id = 92;
UPDATE zones SET zone_code = 'SH' WHERE zone_id = 93;
UPDATE zones SET zone_code = 'TH' WHERE zone_id = 94;

#Tomcraft - 2009-10-01 - changed configuration_group_id
UPDATE configuration SET configuration_group_id = 1 WHERE configuration_id = 17;

#Dokuman - 2009-10-02 - added entries for new moneybookers payment module version 2.4
INSERT INTO configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', '_PAYMENT_MONEYBOOKERS_EMAILID', '',  31, 1, NULL, '', NULL, NULL);
INSERT INTO configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', '_PAYMENT_MONEYBOOKERS_PWD','',  31, 2, NULL, '', NULL, NULL);
INSERT INTO configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', '_PAYMENT_MONEYBOOKERS_MERCHANTID','',  31, 3, NULL, '', NULL, NULL);
INSERT INTO configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', '_PAYMENT_MONEYBOOKERS_TMP_STATUS_ID','0',  31, 4, NULL, '', 'xtc_get_order_status_name' , 'xtc_cfg_pull_down_order_statuses(');
INSERT INTO configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', '_PAYMENT_MONEYBOOKERS_PROCESSED_STATUS_ID','0',  31, 5, NULL, '','xtc_get_order_status_name' , 'xtc_cfg_pull_down_order_statuses(');
INSERT INTO configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', '_PAYMENT_MONEYBOOKERS_PENDING_STATUS_ID','0',  31, 6, NULL, '', 'xtc_get_order_status_name' , 'xtc_cfg_pull_down_order_statuses(');
INSERT INTO configuration (configuration_id,  configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES   ('', '_PAYMENT_MONEYBOOKERS_CANCELED_STATUS_ID','0',  31, 7, NULL, '', 'xtc_get_order_status_name' , 'xtc_cfg_pull_down_order_statuses(');

#Dokuman - 2009-10-02 - added entries for GLS shipping module version 1.1
DROP TABLE IF EXISTS gls_country_to_postal;
CREATE TABLE gls_country_to_postal (
  gls_country char(2) NOT NULL default '',
  gls_postal_reference int(11) NOT NULL default '0',
  PRIMARY KEY  (gls_country)
) TYPE=MyISAM DEFAULT CHARSET=latin1 COLLATE latin1_german1_ci;

DROP TABLE IF EXISTS gls_postal_to_weight;
CREATE TABLE gls_postal_to_weight (
  gls_postal_reference int(11) NOT NULL default '0',
  gls_from_postal varchar(10) NOT NULL default '',
  gls_to_postal varchar(10) NOT NULL default '',
  gls_weight_ref char(3) NOT NULL default '',
  PRIMARY KEY  (gls_postal_reference,gls_from_postal)
) TYPE=MyISAM DEFAULT CHARSET=latin1 COLLATE latin1_german1_ci;

DROP TABLE IF EXISTS gls_weight;
CREATE TABLE gls_weight (
  gls_weight_ref char(3) NOT NULL default '',
  gls_weight_price_string text NOT NULL,
  gls_free_shipping_over decimal(15,4) NOT NULL default '-1.0000',
  gls_shipping_subsidized decimal(15,4) NOT NULL default '-1.0000',
  PRIMARY KEY  (gls_weight_ref)
) TYPE=MyISAM DEFAULT CHARSET=latin1 COLLATE latin1_german1_ci;

#Tomcraft - 2009-09-08 - changed database_version
UPDATE database_version SET version = 'xtcM_1.0.3.0'

#Dokuman - 2009-10-09 - added "erwartete Produkte" in "Artikelkatalog"
ALTER TABLE admin_access ADD products_expected INT( 1 ) NOT NULL DEFAULT '1';