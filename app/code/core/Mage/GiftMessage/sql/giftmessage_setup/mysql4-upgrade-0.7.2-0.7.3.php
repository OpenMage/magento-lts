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
 * @package     Mage_GiftMessage
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* $installer Mage_Core_Model_Resource_Setup */

$pathesForReplace = array(
    'sales/gift_messages/allow_order' => 'sales/gift_options/allow_order',
    'sales/gift_messages/allow_items' => 'sales/gift_options/allow_items'
);

foreach ($pathesForReplace as $from => $to) {
    $installer->run(sprintf("UPDATE `%s` SET `path` = '%s' WHERE `path` = '%s'",
        $this->getTable('core/config_data'), $to, $from
    ));
}

/*
 * Create new attribute group and move gift_message_available attribute to this group
 */
$entityTypeId = $installer->getEntityTypeId('catalog_product');
$attributeId  = $installer->getAttributeId('catalog_product', 'gift_message_available');

$attributeSets = $installer->_conn->fetchAll('select * from '.$this->getTable('eav/attribute_set').' where entity_type_id=?', $entityTypeId);
foreach ($attributeSets as $attributeSet) {
    $setId = $attributeSet['attribute_set_id'];
    $installer->addAttributeGroup($entityTypeId, $setId, 'Gift Options');
    $groupId = $installer->getAttributeGroupId($entityTypeId, $setId, 'Gift Options');
    $installer->addAttributeToGroup($entityTypeId, $setId, $groupId, $attributeId);
}
