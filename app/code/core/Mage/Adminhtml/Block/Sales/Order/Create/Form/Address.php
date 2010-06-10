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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order create address form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Form_Address extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected $_form;

    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
        );
    }

    public function getAddressCollection()
    {
        return $this->getCustomer()->getAddresses();
    }

    public function getAddressCollectionJson()
    {
        $data = array();
        foreach ($this->getAddressCollection() as $address) {
            $data[$address->getId()] = $address->getData();
        }
        return Mage::helper('core')->jsonEncode($data);
    }

    public function getForm()
    {
        $this->_prepareForm();
        return $this->_form;
    }

    protected function _prepareForm()
    {
        if (!$this->_form) {
            $this->_form = new Varien_Data_Form();
            $fieldset = $this->_form->addFieldset('main', array('no_container'=>true));
            $addressModel = Mage::getModel('customer/address');

            foreach ($addressModel->getAttributes() as $attribute) {
                if ($attribute->hasData('is_visible') && !$attribute->getIsVisible()) {
                    continue;
                }
                if ($inputType = $attribute->getFrontend()->getInputType()) {
                    $element = $fieldset->addField($attribute->getAttributeCode(), $inputType,
                        array(
                            'name'  => $attribute->getAttributeCode(),
                            'label' => $this->__($attribute->getFrontend()->getLabel()),
                            'class' => $attribute->getFrontend()->getClass(),
                            'required' => $attribute->getIsRequired(),
                        )
                    )
                    ->setEntityAttribute($attribute);

                    if ('street' === $element->getName()) {
                        $lines = Mage::getStoreConfig('customer/address/street_lines', $this->getStoreId());
                        $element->setLineCount($lines);
                    }

                    if ($inputType == 'select' || $inputType == 'multiselect') {
                        $element->setValues($attribute->getFrontend()->getSelectOptions());
                    }
                }
            }

            if ($regionElement = $this->_form->getElement('region')) {
                $regionElement->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/customer_edit_renderer_region')
                );
            }
            if ($regionElement = $this->_form->getElement('region_id')) {
                $regionElement->setNoDisplay(true);
            }
            $this->_form->setValues($this->getFormValues());
        }
        return $this;
    }

    public function getFormValues()
    {
        return array();
    }

    public function getAddressId()
    {
        return false;
    }

    public function getAddressAsString($address)
    {
        return $address->format('oneline');
    }
}
