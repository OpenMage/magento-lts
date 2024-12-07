<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Frontend_Datetime extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * Retrieve attribute value
     *
     * @return mixed
     */
    public function getValue(Varien_Object $object)
    {
        $data = '';
        $value = parent::getValue($object);
        $format = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
        );

        if ($value) {
            try {
                $data = Mage::getSingleton('core/locale')->date($value, Zend_Date::ISO_8601, null, false)->toString($format);
            } catch (Exception $e) {
                $data = Mage::getSingleton('core/locale')->date($value, null, null, false)->toString($format);
            }
        }

        return $data;
    }
}
