<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Adminhtml_Model_System_Config_Backend_Baseurl extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();

        if (!preg_match('#^{{((un)?secure_)?base_url}}#', $value)) {
            $value = Mage::helper('core/url')->encodePunycode($value);
            $parsedUrl = parse_url($value);
            if (!isset($parsedUrl['scheme']) || !isset($parsedUrl['host'])) {
                Mage::throwException(Mage::helper('core')->__('The %s you entered is invalid. Please make sure that it follows "http://domain.com/" format.', $this->getFieldConfig()->label));
            }
        }

        $value = rtrim($value,  '/');
        /**
         * If value is special ({{}}) we don't need add slash
         */
        if (!preg_match('#}}$#', $value)) {
            $value.= '/';
        }


        $this->setValue($value);
        return $this;
    }

    /**
     * Clean compiled JS/CSS when updating url configuration settings
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::getModel('core/design_package')->cleanMergedJsCss();
        }
    }

    /**
     * Processing object after load data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        if (!preg_match('#^{{((un)?secure_)?base_url}}#', $value)) {
            $value = Mage::helper('core/url')->decodePunycode($value);
        }
        $this->setValue($value);
        return parent::_afterLoad();
    }
}
