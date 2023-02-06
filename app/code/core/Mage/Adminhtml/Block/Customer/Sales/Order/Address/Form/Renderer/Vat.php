<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * VAT ID element renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Sales_Order_Address_Form_Renderer_Vat extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    /**
     * Validate button block
     *
     * @var null|Mage_Adminhtml_Block_Widget_Button
     */
    protected $_validateButton = null;

    /**
     * Set custom template for 'VAT number'
     */
    protected function _construct()
    {
        $this->setTemplate('customer/sales/order/create/address/form/renderer/vat.phtml');
    }

    /**
     * Retrieve validate button block
     *
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    public function getValidateButton()
    {
        if (is_null($this->_validateButton)) {
            /** @var Varien_Data_Form $form */
            $form = $this->_element->getForm();

            $vatElementId = $this->_element->getHtmlId();

            $countryElementId = $form->getElement('country_id')->getHtmlId();
            $validateUrl = Mage::getSingleton('adminhtml/url')
                ->getUrl('*/customer_system_config_validatevat/validateAdvanced');

            $groupSuggestionMessage = Mage::helper('customer')->__('The customer is currently assigned to Customer Group %s.')
                . ' ' . Mage::helper('customer')->__('Would you like to change the Customer Group for this order?');

            $vatValidateOptions = Mage::helper('core')->jsonEncode([
                'vatElementId' => $vatElementId,
                'countryElementId' => $countryElementId,
                'groupIdHtmlId' => 'group_id',
                'validateUrl' => $validateUrl,
                'vatValidMessage' => Mage::helper('customer')->__('The VAT ID is valid. The current Customer Group will be used.'),
                'vatValidAndGroupChangeMessage' => Mage::helper('customer')->__('Based on the VAT ID, the customer would belong to the Customer Group %s.')
                    . "\n" . $groupSuggestionMessage,
                'vatInvalidMessage' => Mage::helper('customer')->__('The VAT ID entered (%s) is not a valid VAT ID. The customer would belong to Customer Group %s.')
                    . "\n" . $groupSuggestionMessage,
                'vatValidationFailedMessage'    => Mage::helper('customer')->__('There was an error validating the VAT ID. The customer would belong to Customer Group %s.')
                    . "\n" . $groupSuggestionMessage,
                'vatErrorMessage' => Mage::helper('customer')->__('There was an error validating the VAT ID.')
            ]);

            $optionsVarName = $this->getJsVariablePrefix() . 'VatParameters';
            $beforeHtml = '<script type="text/javascript">var ' . $optionsVarName . ' = ' . $vatValidateOptions
                . ';</script>';
            $this->_validateButton = $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'       => Mage::helper('customer')->__('Validate VAT Number'),
                'before_html' => $beforeHtml,
                'onclick'     => 'order.validateVat(' . $optionsVarName . ')'
            ]);
        }
        return $this->_validateButton;
    }
}
