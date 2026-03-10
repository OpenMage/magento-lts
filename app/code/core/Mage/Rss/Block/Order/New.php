<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

use Carbon\Carbon;

/**
 * Review form block
 *
 * @package    Mage_Rss
 */
class Mage_Rss_Block_Order_New extends Mage_Core_Block_Template
{
    /**
     * Cache tag constant for feed new orders
     *
     * @var string
     */
    public const CACHE_TAG = 'block_html_rss_order_new';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->setCacheTags([self::CACHE_TAG]);
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_order_new');
        $this->setCacheLifetime(600);
    }

    /**
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _toHtml()
    {
        $storeId = $this->getRequest()->getParam('store');
        $order = Mage::getModel('sales/order');
        $period = Mage::helper('rss')->getRssAdminOrderNewPeriod($storeId);
        $passDate = $order->getResource()->formatDate(
            mktime(0, 0, 0, (int) Carbon::now()->format('m'), (int) Carbon::now()->format('d') - $period),
        );

        $newurl = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order', ['_secure' => true, '_nosecret' => true]);
        $title = Mage::helper('rss')->__('New Orders');

        $rssObj = Mage::getModel('rss/rss');
        $data = ['title' => $title,
            'description' => $title,
            'link'        => $newurl,
            'charset'     => 'UTF-8',
        ];
        $rssObj->_addHeader($data);

        $collection = $order->getCollection()
            ->addAttributeToFilter('created_at', ['date' => true, 'from' => $passDate])
            ->addAttributeToSort('created_at', 'desc')
        ;

        if ($storeId) {
            $collection->addAttributeToFilter('store_id', $storeId);
        }

        $detailBlock = Mage::getBlockSingleton('rss/order_details');

        Mage::dispatchEvent('rss_order_new_collection_select', ['collection' => $collection]);

        Mage::getSingleton('core/resource_iterator')
            ->walk($collection->getSelect(), [[$this, 'addNewOrderXmlCallback']], ['rssObj' => $rssObj, 'order' => $order , 'detailBlock' => $detailBlock]);

        return $rssObj->createRssXml();
    }

    /**
     * @param array $args
     */
    public function addNewOrderXmlCallback($args)
    {
        $rssObj = $args['rssObj'];
        $order = $args['order'];
        $detailBlock = $args['detailBlock'];
        $order->reset()->load($args['row']['entity_id']);
        if ($order && $order->getId()) {
            $title = Mage::helper('rss')->__('Order #%s created at %s', $order->getIncrementId(), $this->formatDate($order->getCreatedAt()));
            $url = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', ['_secure' => true, 'order_id' => $order->getId(), '_nosecret' => true]);
            $detailBlock->setOrder($order);
            $data = [
                'title'         => $title,
                'link'          => $url,
                'description'   => $detailBlock->toHtml(),
            ];
            $rssObj->_addEntry($data);
        }
    }
}
