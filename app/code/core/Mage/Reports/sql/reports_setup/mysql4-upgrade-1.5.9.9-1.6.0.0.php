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
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/compared_product_index'),
    'FK_REPORT_COMPARED_PRODUCT_INDEX_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/compared_product_index'),
    'FK_REPORT_COMPARED_PRODUCT_INDEX_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/compared_product_index'),
    'FK_REPORT_COMPARED_PRODUCT_INDEX_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/event'),
    'FK_REPORT_EVENT_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/event'),
    'FK_REPORT_EVENT_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/viewed_product_index'),
    'FK_REPORT_VIEWED_PRODUCT_INDEX_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/viewed_product_index'),
    'FK_REPORT_VIEWED_PRODUCT_INDEX_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('reports/viewed_product_index'),
    'FK_REPORT_VIEWED_PRODUCT_INDEX_STORE'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('reports/compared_product_index'),
    'UNQ_BY_VISITOR'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/compared_product_index'),
    'UNQ_BY_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/compared_product_index'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/compared_product_index'),
    'IDX_SORT_ADDED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/compared_product_index'),
    'PRODUCT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/event'),
    'IDX_EVENT_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/event'),
    'IDX_SUBJECT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/event'),
    'IDX_OBJECT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/event'),
    'IDX_SUBTYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/event'),
    'FK_REPORT_EVENT_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/viewed_product_index'),
    'UNQ_BY_VISITOR'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/viewed_product_index'),
    'UNQ_BY_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/viewed_product_index'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/viewed_product_index'),
    'IDX_SORT_ADDED_AT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('reports/viewed_product_index'),
    'PRODUCT_ID'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('reports/event') => array(
        'columns' => array(
            'event_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Event Id'
            ),
            'logged_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Logged At'
            ),
            'event_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Event Type Id'
            ),
            'object_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Object Id'
            ),
            'subject_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Subject Id'
            ),
            'subtype' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Subtype'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            )
        ),
        'comment' => 'Reports Event Table'
    ),
    $installer->getTable('reports/event_type') => array(
        'columns' => array(
            'event_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Event Type Id'
            ),
            'event_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Event Name'
            ),
            'customer_login' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer Login'
            )
        ),
        'comment' => 'Reports Event Type Table'
    ),
    $installer->getTable('reports/compared_product_index') => array(
        'columns' => array(
            'index_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Index Id'
            ),
            'visitor_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Visitor Id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'added_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Added At'
            )
        ),
        'comment' => 'Reports Compared Product Index Table'
    ),
    $installer->getTable('reports/viewed_product_index') => array(
        'columns' => array(
            'index_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Index Id'
            ),
            'visitor_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Visitor Id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store Id'
            ),
            'added_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Added At'
            )
        ),
        'comment' => 'Reports Viewed Product Index Table'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('reports/compared_product_index'),
    $installer->getIdxName(
        'reports/compared_product_index',
        array('visitor_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('visitor_id', 'product_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/compared_product_index'),
    $installer->getIdxName(
        'reports/compared_product_index',
        array('customer_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('customer_id', 'product_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/compared_product_index'),
    $installer->getIdxName('reports/compared_product_index', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/compared_product_index'),
    $installer->getIdxName('reports/compared_product_index', array('added_at')),
    array('added_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/compared_product_index'),
    $installer->getIdxName('reports/compared_product_index', array('product_id')),
    array('product_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/event'),
    $installer->getIdxName('reports/event', array('event_type_id')),
    array('event_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/event'),
    $installer->getIdxName('reports/event', array('subject_id')),
    array('subject_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/event'),
    $installer->getIdxName('reports/event', array('object_id')),
    array('object_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/event'),
    $installer->getIdxName('reports/event', array('subtype')),
    array('subtype')
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/event'),
    $installer->getIdxName('reports/event', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/viewed_product_index'),
    $installer->getIdxName(
        'reports/viewed_product_index',
        array('visitor_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('visitor_id', 'product_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/viewed_product_index'),
    $installer->getIdxName(
        'reports/viewed_product_index',
        array('customer_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('customer_id', 'product_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/viewed_product_index'),
    $installer->getIdxName('reports/viewed_product_index', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/viewed_product_index'),
    $installer->getIdxName('reports/viewed_product_index', array('added_at')),
    array('added_at')
);

$installer->getConnection()->addIndex(
    $installer->getTable('reports/viewed_product_index'),
    $installer->getIdxName('reports/viewed_product_index', array('product_id')),
    array('product_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/compared_product_index', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('reports/compared_product_index'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/compared_product_index', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('reports/compared_product_index'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/compared_product_index', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('reports/compared_product_index'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/event', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('reports/event'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/event', 'event_type_id', 'reports/event_type', 'event_type_id'),
    $installer->getTable('reports/event'),
    'event_type_id',
    $installer->getTable('reports/event_type'),
    'event_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/viewed_product_index', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('reports/viewed_product_index'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/viewed_product_index', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('reports/viewed_product_index'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('reports/viewed_product_index', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('reports/viewed_product_index'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->endSetup();
