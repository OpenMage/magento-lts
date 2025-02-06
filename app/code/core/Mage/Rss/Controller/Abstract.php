<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Rss
 */

/**
 * Rss abstract controller
 *
 * @category   Mage
 * @package    Mage_Rss
 */
class Mage_Rss_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Check feed enabled in config
     *
     * @param string $code
     * @return bool
     */
    protected function isFeedEnable($code)
    {
        /** @var Mage_Rss_Helper_Data $helper */
        $helper = $this->_getHelper('rss');
        return $helper->isRssEnabled() && Mage::getStoreConfig('rss/' . $code);
    }

    /**
     * Do check feed enabled and prepare response
     *
     * @param string $code
     * @return bool
     */
    protected function checkFeedEnable($code)
    {
        if ($this->isFeedEnable($code)) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            return true;
        }

        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');
        $this->_forward('nofeed', 'index', 'rss');
        return false;
    }

    /**
     * Retrieve helper instance
     *
     * @param string $name
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($name)
    {
        return Mage::helper($name);
    }
}
