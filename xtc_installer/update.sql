#Add content metatags functionality
ALTER TABLE `content_manager` ADD `content_meta_title` TEXT,
ADD `content_meta_description` TEXT,
ADD `content_meta_keywords` TEXT;

ALTER TABLE `content_manager` ADD FULLTEXT (
`content_meta_title` ,
`content_meta_description` ,
`content_meta_keywords`
);

#Dokuman - 2009-08-20 - Added Bulgaria and Romania to EU Zones (since 01.01.2007)
UPDATE zones_to_geo_zones SET geo_zone_id= 5 WHERE zone_country_id IN (33,175);

#Dokuman - 2009-08-21 - Bundesländer->ISO-3166-2
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

#Tomcraft - 2009-09-08 - changed database_version
UPDATE database_version SET version = 'xtcM_1.0.3.0'