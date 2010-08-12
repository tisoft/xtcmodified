#Tomcraft - 2010-07-19 - changed database_version
UPDATE database_version SET version = 'xtcM_1.0.6.0';

#DokuMan - 2010-08-05 - mark out of stock products red by default
UPDATE configuration SET configuration_value = '<span style="color:red">***</span>' WHERE configuration_key = 'STOCK_MARK_PRODUCT_OUT_OF_STOCK';

#Hendrik - 2010-08-11 - Thumbnails in products list 
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'USE_ADMIN_THUMBS_IN_LIST', 'true', 1, 32, NULL , NOW( ) , NULL , 'xtc_cfg_select_option(array(\'true\', \'false\'),'); 
# Keep an empty line at the end of this file for the db_updater to work properly
