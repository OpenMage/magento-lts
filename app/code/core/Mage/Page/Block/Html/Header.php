<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */

/**
 * Html page block
 *
 * @package    Mage_Page
 *
 * @method $this setLogoAlt(string $value)
 * @method $this setLogoSrc(string $value)
 */
class Mage_Page_Block_Html_Header extends Mage_Core_Block_Template
{
    public function _construct()
    {
        $this->setTemplate('page/html/header.phtml');
    }

    /**
     * Check if current url is url for home page
     *
     * @return bool
     */
    public function getIsHomePage()
    {
        return $this->getUrl('') == $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
    }

    /**
     * @param string $logoSrc
     * @param string $logoAlt
     * @return $this
     */
    public function setLogo($logoSrc, $logoAlt)
    {
        $this->setLogoSrc($logoSrc);
        $this->setLogoAlt($logoAlt);
        return $this;
    }

    /**
     * @return string
     */
    public function getLogoSrc()
    {
        if (empty($this->_data['logo_src'])) {
            $this->_data['logo_src'] = $this->escapeHtmlAsObject((string) Mage::getStoreConfig('design/header/logo_src'));
        }
        return $this->getSkinUrl($this->_data['logo_src']);
    }

    /**
     * @return string
     */
    public function getLogoSrcSmall()
    {
        if (empty($this->_data['logo_src_small'])) {
            $this->_data['logo_src_small'] = $this->escapeHtmlAsObject((string) Mage::getStoreConfig('design/header/logo_src_small'));
        }
        return $this->getSkinUrl($this->_data['logo_src_small']);
    }

    /**
     * @return string
     */
    public function getLogoAlt()
    {
        if (empty($this->_data['logo_alt'])) {
            $this->_data['logo_alt'] = $this->escapeHtmlAsObject((string) Mage::getStoreConfig('design/header/logo_alt'));
        }
        return $this->_data['logo_alt'];
    }

    /**
     * Retrieve page welcome message
     *
     * @deprecated after 1.7.0.2
     * @see Mage_Page_Block_Html_Welcome
     * @return mixed
     */
    public function getWelcome()
    {
        if (empty($this->_data['welcome'])) {
            if (Mage::isInstalled() && Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_data['welcome'] = $this->__('Welcome, %s!', $this->escapeHtml(Mage::getSingleton('customer/session')->getCustomer()->getName()));
            } else {
                $this->_data['welcome'] = $this->escapeHtmlAsObject((string) Mage::getStoreConfig('design/header/welcome'));
            }
        }

        return $this->_data['welcome'];
    }
}
