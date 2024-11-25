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
$installer->startSetup();

$installer->getConnection()->dropColumn($installer->getTable('sales_flat_quote_item'), 'super_product_id');
$installer->getConnection()->changeColumn($installer->getTable('sales_flat_quote_item'), 'parent_product_id', 'parent_item_id', 'INTEGER UNSIGNED DEFAULT NULL');
$installer->getConnection()->addConstraint(
    'FK_SALES_FLAT_QUOTE_ITEM_PARENT_ITEM',
    $installer->getTable('sales_flat_quote_item'),
    'parent_item_id',
    $installer->getTable('sales_flat_quote_item'),
    'item_id',
);

$installer->endSetup();
