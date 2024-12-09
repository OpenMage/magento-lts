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

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_super_attribute_label'),
    'IDX_CATALOG_PRODUCT_SUPER_ATTRIBUTE_STORE_PSAI_SI',
    ['product_super_attribute_id', 'store_id']
);

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_price'),
    'IDX_CATALOG_PRODUCT_OPTION_PRICE_SI_OI',
    ['store_id', 'option_id']
);

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TITLE_SI_OI',
    ['store_id', 'option_id']
);

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_type_price'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_PRICE_SI_OTI',
    ['store_id', 'option_type_id']
);

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_type_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_TITLE_SI_OTI',
    ['store_id', 'option_type_id']
);

$installer->endSetup();
