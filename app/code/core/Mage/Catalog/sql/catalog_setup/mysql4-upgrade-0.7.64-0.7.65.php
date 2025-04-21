<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection->addColumn($installer->getTable('catalog/product_super_attribute_pricing'), 'website_id', 'smallint(5) UNSIGNED NOT NULL DEFAULT 0');
$connection->addConstraint(
    'FK_CATALOG_PRODUCT_SUPER_PRICE_WEBSITE',
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
    'cascade',
    'cascade',
);

$installer->endSetup();
