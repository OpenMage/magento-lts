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
 * @package     Mage_Wishlist
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->dropForeignKey($installer->getTable('wishlist/item'), 'FK_WISHLIST_ITEM_STORE');
$installer->getConnection()->dropForeignKey($installer->getTable('wishlist/item'), 'FK_ITEM_WISHLIST');
$installer->getConnection()->dropForeignKey($installer->getTable('wishlist/item'), 'FK_WISHLIST_PRODUCT');
$installer->getConnection()->dropForeignKey($installer->getTable('wishlist/wishlist'), 'FK_CUSTOMER');

$installer->getConnection()->dropKey($installer->getTable('wishlist/item'), 'FK_ITEM_WISHLIST');
$installer->getConnection()->dropKey($installer->getTable('wishlist/item'), 'FK_WISHLIST_PRODUCT');
$installer->getConnection()->dropKey($installer->getTable('wishlist/item'), 'FK_WISHLIST_STORE');
$installer->getConnection()->dropKey($installer->getTable('wishlist/wishlist'), 'FK_CUSTOMER');

$installer->getConnection()->modifyColumn($installer->getTable('wishlist/item'), 'store_id',
    'smallint UNSIGNED DEFAULT NULL');

$installer->getConnection()->addKey($installer->getTable('wishlist/item'), 'IDX_WISHLIST', 'wishlist_id');
$installer->getConnection()->addKey($installer->getTable('wishlist/item'), 'IDX_PRODUCT', 'product_id');
$installer->getConnection()->addKey($installer->getTable('wishlist/item'), 'IDX_STORE', 'store_id');
$installer->getConnection()->addKey($installer->getTable('wishlist/wishlist'), 'UNQ_CUSTOMER', 'customer_id', 'unique');
$installer->getConnection()->addKey($installer->getTable('wishlist/wishlist'), 'IDX_IS_SHARED', 'shared');

$installer->getConnection()->addConstraint('FK_WISHLIST_ITEM_STORE',
    $installer->getTable('wishlist/item'), 'store_id',
    $installer->getTable('core/store'), 'store_id',
    'set null', 'cascade'
);
$installer->getConnection()->addConstraint('FK_WISHLIST_ITEM_WISHLIST',
    $installer->getTable('wishlist/item'), 'wishlist_id',
    $installer->getTable('wishlist/wishlist'), 'wishlist_id',
    'cascade', 'cascade'
);
$installer->getConnection()->addConstraint('FK_WISHLIST_ITEM_PRODUCT',
    $installer->getTable('wishlist/item'), 'product_id',
    $installer->getTable('catalog/product'), 'entity_id',
    'cascade', 'cascade'
);
$installer->getConnection()->addConstraint('FK_WISHLIST_CUSTOMER',
    $installer->getTable('wishlist/wishlist'), 'customer_id',
    $installer->getTable('customer/entity'), 'entity_id',
    'cascade', 'cascade'
);

$installer->endSetup();
