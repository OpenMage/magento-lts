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
class Mage_Eav_Model_Entity_Attribute_Backend_Increment extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set new increment id
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        if (!$object->getId()) {
            $this->getAttribute()->getEntity()->setNewIncrementId($object);
        }

        return $this;
    }
}
