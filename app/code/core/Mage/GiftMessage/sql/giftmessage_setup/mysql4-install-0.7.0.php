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

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('gift_message')};
CREATE TABLE {$this->getTable('gift_message')} (
    `gift_message_id` int(7) unsigned NOT NULL auto_increment,
    `customer_id` int(7) unsigned NOT NULL default '0',
    `sender` varchar(255) NOT NULL default '',
    `recipient` varchar(255) NOT NULL default '',
    `message` text NOT NULL,
    PRIMARY KEY  (`gift_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();

$installer->addAttribute('quote', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('quote_address', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('quote_item', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('quote_address_item', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('order', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('order_item', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('order_item', 'gift_message_available', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('catalog_product', 'gift_message_available', [
    'backend'       => 'giftmessage/entity_attribute_backend_boolean_config',
    'frontend'      => '',
    'label'         => 'Allow Gift Message',
    'input'         => 'select',
    'class'         => '',
    'source'        => 'giftmessage/entity_attribute_source_boolean_config',
    'global'        => true,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '2',
    'visible_on_front' => false,
]);
