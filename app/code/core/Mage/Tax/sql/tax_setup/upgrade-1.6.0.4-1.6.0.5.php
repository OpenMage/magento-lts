<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Tax_Model_Resource_Setup $this */

$this->startSetup();

$taxTable = $this->getTable('tax/sales_order_tax');
$orderTable = $this->getTable('sales/order');

// adds FK_SALES_ORDER_TAX_ORDER back again
$this->getConnection()->addForeignKey(
    $this->getFkName($taxTable, 'order_id', $orderTable, 'entity_id'),
    $taxTable,
    'order_id',
    $orderTable,
    'entity_id',
    Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
    Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
    true,
);

$this->endSetup();
