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

$installer->getConnection()->addKey($installer->getTable('catalog_product_entity_int'), 'IDX_ATTRIBUTE_VALUE', ['entity_id', 'attribute_id', 'store_id']);
$installer->getConnection()->addKey($installer->getTable('catalog_product_entity_datetime'), 'IDX_ATTRIBUTE_VALUE', ['entity_id', 'attribute_id', 'store_id']);
$installer->getConnection()->addKey($installer->getTable('catalog_product_entity_decimal'), 'IDX_ATTRIBUTE_VALUE', ['entity_id', 'attribute_id', 'store_id']);
$installer->getConnection()->addKey($installer->getTable('catalog_product_entity_text'), 'IDX_ATTRIBUTE_VALUE', ['entity_id', 'attribute_id', 'store_id']);
$installer->getConnection()->addKey($installer->getTable('catalog_product_entity_varchar'), 'IDX_ATTRIBUTE_VALUE', ['entity_id', 'attribute_id', 'store_id']);

$installer->endSetup();
