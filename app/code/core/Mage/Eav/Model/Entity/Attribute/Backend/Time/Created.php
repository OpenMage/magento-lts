<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Entity/Attribute/Model - attribute backend default
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Time_Created extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Returns date format if it matches a certain mask.
     * @param  string      $date
     * @return null|string
     */
    protected function _getFormat($date)
    {
        if (is_string($date) && (preg_match('#^\d{4,4}-\d{2,2}-\d{2,2}\s\d{2,2}:\d{2,2}:\d{2,2}$#', $date)
            || preg_match('#^\d{4,4}-\d{2,2}-\d{2,2}\w{1,1}\d{2,2}:\d{2,2}:\d{2,2}[+-]\d{2,2}:\d{2,2}$#', $date))
        ) {
            return 'yyyy-MM-dd HH:mm:ss';
        }

        return null;
    }

    /**
     * Set created date
     * Set created date in UTC time zone
     *
     * @param  Mage_Core_Model_Abstract $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $date = $object->getData($attributeCode);
        if (is_null($date)) {
            if ($object->isObjectNew()) {
                $object->setData($attributeCode, Varien_Date::now());
            }
        } else {
            // convert to UTC
            $zendDate = Mage::app()->getLocale()->utcDate(null, $date, true, $this->_getFormat($date));
            $object->setData($attributeCode, $zendDate->getIso());
        }

        return $this;
    }

    /**
     * Convert create date from UTC to current store time zone
     *
     * @param  Varien_Object $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $date = $object->getData($attributeCode);

        $zendDate = Mage::app()->getLocale()->storeDate(null, $date, true, $this->_getFormat($date));
        $object->setData($attributeCode, $zendDate->getIso());

        parent::afterLoad($object);

        return $this;
    }
}
