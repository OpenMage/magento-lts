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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity/Attribute/Model - attribute backend default
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Time_Created extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    /**
     * Returns date format if it matches a certain mask.
     * @param $date
     * @return null|string
     */
    protected function _getFormat($date)
    {
        if (is_string($date) && preg_match('#^\d{4,4}-\d{2,2}-\d{2,2}\s\d{2,2}:\d{2,2}:\d{2,2}$#', $date)
            || preg_match('#^\d{4,4}-\d{2,2}-\d{2,2}\w{1,1}\d{2,2}:\d{2,2}:\d{2,2}[+-]\d{2,2}:\d{2,2}$#', $date)) {
            return 'yyyy-MM-dd HH:mm:ss';
        }
        return null;
    }
    /**
     * Set created date
     * Set created date in UTC time zone
     *
     * @param Mage_Core_Model_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Time_Created
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
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Time_Created
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
