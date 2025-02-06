<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('order', 'can_ship_partially', ['type' => 'int']);
$installer->addAttribute('order', 'can_ship_partially_item', ['type' => 'int']);

$installer->endSetup();
