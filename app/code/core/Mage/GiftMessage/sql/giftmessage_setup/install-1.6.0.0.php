<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/** @var Mage_GiftMessage_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'gift_message'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('giftmessage/message'))
    ->addColumn('gift_message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'GiftMessage Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Customer id')
    ->addColumn('sender', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Sender')
    ->addColumn('recipient', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Recipient')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
    ], 'Message')
    ->setComment('Gift Message');

$installer->getConnection()->createTable($table);

/**
 * Add 'gift_message_id' attributes for entities
 */
$entities = [
    'quote',
    'quote_address',
    'quote_item',
    'quote_address_item',
    'order',
    'order_item',
];
$options = [
    'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'visible'  => false,
    'required' => false,
];
foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'gift_message_id', $options);
}

/**
 * Add 'gift_message_available' attributes for entities
 */
$installer->addAttribute('order_item', 'gift_message_available', $options);
// @phpstan-ignore argument.type
Mage::getResourceModel('catalog/setup', 'catalog_setup')->addAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'gift_message_available',
    [
        'group'         => 'Gift Options',
        'backend'       => 'catalog/product_attribute_backend_boolean',
        'frontend'      => '',
        'label'         => 'Allow Gift Message',
        'input'         => 'select',
        'class'         => '',
        'source'        => 'eav/entity_attribute_source_boolean',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => '',
        'input_renderer'   => 'giftmessage/adminhtml_product_helper_form_config',
        'is_configurable'  => 0,
        'visible_on_front' => false,
    ],
);

$installer->endSetup();
