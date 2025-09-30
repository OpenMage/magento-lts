<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('cms/page_store')}`;
CREATE TABLE `{$this->getTable('cms/page_store')}` (
  `page_id` smallint(6) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`page_id`,`store_id`),
  CONSTRAINT `FK_CMS_PAGE_STORE_PAGE` FOREIGN KEY (`page_id`) REFERENCES `{$this->getTable('cms/page')}` (`page_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_CMS_PAGE_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS Pages to Stores';

INSERT INTO {$this->getTable('cms/page_store')} (`page_id`, `store_id`) SELECT `page_id`, `store_id` FROM {$this->getTable('cms/page')};

DROP TABLE IF EXISTS {$this->getTable('cms/block_store')};
CREATE TABLE {$this->getTable('cms/block_store')} (
  `block_id` smallint(6) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`block_id`,`store_id`),
  CONSTRAINT `FK_CMS_BLOCK_STORE_BLOCK` FOREIGN KEY (`block_id`) REFERENCES {$this->getTable('cms/block')} (`block_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_CMS_BLOCK_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS Blocks to Stores';

INSERT INTO {$this->getTable('cms/block_store')} (`block_id`, `store_id`) SELECT `block_id`, `store_id` FROM {$this->getTable('cms/block')};

");

$installer->endSetup();
