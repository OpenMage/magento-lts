<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$connection = $installer->getConnection();

$indexTable = $installer->getTable('catalog/category_product_index');
$connection->modifyColumn($indexTable, 'position', 'int(10) unsigned NULL default NULL');

$tmpTable = $installer->getTable('catalog/category_anchor_products_indexer_idx');
$connection->addColumn($tmpTable, 'position', 'int(10) unsigned NULL default NULL');
