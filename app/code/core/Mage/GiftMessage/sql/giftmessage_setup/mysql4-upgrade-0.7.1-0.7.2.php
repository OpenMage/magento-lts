<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
