<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('order', 'ext_order_item_id');
$installer->addAttribute('order_item', 'ext_order_item_id', ['type' => 'varchar']);

$installer->endSetup();
