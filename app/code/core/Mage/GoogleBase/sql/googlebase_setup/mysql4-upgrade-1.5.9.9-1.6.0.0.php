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
 * @package     Mage_GoogleBase
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('googlebase/attributes'),
    'GOOGLEBASE_ATTRIBUTES_ATTRIBUTE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('googlebase/attributes'),
    'GOOGLEBASE_ATTRIBUTES_TYPE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('googlebase/items'),
    'GOOGLEBASE_ITEMS_PRODUCT_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('googlebase/items'),
    'GOOGLEBASE_ITEMS_STORE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('googlebase/types'),
    'GOOGLEBASE_TYPES_ATTRIBUTE_SET_ID'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('googlebase/attributes'),
    'GOOGLEBASE_ATTRIBUTES_ATTRIBUTE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('googlebase/attributes'),
    'GOOGLEBASE_ATTRIBUTES_TYPE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('googlebase/items'),
    'GOOGLEBASE_ITEMS_PRODUCT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('googlebase/items'),
    'GOOGLEBASE_ITEMS_STORE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('googlebase/types'),
    'GOOGLEBASE_TYPES_ATTRIBUTE_SET_ID'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('googlebase/types') => array(
        'columns' => array(
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Type id'
            ),
            'attribute_set_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute set id'
            ),
            'gbase_itemtype' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Google base item type'
            ),
            'target_country' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 2,
                'nullable'  => false,
                'default'   => 'US',
                'comment'   => 'Target country'
            )
        ),
        'comment' => 'Google Base Item Types link Attribute Sets'
    ),
    $installer->getTable('googlebase/items') => array(
        'columns' => array(
            'item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Item id'
            ),
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Type id'
            ),
            'product_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Product id'
            ),
            'gbase_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Google base item id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ),
            'published' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Published'
            ),
            'expires' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Expires'
            ),
            'impr' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Google impressions'
            ),
            'clicks' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Google clicks'
            ),
            'views' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Google views'
            ),
            'is_hidden' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Hidden flag'
            )
        ),
        'comment' => 'Google Base Items Products'
    ),
    $installer->getTable('googlebase/attributes') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute id'
            ),
            'gbase_attribute' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Google base attribute'
            ),
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Type id'
            )
        ),
        'comment' => 'Google Base Attributes link Product Attributes'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('googlebase/attributes'),
    $installer->getIdxName('googlebase/attributes', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('googlebase/attributes'),
    $installer->getIdxName('googlebase/attributes', array('type_id')),
    array('type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('googlebase/items'),
    $installer->getIdxName('googlebase/items', array('product_id')),
    array('product_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('googlebase/items'),
    $installer->getIdxName('googlebase/items', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('googlebase/types'),
    $installer->getIdxName('googlebase/types', array('attribute_set_id')),
    array('attribute_set_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('googlebase/attributes', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('googlebase/attributes'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('googlebase/attributes', 'type_id', 'googlebase/types', 'type_id'),
    $installer->getTable('googlebase/attributes'),
    'type_id',
    $installer->getTable('googlebase/types'),
    'type_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('googlebase/items', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('googlebase/items'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('googlebase/items', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('googlebase/items'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('googlebase/types', 'attribute_set_id', 'eav/attribute_set', 'attribute_set_id'),
    $installer->getTable('googlebase/types'),
    'attribute_set_id',
    $installer->getTable('eav/attribute_set'),
    'attribute_set_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->endSetup();
