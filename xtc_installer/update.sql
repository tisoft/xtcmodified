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