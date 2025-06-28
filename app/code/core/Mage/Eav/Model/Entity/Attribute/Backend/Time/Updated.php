<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

use Carbon\Carbon;

/**
 * Entity/Attribute/Model - attribute backend default
 *
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
        $object->setData($this->getAttribute()->getAttributeCode(), Carbon::now()->format(Carbon::DEFAULT_TO_STRING_FORMAT));
        return $this;
    }
}
