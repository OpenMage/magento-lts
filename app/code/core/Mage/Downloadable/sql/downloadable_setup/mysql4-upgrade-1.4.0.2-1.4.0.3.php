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
 * @package     Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$adapter = $installer->getConnection();

$adapter->modifyColumn(
    $installer->getTable('downloadable/link_purchased'),
    'order_id',
    "INT(10) UNSIGNED NULL DEFAULT '0'"
);

$adapter->modifyColumn(
    $installer->getTable('downloadable/link_purchased_item'),
    'order_item_id',
    "INT(10) UNSIGNED NULL DEFAULT '0'"
);

/**
 * Update order_id/order_item_id to NULL which contained invalid values
 */
$adapter->beginTransaction();
//update downloadable purchased data
$select = $adapter->select()
    ->from(array('d' => $installer->getTable('downloadable/link_purchased')), array('purchased_id', 'purchased_id'))
    ->joinLeft(array('o' => $installer->getTable('sales/order')),
        'd.order_id = o.entity_id', array())
    ->where('o.entity_id IS NULL')
    ->where('d.order_id IS NOT NULL')
;
$ids = $adapter->fetchPairs($select);
if ($ids) {
    $adapter->update($installer->getTable('downloadable/link_purchased'),
        array('order_id' => new Zend_Db_Expr('(NULL)')),
        $adapter->quoteInto('purchased_id IN (?)', $ids)
    );
}
//update downloadable purchased items data
$select = $adapter->select()
    ->from(array('d' => $installer->getTable('downloadable/link_purchased_item')), array('item_id', 'item_id'))
    ->joinLeft(array('o' => $installer->getTable('sales/order_item')),
        'd.order_item_id = o.item_id', array())
    ->where('o.item_id IS NULL')
    ->where('d.order_item_id IS NOT NULL')
;
$ids = $adapter->fetchPairs($select);
if ($ids) {
    $adapter->update($installer->getTable('downloadable/link_purchased_item'),
        array('order_item_id' => new Zend_Db_Expr('(NULL)')),
        $adapter->quoteInto('item_id IN (?)', $ids)
    );
}
$adapter->commit();

//add foreign keys
$adapter->addConstraint(
    'FK_DOWNLOADABLE_LINK_ORDER_ID',
    $installer->getTable('downloadable/link_purchased'),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id',
    'set null'
);
$adapter->addConstraint(
    'FK_DOWNLOADABLE_LINK_ORDER_ITEM_ID',
    $installer->getTable('downloadable/link_purchased_item'),
    'order_item_id',
    $installer->getTable('sales/order_item'),
    'item_id',
    'set null'
);
