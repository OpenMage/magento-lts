<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV Entity Attribute Date time Data Model
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Attribute_Data_Datetime extends Mage_Eav_Model_Attribute_Data_Date
{
    /**
     * Return Data Form Input/Output Filter
     *
     * @return Varien_Data_Form_Filter_Interface|false
     */
    protected function _getFormFilter()
    {
        $filterCode = $this->getAttribute()->getInputFilter();
        if ($filterCode) {
            $filterClass = 'Varien_Data_Form_Filter_' . ucfirst($filterCode);
            if ($filterCode == 'datetime') {
                $filter = new $filterClass(
                    $this->_getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                    $this->_getLocale()->getLocale(),
                );
            } else {
                $filter = new $filterClass();
            }
            return $filter;
        }
        return false;
    }

    /**
     * Get Locale
     *
     * @return Mage_Core_Model_Locale
     */
    protected function _getLocale()
    {
        return Mage::app()->getLocale();
    }
}
