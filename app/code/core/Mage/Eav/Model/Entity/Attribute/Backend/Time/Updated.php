<?php

/**
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity/Attribute/Model - attribute backend default
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Time_Updated extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set modified date
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $object->setData($this->getAttribute()->getAttributeCode(), Varien_Date::now());
        return $this;
    }
}
