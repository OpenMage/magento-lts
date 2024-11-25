<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Field renderer for PayPal merchant country selector
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Field_Country extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     *
     * Request parameters names
     */
    public const REQUEST_PARAM_COUNTRY = 'country';
    public const REQUEST_PARAM_DEFAULT = 'default_country';

    /**
     * Country of default scope
     *
     * @var string|null
     */
    protected $_defaultCountry;

    /**
     * Render country field considering request parameter
     *
     * @return string
     * @throws Exception
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $country = $this->getRequest()->getParam(self::REQUEST_PARAM_COUNTRY);
        if ($country) {
            $element->setValue($country);
        }

        if ($element->getCanUseDefaultValue()) {
            $defaultConfigNode = Mage::getConfig()->getNode(null, 'default');
            if ($defaultConfigNode) {
                $this->_defaultCountry = (string)$defaultConfigNode->descend('paypal/general/merchant_country');
            }
            if (!$this->_defaultCountry) {
                $this->_defaultCountry = Mage::helper('core')->getDefaultCountry();
            }
            if ($country) {
                $shouldInherit = $country == $this->_defaultCountry
                    && $this->getRequest()->getParam(self::REQUEST_PARAM_DEFAULT);
                $element->setInherit($shouldInherit);
            }
            if ($element->getInherit()) {
                $this->_defaultCountry = null;
            }
        }

        return parent::render($element);
    }

    /**
     * Get country selector html
     *
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $configDataModel = Mage::getSingleton('adminhtml/config_data');
        $urlParams = [
            'section' => $configDataModel->getSection(),
            'website' => $configDataModel->getWebsite(),
            'store' => $configDataModel->getStore(),
            self::REQUEST_PARAM_COUNTRY => '__country__',
        ];
        $urlString = $this->helper('core')
            ->jsQuoteEscape(Mage::getModel('adminhtml/url')->getUrl('*/*/*', $urlParams));
        $jsString = '
            $("' . $element->getHtmlId() . '").observe("change", function () {
                location.href = \'' . $urlString . '\'.replace("__country__", this.value);
            });
        ';

        if ($this->_defaultCountry) {
            $urlParams[self::REQUEST_PARAM_DEFAULT] = '__default__';
            $urlString = $this->helper('core')
                ->jsQuoteEscape(Mage::getModel('adminhtml/url')->getUrl('*/*/*', $urlParams));
            $jsParentCountry = $this->helper('core')->jsQuoteEscape($this->_defaultCountry);
            $jsString .= '
                $("' . $element->getHtmlId() . '_inherit").observe("click", function () {
                    if (this.checked) {
                        location.href = \'' . $urlString . '\'.replace("__country__", \'' . $jsParentCountry . '\')
                            .replace("__default__", "1");
                    }
                });
            ';
        }

        /** @var Mage_Adminhtml_Helper_Js $helper */
        $helper = $this->helper('adminhtml/js');
        return parent::_getElementHtml($element) .
            $helper->getScript('document.observe("dom:loaded", function() {' . $jsString . '});');
    }
}
