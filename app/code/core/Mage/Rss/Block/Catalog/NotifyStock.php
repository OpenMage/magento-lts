<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_Catalog_NotifyStock extends Mage_Rss_Block_Abstract
{

    /**
     * Cache tag constant for feed notify stock
     *
     * @var string
     */
    const CACHE_TAG = 'block_html_rss_catalog_notifystock';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setCacheTags([self::CACHE_TAG]);
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_notifystock');
        $this->setCacheLifetime(600);
    }

    /**
     * Render RSS
     *
     * @return string
     */
    protected function _toHtml()
    {
        $newUrl = Mage::getUrl('rss/catalog/notifystock');
        $title = Mage::helper('rss')->__('Low Stock Products');

        $rssObj = Mage::getModel('rss/rss');
        $data = [
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
        ];
        $rssObj->_addHeader($data);

        $globalNotifyStockQty = (float) Mage::getStoreConfig(
            Mage_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY);
        Mage::helper('rss')->disableFlat();
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product');
        $collection = $product->getCollection();
        Mage::getResourceModel('cataloginventory/stock')->addLowStockFilter($collection, [
            'qty',
            'notify_stock_qty',
            'low_stock_date',
            'use_config' => 'use_config_notify_stock_qty'
        ]);
        $collection
            ->addAttributeToSelect('name', true)
            ->addAttributeToFilter('status',
                ['in' => Mage::getSingleton('catalog/product_status')->getVisibleStatusIds()]
            )
            ->setOrder('low_stock_date');
        Mage::dispatchEvent('rss_catalog_notify_stock_collection_select', ['collection' => $collection]);

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        Mage::getSingleton('core/resource_iterator')->walk(
            $collection->getSelect(),
            [[$this, 'addNotifyItemXmlCallback']],
            ['rssObj'=> $rssObj, 'product'=>$product, 'globalQty' => $globalNotifyStockQty]
        );

        return $rssObj->createRssXml();
    }

    /**
     * Adds single product to feed
     *
     * @param array $args
     */
    public function addNotifyItemXmlCallback($args)
    {
        $product = $args['product'];
        $product->setData($args['row']);
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit/',
            ['id' => $product->getId(), '_secure' => true, '_nosecret' => true]);
        $qty = 1 * $product->getQty();
        $description = Mage::helper('rss')->__('%s has reached a quantity of %s.', $product->getName(), $qty);
        $rssObj = $args['rssObj'];
        $data = [
            'title'         => $product->getName(),
            'link'          => $url,
            'description'   => $description,
        ];
        $rssObj->_addEntry($data);
    }
}
