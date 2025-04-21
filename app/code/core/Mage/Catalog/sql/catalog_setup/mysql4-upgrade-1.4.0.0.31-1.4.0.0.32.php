<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$connection = $installer->getConnection();

$indexTable = $installer->getTable('catalog/category_product_index');
$connection->modifyColumn($indexTable, 'position', 'int(10) unsigned NULL default NULL');

$tmpTable = $installer->getTable('catalog/category_anchor_products_indexer_idx');
$connection->addColumn($tmpTable, 'position', 'int(10) unsigned NULL default NULL');
