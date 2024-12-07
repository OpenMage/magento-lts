<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
