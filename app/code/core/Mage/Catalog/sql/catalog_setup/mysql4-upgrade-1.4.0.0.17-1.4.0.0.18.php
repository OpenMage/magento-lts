<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_attribute_tier_price'),
    'UNQ_CATALOG_PRODUCT_TIER_PRICE',
    ['entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id'],
    'unique',
);

$installer->endSetup();
