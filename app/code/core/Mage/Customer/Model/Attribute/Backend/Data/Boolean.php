<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Boolean customer attribute backend model
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Attribute_Backend_Data_Boolean extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare data before attribute save
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return $this
     */
    public function beforeSave($customer)
    {
        $attributeName = $this->getAttribute()->getName();
        $inputValue = $customer->getData($attributeName);
        $sanitizedValue = (!empty($inputValue)) ? '1' : '0';
        $customer->setData($attributeName, $sanitizedValue);
        return $this;
    }
}
