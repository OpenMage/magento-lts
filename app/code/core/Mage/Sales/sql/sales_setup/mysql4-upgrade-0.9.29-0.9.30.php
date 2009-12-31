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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$tableOrder       = $this->getTable('sales/order');
$tableOrderEntity = $this->getTable('sales/order_entity');

$cleanTables = array(
    $tableOrder . '_datetime',
    $tableOrder . '_decimal',
    $tableOrder . '_int',
    $tableOrder . '_text',
    $tableOrder . '_varchar',
    $tableOrderEntity . '_decimal',
    $tableOrderEntity . '_datetime',
    $tableOrderEntity . '_int',
    $tableOrderEntity . '_text',
    $tableOrderEntity . '_varchar'
);
foreach ($cleanTables as $tableName) {
    $select = $installer->getConnection()->select()
        ->from($tableName, array(
            'entity_id'         => 'entity_id',
            'attribute_id'      => 'attribute_id',
            'entity_type_id'    => 'entity_type_id',
            'rows_count'        => 'COUNT(*)'))
        ->group(array('entity_id', 'attribute_id', 'entity_type_id'))
        ->having('rows_count > 1');
    $query = $installer->getConnection()->query($select);

    while ($row = $query->fetch()) {
        $sql = 'DELETE FROM `' . $tableName . '`'
            . ' WHERE entity_id=? AND attribute_id=? AND entity_type_id=?'
            . ' LIMIT ' . ($row['rows_count'] - 1);
        $installer->getConnection()->query($sql, array(
            $row['entity_id'],
            $row['attribute_id'],
            $row['entity_type_id']
        ));
    }
}

$installer->getConnection()->addKey("{$tableOrder}_datetime", 'UNQ_ENTITY_ATTRIBUTE_TYPE', array('entity_id', 'attribute_id', 'entity_type_id'), 'unique');
$installer->getConnection()->addKey("{$tableOrder}_decimal", 'UNQ_ENTITY_ATTRIBUTE_TYPE', array('entity_id', 'attribute_id', 'entity_type_id'), 'unique');
$installer->getConnection()->addKey("{$tableOrder}_int", 'UNQ_ENTITY_ATTRIBUTE_TYPE', array('entity_id', 'attribute_id', 'entity_type_id'), 'unique');
$installer->getConnection()->addKey("{$tableOrder}_text", 'UNQ_ENTITY_ATTRIBUTE_TYPE', array('entity_id', 'attribute_id', 'entity_type_id'), 'unique');
$installer->getConnection()->addKey("{$tableOrder}_varchar", 'UNQ_ENTITY_ATTRIBUTE_TYPE', array('entity_id', 'attribute_id', 'entity_type_id'), 'unique');

$installer->getConnection()->addKey("{$tableOrderEntity}_datetime", 'UNQ_ENTITY_ATTRIBUTE_TYPE', array('entity_id', 'attribute_id', 'entity_type_id'), 'unique');
$installer->getConnection()->addKey("{$tableOrderEntity}_decimal", 'UNQ_ENTITY_ATTRIBUTE_TYPE', array('entity_id', 'attribute_id', 'entity_type_id'), 'unique');
$installer->getConnection()->addKey("{$tableOrderEntity}_int", 'UNQ_ENTITY_ATTRIBUTE_TYPE', array('entity_id', 'attribute_id', 'entity_type_id'), 'unique');
$installer->getConnection()->addKey("{$tableOrderEntity}_text", 'UNQ_ENTITY_ATTRIBUTE_TYPE', array('entity_id', 'attribute_id', 'entity_type_id'), 'unique');
$installer->getConnection()->addKey("{$tableOrderEntity}_varchar", 'UNQ_ENTITY_ATTRIBUTE_TYPE', array('entity_id', 'attribute_id', 'entity_type_id'), 'unique');

$installer->getConnection()->addKey($tableOrderEntity, 'IDX_SALES_ORDER_ENTITY_PARENT', 'parent_id');
