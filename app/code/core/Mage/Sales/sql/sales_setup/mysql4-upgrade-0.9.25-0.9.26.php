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

$installer->run("
DELETE FROM `{$this->getTable('sales_order_tax')}`
WHERE `order_id` NOT IN (
    SELECT `entity_id` FROM `{$this->getTable('sales_order')}`
)
");

$installer->getConnection()->addConstraint(
    'FK_SALES_ORDER_TAX_ORDER',
    $this->getTable('sales_order_tax'),
    'order_id',
    $this->getTable('sales_order'),
    'entity_id',
);
