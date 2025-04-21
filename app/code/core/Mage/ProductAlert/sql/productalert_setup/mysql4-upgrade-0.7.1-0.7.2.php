<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
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
