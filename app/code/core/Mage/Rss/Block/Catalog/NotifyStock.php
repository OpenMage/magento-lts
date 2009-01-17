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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_Catalog_NotifyStock extends Mage_Rss_Block_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_notifystock');
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $newurl = Mage::getUrl('rss/catalog/notifystock');
        $title = Mage::helper('rss')->__('Low Stock Products');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                );
        $rssObj->_addHeader($data);

        $stockItemWhere = "({{table}}.low_stock_date is not null) and ({{table}}.low_stock_date>'0000-00-00')";

        $product = Mage::getModel('catalog/product');
        $collection = $product->getCollection()
            ->addAttributeToSelect('name', true)
            ->joinTable('cataloginventory/stock_item', 'product_id=entity_id', array('qty'=>'qty', 'notify_stock_qty'=>'notify_stock_qty', 'use_config' => 'use_config_notify_stock_qty','low_stock_date' => 'low_stock_date'), $stockItemWhere, 'inner')
            ->setOrder('low_stock_date')
        ;

        $_globalNotifyStockQty = (float) Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY);

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        Mage::getSingleton('core/resource_iterator')
            ->walk($collection->getSelect(), array(array($this, 'addNotifyItemXmlCallback')), array('rssObj'=> $rssObj, 'product'=>$product, 'globalQty' => $_globalNotifyStockQty));

        return $rssObj->createRssXml();
    }

    public function addNotifyItemXmlCallback($args)
    {
        $product = $args['product'];
        $product->setData($args['row']);
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit/', array('id'=>$product->getId(),'_secure' => true));
        $description = Mage::helper('rss')->__('%s has reached a quantity of %s.', $product->getName(),(1*$product->getQty()));
        $rssObj = $args['rssObj'];
        $data = array(
        'title'         => $product->getName(),
        'link'          => $url,
        'description'   => $description,
        );
        $rssObj->_addEntry($data);
    }
}