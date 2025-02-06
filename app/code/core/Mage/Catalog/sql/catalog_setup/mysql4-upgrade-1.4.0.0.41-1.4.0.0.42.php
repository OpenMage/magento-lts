<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$connection = $installer->getConnection();
$table      = $installer->getTable('catalog/product_super_attribute_pricing');
$connection->changeColumn($table, 'pricing_value', 'pricing_value', 'DECIMAL(12, 4) NULL DEFAULT NULL');
