<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Page
 */

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 *
 * @method $this setWelcome(string $value)
 */
class Mage_Page_Block_Html_Welcome extends Mage_Core_Block_Template
{
    /**
     * Get customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get block message
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (empty($this->_data['welcome'])) {
            if (Mage::isInstalled() && $this->_getSession()->isLoggedIn()) {
                $this->_data['welcome'] = $this->__('Welcome, %s!', $this->escapeHtml($this->_getSession()->getCustomer()->getName()));
            } else {
                $this->_data['welcome'] = $this->escapeHtmlAsObject((string) Mage::getStoreConfig('design/header/welcome'));
            }
        }

        return $this->_data['welcome'];
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    public function getCacheTags()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->addModelTags($this->_getSession()->getCustomer());
        }

        return parent::getCacheTags();
    }
}
