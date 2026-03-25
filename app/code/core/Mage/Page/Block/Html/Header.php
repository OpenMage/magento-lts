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
    protected function _construct()
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
     * @param  string $logoSrc
     * @param  string $logoAlt
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
            $src = (string) Mage::getStoreConfig(Mage_Page_Helper_Data::XML_PATH_LOGO_SRC);
            $this->_data['logo_src'] = Mage::helper('page')->getLogoSrc($src);
        }

        return $this->_data['logo_src'];
    }

    /**
     * @return string
     */
    public function getLogoSrcSmall()
    {
        if (empty($this->_data['logo_src_small'])) {
            // Check if user wants to use the same image as main logo
            $useSameAsMain = Mage::getStoreConfigFlag(Mage_Page_Helper_Data::XML_PATH_LOGO_SRC_SMALL_SAME_AS_MAIN);
            if ($useSameAsMain) {
                $this->_data['logo_src_small'] = $this->getLogoSrc();
            } else {
                $src = (string) Mage::getStoreConfig(Mage_Page_Helper_Data::XML_PATH_LOGO_SRC_SMALL);
                $this->_data['logo_src_small'] = Mage::helper('page')->getLogoSrc($src);
            }
        }

        return $this->_data['logo_src_small'];
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
     * @return mixed
     * @deprecated after 1.7.0.2
     * @see Mage_Page_Block_Html_Welcome
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
