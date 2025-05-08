<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/** @var Mage_GiftMessage_Model_Resource_Setup $installer */
$installer = $this;

$installer->updateAttribute('catalog_product', 'gift_message_available', 'is_configurable', 0);
