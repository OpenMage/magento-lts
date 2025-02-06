<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 */

/**
 * GoogleTagManager Page Block
 *
 * @category   Mage
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
