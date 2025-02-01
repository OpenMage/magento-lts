<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Recurring profile editing form
 * Can work in scope of product edit form
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile_Edit_Form extends Mage_Adminhtml_Block_Abstract
{
    /**
     * Reference to the parent element (optional)
     *
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_parentElement = null;

    /**
     * Whether the form contents can be editable
     *
     * @var bool
     */
    protected $_isReadOnly = false;

    /**
     * Recurring profile instance used for getting labels and options
     *
     * @var Mage_Sales_Model_Recurring_Profile
     */
    protected $_profile = null;

    /**
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * Setter for parent element
     *
     * @return Mage_Sales_Block_Adminhtml_Recurring_Profile_Edit_Form
     */
    public function setParentElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_parentElement = $element;
        return $this;
    }

    /**
     * Setter for current product
     *
     * @return Mage_Sales_Block_Adminhtml_Recurring_Profile_Edit_Form
     */
    public function setProductEntity(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Instantiate a recurring payment profile to use it as a helper
     */
    protected function _construct()
    {
        $this->_profile = Mage::getSingleton('sales/recurring_profile');
        parent::_construct();
    }

    /**
     * Prepare and render the form
     *
     * @return string
     */
    protected function _toHtml()
    {
        // TODO: implement $this->_isReadonly setting
        $form = $this->_prepareForm();
        if ($this->_product && $this->_product->getRecurringProfile()) {
            $form->setValues($this->_product->getRecurringProfile());
        }
        return $form->toHtml();
    }

    /**
     * Instantiate form and fields
     *
     * @return Varien_Data_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            $form::setFieldsetRenderer($renderer);
        }

        $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            $form::setFieldsetElementRenderer($renderer);
        }

        /**
         * if there is a parent element defined, it will be replaced by a hidden element with the same name
         * and overridden by the form elements
         * It is needed to maintain HTML consistency of the parent element's form
         */
        if ($this->_parentElement) {
            $form->setHtmlIdPrefix($this->_parentElement->getHtmlId())
                ->setFieldNameSuffix($this->_parentElement->getName());
            $form->addField('', 'hidden', ['name' => '']);
        }

        $noYes = [Mage::helper('adminhtml')->__('No'), Mage::helper('adminhtml')->__('Yes')];

        // schedule
        $schedule = $form->addFieldset('schedule_fieldset', [
            'legend' => Mage::helper('sales')->__('Schedule'),
            'disabled'  => $this->_isReadOnly,
        ]);
        $schedule->addField('start_date_is_editable', 'select', [
            'name'    => 'start_date_is_editable',
            'label'   => Mage::helper('sales')->__('Customer Can Define Start Date'),
            'comment' => Mage::helper('sales')->__('Whether buyer can define the date when billing for the profile begins.'),
            'options' => $noYes,
            'disabled' => $this->_isReadOnly,
        ]);
        $this->_addField($schedule, 'schedule_description');
        $this->_addField($schedule, 'suspension_threshold');
        $this->_addField($schedule, 'bill_failed_later', ['options' => $noYes], 'select');

        // billing
        $billing = $form->addFieldset('billing_fieldset', [
            'legend' => Mage::helper('sales')->__('Billing'),
            'disabled'  => $this->_isReadOnly,
        ]);
        $this->_addField($billing, 'period_unit', [
            'options' => $this->_getPeriodUnitOptions(Mage::helper('adminhtml')->__('-- Please Select --')),
        ], 'select');
        $this->_addField($billing, 'period_frequency');
        $this->_addField($billing, 'period_max_cycles');

        // trial
        $trial = $form->addFieldset('trial_fieldset', [
            'legend' => Mage::helper('sales')->__('Trial Period'),
            'disabled'  => $this->_isReadOnly,
        ]);
        $this->_addField($trial, 'trial_period_unit', [
            'options' => $this->_getPeriodUnitOptions(Mage::helper('adminhtml')->__('-- Not Selected --')),
        ], 'select');
        $this->_addField($trial, 'trial_period_frequency');
        $this->_addField($trial, 'trial_period_max_cycles');
        $this->_addField($trial, 'trial_billing_amount');

        // initial fees
        $initial = $form->addFieldset('initial_fieldset', [
            'legend' => Mage::helper('sales')->__('Initial Fees'),
            'disabled'  => $this->_isReadOnly,
        ]);
        $this->_addField($initial, 'init_amount');
        $this->_addField($initial, 'init_may_fail', ['options' => $noYes], 'select');

        return $form;
    }

    /**
     * Add a field to the form or fieldset
     * Form and fieldset have same abstract
     *
     * @param Varien_Data_Form|Varien_Data_Form_Element_Fieldset $formOrFieldset
     * @param string $elementName
     * @param array $options
     * @param string $type
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function _addField($formOrFieldset, $elementName, $options = [], $type = 'text')
    {
        $options = array_merge($options, [
            'name'     => $elementName,
            'label'    => $this->_profile->getFieldLabel($elementName),
            'note'     => $this->_profile->getFieldComment($elementName),
            'disabled' => $this->_isReadOnly,
        ]);
        if (in_array($elementName, ['period_unit', 'period_frequency'])) {
            $options['required'] = true;
        }
        return $formOrFieldset->addField($elementName, $type, $options);
    }

    /**
     * Getter for period unit options with "Please Select" label
     *
     * @param string $emptyLabel
     * @return array
     */
    protected function _getPeriodUnitOptions($emptyLabel)
    {
        return array_merge(
            ['' => $emptyLabel],
            $this->_profile->getAllPeriodUnits(),
        );
    }

    /**
     * Set readonly flag
     *
     * @param bool $isReadonly
     * @return $this
     */
    public function setIsReadonly($isReadonly)
    {
        $this->_isReadOnly = $isReadonly;
        return $this;
    }

    /**
     * Get readonly flag
     *
     * @return bool
     */
    public function getIsReadonly()
    {
        return $this->_isReadOnly;
    }
}
