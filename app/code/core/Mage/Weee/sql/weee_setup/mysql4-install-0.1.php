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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Weee_Model_Mysql4_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE {$installer->getTable('weee_tax')} (
  `value_id` int(11) NOT NULL auto_increment,
  `website_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `country` varchar(2) NOT NULL default '',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_WEBSITE` (`website_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_PRODUCT_ENTITY` (`entity_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_COUNTRY` (`country`),
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_PRODUCT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES {$this->getTable('core_website')} (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_WEEE_TAX_COUNTRY` FOREIGN KEY (`country`) REFERENCES {$this->getTable('directory_country')} (`country_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();