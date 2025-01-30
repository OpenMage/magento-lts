<?php

/**
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
