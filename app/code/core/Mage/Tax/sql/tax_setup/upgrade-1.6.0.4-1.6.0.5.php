<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
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
