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
 * @package     Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/*
$connection = $installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_index_eav'),
    'FK_CATALOG_PRODUCT_INDEX_EAV_ATTRIBUTE'
);

$connection = $installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_index_eav'),
    'FK_CATALOG_PRODUCT_INDEX_EAV_ENTITY'
);

$connection = $installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_index_eav'),
    'FK_CATALOG_PRODUCT_INDEX_EAV_STORE'
);

$connection = $installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog_product_index_eav', 'attribute_id', 'eav_attribute', 'attribute_id'),
    $installer->getTable('catalog_product_index_eav'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id'
);

$connection = $installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog_product_index_eav', 'entity_id', 'catalog_product_entity', 'entity_id'),
    $installer->getTable('catalog_product_index_eav'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id'
);

$connection = $installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog_product_index_eav', 'store_id', 'core_store', 'store_id'),
    $installer->getTable('catalog_product_index_eav'),
    'store_id',
    $installer->getTable('core_store'),
    'store_id'
);

$connection = $installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_index_price'),
    'FK_CATALOG_PRODUCT_INDEX_PRICE_CUSTOMER_GROUP'
);

$connection = $installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_index_price'),
    'FK_CATALOG_PRODUCT_INDEX_PRICE_ENTITY'
);

$connection = $installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog_product_index_price'),
    'FK_CATALOG_PRODUCT_INDEX_PRICE_WEBSITE'
);

$connection = $installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog_product_index_price', 'customer_group_id', 'customer_group', 'customer_group_id'),
    $installer->getTable('catalog_product_index_price'),
    'customer_group_id',
    $installer->getTable('customer_group'),
    'customer_group_id'
);

$connection = $installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog_product_index_price', 'entity_id', 'catalog_product_entity', 'entity_id'),
    $installer->getTable('catalog_product_index_price'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id'
);

$connection = $installer->getConnection()->addForeignKey(
    $installer->getFkName('catalog_product_index_price', 'website_id', 'core_website', 'website_id'),
    $installer->getTable('catalog_product_index_price'),
    'website_id',
    $installer->getTable('core_website'),
    'website_id'
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('catalog_product_index_eav'),
    'IDX_ENTITY'
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('catalog_product_index_eav'),
    'IDX_ATTRIBUTE'
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('catalog_product_index_eav'),
    'IDX_STORE'
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('catalog_product_index_eav'),
    'IDX_VALUE'
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_index_eav'),
    $installer->getIdxName('catalog_product_index_eav', array('entity_id')),
    array('entity_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_index_eav'),
    $installer->getIdxName('catalog_product_index_eav', array('attribute_id')),
    array('attribute_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_index_eav'),
    $installer->getIdxName('catalog_product_index_eav', array('store_id')),
    array('store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_index_eav'),
    $installer->getIdxName('catalog_product_index_eav', array('value')),
    array('value'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('catalog_product_index_price'),
    'IDX_CUSTOMER_GROUP'
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('catalog_product_index_price'),
    'IDX_WEBSITE'
);

$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('catalog_product_index_price'),
    'IDX_MIN_PRICE'
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_index_price'),
    $installer->getIdxName('catalog_product_index_price', array('customer_group_id')),
    array('customer_group_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_index_price'),
    $installer->getIdxName('catalog_product_index_price', array('website_id')),
    array('website_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$connection = $installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_index_price'),
    $installer->getIdxName('catalog_product_index_price', array('min_price')),
    array('min_price'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

*/
$installer->endSetup();
