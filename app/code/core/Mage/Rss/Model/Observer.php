<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rss Observer Model
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
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

    /**
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
        $this->_app = !empty($args['app']) ? $args['app'] : Mage::app();
    }

    /**
     * Clean cache for catalog review rss
     *
     * @param Varien_Event_Observer $observer
     */
    public function reviewSaveAfter(Varien_Event_Observer $observer)
    {
        $this->_cleanCache(Mage_Rss_Block_Catalog_Review::CACHE_TAG);
    }

    /**
     * Clean cache for notify stock rss
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesOrderItemSaveAfterNotifyStock(Varien_Event_Observer $observer)
    {
        $this->_cleanCache(Mage_Rss_Block_Catalog_NotifyStock::CACHE_TAG);
    }

    /**
     * Clean cache for catalog new orders rss
     *
     * @param Varien_Event_Observer $observer
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
