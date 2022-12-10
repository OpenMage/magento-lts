<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_option_price'),
    'IDX_CATALOG_PRODUCT_OPTION_PRICE_SI_OI'
);
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_option_price'),
    'UNQ_OPTION_STORE',
    ['option_id', 'store_id'],
    'unique'
);

$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_option_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TITLE_SI_OI'
);
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_option_title'),
    'UNQ_OPTION_STORE',
    ['option_id', 'store_id'],
    'unique'
);

$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_option_type_price'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_PRICE_SI_OTI'
);
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_option_type_price'),
    'UNQ_OPTION_TYPE_STORE',
    ['option_type_id', 'store_id'],
    'unique'
);

$installer->getConnection()->dropKey(
    $installer->getTable('catalog/product_option_type_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_TITLE_SI_OTI'
);
$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_option_type_title'),
    'UNQ_OPTION_TYPE_STORE',
    ['option_type_id', 'store_id'],
    'unique'
);

$installer->endSetup();
