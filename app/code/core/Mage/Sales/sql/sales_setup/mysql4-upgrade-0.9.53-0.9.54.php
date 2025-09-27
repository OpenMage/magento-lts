<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
