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

$installer->getConnection()->dropForeignKey($installer->getTable('sales_flat_quote_address_item'), 'FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM');

$installer->getConnection()->addConstraint(
    'FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM',
    $installer->getTable('sales_flat_quote_address_item'),
    'quote_item_id',
    $installer->getTable('sales_flat_quote_item'),
    'item_id',
);

$installer->endSetup();
