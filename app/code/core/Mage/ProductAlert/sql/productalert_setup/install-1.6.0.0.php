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
 * @package     Mage_ProductAlert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ProductAlert install
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var Mage_Core_Model_Resource_Setup $installer */

$installer->startSetup();
/**
 * Create table 'productalert/price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('productalert/price'))
    ->addColumn('alert_price_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Product alert price id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product id')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Price amount')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website id')
    ->addColumn('add_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Product alert add date')
    ->addColumn('last_send_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Product alert last send date')
    ->addColumn('send_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product alert send count')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product alert status')
    ->addIndex(
        $installer->getIdxName('productalert/price', array('customer_id')),
        array('customer_id')
    )
    ->addIndex(
        $installer->getIdxName('productalert/price', array('product_id')),
        array('product_id')
    )
    ->addIndex(
        $installer->getIdxName('productalert/price', array('website_id')),
        array('website_id')
    )
    ->addForeignKey(
        $installer->getFkName('productalert/price', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('productalert/price', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('productalert/price', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Product Alert Price');
$installer->getConnection()->createTable($table);

/**
 * Create table 'productalert/stock'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('productalert/stock'))
    ->addColumn('alert_stock_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Product alert stock id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website id')
    ->addColumn('add_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Product alert add date')
    ->addColumn('send_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Product alert send date')
    ->addColumn('send_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Send Count')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product alert status')
    ->addIndex(
        $installer->getIdxName('productalert/stock', array('customer_id')),
        array('customer_id')
    )
    ->addIndex(
        $installer->getIdxName('productalert/stock', array('product_id')),
        array('product_id')
    )
    ->addIndex(
        $installer->getIdxName('productalert/stock', array('website_id')),
        array('website_id')
    )
    ->addForeignKey(
        $installer->getFkName('productalert/stock', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('productalert/stock', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('productalert/stock', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Product Alert Stock');
$installer->getConnection()->createTable($table);

$installer->endSetup();
