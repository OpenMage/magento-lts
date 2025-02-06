<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * Class Mage_Tax_Model_Class_Source_Customer
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Class_Source_Customer extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('tax/class_collection')
                ->addFieldToFilter('class_type', Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
                ->load()->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
