<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * Rss abstract controller
 *
 * @package    Mage_Rss
 */
class Mage_Rss_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Check feed enabled in config
     *
     * @param  string $code
     * @return bool
     */
    protected function isFeedEnable($code)
    {
        /** @var Mage_Rss_Helper_Data $helper */
        $helper = $this->_getHelper('rss');
        return $helper->isRssEnabled() && Mage::getStoreConfig('rss/' . $code);
    }

    /**
     * Force the XML content type on the response
     *
     * The default response header is text/html and setHeader() appends by default,
     * so the header has to be replaced. Feeds rendered as HTML would let any markup
     * kept in a feed entry, including the contents of a CDATA section, be parsed and
     * executed by the browser.
     *
     * @return $this
     */
    protected function _setFeedContentType()
    {
        $this->getResponse()
            ->setHeader('Content-Type', 'text/xml; charset=UTF-8', true)
            ->setHeader('X-Content-Type-Options', 'nosniff', true);
        return $this;
    }

    /**
     * Do check feed enabled and prepare response
     *
     * @param  string $code
     * @return bool
     */
    protected function checkFeedEnable($code)
    {
        if ($this->isFeedEnable($code)) {
            $this->_setFeedContentType();
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
     * @param  string                    $name
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($name)
    {
        return Mage::helper($name);
    }
}
