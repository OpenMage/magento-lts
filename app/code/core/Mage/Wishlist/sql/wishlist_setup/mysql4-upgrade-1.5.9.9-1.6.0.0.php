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
 * @package     Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('wishlist/wishlist'),
    'FK_WISHLIST_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('wishlist/item'),
    'FK_WISHLIST_ITEM_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('wishlist/item'),
    'FK_WISHLIST_ITEM_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('wishlist/item'),
    'FK_WISHLIST_ITEM_WISHLIST'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('wishlist/item_option'),
    'FK_WISHLIST_ITEM_OPTION_ITEM_ID'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/wishlist'),
    'UNQ_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/wishlist'),
    'IDX_IS_SHARED'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/item'),
    'IDX_WISHLIST'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/item'),
    'IDX_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/item'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('wishlist/item_option'),
    'FK_WISHLIST_ITEM_OPTION_ITEM_ID'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('wishlist/wishlist') => array(
        'columns' => array(
            'wishlist_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Wishlist ID'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer ID'
            ),
            'shared' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sharing flag (0 or 1)'
            ),
            'sharing_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Sharing encrypted code'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Last updated date'
            )
        ),
        'comment' => 'Wishlist main Table'
    ),
    $installer->getTable('wishlist/item') => array(
        'columns' => array(
            'wishlist_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Wishlist item ID'
            ),
            'wishlist_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Wishlist ID'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Product ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store ID'
            ),
            'added_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Add date and time'
            ),
            'description' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Short description of wish list item'
            ),
            'qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'comment'   => 'Qty'
            )
        ),
        'comment' => 'Wishlist items'
    ),
    $installer->getTable('wishlist/item_option') => array(
        'columns' => array(
            'option_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Id'
            ),
            'wishlist_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Wishlist Item Id'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Code'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => true,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Wishlist Item Option Table'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('wishlist/wishlist'),
    $installer->getIdxName(
        'wishlist/wishlist',
        array('customer_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
    array('customer_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('wishlist/wishlist'),
    $installer->getIdxName('wishlist/wishlist', array('shared')),
    array('shared')
);

$installer->getConnection()->addIndex(
    $installer->getTable('wishlist/item'),
    $installer->getIdxName('wishlist/item', array('wishlist_id')),
    array('wishlist_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('wishlist/item'),
    $installer->getIdxName('wishlist/item', array('product_id')),
    array('product_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('wishlist/item'),
    $installer->getIdxName('wishlist/item', array('store_id')),
    array('store_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist/wishlist', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('wishlist/wishlist'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist/item', 'wishlist_id', 'wishlist/wishlist', 'wishlist_id'),
    $installer->getTable('wishlist/item'),
    'wishlist_id',
    $installer->getTable('wishlist/wishlist'),
    'wishlist_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist/item', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('wishlist/item'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist/item', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('wishlist/item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist/item_option', 'wishlist_item_id', 'wishlist/item', 'wishlist_item_id'),
    $installer->getTable('wishlist/item_option'),
    'wishlist_item_id',
    $installer->getTable('wishlist/item'),
    'wishlist_item_id'
);

$installer->endSetup();
