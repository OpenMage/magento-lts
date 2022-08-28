<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('catalog/product_index_eav_decimal');
$indexName = $installer->getConnection()->getPrimaryKeyName($tableName);

$tableNameTmp = $installer->getTable('catalog/product_eav_decimal_indexer_tmp');
$indexNameTmp = $installer->getConnection()->getPrimaryKeyName($tableNameTmp);

$fields = ['entity_id', 'attribute_id', 'store_id'];

$installer->getConnection()
        ->addIndex($tableName, $indexName, $fields, Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->getConnection()
        ->addIndex($tableNameTmp, $indexNameTmp, $fields, Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);
