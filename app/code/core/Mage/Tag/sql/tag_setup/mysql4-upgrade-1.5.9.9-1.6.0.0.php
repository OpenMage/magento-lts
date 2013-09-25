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
 * @package     Mage_Tag
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('tag/properties'),
    'FK_TAG_PROPERTIES_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tag/properties'),
    'FK_TAG_PROPERTIES_TAG'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tag/relation'),
    'FK_TAG_RELATION_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tag/relation'),
    'FK_TAG_RELATION_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tag/relation'),
    'FK_TAG_RELATION_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tag/relation'),
    'FK_TAG_RELATION_TAG'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tag/summary'),
    'FK_TAG_SUMMARY_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tag/summary'),
    'FK_TAG_SUMMARY_TAG'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('tag/relation'),
    'UNQ_TAG_CUSTOMER_PRODUCT_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tag/relation'),
    'IDX_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tag/relation'),
    'IDX_TAG'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tag/relation'),
    'IDX_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tag/relation'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tag/properties'),
    'FK_TAG_PROPERTIES_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tag/summary'),
    'FK_TAG_SUMMARY_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('tag/summary'),
    'IDX_TAG'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('tag/tag') => array(
        'columns' => array(
            'tag_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tag Id'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Status'
            ),
            'first_customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'First Customer Id'
            ),
            'first_store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'First Store Id'
            )
        ),
        'comment' => 'Tag'
    ),
    $installer->getTable('tag/relation') => array(
        'columns' => array(
            'tag_relation_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Tag Relation Id'
            ),
            'tag_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Tag Id'
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
                'default'   => '0',
                'comment'   => 'Product Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Store Id'
            ),
            'active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Active'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            )
        ),
        'comment' => 'Tag Relation'
    ),
    $installer->getTable('tag/summary') => array(
        'columns' => array(
            'tag_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Tag Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'customers' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customers'
            ),
            'products' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Products'
            ),
            'uses' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Uses'
            ),
            'historical_uses' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Historical Uses'
            ),
            'popularity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Popularity'
            ),
            'base_popularity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Base Popularity'
            )
        ),
        'comment' => 'Tag Summary'
    ),
    $installer->getTable('tag/properties') => array(
        'columns' => array(
            'tag_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Tag Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'base_popularity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Base Popularity'
            )
        ),
        'comment' => 'Tag Properties'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('tag/properties'),
    $installer->getIdxName('tag/properties', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tag/relation'),
    $installer->getIdxName(
        'tag/relation',
        array('tag_id', 'customer_id', 'product_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('tag_id', 'customer_id', 'product_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('tag/relation'),
    $installer->getIdxName('tag/relation', array('product_id')),
    array('product_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tag/relation'),
    $installer->getIdxName('tag/relation', array('tag_id')),
    array('tag_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tag/relation'),
    $installer->getIdxName('tag/relation', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tag/relation'),
    $installer->getIdxName('tag/relation', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tag/summary'),
    $installer->getIdxName('tag/summary', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('tag/summary'),
    $installer->getIdxName('tag/summary', array('tag_id')),
    array('tag_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('tag/tag', 'first_customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('tag/tag'),
    'first_customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tag/tag', 'first_store_id', 'core/store', 'store_id'),
    $installer->getTable('tag/tag'),
    'first_store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tag/properties', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('tag/properties'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tag/properties', 'tag_id', 'tag/tag', 'tag_id'),
    $installer->getTable('tag/properties'),
    'tag_id',
    $installer->getTable('tag/tag'),
    'tag_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tag/relation', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('tag/relation'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tag/relation', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('tag/relation'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tag/relation', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('tag/relation'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tag/relation', 'tag_id', 'tag/tag', 'tag_id'),
    $installer->getTable('tag/relation'),
    'tag_id',
    $installer->getTable('tag/tag'),
    'tag_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tag/summary', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('tag/summary'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tag/summary', 'tag_id', 'tag/tag', 'tag_id'),
    $installer->getTable('tag/summary'),
    'tag_id',
    $installer->getTable('tag/tag'),
    'tag_id'
);

$installer->endSetup();
