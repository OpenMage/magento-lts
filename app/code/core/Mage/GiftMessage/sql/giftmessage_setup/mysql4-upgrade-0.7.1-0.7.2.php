<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */

/** @var Mage_GiftMessage_Model_Resource_Setup $this */
$installer = $this;

$installer->addAttribute('quote', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('quote_address', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('quote_item', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('quote_address_item', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('order', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('order_item', 'gift_message_id', ['type' => 'int', 'visible' => false, 'required' => false]);
$installer->addAttribute('order_item', 'gift_message_available', ['type' => 'int', 'visible' => false, 'required' => false]);
