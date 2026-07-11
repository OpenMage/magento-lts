<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * Rss Observer Model
 *
 * @package    Mage_Rss
 */
class Mage_Rss_Model_Observer
{
    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_factory;

    /**
     * Application instance
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    public function __construct(array $args = [])
    {
        $this->_factory = empty($args['factory']) ? Mage::getSingleton('core/factory') : $args['factory'];
        $this->_app = empty($args['app']) ? Mage::app() : $args['app'];
    }

    /**
     * Clean cache for catalog review rss
     */
    public function reviewSaveAfter(Varien_Event_Observer $observer)
    {
        $this->_cleanCache(Mage_Rss_Block_Catalog_Review::CACHE_TAG);
    }

    /**
     * Clean cache for notify stock rss
     */
    public function salesOrderItemSaveAfterNotifyStock(Varien_Event_Observer $observer)
    {
        $this->_cleanCache(Mage_Rss_Block_Catalog_NotifyStock::CACHE_TAG);
    }

    /**
     * Clean cache for catalog new orders rss
     */
    public function salesOrderItemSaveAfterOrderNew(Varien_Event_Observer $observer)
    {
        $this->_cleanCache(Mage_Rss_Block_Order_New::CACHE_TAG);
    }

    /**
     * Cleaning cache
     *
     * @param string $tag
     */
    protected function _cleanCache($tag)
    {
        if ($this->_factory->getHelper('rss')->isRssEnabled()) {
            $this->_app->cleanCache([$tag]);
        }
    }
}
