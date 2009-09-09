#Add content metatags functionality
ALTER TABLE `content_manager` ADD `content_meta_title` TEXT,
ADD `content_meta_description` TEXT,
ADD `content_meta_keywords` TEXT;

ALTER TABLE `content_manager` ADD FULLTEXT (
`content_meta_title` ,
`content_meta_description` ,
`content_meta_keywords`
);

#Tomcraft - 2009-09-09 - changed database_version
UPDATE database_version SET version = 'xtcM_1.0.2.0'