<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('googlebase/types')};
CREATE TABLE {$this->getTable('googlebase/types')} (
  `type_id` int(10) unsigned not null auto_increment ,
  `attribute_set_id` smallint(5) unsigned not null ,
  `gbase_itemtype` varchar(255) not null ,
  PRIMARY KEY (`type_id`),
  CONSTRAINT `GOOGLEBASE_TYPES_ATTRIBUTE_SET_ID` FOREIGN KEY (`attribute_set_id`) REFERENCES `{$this->getTable('eav/attribute_set')}` (`attribute_set_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Google Base Item Types link Attribute Sets';

-- DROP TABLE IF EXISTS {$this->getTable('googlebase/items')};
CREATE TABLE {$this->getTable('googlebase/items')} (
  `item_id` int(10) unsigned not null auto_increment ,
  `type_id` int(10) unsigned not null default '0',
  `product_id` int(10) unsigned not null ,
  `gbase_item_id` varchar(255) not null ,
  `store_id` smallint(5) unsigned not null ,
  `published` datetime NOT NULL default '0000-00-00 00:00:00',
  `expires` datetime NOT NULL default '0000-00-00 00:00:00',
  `impr` smallint(5) unsigned not null default '0',
  `clicks` smallint(5) unsigned not null default '0',
  `views` smallint(5) unsigned not null default '0',
  `is_hidden` tinyint not null default '0',
  PRIMARY KEY (`item_id`),
  CONSTRAINT `GOOGLEBASE_ITEMS_PRODUCT_ID` FOREIGN KEY (`product_id`) REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE,
  CONSTRAINT `GOOGLEBASE_ITEMS_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Google Base Items Products';

-- DROP TABLE IF EXISTS {$this->getTable('googlebase/attributes')};
CREATE TABLE {$this->getTable('googlebase/attributes')} (
  `id` int(10) unsigned not null auto_increment ,
  `attribute_id` smallint(5) unsigned not null ,
  `gbase_attribute` varchar(255) not null ,
  `type_id` int(10) unsigned not null ,
  PRIMARY KEY (`id`),
  CONSTRAINT `GOOGLEBASE_ATTRIBUTES_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE,
  CONSTRAINT `GOOGLEBASE_ATTRIBUTES_TYPE_ID` FOREIGN KEY (`type_id`) REFERENCES `{$this->getTable('googlebase/types')}` (`type_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Google Base Attributes link Product Attributes';

");

$installer->endSetup();
