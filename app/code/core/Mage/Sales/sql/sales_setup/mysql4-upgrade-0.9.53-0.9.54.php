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
$this->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_aggregated_created'),
    'base_canceled_amount',
    'decimal(12,4) NOT NULL DEFAULT \'0\'',
);

$this->endSetup();
