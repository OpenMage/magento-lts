<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$orderTable = $installer->getTable('sales/order');

$installer->run("
UPDATE {$orderTable} SET
    base_discount_canceled = (ABS(base_discount_amount) - IFNULL(base_discount_invoiced, 0)),
    base_total_canceled = (base_subtotal_canceled + IFNULL(base_tax_canceled, 0) + IFNULL(base_shipping_canceled, 0) - IFNULL(ABS(base_discount_amount) - IFNULL(base_discount_invoiced, 0), 0)),
    discount_canceled = (ABS(discount_amount) - IFNULL(discount_invoiced, 0)),
    total_canceled = (subtotal_canceled + IFNULL(tax_canceled, 0) + IFNULL(shipping_canceled, 0) - IFNULL(ABS(discount_amount) - IFNULL(discount_invoiced, 0), 0))
");
