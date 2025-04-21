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
$installer->getConnection()->addColumn($this->getTable('cataloginventory_stock_item'), 'stock_status_changed_automatically', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
$installer->endSetup();
