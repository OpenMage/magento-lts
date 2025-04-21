<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 *
 * Start Date attribute backend
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Startdate extends Mage_Eav_Model_Entity_Attribute_Backend_Datetime
{
    /**
     * Get attribute value for save.
     *
     * @param Varien_Object $object
     * @return string|bool
     */
    protected function _getValueForSave($object)
    {
        $attributeName  = $this->getAttribute()->getName();
        return $object->getData($attributeName);
    }

    /**
     * Before save hook.
     * Prepare attribute value for save
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $startDate = $this->_getValueForSave($object);
        if ($startDate === false) {
            return $this;
        }
        parent::beforeSave($object);
        return $this;
    }

    /**
     * Product from date attribute validate function.
     * In case invalid data throws exception.
     *
     * @param Mage_Catalog_Model_Product $object
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     * @return bool
     */
    public function validate($object)
    {
        $attr      = $this->getAttribute();
        $maxDate   = $attr->getMaxValue();
        $startDate = $this->_getValueForSave($object);
        if ($startDate === false) {
            return true;
        }

        if ($maxDate) {
            $date     = Mage::getModel('core/date');
            $value    = $date->timestamp($startDate);
            $maxValue = $date->timestamp($maxDate);

            if ($value > $maxValue) {
                $message = Mage::helper('catalog')->__('The From Date value should be less than or equal to the To Date value.');
                $eavExc  = new Mage_Eav_Model_Entity_Attribute_Exception($message);
                $eavExc->setAttributeCode($attr->getName());
                throw $eavExc;
            }
        }
        return true;
    }
}
