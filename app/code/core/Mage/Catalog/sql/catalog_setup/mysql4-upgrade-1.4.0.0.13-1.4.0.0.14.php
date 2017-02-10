<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();
$installer->getConnection()->dropKey($installer->getTable('catalog/product_option_price'),
    'IDX_CATALOG_PRODUCT_OPTION_PRICE_SI_OI');
$installer->getConnection()->addKey($installer->getTable('catalog/product_option_price'),
    'UNQ_OPTION_STORE', array('option_id', 'store_id'), 'unique');

$installer->getConnection()->dropKey($installer->getTable('catalog/product_option_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TITLE_SI_OI');
$installer->getConnection()->addKey($installer->getTable('catalog/product_option_title'),
    'UNQ_OPTION_STORE', array('option_id', 'store_id'), 'unique');

$installer->getConnection()->dropKey($installer->getTable('catalog/product_option_type_price'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_PRICE_SI_OTI');
$installer->getConnection()->addKey($installer->getTable('catalog/product_option_type_price'),
    'UNQ_OPTION_TYPE_STORE', array('option_type_id', 'store_id'), 'unique');

$installer->getConnection()->dropKey($installer->getTable('catalog/product_option_type_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_TITLE_SI_OTI');
$installer->getConnection()->addKey($installer->getTable('catalog/product_option_type_title'),
    'UNQ_OPTION_TYPE_STORE', array('option_type_id', 'store_id'), 'unique');

$installer->endSetup();
