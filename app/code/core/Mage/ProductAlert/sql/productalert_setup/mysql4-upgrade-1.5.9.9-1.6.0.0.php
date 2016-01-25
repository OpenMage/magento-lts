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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_WEBSITE'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/price'),
    'FK_PRODUCT_ALERT_PRICE_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('productalert/stock'),
    'FK_PRODUCT_ALERT_STOCK_WEBSITE'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('productalert/price') => array(
        'columns' => array(
            'alert_price_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product alert price id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer id'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product id'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price amount'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website id'
            ),
            'add_date' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Product alert add date'
            ),
            'last_send_date' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Product alert last send date'
            ),
            'send_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product alert send count'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product alert status'
            )
        ),
        'comment' => 'Product Alert Price'
    ),
    $installer->getTable('productalert/stock') => array(
        'columns' => array(
            'alert_stock_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Product alert stock id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer id'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product id'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website id'
            ),
            'add_date' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Product alert add date'
            ),
            'send_date' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Product alert send date'
            ),
            'send_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Send Count'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product alert status'
            )
        ),
        'comment' => 'Product Alert Stock'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('productalert/price'),
    $installer->getIdxName('productalert/price', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('productalert/price'),
    $installer->getIdxName('productalert/price', array('product_id')),
    array('product_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('productalert/price'),
    $installer->getIdxName('productalert/price', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('productalert/stock'),
    $installer->getIdxName('productalert/stock', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('productalert/stock'),
    $installer->getIdxName('productalert/stock', array('product_id')),
    array('product_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('productalert/stock'),
    $installer->getIdxName('productalert/stock', array('website_id')),
    array('website_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/price', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('productalert/price'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/price', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('productalert/price'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('productalert/price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/stock', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('productalert/stock'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/stock', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('productalert/stock'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('productalert/stock', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('productalert/stock'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->endSetup();
