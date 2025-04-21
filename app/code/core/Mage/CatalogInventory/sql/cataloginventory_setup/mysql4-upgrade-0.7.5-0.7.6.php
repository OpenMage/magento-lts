<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();

$tableCataloginventoryStockItem = $installer->getTable('cataloginventory_stock_item');
$connection->addColumn($tableCataloginventoryStockItem, 'use_config_qty_increments', "tinyint(1) unsigned NOT NULL default '1'");
$connection->addColumn($tableCataloginventoryStockItem, 'qty_increments', "decimal(12,4) NOT NULL DEFAULT '0.0000'");
