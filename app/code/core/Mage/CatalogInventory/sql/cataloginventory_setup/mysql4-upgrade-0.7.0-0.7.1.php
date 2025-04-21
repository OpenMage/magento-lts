<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('cataloginventory_stock_item'), 'low_stock_date', 'datetime');
$installer->getConnection()->addColumn($this->getTable('cataloginventory_stock_item'), 'notify_stock_qty', 'decimal(12,4)');
$installer->getConnection()->addColumn($this->getTable('cataloginventory_stock_item'), 'use_config_notify_stock_qty', "tinyint(1) unsigned NOT NULL default '1'");

$installer->endSetup();
