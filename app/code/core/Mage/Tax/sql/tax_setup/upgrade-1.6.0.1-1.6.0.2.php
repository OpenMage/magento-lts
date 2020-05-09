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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Tax_Model_Resource_Setup */
$installer = $this;

/**
 * Create table 'tax/sales_order_tax_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tax/sales_order_tax_item'))
    ->addColumn('tax_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Tax Item Id')
    ->addColumn('tax_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Tax Id')
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Item Id')
    ->addIndex($installer->getIdxName('tax/sales_order_tax_item', array('tax_id')),
        array('tax_id'))
    ->addIndex($installer->getIdxName('tax/sales_order_tax_item', array('item_id')),
        array('item_id'))
    ->addIndex(
        $installer->getIdxName(
            'tax/sales_order_tax_item', array('tax_id', 'item_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('tax_id', 'item_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addForeignKey(
        $installer->getFkName(
            'tax/sales_order_tax_item',
            'tax_id',
            'tax/sales_order_tax',
            'tax_id'
        ),
        'tax_id',
        $installer->getTable('tax/sales_order_tax'),
        'tax_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'tax/sales_order_tax_item',
            'item_id',
            'sales_flat_order_item',
            'item_id'
        ),
        'item_id',
        $installer->getTable('sales_flat_order_item'),
        'item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Sales Order Tax Item');
$installer->getConnection()->createTable($table);
