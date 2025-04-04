<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
