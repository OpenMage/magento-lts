<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Eav
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
