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
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * VAT ID element renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Sales_Order_Address_Form_Billing_Renderer_Vat
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
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
        $this->setTemplate('customer/sales/order/create/billing/form/renderer/vat.phtml');
    }

    /**
     * Retrieve validate button block
     *
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    public function getValidateButton()
    {
        if (is_null($this->_validateButton)) {
            /** @var $form Varien_Data_Form */
            $form = $this->_element->getForm();

            $vatElementId = $this->_element->getHtmlId();

            /** @var $formAccountBlock Mage_Adminhtml_Block_Sales_Order_Create_Form_Account */
            $formAccountBlock = $this->getLayout()->getBlock('form_account');
            $groupIdHtmlId = $formAccountBlock->getForm()->getElement('group_id')->getHtmlId();

            $countryElementId = $form->getElement('country_id')->getHtmlId();
            $validateUrl = Mage::getSingleton('adminhtml/url')
                ->getUrl('*/customer_system_config_validatevat/validateAdvanced');

            $vatValidateOptions = Mage::helper('core')->jsonEncode(array(
                'vatElementId'                  => $vatElementId,
                'countryElementId'              => $countryElementId,
                'groupIdHtmlId'                 => $groupIdHtmlId,
                'validateUrl'                   => $validateUrl,
                'vatValidMessage'               => Mage::helper('customer')->__('The VAT ID is valid. The current Customer Group will be used.'),
                'vatValidAndGroupChangeMessage' =>
                    Mage::helper('customer')->__('Based on the VAT ID, the customer would belong to Customer Group %s.') . "\n"
                    . Mage::helper('customer')->__('The customer is currently assigned to Customer Group %s.') . ' '
                    . Mage::helper('customer')->__('Would you like to change the Customer Group for this order?'),
                'vatInvalidMessage' => Mage::helper('customer')->__('The VAT ID entered (%s) is not valid VAT ID.'),
                'vatValidationFailedMessage'    => Mage::helper('customer')->__('There was an error validating the VAT ID. Please try again later.'),
            ));

            $beforeHtml = '<script type="text/javascript">var vatValidateOptions = '
                . $vatValidateOptions . ';</script>';
            $this->_validateButton = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'       => Mage::helper('customer')->__('Validate VAT Number'),
                'before_html' => $beforeHtml,
                'onclick'     => "order.validateVat(vatValidateOptions)"
            ));
        }
        return $this->_validateButton;
    }
}
