<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
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
    'entity_id',
);
