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

// Remove sales foreign keys
$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable_link_purchased'),
    'FK_DOWNLOADABLE_ORDER_ID',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable_link_purchased'),
    'FK_DOWNLOADABLE_PURCHASED_ORDER_ITEM_ID',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('downloadable_link_purchased_item'),
    'FK_DOWNLOADABLE_ORDER_ITEM_ID',
);

$installer->endSetup();
