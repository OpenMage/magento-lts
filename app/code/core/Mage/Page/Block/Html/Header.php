<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Page
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 *
 * @method $this setLogoAlt(string $value)
 * @method $this setLogoSrc(string $value)
 */
class Mage_Page_Block_Html_Header extends Mage_Core_Block_Template
{
    private string $customHeaderImageOption = '';

    public function _construct()
    {
        $this->customHeaderImageOption = Mage::getStoreConfig('design/header/header_image_option');
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
     * @param string $logo_src
     * @param string $logo_alt
     * @return $this
     */
    public function setLogo($logo_src, $logo_alt)
    {
        $this->setLogoSrc($logo_src);
        $this->setLogoAlt($logo_alt);
        return $this;
    }

    /**
     * @return string
     */
    public function getLogoSrc()
    {

        if (!$this->customHeaderImageOption){
            if (empty($this->_data['logo_src'])) {
                $this->_data['logo_src'] = Mage::getStoreConfig('design/header/logo_src');
            }
            return $this->getSkinUrl($this->_data['logo_src']);
        } else {
            $this->_data['logo_src'] = Mage::getBaseUrl('media') . 'header/' .
                Mage::getStoreConfig('design/header/custom_logo_src');
        }

        return $this->_data['logo_src'];
    }

    /**
     * @return string
     */
    public function getLogoSrcSmall()
    {
        if (!$this->customHeaderImageOption){
            if (empty($this->_data['logo_src'])) {
                $this->_data['logo_src'] = Mage::getStoreConfig('design/header/logo_src');
            }
            return $this->getSkinUrl($this->_data['logo_src_small']);
        } else {
            $this->_data['logo_src'] = Mage::getBaseUrl('media') . 'header/' .
                Mage::getStoreConfig('design/header/custom_logo_src_small');
        }

        return $this->_data['logo_src_small'];
    }

    /**
     * @return string
     */
    public function getLogoAlt()
    {
        if (!$this->customHeaderImageOption){
            if (empty($this->_data['logo_alt'])) {
                $this->_data['logo_alt'] = Mage::getStoreConfig('design/header/logo_alt');
            }
        } else {
            $this->_data['logo_alt'] = Mage::getStoreConfig('design/header/custom_logo_alt');
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
                $this->_data['welcome'] = Mage::getStoreConfig('design/header/welcome');
            }
        }

        return $this->_data['welcome'];
    }
}
