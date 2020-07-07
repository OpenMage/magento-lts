<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer  = $this;
$connection = $installer->getConnection();

$connection->addIndex(
    $installer->getTable('catalog/category_product_indexer_tmp'),
    $installer->getIdxName('catalog/category_product_indexer_tmp', array('product_id', 'category_id', 'store_id')),
    array('product_id', 'category_id', 'store_id')
);

$table = $installer->getTable('catalog/category_product_enabled_indexer_idx');
$connection->dropIndex($table, 'IDX_CATALOG_CATEGORY_PRODUCT_INDEX_ENBL_IDX_PRODUCT_ID');
$connection->addIndex(
    $table,
    $installer->getIdxName('catalog/category_product_enabled_indexer_idx', array('product_id', 'visibility')),
    array('product_id', 'visibility')
);


$table = $installer->getTable('catalog/category_product_enabled_indexer_tmp');
$connection->dropIndex($table, 'IDX_CATALOG_CATEGORY_PRODUCT_INDEX_ENBL_TMP_PRODUCT_ID');
$connection->addIndex(
    $table,
    $installer->getIdxName('catalog/category_product_enabled_indexer_tmp', array('product_id', 'visibility')),
    array('product_id', 'visibility')
);

$connection->addIndex(
    $installer->getTable('catalog/category_anchor_products_indexer_idx'),
    $installer->getIdxName(
        'catalog/category_anchor_products_indexer_idx',
        array('category_id', 'product_id', 'position')
    ),
    array('category_id', 'product_id', 'position')
);

$connection->addIndex(
    $installer->getTable('catalog/category_anchor_products_indexer_tmp'),
    $installer->getIdxName(
        'catalog/category_anchor_products_indexer_tmp',
        array('category_id', 'product_id', 'position')
    ),
    array('category_id', 'product_id', 'position')
);

$connection->addIndex(
    $installer->getTable('catalog/category_anchor_indexer_idx'),
    $installer->getIdxName(
        'catalog/category_anchor_indexer_idx',
        array('path', 'category_id')
    ),
    array('path', 'category_id')
);

$connection->addIndex(
    $installer->getTable('catalog/category_anchor_indexer_tmp'),
    $installer->getIdxName(
        'catalog/category_anchor_indexer_tmp',
        array('path', 'category_id')
    ),
    array('path', 'category_id')
);

$connection->addIndex(
    $installer->getTable('catalog/category'),
    $installer->getIdxName(
        'catalog/category',
        array('path', 'entity_id')
    ),
    array('path', 'entity_id')
);
