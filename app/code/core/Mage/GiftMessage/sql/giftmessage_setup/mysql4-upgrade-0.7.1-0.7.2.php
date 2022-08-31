<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_GiftMessage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
