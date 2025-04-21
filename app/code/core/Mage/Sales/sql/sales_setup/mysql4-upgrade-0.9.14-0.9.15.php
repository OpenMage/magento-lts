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

$installer->getConnection()->dropForeignKey($installer->getTable('sales_flat_quote_address_item'), 'FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM');

$installer->getConnection()->addConstraint(
    'FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM',
    $installer->getTable('sales_flat_quote_address_item'),
    'quote_item_id',
    $installer->getTable('sales_flat_quote_item'),
    'item_id',
);

$installer->endSetup();
