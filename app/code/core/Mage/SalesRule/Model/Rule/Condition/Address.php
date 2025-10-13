<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * Class Mage_SalesRule_Model_Rule_Condition_Address
 *
 * @package    Mage_SalesRule
 *
 * @method $this setAttributeOption(array $attributes)
 */
class Mage_SalesRule_Model_Rule_Condition_Address extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * @return $this|Mage_Rule_Model_Condition_Abstract
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'base_subtotal' => Mage::helper('salesrule')->__('Subtotal'),
            'total_qty' => Mage::helper('salesrule')->__('Total Items Quantity'),
            'weight' => Mage::helper('salesrule')->__('Total Weight'),
            'payment_method' => Mage::helper('salesrule')->__('Payment Method'),
            'shipping_method' => Mage::helper('salesrule')->__('Shipping Method'),
            'postcode' => Mage::helper('salesrule')->__('Shipping Postcode'),
            'region' => Mage::helper('salesrule')->__('Shipping Region'),
            'region_id' => Mage::helper('salesrule')->__('Shipping State/Province'),
            'country_id' => Mage::helper('salesrule')->__('Shipping Country'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return match ($this->getAttribute()) {
            'base_subtotal', 'weight', 'total_qty' => 'numeric',
            'shipping_method', 'payment_method', 'country_id', 'region_id' => 'select',
            default => 'string',
        };
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return match ($this->getAttribute()) {
            'shipping_method', 'payment_method', 'country_id', 'region_id' => 'select',
            default => 'text',
        };
    }

    /**
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $options = match ($this->getAttribute()) {
                'country_id' => Mage::getModel('adminhtml/system_config_source_country')
                    ->toOptionArray(),
                'region_id' => Mage::getModel('adminhtml/system_config_source_allregion')
                    ->toOptionArray(),
                'shipping_method' => Mage::getModel('adminhtml/system_config_source_shipping_allmethods')
                    ->toOptionArray(),
                'payment_method' => Mage::getModel('adminhtml/system_config_source_payment_allmethods')
                    ->toOptionArray(),
                default => [],
            };
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param Mage_Sales_Model_Quote_Address $object
     * @inheritDoc
     */
    public function validate(Varien_Object $object)
    {
        $address = $object;
        if (!$address instanceof Mage_Sales_Model_Quote_Address) {
            if ($object->getQuote()->isVirtual()) {
                $address = $object->getQuote()->getBillingAddress();
            } else {
                $address = $object->getQuote()->getShippingAddress();
            }
        }

        if ($this->getAttribute() == 'payment_method' && ! $address->hasPaymentMethod()) {
            $address->setPaymentMethod($object->getQuote()->getPayment()->getMethod());
        }

        return parent::validate($address);
    }
}
