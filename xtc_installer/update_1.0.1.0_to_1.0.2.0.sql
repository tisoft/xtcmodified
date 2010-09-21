# -----------------------------------------------------------------------------------------
#  $Id$
#
#  xtc-Modified
#  http://www.xtc-modified.org
#
#  Copyright (c) 2010 xtc-Modified
#  -----------------------------------------------------------------------------------------

#Tomcraft - 2009-09-09 - changed database_version
UPDATE database_version SET version = 'xtcM_1.0.2.0'

#Add content metatags functionality
ALTER TABLE content_manager ADD content_meta_title TEXT,
ADD content_meta_description TEXT,
ADD content_meta_keywords TEXT;

ALTER TABLE content_manager ADD FULLTEXT (
content_meta_title ,
content_meta_description ,
content_meta_keywords
);
# Keep an empty line at the end of this file for the db_updater to work properly
