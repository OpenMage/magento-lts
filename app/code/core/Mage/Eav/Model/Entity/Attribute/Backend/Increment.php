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
