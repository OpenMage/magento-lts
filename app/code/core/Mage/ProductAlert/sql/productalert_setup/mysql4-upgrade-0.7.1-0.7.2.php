<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

// fix for sample data 1.2.0
$installer->getConnection()->changeTableEngine($installer->getTable('productalert/stock'), 'INNODB');
$installer->getConnection()->dropKey($installer->getTable('productalert/stock'), 'FK_PRODUCT_ALERT_PRICE_CUSTOMER');
$installer->getConnection()->dropKey($installer->getTable('productalert/stock'), 'FK_PRODUCT_ALERT_PRICE_PRODUCT');
$installer->getConnection()->dropKey($installer->getTable('productalert/stock'), 'FK_PRODUCT_ALERT_PRICE_WEBSITE');
$installer->getConnection()->addConstraint(
    'FK_PRODUCT_ALERT_STOCK_CUSTOMER',
    $installer->getTable('productalert/stock'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_PRODUCT_ALERT_STOCK_PRODUCT',
    $installer->getTable('productalert/stock'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_PRODUCT_ALERT_STOCK_WEBSITE',
    $installer->getTable('productalert/stock'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->endSetup();
