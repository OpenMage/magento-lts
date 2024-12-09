<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addConstraint(
    'FK_CATALOGRULE_PRODUCT_PRODUCT',
    $installer->getTable('catalogrule_product'),
    'product_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id'
);

$installer->getConnection()->addConstraint(
    'FK_CATALOGRULE_PRODUCT_PRICE_PRODUCT',
    $installer->getTable('catalogrule_product_price'),
    'product_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id'
);

$installer->endSetup();
