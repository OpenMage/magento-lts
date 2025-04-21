<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GoogleAnalytics
 */

/**
 * GoogleTagManager Page Block
 *
 * @package    Mage_GoogleAnalytics
 */
class Mage_GoogleAnalytics_Block_Gtm extends Mage_Core_Block_Template
{
    /**
     * @return bool
     */
    protected function _isAvailable()
    {
        return Mage::helper('googleanalytics')->isGoogleTagManagerAvailable();
    }

    /**
     * Render GA tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isAvailable()) {
            return '';
        }
        return parent::_toHtml();
    }
}
