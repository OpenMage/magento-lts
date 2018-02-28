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
 * @package     Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */

$installer->run("
    CREATE TABLE `{$installer->getTable('cataloginventory_stock_status')}` (
      `product_id` int(10) unsigned NOT NULL,
      `website_id` smallint(5) unsigned NOT NULL,
      `stock_id` smallint(4) unsigned NOT NULL,
      `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
      `stock_status` tinyint(3) unsigned NOT NULL,
      PRIMARY KEY (`product_id`,`website_id`,`stock_id`),
      CONSTRAINT `FK_CATALOGINVENTORY_STOCK_STATUS_STOCK` FOREIGN KEY (`stock_id`)
        REFERENCES `{$installer->getTable('cataloginventory_stock')}` (`stock_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_CATALOGINVENTORY_STOCK_STATUS_PRODUCT` FOREIGN KEY (`product_id`)
        REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE` FOREIGN KEY (`website_id`)
        REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

Mage::getModel('cataloginventory/stock_status')->rebuild();
$installer->endSetup();
