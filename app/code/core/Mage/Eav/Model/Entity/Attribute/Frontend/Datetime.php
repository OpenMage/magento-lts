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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Eav_Model_Entity_Attribute_Frontend_Datetime extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * Retreive attribute value
     *
     * @param Varien_Object $object
     * @return mixed
     */
    public function getValue(Varien_Object $object)
    {
        $data = '';
        $value = parent::getValue($object);
        $format = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
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
