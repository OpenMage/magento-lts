<?php

$this->startSetup()->run("

drop table if exists {$this->getTable('dataflow_profile')};
CREATE TABLE {$this->getTable('dataflow_profile')} (
  `profile_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `actions_xml` text,
  `gui_data` text,
  `direction` enum('import','export') default NULL,
  `entity_type` varchar(64) NOT NULL default '',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `data_transfer` enum('file', 'interactive'),
  PRIMARY KEY  (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists {$this->getTable('dataflow_profile_history')};
CREATE TABLE {$this->getTable('dataflow_profile_history')} (
  `history_id` int(10) unsigned NOT NULL auto_increment,
  `profile_id` int(10) unsigned NOT NULL default '0',
  `action_code` varchar(64) default NULL,
  `user_id` int(10) unsigned NOT NULL default '0',
  `performed_at` datetime default NULL,
  PRIMARY KEY  (`history_id`),
  KEY `FK_dataflow_profile_history` (`profile_id`),
  CONSTRAINT `FK_dataflow_profile_history` FOREIGN KEY (`profile_id`) REFERENCES {$this->getTable('dataflow_profile')} (`profile_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

if ($this->tableExists($this->getTable('core_convert_profile'))) {
    $this->run("
    insert into {$this->getTable('dataflow_profile')} select * from {$this->getTable('core_convert_profile')};
    insert into {$this->getTable('dataflow_profile_history')} select * from {$this->getTable('core_convert_history')};

    drop table {$this->getTable('core_convert_profile')};
    drop table {$this->getTable('core_convert_history')};
    ");
}

$this->endSetup();