<?php

/**
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('cataloginventory_stock_item'), 'manage_stock', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
$installer->getConnection()->addColumn($this->getTable('cataloginventory_stock_item'), 'use_config_manage_stock', 'tinyint(1) unsigned NOT NULL DEFAULT 1');

$installer->endSetup();
