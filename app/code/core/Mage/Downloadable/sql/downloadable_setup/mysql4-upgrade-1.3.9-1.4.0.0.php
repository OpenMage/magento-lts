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
