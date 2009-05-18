<?php

/**
 * ----------------------------------------------------------------------------
 * Package: Tag-Cloud / xt:Commerce 3.0.4
 * Copyright (c) 2007 by seo-one - Suchmaschinenoptimierung-Hamburg.de <info@seo-one.de>
 * Hamburg, Germany
 * ----------------------------------------------------------------------------
 * Released under the GNU General Public License
 * ----------------------------------------------------------------------------
 * Original Author of file: Oliver Oestrup <oestrup@seo-one.de>
 * ----------------------------------------------------------------------------
 * Purpose: Setup
 * ----------------------------------------------------------------------------
 **/


defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );


define('MODULE_TAG_CLOUD_TEXT_TITLE', 'Tag-Cloud');
define('MODULE_TAG_CLOUD_TEXT_DESCRIPTION', 'Tag-Cloud - Setup');
define('MODULE_TAG_CLOUD_STATUS_DESC', 'Modulstatus');
define('MODULE_TAG_CLOUD_STATUS_TITLE', 'Status');
define('MODULE_TAG_CLOUD_MAX_DISPLAY_TITLE', 'Maximal angezeigt');
define('MODULE_TAG_CLOUD_MAX_DISPLAY_DESC', 'Maximale Anzahl an Tags, die angezeigt werden sollen');
define('MODULE_TAG_CLOUD_MIN_SEARCHES_TITLE', 'Mindesteingaben');
define('MODULE_TAG_CLOUD_MIN_SEARCHES_DESC', 'Mindesteingaben eines Tags, um angezeigt zu werden');
define('MODULE_TAG_CLOUD_LOG_TITLE', 'Logarithmierung');
define('MODULE_TAG_CLOUD_LOG_DESC', 'Faktor, der für die logarithmische Darstellung benutzt werden soll, 0 = keine Logarithmierung');
define('MODULE_TAG_CLOUD_MAX_TAGS_TITLE', 'Maximal in DB');
define('MODULE_TAG_CLOUD_MAX_TAGS_DESC', 'Maximale Anzahl an Tags, die in der Datenbank gespeichert werden');


class tag_cloud {
	var $code, $title, $description, $enabled;
	
	function tag_cloud() {
		$this->code = 'tag_cloud';
		$this->title = MODULE_TAG_CLOUD_TEXT_TITLE;
		$this->description = MODULE_TAG_CLOUD_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_TAG_CLOUD_SORT_ORDER;
		$this->enabled = MODULE_TAG_CLOUD_STATUS == 'True';
	}
	
	function process($file) {}
	
	function display() {
		return array('text' => '<br>'.xtc_button(BUTTON_SAVE).xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set='.$_GET['set'].'&module=tag_cloud')));
	}
	
	function check() {
		if(!isset($this->_check)) {
			$check_query = xtc_db_query("SELECT configuration_value FROM ".TABLE_CONFIGURATION." WHERE configuration_key = 'MODULE_TAG_CLOUD_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}
	
	function install() {
		xtc_db_query("DROP TABLE IF EXISTS module_tag_cloud");
		xtc_db_query(
			"CREATE TABLE module_tag_cloud (".
				"tag varchar(64) NOT NULL default '',".
				"language_id int(11) NOT NULL default '0',".
				"searches int(10) unsigned NOT NULL default '1',".
				"offset int(10) unsigned NOT NULL default '0',".
				"inserted datetime NOT NULL default '0000-00-00 00:00:00',".
				"not_found tinyint(4) NOT NULL default '0',".
				"PRIMARY KEY (tag, language_id)".
			") TYPE=MyISAM"
		);
		
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_TAG_CLOUD_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', NOW())");
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_TAG_CLOUD_MAX_DISPLAY', '25', '6', '1', '', NOW())");
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_TAG_CLOUD_MIN_SEARCHES', '2', '6', '1', '', NOW())");
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_TAG_CLOUD_LOG', '10', '6', '1', '', NOW())");
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_TAG_CLOUD_MAX_TAGS', '500', '6', '1', '', NOW())");
	}
	
	function remove() {
		xtc_db_query("DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_key IN ('".implode("', '", $this->keys())."')");
		xtc_db_query("DROP TABLE IF EXISTS module_tag_cloud");
	}
	
	function keys() {
		return array('MODULE_TAG_CLOUD_STATUS', 'MODULE_TAG_CLOUD_MAX_DISPLAY', 'MODULE_TAG_CLOUD_MIN_SEARCHES', 'MODULE_TAG_CLOUD_LOG', 'MODULE_TAG_CLOUD_MAX_TAGS');
	}
}

?>