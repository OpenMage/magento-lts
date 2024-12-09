<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    'cascade'
);

$installer->endSetup();
