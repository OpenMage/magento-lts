<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
