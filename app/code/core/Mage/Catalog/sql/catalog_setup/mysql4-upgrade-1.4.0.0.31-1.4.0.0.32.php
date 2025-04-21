<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$connection = $installer->getConnection();

$indexTable = $installer->getTable('catalog/category_product_index');
$connection->modifyColumn($indexTable, 'position', 'int(10) unsigned NULL default NULL');

$tmpTable = $installer->getTable('catalog/category_anchor_products_indexer_idx');
$connection->addColumn($tmpTable, 'position', 'int(10) unsigned NULL default NULL');
