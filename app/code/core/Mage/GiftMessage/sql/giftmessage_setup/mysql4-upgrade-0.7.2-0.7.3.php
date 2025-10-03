<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
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
