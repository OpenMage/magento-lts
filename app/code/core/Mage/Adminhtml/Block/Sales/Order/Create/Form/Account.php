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
 * Create order account form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Form_Account extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
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

    public function getHeaderCssClass()
    {
        return 'head-account';
    }

    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Account Information');
    }

    public function getForm()
    {
        $this->_prepareForm();
        return $this->_form;
    }

    protected function _prepareForm()
    {
        if (!$this->_form) {

            $display = $this->getDisplayFields();

            $this->_form = new Varien_Data_Form();
            $fieldset = $this->_form->addFieldset('main', array());
            $customerModel = Mage::getModel('customer/customer');

            foreach ($customerModel->getAttributes() as $attribute) {
                if (!array_key_exists($attribute->getAttributeCode(), $display)) {
                    continue;
                }

                if ($inputType = $attribute->getFrontend()->getInputType()) {
                    $field = $display[$attribute->getAttributeCode()];
                    $element = $fieldset->addField($attribute->getAttributeCode(), $inputType,
                        array(
                            'name'      => $attribute->getAttributeCode(),
                            'label'     => $attribute->getFrontend()->getLabel(),
                            'class'     => isset($field['class']) ? $field['class'] : $attribute->getFrontend()->getClass(),
                            'required'  => isset($field['required']) ? $field['required'] : $attribute->getIsRequired(),
                        )
                    )
                    ->setEntityAttribute($attribute)
                    ;

                    if ($inputType == 'select' || $inputType == 'multiselect') {
                        $element->setValues($attribute->getFrontend()->getSelectOptions());
                    }
                    $element->setSortOrder($display[$attribute->getAttributeCode()]);
                }
            }

            /*
            * want to sort element only when there are more than one element
            */
            if ($fieldset->getElements()->count()>1) {
                $fieldset->getElements()->usort(array($this, '_sortMethods'));
            }

            $this->_form->addFieldNameSuffix('order[account]');
            $this->_form->setValues($this->getCustomerData());
        }
        return $this;
    }

    public function _sortMethods($a, $b)
    {
        if (is_object($a)) {
            return (int)$a->sort_order < (int)$b->sort_order ? -1 : ((int)$a->sort_order > (int)$b->sort_order ? 1 : 0);
        }
        return 0;
    }

    /**
     * Return new customer account fields for order
     *
     * @return array
     */
    public function getDisplayFields()
    {
        $fields = array(
            'group_id' => array(
                'order' => 1
            ),
            'email' => array(
                'order' => 2,
                'class' => 'validate-email',
                'required' => true
            ),
        );

        if ($this->getQuote()->getCustomerIsGuest()) {
            unset($fields['group_id']);
        }

        return $fields;
    }


    public function getCustomerData()
    {
        $data = $this->getCustomer()->getData();
        foreach ($this->getQuote()->getData() as $key=>$value) {
            if (strstr($key, 'customer_')) {
                $data[str_replace('customer_', '', $key)] = $value;
            }
        }
        $data['group_id'] = $this->getCreateOrderModel()->getCustomerGroupId();
        $data['email'] = ($this->getQuote()->getCustomerEmail() ? $this->getQuote()->getCustomerEmail() :$this->getCustomer()->getData('email'));
        return $data;
    }
}
