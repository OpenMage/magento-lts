<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @var Mage_Sales_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->addAttribute('quote_item', 'original_custom_price', ['type' => 'decimal']);
