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
