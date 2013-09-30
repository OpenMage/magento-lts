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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $flatResource Mage_Catalog_Model_Resource_Product_Flat */
$flatResource = Mage::getResourceModel('catalog/product_flat');
/** @var $flag Mage_Catalog_Model_Product_Flat_Flag */
$flag = Mage::helper('catalog/product_flat')->getFlag();

Mage::app()->reinitStores();
foreach(Mage::app()->getStores() as $store) {
    $storeId = (int)$store->getId();
    $flag->setStoreBuilt($storeId, $flatResource->isBuilt($storeId));
}
$flag->save();

/**
 * Create table array('catalog/product', 'url_key')
 */
/**
 * @var $this Mage_Catalog_Model_Resource_Setup
 */
$productUrlKeyTableName = array('catalog/product', 'url_key');
$table = $this->getConnection()
    ->newTable($this->getTable($productUrlKeyTableName))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        'unsigned'  => true,
    ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Product Url Key')

    ->addIndex(
        $this->getIdxName(
            $productUrlKeyTableName,
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex($this->getIdxName($productUrlKeyTableName, array('attribute_id')), array('attribute_id'))
    ->addIndex($this->getIdxName($productUrlKeyTableName, array('store_id')), array('store_id'))
    ->addIndex($this->getIdxName($productUrlKeyTableName, array('entity_id')), array('entity_id'))

    ->addForeignKey(
        $this->getFkName($productUrlKeyTableName, 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $this->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $this->getFkName($productUrlKeyTableName, 'entity_id', 'catalog/product', 'entity_id'),
        'entity_id', $this->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $this->getFkName($productUrlKeyTableName, 'store_id', 'core/store', 'store_id'),
        'store_id', $this->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Product Url Key Attribute Backend Table');

$this->getConnection()->createTable($table);

/**
 * Create table array('catalog/category', 'url_key')
 */
$categoryUrlKeyTableName = array('catalog/category', 'url_key');
$table = $this->getConnection()
    ->newTable($this->getTable($categoryUrlKeyTableName))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        'unsigned'  => true,
    ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Entity Type ID')
        ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Category Url Key')

    ->addIndex(
        $this->getIdxName(
            $categoryUrlKeyTableName,
            array('entity_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($this->getIdxName($categoryUrlKeyTableName, array('attribute_id')), array('attribute_id'))
    ->addIndex($this->getIdxName($categoryUrlKeyTableName, array('store_id')), array('store_id'))
    ->addIndex($this->getIdxName($categoryUrlKeyTableName, array('entity_id')), array('entity_id'))

    ->addForeignKey(
        $this->getFkName($categoryUrlKeyTableName, 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $this->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $this->getFkName($categoryUrlKeyTableName, 'entity_id', 'catalog/category', 'entity_id'),
        'entity_id', $this->getTable('catalog/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $this->getFkName($categoryUrlKeyTableName, 'store_id', 'core/store', 'store_id'),
        'store_id', $this->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Category Url Key Attribute Backend Table');

$this->getConnection()->createTable($table);

$attributesUpdateData = array(
    Mage_Catalog_Model_Product::ENTITY => $productUrlKeyTableName,
    Mage_Catalog_Model_Category::ENTITY => $categoryUrlKeyTableName
);

foreach ($attributesUpdateData as $entityCode => $tableName) {
    $entityTypeId = $this->getEntityTypeId($entityCode);
    $attributeId = $this->getAttributeId($entityTypeId, 'url_key');
    $this->updateAttribute($entityTypeId, $attributeId, 'backend_table', $this->getTable($tableName));
}
