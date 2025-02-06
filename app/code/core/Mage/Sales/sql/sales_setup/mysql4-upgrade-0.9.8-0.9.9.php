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

$installer->addAttribute('invoice', 'store_id', ['type' => 'static']);
$installer->addAttribute('creditmemo', 'store_id', ['type' => 'static']);
$installer->addAttribute('shipment', 'store_id', ['type' => 'static']);
