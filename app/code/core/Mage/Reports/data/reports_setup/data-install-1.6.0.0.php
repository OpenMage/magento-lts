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
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
/*
 * Prepare database for data upgrade
 */
$installer->startSetup();
/*
 * Report Event Types default data
 */
$eventTypeData = array(
    array(
        'event_type_id' => Mage_Reports_Model_Event::EVENT_PRODUCT_VIEW,
        'event_name'    => 'catalog_product_view'
    ),
    array(
        'event_type_id' => Mage_Reports_Model_Event::EVENT_PRODUCT_SEND,
        'event_name'    => 'sendfriend_product'
    ),
    array(
        'event_type_id' => Mage_Reports_Model_Event::EVENT_PRODUCT_COMPARE,
        'event_name'    => 'catalog_product_compare_add_product'
    ),
    array(
        'event_type_id' => Mage_Reports_Model_Event::EVENT_PRODUCT_TO_CART,
        'event_name'    => 'checkout_cart_add_product'
    ),
    array(
        'event_type_id' => Mage_Reports_Model_Event::EVENT_PRODUCT_TO_WISHLIST,
        'event_name'    => 'wishlist_add_product'
    ),
    array(
        'event_type_id' => Mage_Reports_Model_Event::EVENT_WISHLIST_SHARE,
        'event_name'    => 'wishlist_share'
    )
);

foreach ($eventTypeData as $row) {
    $installer->getConnection()->insertForce($installer->getTable('reports/event_type'), $row);
}

/**
 * Prepare database after data upgrade
 */
$installer->endSetup();

/**
 * Cms Page  with 'home' identifier page modification for report pages
 */
/** @var $cms Mage_Cms_Model_Page */
$cms = Mage::getModel('cms/page')->load('home', 'identifier');

$reportLayoutUpdate    = '<!--<reference name="content">
        <block type="catalog/product_new" name="home.catalog.product.new" alias="product_new" template="catalog/product/new.phtml" after="cms_page">
            <action method="addPriceBlockType">
                <type>bundle</type>
                <block>bundle/catalog_product_price</block>
                <template>bundle/catalog/product/price.phtml</template>
            </action>
        </block>
        <block type="reports/product_viewed" name="home.reports.product.viewed" alias="product_viewed" template="reports/home_product_viewed.phtml" after="product_new">
            <action method="addPriceBlockType">
                <type>bundle</type>
                <block>bundle/catalog_product_price</block>
                <template>bundle/catalog/product/price.phtml</template>
            </action>
        </block>
        <block type="reports/product_compared" name="home.reports.product.compared" template="reports/home_product_compared.phtml" after="product_viewed">
            <action method="addPriceBlockType">
                <type>bundle</type>
                <block>bundle/catalog_product_price</block>
                <template>bundle/catalog/product/price.phtml</template>
            </action>
        </block>
    </reference>
    <reference name="right">
        <action method="unsetChild"><alias>right.reports.product.viewed</alias></action>
        <action method="unsetChild"><alias>right.reports.product.compared</alias></action>
    </reference>-->';

/*
 * Merge and save old layout update data with report layout data
 */
$cms->setLayoutUpdateXml($cms->getLayoutUpdateXml() . $reportLayoutUpdate)->save();
