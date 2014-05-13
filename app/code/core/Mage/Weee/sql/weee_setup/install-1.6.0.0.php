<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Weee
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Weee_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
/**
 * Create table 'weee/tax'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('weee/tax'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('country', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
        'nullable'  => true,
        ), 'Country')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Value')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '*',
        ), 'State')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Attribute Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Entity Type Id')
    ->addIndex($installer->getIdxName('weee/tax', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('weee/tax', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('weee/tax', array('country')),
        array('country'))
    ->addIndex($installer->getIdxName('weee/tax', array('attribute_id')),
        array('attribute_id'))
    ->addForeignKey($installer->getFkName('weee/tax', 'country', 'directory/country', 'country_id'),
        'country', $installer->getTable('directory/country'), 'country_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('weee/tax', 'entity_id', 'catalog/product', 'entity_id'),
        'entity_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('weee/tax', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('weee/tax', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Weee Tax');
$installer->getConnection()->createTable($table);

/**
 * Create table 'weee/discount'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('weee/discount'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Customer Group Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Value')
    ->addIndex($installer->getIdxName('weee/discount', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('weee/discount', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('weee/discount', array('customer_group_id')),
        array('customer_group_id'))
    ->addForeignKey(
        $installer->getFkName('weee/discount', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
        'customer_group_id', $installer->getTable('customer/customer_group'), 'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('weee/discount', 'entity_id', 'catalog/product', 'entity_id'),
        'entity_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('weee/discount', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Weee Discount');
$installer->getConnection()->createTable($table);

$installer->addAttribute('order_item', 'base_weee_tax_applied_amount', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'base_weee_tax_applied_row_amnt', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'weee_tax_applied_amount', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'weee_tax_applied_row_amount', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'weee_tax_applied', array('type'=>'text'));

$installer->addAttribute('quote_item', 'weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'weee_tax_row_disposition', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'base_weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'base_weee_tax_row_disposition', array('type'=>'decimal'));

$installer->addAttribute('order_item', 'weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'weee_tax_row_disposition', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'base_weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'base_weee_tax_row_disposition', array('type'=>'decimal'));

$installer->addAttribute('invoice_item', 'base_weee_tax_applied_amount', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'base_weee_tax_applied_row_amnt', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'weee_tax_applied_amount', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'weee_tax_applied_row_amount', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'weee_tax_applied', array('type'=>'text'));
$installer->addAttribute('invoice_item', 'weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'weee_tax_row_disposition', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'base_weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'base_weee_tax_row_disposition', array('type'=>'decimal'));

$installer->addAttribute('quote_item', 'weee_tax_applied', array('type'=>'text'));
$installer->addAttribute('quote_item', 'weee_tax_applied_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'weee_tax_applied_row_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'base_weee_tax_applied_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'base_weee_tax_applied_row_amnt', array('type'=>'decimal'));

$installer->addAttribute('creditmemo_item', 'weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'weee_tax_row_disposition', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'base_weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'base_weee_tax_row_disposition', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'weee_tax_applied', array('type'=>'text'));
$installer->addAttribute('creditmemo_item', 'base_weee_tax_applied_amount', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'base_weee_tax_applied_row_amnt', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'weee_tax_applied_amount', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'weee_tax_applied_row_amount', array('type'=>'decimal'));

$installer->endSetup();
