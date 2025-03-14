<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_GiftMessage_Model_Resource_Setup $this */
$installer = $this;

$pathesForReplace = [
    'sales/gift_messages/allow_order' => 'sales/gift_options/allow_order',
    'sales/gift_messages/allow_items' => 'sales/gift_options/allow_items',
];

foreach ($pathesForReplace as $from => $to) {
    $installer->run(sprintf(
        "UPDATE `%s` SET `path` = '%s' WHERE `path` = '%s'",
        $this->getTable('core/config_data'),
        $to,
        $from,
    ));
}

/*
 * Create new attribute group and move gift_message_available attribute to this group
 */
$entityTypeId = $installer->getEntityTypeId('catalog_product');
$attributeId  = $installer->getAttributeId('catalog_product', 'gift_message_available');

$attributeSets = $installer->_conn->fetchAll('select * from ' . $this->getTable('eav/attribute_set') . ' where entity_type_id=?', $entityTypeId);
foreach ($attributeSets as $attributeSet) {
    $setId = $attributeSet['attribute_set_id'];
    $installer->addAttributeGroup($entityTypeId, $setId, 'Gift Options');
    $groupId = $installer->getAttributeGroupId($entityTypeId, $setId, 'Gift Options');
    $installer->addAttributeToGroup($entityTypeId, $setId, $groupId, $attributeId);
}
