<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */

$this->startSetup()
    ->addAttribute('quote', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false])
    ->addAttribute('quote_address', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false])
    ->addAttribute('quote_item', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false])
    ->addAttribute('quote_address_item', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false])
    ->addAttribute('order', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false])
    ->addAttribute('order_item', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false])
    ->addAttribute('order_item', 'gift_message_available', ['type' => 'int', 'visible' => false, 'required' => false])
    ->addAttribute('catalog_product', 'gift_message_available', [
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
    ])
    ->removeAttribute('catalog_product', 'gift_message_aviable')
    ->setConfigData('sales/gift_messages/allow', 1)
    ->endSetup();
