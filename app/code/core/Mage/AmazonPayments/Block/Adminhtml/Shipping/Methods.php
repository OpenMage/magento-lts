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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   design_default
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_AmazonPayments_Block_Adminhtml_Shipping_Methods
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $html = '<select name="'.$this->getElement()->getName().'[method]" '.$this->_getDisabled().'>';
        $html .= '<option value="None">'.$this->__(' Select shipping method').'</option>';
        foreach ($this->getShippingMethods() as $carrierCode=>$carrier) {
            $html .= '<optgroup label="'.$carrier['title'].'" style="border-top:solid 1px black; margin-top:3px;">';
            foreach ($carrier['methods'] as $methodCode=>$method) {
                $code = $carrierCode.'/'.$methodCode;
                $html .= '<option value="'.$code.'" '.$this->_getSelected($code).' style="background:white;">'.$method['title'].'</option>';
            }
            $html .= '</optgroup>';
        }
        $html .= '</select>';

        return $html;
    }

    protected function getShippingMethods()
    {
        if (!$this->hasData('shipping_methods')) {
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');

            $storeId = null;
            if (!is_null($website)) {
                $storeId = Mage::getModel('core/website')->load($website, 'code')->getDefaultGroup()->getDefaultStoreId();
            } elseif (!is_null($store)) {
                $storeId = Mage::getModel('core/store')->load($store, 'code')->getId();
            }

            $methods = array();
            $carriers = Mage::getSingleton('shipping/config')->getActiveCarriers($storeId);
            foreach ($carriers as $carrierCode=>$carrierModel) {
                if (!$carrierModel->isActive()) {
                    continue;
                }
                $carrierMethods = $carrierModel->getAllowedMethods();
                if (!$carrierMethods) {
                    continue;
                }
                $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title', $storeId);
                $methods[$carrierCode] = array(
                    'title'   => $carrierTitle,
                    'methods' => array(),
                );
                foreach ($carrierMethods as $methodCode=>$methodTitle) {
                    $methods[$carrierCode]['methods'][$methodCode] = array(
                        'title' => '['.$carrierCode.'] '.$methodTitle,
                    );
                }
            }
            $this->setData('shipping_methods', $methods);
        }
        return $this->getData('shipping_methods');
    }

    protected function _getDisabled()
    {
        return $this->getElement()->getDisabled() ? ' disabled' : '';
    }

    protected function _getValue($key)
    {
        return $this->getElement()->getData('value/'.$key);
    }

    protected function _getSelected($value)
    {
        return $this->getElement()->getData('value/method') == $value ? 'selected="selected"' : '';
    }
}