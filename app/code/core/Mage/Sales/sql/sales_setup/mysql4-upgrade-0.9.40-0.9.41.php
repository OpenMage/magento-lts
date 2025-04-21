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

$installer->addAttribute('quote_item', 'base_cost', [
    'type'              => 'decimal',
    'label'             => 'Cost',
    'visible'           => false,
    'required'          => false,
]);

$installer->addAttribute('quote_address_item', 'base_cost', [
    'type'              => 'decimal',
    'label'             => 'Cost',
    'visible'           => false,
    'required'          => false,
]);

$installer->getConnection()->changeColumn($installer->getTable('sales_flat_order_item'), 'cost', 'base_cost', 'DECIMAL( 12, 4 ) NULL DEFAULT \'0.0000\'');

$installer->getConnection()->addColumn($installer->getTable('sales_order'), 'base_total_invoiced_cost', 'DECIMAL( 12, 4 ) NULL DEFAULT NULL');

$installer->addAttribute('order', 'base_total_invoiced_cost', [
    'type'              => 'static',
]);

$installer->updateAttribute('order_item', 'cost', ['attribute_code' => 'base_cost']);
$installer->updateAttribute('invoice_item', 'cost', ['attribute_code' => 'base_cost']);
$installer->updateAttribute('creditmemo_item', 'cost', ['attribute_code' => 'base_cost']);

$installer->endSetup();
