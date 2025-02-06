<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$connection = $installer->getConnection();
$table      = $installer->getTable('catalog/category_product_indexer_idx');
$connection->addKey($table, 'IDX_PRODUCT_CATEGORY_STORE', ['product_id', 'category_id', 'store_id']);
