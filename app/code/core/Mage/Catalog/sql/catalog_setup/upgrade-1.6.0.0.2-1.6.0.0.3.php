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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$tableName = $installer->getTable('catalog/product_index_eav_decimal');
$indexName = $installer->getConnection()->getPrimaryKeyName($tableName);


$tableNameTmp = $installer->getTable('catalog/product_eav_decimal_indexer_tmp');
$indexNameTmp = $installer->getConnection()->getPrimaryKeyName($tableNameTmp);

$fields = array('entity_id', 'attribute_id', 'store_id');

$installer->getConnection()
        ->addIndex($tableName, $indexName, $fields, Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->getConnection()
        ->addIndex($tableNameTmp, $indexNameTmp, $fields, Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

