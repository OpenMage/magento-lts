<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
