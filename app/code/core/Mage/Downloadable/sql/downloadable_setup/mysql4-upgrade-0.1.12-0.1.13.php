<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('downloadable/link_purchased_item'), 'link_hash', "varchar(255) NOT NULL default '' AFTER `product_id`");

$installer->getConnection()->addKey($installer->getTable('downloadable/link_purchased_item'), 'DOWNLOADALBE_LINK_HASH', 'link_hash');

$select = $installer->getConnection()->select()
    ->from($installer->getTable('downloadable/link_purchased_item'), [
        'item_id',
        'purchased_id',
        'order_item_id',
        'product_id',
    ]);
$result = $installer->getConnection()->fetchAll($select);

foreach ($result as $row) {
    $installer->getConnection()->update(
        $installer->getTable('downloadable/link_purchased_item'),
        ['link_hash' => strtr(base64_encode(microtime() . $row['purchased_id'] . $row['order_item_id'] . $row['product_id']), '+/=', '-_,')],
        $installer->getConnection()->quoteInto('item_id = ?', $row['item_id']),
    );
}

$installer->endSetup();
