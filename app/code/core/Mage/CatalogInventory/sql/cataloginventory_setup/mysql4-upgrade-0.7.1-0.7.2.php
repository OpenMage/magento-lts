<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('cataloginventory_stock_item'), 'manage_stock', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
$installer->getConnection()->addColumn($this->getTable('cataloginventory_stock_item'), 'use_config_manage_stock', 'tinyint(1) unsigned NOT NULL DEFAULT 1');

$installer->endSetup();
