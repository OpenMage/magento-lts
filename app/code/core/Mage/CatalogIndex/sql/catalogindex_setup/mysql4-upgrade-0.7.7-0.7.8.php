<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/** @var Mage_CatalogIndex_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('catalogindex_price'), 'website_id', 'smallint(5) unsigned');
$installer->getConnection()->addColumn($installer->getTable('catalogindex_minimal_price'), 'website_id', 'smallint(5) unsigned');

$installer->convertStoreToWebsite($installer->getTable('catalogindex_minimal_price'));
$installer->convertStoreToWebsite($installer->getTable('catalogindex_price'));

$installer->endSetup();
