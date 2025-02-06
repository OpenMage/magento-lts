<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml abstract Rss controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Controller_Rss_Abstract extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check feed enabled in config
     *
     * @param string $code
     * @return bool
     */
    protected function isFeedEnable($code)
    {
        return Mage::helper('rss')->isRssEnabled()
            && Mage::getStoreConfig('rss/' . $code);
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
        } else {
            $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            $this->getResponse()->setHeader('Status', '404 File not found');
            $this->_forward('noRoute');
            return false;
        }
    }

    /**
     * Retrieve helper instance
     *
     * @param string $name
     * @return Mage_Core_Helper_Abstract
     * @deprecated this method is incompatible with parent class. Use Mage::helper instead
     */
    protected function _getHelper($name)
    {
        return Mage::helper($name);
    }
}
