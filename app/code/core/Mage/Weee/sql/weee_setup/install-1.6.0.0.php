<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Weee
 */

/** @var Mage_Weee_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'weee/tax'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('weee/tax'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Website Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('country', Varien_Db_Ddl_Table::TYPE_TEXT, 2, [
        'nullable'  => true,
    ], 'Country')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Value')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
        'default'   => '*',
    ], 'State')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Attribute Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Entity Type Id')
    ->addIndex(
        $installer->getIdxName('weee/tax', ['website_id']),
        ['website_id'],
    )
    ->addIndex(
        $installer->getIdxName('weee/tax', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('weee/tax', ['country']),
        ['country'],
    )
    ->addIndex(
        $installer->getIdxName('weee/tax', ['attribute_id']),
        ['attribute_id'],
    )
    ->addForeignKey(
        $installer->getFkName('weee/tax', 'country', 'directory/country', 'country_id'),
        'country',
        $installer->getTable('directory/country'),
        'country_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('weee/tax', 'entity_id', 'catalog/product', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('weee/tax', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('weee/tax', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Weee Tax');
$installer->getConnection()->createTable($table);

/**
 * Create table 'weee/discount'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('weee/discount'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Website Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Customer Group Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('weee/discount', ['website_id']),
        ['website_id'],
    )
    ->addIndex(
        $installer->getIdxName('weee/discount', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('weee/discount', ['customer_group_id']),
        ['customer_group_id'],
    )
    ->addForeignKey(
        $installer->getFkName('weee/discount', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('weee/discount', 'entity_id', 'catalog/product', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('weee/discount', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Weee Discount');
$installer->getConnection()->createTable($table);

$installer->addAttribute('order_item', 'base_weee_tax_applied_amount', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'base_weee_tax_applied_row_amnt', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'weee_tax_applied_amount', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'weee_tax_applied_row_amount', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'weee_tax_applied', ['type' => 'text']);

$installer->addAttribute('quote_item', 'weee_tax_disposition', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'weee_tax_row_disposition', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'base_weee_tax_disposition', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'base_weee_tax_row_disposition', ['type' => 'decimal']);

$installer->addAttribute('order_item', 'weee_tax_disposition', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'weee_tax_row_disposition', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'base_weee_tax_disposition', ['type' => 'decimal']);
$installer->addAttribute('order_item', 'base_weee_tax_row_disposition', ['type' => 'decimal']);

$installer->addAttribute('invoice_item', 'base_weee_tax_applied_amount', ['type' => 'decimal']);
$installer->addAttribute('invoice_item', 'base_weee_tax_applied_row_amnt', ['type' => 'decimal']);
$installer->addAttribute('invoice_item', 'weee_tax_applied_amount', ['type' => 'decimal']);
$installer->addAttribute('invoice_item', 'weee_tax_applied_row_amount', ['type' => 'decimal']);
$installer->addAttribute('invoice_item', 'weee_tax_applied', ['type' => 'text']);
$installer->addAttribute('invoice_item', 'weee_tax_disposition', ['type' => 'decimal']);
$installer->addAttribute('invoice_item', 'weee_tax_row_disposition', ['type' => 'decimal']);
$installer->addAttribute('invoice_item', 'base_weee_tax_disposition', ['type' => 'decimal']);
$installer->addAttribute('invoice_item', 'base_weee_tax_row_disposition', ['type' => 'decimal']);

$installer->addAttribute('quote_item', 'weee_tax_applied', ['type' => 'text']);
$installer->addAttribute('quote_item', 'weee_tax_applied_amount', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'weee_tax_applied_row_amount', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'base_weee_tax_applied_amount', ['type' => 'decimal']);
$installer->addAttribute('quote_item', 'base_weee_tax_applied_row_amnt', ['type' => 'decimal']);

$installer->addAttribute('creditmemo_item', 'weee_tax_disposition', ['type' => 'decimal']);
$installer->addAttribute('creditmemo_item', 'weee_tax_row_disposition', ['type' => 'decimal']);
$installer->addAttribute('creditmemo_item', 'base_weee_tax_disposition', ['type' => 'decimal']);
$installer->addAttribute('creditmemo_item', 'base_weee_tax_row_disposition', ['type' => 'decimal']);
$installer->addAttribute('creditmemo_item', 'weee_tax_applied', ['type' => 'text']);
$installer->addAttribute('creditmemo_item', 'base_weee_tax_applied_amount', ['type' => 'decimal']);
$installer->addAttribute('creditmemo_item', 'base_weee_tax_applied_row_amnt', ['type' => 'decimal']);
$installer->addAttribute('creditmemo_item', 'weee_tax_applied_amount', ['type' => 'decimal']);
$installer->addAttribute('creditmemo_item', 'weee_tax_applied_row_amount', ['type' => 'decimal']);

$installer->endSetup();
