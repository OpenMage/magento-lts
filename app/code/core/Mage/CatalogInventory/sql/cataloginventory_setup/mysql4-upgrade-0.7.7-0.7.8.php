<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;

$connection = $installer->getConnection();
/** @var Varien_Db_Adapter_Pdo_Mysql $connection */

$tableCataloginventoryStockItem = $installer->getTable('cataloginventory/stock_item');
$connection->addColumn($tableCataloginventoryStockItem, 'use_config_enable_qty_increments', "tinyint(1) unsigned NOT NULL default '1'");
$connection->addColumn($tableCataloginventoryStockItem, 'enable_qty_increments', "tinyint(1) unsigned NOT NULL default '0'");
