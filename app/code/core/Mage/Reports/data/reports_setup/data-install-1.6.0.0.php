<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/*
 * Report Event Types default data
 */
$eventTypeData = [
    [
        'event_type_id' => Mage_Reports_Model_Event::EVENT_PRODUCT_VIEW,
        'event_name'    => 'catalog_product_view',
    ],
    [
        'event_type_id' => Mage_Reports_Model_Event::EVENT_PRODUCT_SEND,
        'event_name'    => 'sendfriend_product',
    ],
    [
        'event_type_id' => Mage_Reports_Model_Event::EVENT_PRODUCT_COMPARE,
        'event_name'    => 'catalog_product_compare_add_product',
    ],
    [
        'event_type_id' => Mage_Reports_Model_Event::EVENT_PRODUCT_TO_CART,
        'event_name'    => 'checkout_cart_add_product',
    ],
    [
        'event_type_id' => Mage_Reports_Model_Event::EVENT_PRODUCT_TO_WISHLIST,
        'event_name'    => 'wishlist_add_product',
    ],
    [
        'event_type_id' => Mage_Reports_Model_Event::EVENT_WISHLIST_SHARE,
        'event_name'    => 'wishlist_share',
    ],
];

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
/** @var Mage_Cms_Model_Page $cms */
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
