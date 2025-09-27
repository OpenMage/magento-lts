<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/** @var Mage_GiftMessage_Model_Resource_Setup $this */
$installer = $this;

$installer->updateAttribute(
    'catalog_product',
    'gift_message_available',
    'source_model',
    'eav/entity_attribute_source_boolean',
);

$installer->updateAttribute(
    'catalog_product',
    'gift_message_available',
    'backend_model',
    'catalog/product_attribute_backend_boolean',
);

$installer->updateAttribute(
    'catalog_product',
    'gift_message_available',
    'frontend_input_renderer',
    'adminhtml/catalog_product_helper_form_config',
);

$installer->updateAttribute(
    'catalog_product',
    'gift_message_available',
    'default_value',
    '',
);

/*
 * Update previously saved data for 'gift_message_available' attribute
 */
$entityTypeId = $installer->getEntityTypeId('catalog_product');
$attributeId  = $installer->getAttributeId($entityTypeId, 'gift_message_available');

$installer->getConnection()->update(
    $installer->getTable('catalog_product_entity_varchar'),
    ['value' => ''],
    [
        'entity_type_id =?' => $entityTypeId,
        'attribute_id =?' => $attributeId,
        'value =?' => '2',
    ],
);
