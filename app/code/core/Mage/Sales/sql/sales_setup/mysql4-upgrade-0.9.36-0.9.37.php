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

$installer->addAttribute('order_item', 'locked_do_invoice', ['type' => 'int', 'default' => 0]);
$installer->addAttribute('order_item', 'locked_do_ship', ['type' => 'int', 'default' => 0]);

$installer->endSetup();
