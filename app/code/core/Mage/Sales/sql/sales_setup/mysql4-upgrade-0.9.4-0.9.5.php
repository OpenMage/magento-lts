<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropColumn($installer->getTable('sales_flat_quote_item'), 'super_product_id');
$installer->getConnection()->changeColumn($installer->getTable('sales_flat_quote_item'), 'parent_product_id', 'parent_item_id', 'INTEGER UNSIGNED DEFAULT NULL');
$installer->getConnection()->addConstraint(
    'FK_SALES_FLAT_QUOTE_ITEM_PARENT_ITEM',
    $installer->getTable('sales_flat_quote_item'),
    'parent_item_id',
    $installer->getTable('sales_flat_quote_item'),
    'item_id',
);

$installer->endSetup();
