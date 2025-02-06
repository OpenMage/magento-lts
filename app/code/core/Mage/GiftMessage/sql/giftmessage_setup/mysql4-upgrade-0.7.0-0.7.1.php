<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */

/** @var Mage_GiftMessage_Model_Resource_Setup $installer */
$installer = $this;

$installer->updateAttribute('catalog_product', 'gift_message_available', 'is_configurable', 0);
