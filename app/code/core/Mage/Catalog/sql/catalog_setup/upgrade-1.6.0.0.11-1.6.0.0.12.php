<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer  = $this;

$indexFields = ['website_id', 'customer_group_id', 'min_price'];
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_price'),
    $installer->getIdxName('catalog/product_index_price', $indexFields),
    $indexFields,
);
