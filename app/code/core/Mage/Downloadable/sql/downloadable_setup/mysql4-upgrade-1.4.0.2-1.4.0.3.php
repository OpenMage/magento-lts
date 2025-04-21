<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$adapter = $installer->getConnection();

$adapter->modifyColumn(
    $installer->getTable('downloadable/link_purchased'),
    'order_id',
    "INT(10) UNSIGNED NULL DEFAULT '0'",
);

$adapter->modifyColumn(
    $installer->getTable('downloadable/link_purchased_item'),
    'order_item_id',
    "INT(10) UNSIGNED NULL DEFAULT '0'",
);

/**
 * Update order_id/order_item_id to NULL which contained invalid values
 */
$adapter->beginTransaction();
//update downloadable purchased data
$select = $adapter->select()
    ->from(['d' => $installer->getTable('downloadable/link_purchased')], ['purchased_id', 'purchased_id'])
    ->joinLeft(
        ['o' => $installer->getTable('sales/order')],
        'd.order_id = o.entity_id',
        [],
    )
    ->where('o.entity_id IS NULL')
    ->where('d.order_id IS NOT NULL')
;
$ids = $adapter->fetchPairs($select);
if ($ids) {
    $adapter->update(
        $installer->getTable('downloadable/link_purchased'),
        ['order_id' => new Zend_Db_Expr('(NULL)')],
        $adapter->quoteInto('purchased_id IN (?)', $ids),
    );
}
//update downloadable purchased items data
$select = $adapter->select()
    ->from(['d' => $installer->getTable('downloadable/link_purchased_item')], ['item_id', 'item_id'])
    ->joinLeft(
        ['o' => $installer->getTable('sales/order_item')],
        'd.order_item_id = o.item_id',
        [],
    )
    ->where('o.item_id IS NULL')
    ->where('d.order_item_id IS NOT NULL')
;
$ids = $adapter->fetchPairs($select);
if ($ids) {
    $adapter->update(
        $installer->getTable('downloadable/link_purchased_item'),
        ['order_item_id' => new Zend_Db_Expr('(NULL)')],
        $adapter->quoteInto('item_id IN (?)', $ids),
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
    'set null',
);
$adapter->addConstraint(
    'FK_DOWNLOADABLE_LINK_ORDER_ITEM_ID',
    $installer->getTable('downloadable/link_purchased_item'),
    'order_item_id',
    $installer->getTable('sales/order_item'),
    'item_id',
    'set null',
);
