<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

// add FK constraint on products to flat quote items

$installer->run("
DELETE FROM `{$this->getTable('sales_flat_quote_item')}`
WHERE `product_id` NOT IN (
    SELECT `entity_id` FROM `{$this->getTable('catalog_product_entity')}`
)
");

$installer->getConnection()->addConstraint(
    'FK_SALES_QUOTE_ITEM_CATALOG_PRODUCT_ENTITY',
    $this->getTable('sales_flat_quote_item'),
    'product_id',
    $this->getTable('catalog_product_entity'),
    'entity_id'
);
