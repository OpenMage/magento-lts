<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Customer account form block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Account extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Initialize form
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_account');
        $form->setFieldNameSuffix('account');

        $customer = Mage::registry('current_customer');

        /** @var Mage_Customer_Model_Form $customerForm */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setEntity($customer)
            ->setFormCode('adminhtml_customer')
            ->initDefaultValues();

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => Mage::helper('customer')->__('Account Information'),
        ]);

        $attributes = $customerForm->getAttributes();
        foreach ($attributes as $attribute) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
            $attribute->setNote(Mage::helper('customer')->__($attribute->getNote()));
            $attribute->unsIsVisible();
        }

        $disableAutoGroupChangeAttributeName = 'disable_auto_group_change';
        $this->_setFieldset($attributes, $fieldset, [$disableAutoGroupChangeAttributeName]);

        $form->getElement('group_id')->setRenderer($this->getLayout()
            ->createBlock('adminhtml/customer_edit_renderer_attribute_group')
            ->setDisableAutoGroupChangeAttribute($customerForm->getAttribute($disableAutoGroupChangeAttributeName))
            ->setDisableAutoGroupChangeAttributeValue($customer->getData($disableAutoGroupChangeAttributeName)));

        if ($customer->getId()) {
            $form->getElement('website_id')->setDisabled('disabled');
            $form->getElement('created_in')->setDisabled('disabled');
        } else {
            $fieldset->removeField('created_in');
            $form->getElement('website_id')->addClass('validate-website-has-store');

            $websites = [];
            foreach (Mage::app()->getWebsites(true) as $website) {
                $websites[$website->getId()] = !is_null($website->getDefaultStore());
            }

            $prefix = $form->getHtmlIdPrefix();

            // @codingStandardsIgnoreStart
            $form->getElement('website_id')->setAfterElementHtml(
                '<script type="text/javascript">'
                . "
                var {$prefix}_websites = " . Mage::helper('core')->jsonEncode($websites) . ";
                Validation.add(
                    'validate-website-has-store',
                    '" . Mage::helper('core')->jsQuoteEscape(
                    Mage::helper('customer')->__('Please select a website which contains store view'),
                ) . "',
                    function(v, elem){
                        return {$prefix}_websites[elem.value] == true;
                    }
                );
                Element.observe('{$prefix}website_id', 'change', function(){
                    Validation.validate($('{$prefix}website_id'))
                }.bind($('{$prefix}website_id')));
                "
                . '</script>',
            );
            // @codingStandardsIgnoreEnd
        }

        $renderer = $this->getStoreSwitcherRenderer();
        $form->getElement('website_id')->setRenderer($renderer);

        $customerStoreId = null;
        if ($customer->getId()) {
            $customerStoreId = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
        }

        $prefixElement = $form->getElement('prefix');
        if ($prefixElement) {
            /** @var Mage_Customer_Helper_Data $helper */
            $helper = $this->helper('customer');
            $prefixOptions = $helper->getNamePrefixOptions($customerStoreId);
            if (!empty($prefixOptions)) {
                $fieldset->removeField($prefixElement->getId());
                $prefixField = $fieldset->addField(
                    $prefixElement->getId(),
                    'select',
                    $prefixElement->getData(),
                    $form->getElement('group_id')->getId(),
                );
                $prefixField->setValues($prefixOptions);
                if ($customer->getId()) {
                    $prefixField->addElementValues($customer->getPrefix());
                }
            }
        }

        $suffixElement = $form->getElement('suffix');
        if ($suffixElement) {
            /** @var Mage_Customer_Helper_Data $helper */
            $helper = $this->helper('customer');
            $suffixOptions = $helper->getNameSuffixOptions($customerStoreId);
            if (!empty($suffixOptions)) {
                $fieldset->removeField($suffixElement->getId());
                $suffixField = $fieldset->addField(
                    $suffixElement->getId(),
                    'select',
                    $suffixElement->getData(),
                    $form->getElement('lastname')->getId(),
                );
                $suffixField->setValues($suffixOptions);
                if ($customer->getId()) {
                    $suffixField->addElementValues($customer->getSuffix());
                }
            }
        }

        $minPasswordLength = Mage::getModel('customer/customer')->getMinPasswordLength();
        if ($customer->getId()) {
            if (!$customer->isReadonly()) {
                // Add password management fieldset
                $newFieldset = $form->addFieldset(
                    'password_fieldset',
                    ['legend' => Mage::helper('customer')->__('Password Management')],
                );
                // New customer password
                $field = $newFieldset->addField(
                    'new_password',
                    'text',
                    [
                        'label' => Mage::helper('customer')->__('New Password'),
                        'name'  => 'new_password',
                        'class' => 'validate-new-password min-pass-length-' . $minPasswordLength,
                        'note' => Mage::helper('adminhtml')
                            ->__('Password must be at least of %d characters.', $minPasswordLength),
                    ],
                );

                $renderer = $this->getLayout()->createBlock('adminhtml/customer_edit_renderer_newpass');
                if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
                    $field->setRenderer($renderer);
                }

                // Prepare customer confirmation control (only for existing customers)
                $confirmationKey = $customer->getConfirmation();
                if ($confirmationKey || $customer->isConfirmationRequired()) {
                    $confirmationAttribute = $customer->getAttribute('confirmation');
                    if (!$confirmationKey) {
                        $confirmationKey = $customer->getRandomConfirmationKey();
                    }

                    $element = $fieldset->addField('confirmation', 'select', [
                        'name'  => 'confirmation',
                        'label' => Mage::helper('customer')->__($confirmationAttribute->getFrontendLabel()),
                    ])->setEntityAttribute($confirmationAttribute)
                        ->setValues(['' => 'Confirmed', $confirmationKey => 'Not confirmed']);

                    // Prepare send welcome email checkbox if customer is not confirmed
                    // no need to add it, if website ID is empty
                    if ($customer->getConfirmation() && $customer->getWebsiteId()) {
                        $fieldset->addField('sendemail', 'checkbox', [
                            'name'  => 'sendemail',
                            'label' => Mage::helper('customer')->__('Send Welcome Email after Confirmation'),
                        ]);
                        $customer->setData('sendemail', '1');
                    }
                }

                if (Mage::helper('customer')->getIsRequireAdminUserToChangeUserPassword()) {
                    $field = $newFieldset->addField(
                        'current_password',
                        'obscure',
                        [
                            'name'  => 'current_password',
                            'label' => Mage::helper('customer')->__('Current Admin Password'),
                            'title' => Mage::helper('customer')->__('Current Admin Password'),
                            'required' => true,
                        ],
                    );

                    $renderer = $this->getLayout()->createBlock('adminhtml/customer_edit_renderer_adminpass');
                    if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
                        $field->setRenderer($renderer);
                    }
                }
            }
        } else {
            $newFieldset = $form->addFieldset(
                'password_fieldset',
                ['legend' => Mage::helper('customer')->__('Password Management')],
            );
            $field = $newFieldset->addField(
                'password',
                'text',
                [
                    'label' => Mage::helper('customer')->__('Password'),
                    'class' => 'input-text required-entry validate-password min-pass-length-' . $minPasswordLength,
                    'name'  => 'password',
                    'required' => true,
                    'note' => Mage::helper('adminhtml')
                        ->__('Password must be at least of %d characters.', $minPasswordLength),
                ],
            );

            $renderer = $this->getLayout()->createBlock('adminhtml/customer_edit_renderer_newpass');
            if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
                $field->setRenderer($renderer);
            }

            // Prepare send welcome email checkbox
            $fieldset->addField('sendemail', 'checkbox', [
                'label' => Mage::helper('customer')->__('Send Welcome Email'),
                'name'  => 'sendemail',
                'id'    => 'sendemail',
            ]);
            $customer->setData('sendemail', '1');
            if (!Mage::app()->isSingleStoreMode()) {
                $fieldset->addField('sendemail_store_id', 'select', [
                    'label' => $this->helper('customer')->__('Send From'),
                    'name' => 'sendemail_store_id',
                    'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                ]);
            }
        }

        // Make sendemail and sendmail_store_id disabled if website_id has empty value
        $isSingleMode = Mage::app()->isSingleStoreMode();
        $sendEmailId = $isSingleMode ? 'sendemail' : 'sendemail_store_id';
        $sendEmail = $form->getElement($sendEmailId);

        $prefix = $form->getHtmlIdPrefix();
        if ($sendEmail) {
            $disableStoreField = '';
            if (!$isSingleMode) {
                $disableStoreField = "$('{$prefix}sendemail_store_id').disabled=(''==this.value || '0'==this.value);";
            }

            $sendEmail->setAfterElementHtml(
                '<script type="text/javascript">'
                . "
                $('{$prefix}website_id').disableSendemail = function() {
                    $('{$prefix}sendemail').disabled = ('' == this.value || '0' == this.value);" .
                    $disableStoreField
                . "}.bind($('{$prefix}website_id'));
                Event.observe('{$prefix}website_id', 'change', $('{$prefix}website_id').disableSendemail);
                $('{$prefix}website_id').disableSendemail();
                "
                . '</script>',
            );
        }

        if ($customer->isReadonly()) {
            foreach ($customer->getAttributes() as $attribute) {
                $element = $form->getElement($attribute->getAttributeCode());
                if ($element) {
                    $element->setReadonly(true, true);
                }
            }
        }

        $form->setValues($customer->getData());
        $this->setForm($form);
        return $this;
    }

    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return [
            'file'      => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_file'),
            'image'     => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_image'),
            'boolean'   => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_boolean'),
        ];
    }
}
