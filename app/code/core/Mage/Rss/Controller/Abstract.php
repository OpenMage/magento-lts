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
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rss abstract controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Check feed enabled in config
     *
     * @param string $code
     * @return boolean
     */
    protected function isFeedEnable($code)
    {
        /** @var Mage_Rss_Helper_Data $helper */
        $helper = $this->_getHelper('rss');
        return $helper->isRssEnabled() && Mage::getStoreConfig('rss/'. $code);
    }

    /**
     * Do check feed enabled and prepare response
     *
     * @param string $code
     * @return boolean
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
