#Tomcraft - 2010-07-19 - changed database_version
UPDATE database_version SET version = 'xtcM_1.0.6.0';

#DokuMan - 2010-08-05 - mark out of stock products red by default
UPDATE configuration SET configuration_value = 'span style="color:red">***</span>' WHERE configuration_key = 'STOCK_MARK_PRODUCT_OUT_OF_STOCK';
