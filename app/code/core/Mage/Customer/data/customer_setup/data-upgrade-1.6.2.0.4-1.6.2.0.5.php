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
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

$eavConfig = Mage::getSingleton('eav/config');
$customerEntityTypeId = $eavConfig->getEntityType('customer')->getEntityTypeId();
$customerAddressEntityTypeId = $eavConfig->getEntityType('customer_address')->getEntityTypeId();

$entityTypeIds = array($customerAddressEntityTypeId, $customerEntityTypeId);

$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
    ->addFieldToFilter('frontend_input', 'multiselect')
    ->addFieldToFilter('entity_type_id', array('in' => $entityTypeIds))
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
            ->from($attributeTableOld, array('entity_type_id', 'attribute_id', 'entity_id', 'value'))
            ->where('entity_type_id = ?', $entityTypeId)
            ->where('attribute_id = ?', $attributeId);

        $query = $select->insertFromSelect($attributeTableNew,
            array('entity_type_id', 'attribute_id', 'entity_id', 'value')
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

$installer->endSetup();
