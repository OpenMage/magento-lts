<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('downloadable/link_purchased'), 'order_item_id', "int(10) unsigned NOT NULL default '0' AFTER `order_increment_id`");

$conn->addKey($installer->getTable('downloadable/link_purchased'), 'KEY_DOWNLOADABLE_ORDER_ITEM_ID', 'order_item_id');

$conn->addConstraint(
    'FK_DOWNLOADABLE_PURCHASED_ORDER_ITEM_ID',
    $installer->getTable('downloadable/link_purchased'),
    'order_item_id',
    $installer->getTable('sales/order_item'),
    'item_id',
);

$select = $installer->getConnection()->select()
    ->from($installer->getTable('downloadable/link_purchased_item'), [
        'purchased_id',
        'order_item_id',
    ]);
$result = $installer->getConnection()->fetchAll($select);

foreach ($result as $row) {
    $installer->getConnection()->update(
        $installer->getTable('downloadable/link_purchased'),
        ['order_item_id' => $row['order_item_id']],
        $installer->getConnection()->quoteInto('purchased_id = ?', $row['purchased_id']),
    );
}

$installer->endSetup();
