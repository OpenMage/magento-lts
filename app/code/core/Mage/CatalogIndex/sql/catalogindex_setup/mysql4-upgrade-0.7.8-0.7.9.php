<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->getConnection()->dropColumn($installer->getTable('catalogindex_price'), 'store_id');
$installer->getConnection()->dropColumn($installer->getTable('catalogindex_minimal_price'), 'store_id');

$installer->getConnection()->addConstraint('FK_CI_PRICE_WEBSITE_ID', $installer->getTable('catalogindex_price'), 'website_id', $installer->getTable('core_website'), 'website_id');
$installer->getConnection()->addConstraint('FK_CI_MINIMAL_PRICE_WEBSITE_ID', $installer->getTable('catalogindex_minimal_price'), 'website_id', $installer->getTable('core_website'), 'website_id');

$installer->getConnection()->addKey($installer->getTable('catalogindex_price'), 'IDX_FULL', ['entity_id', 'attribute_id', 'customer_group_id', 'value', 'website_id']);
$installer->getConnection()->addKey($installer->getTable('catalogindex_minimal_price'), 'IDX_FULL', ['entity_id', 'qty', 'customer_group_id', 'value', 'website_id']);

$installer->endSetup();
