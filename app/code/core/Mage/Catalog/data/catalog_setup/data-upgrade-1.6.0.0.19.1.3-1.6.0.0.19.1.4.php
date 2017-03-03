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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/** @var $installer Mage_Catalog_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$catalogProductEntityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getEntityTypeId();

$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
    ->addFieldToFilter('frontend_input', 'multiselect')
    ->addFieldToFilter('entity_type_id', $catalogProductEntityTypeId)
    ->getItems();

foreach ($attributes as $attribute) {
    $entityTypeId = $attribute->getEntityTypeId();
    $attributeId = $attribute->getId();
    $attributeTableOld = $installer->getAttributeTable($entityTypeId, $attributeId);

    $installer->updateAttribute($entityTypeId, $attributeId, 'backend_type', 'text');

    $attributeTableNew = $installer->getAttributeTable($entityTypeId, $attributeId);

    if ($attributeTableOld != $attributeTableNew) {
        $connection->disableTableKeys($attributeTableOld)
            ->disableTableKeys($attributeTableNew);

        $select = $connection->select()
            ->from($attributeTableOld, array('entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value'))
            ->where('entity_type_id = ?', $entityTypeId)
            ->where('attribute_id = ?', $attributeId);

        $query = $select->insertFromSelect($attributeTableNew,
            array('entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value')
        );

        $connection->query($query);

        $connection->delete($attributeTableOld,
            $connection->quoteInto('entity_type_id = ?', $entityTypeId)
            . $connection->quoteInto(' AND attribute_id = ?', $attributeId)
        );

        $connection->enableTableKeys($attributeTableOld)
            ->enableTableKeys($attributeTableNew);
    }
}

Mage::getModel('index/indexer')
    ->getProcessByCode(Mage_Catalog_Helper_Product_Flat::CATALOG_FLAT_PROCESS_CODE)
    ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);

$installer->endSetup();
