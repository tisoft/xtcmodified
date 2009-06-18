ALTER TABLE `content_manager` ADD `content_meta_title` TEXT,
ADD `content_meta_description` TEXT,
ADD `content_meta_keywords` TEXT;

ALTER TABLE `content_manager` ADD FULLTEXT (
`content_meta_title` ,
`content_meta_description` ,
`content_meta_keywords`
);
